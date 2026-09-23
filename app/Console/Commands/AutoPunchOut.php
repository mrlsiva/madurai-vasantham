<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Staff who did not punch out before midnight are punched out automatically
 * at their shift end time (or at their punch in time, if they punched in
 * after the shift had already ended). Scheduled just after midnight.
 */
class AutoPunchOut extends Command
{
    protected $signature = 'attendance:auto-punch-out
                            {--date= : Only this day (YYYY-MM-DD)}
                            {--all : Every day before today (default: the last 7 days)}
                            {--dry-run : Show what would change without saving}';

    protected $description = 'Punch out staff who did not punch out before midnight, at their shift end time';

    public function handle(): int
    {
        $rows = DB::table('attendance as a')
            ->leftJoin('shifttime as s', 's.shiftId', '=', 'a.shiftId')
            ->leftJoin('shifttimewithusers as su', 'su.userId', '=', 'a.userId')
            ->leftJoin('shifttime as us', 'us.shiftId', '=', 'su.shiftId')
            ->whereNull('a.endTime')
            ->where('a.is_leave', '!=', 1)
            ->when($this->option('date'),
                fn ($q) => $q->where('a.startDate', $this->option('date')),
                fn ($q) => $q->where('a.startDate', '<', Carbon::today()->toDateString())
                    ->when(!$this->option('all'), fn ($q) => $q->where('a.startDate', '>=', Carbon::today()->subDays(7)->toDateString())))
            ->get(['a.attandanceId', 'a.userId', 'a.startDate', 'a.startTime',
                   DB::raw('COALESCE(s.endTime, us.endTime) as shiftEnd')]);

        $done = 0;
        foreach ($rows as $row) {
            if (!$row->shiftEnd) {
                $this->warn("#{$row->attandanceId} user {$row->userId}: no shift, skipped");
                continue;
            }

            $in  = Carbon::parse($row->startTime);
            $end = Carbon::parse($row->startDate.' '.$row->shiftEnd);
            if ($end->lt($in)) {
                $end = $in->copy(); // punched in after shift end: nothing to pay
            }

            $this->line("#{$row->attandanceId} user {$row->userId} {$row->startDate}: punch out at ".$end->format('h:i A'));

            if (!$this->option('dry-run')) {
                DB::table('attendance')->where('attandanceId', $row->attandanceId)->whereNull('endTime')->update([
                    'endTime'           => $end,
                    'endDate'           => $end->toDateString(),
                    'is_auto_punch_out' => 1,
                    'modifiedBy'        => 0,
                    'modifiedOn'        => Carbon::now(),
                ]);
            }
            $done++;
        }

        $this->info(($this->option('dry-run') ? 'Would punch out ' : 'Punched out ').$done.' record(s).');
        return self::SUCCESS;
    }
}
