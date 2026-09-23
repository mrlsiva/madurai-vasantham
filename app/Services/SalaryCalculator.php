<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use App\Models\StaffSalary;
use App\Models\SalaryWorkingDay;

/**
 * Salary by the minute, from attendance punches.
 *
 *   per-minute rate = monthly salary / (working days of the month x shift minutes per day)
 *   earned          = paid minutes in the month x per-minute rate
 *
 * Paid minutes for a day run from punch in to:
 *   - punch out, if the staff punched out (capped at 12:00 midnight),
 *   - otherwise the shift end time (the staff forgot to punch out),
 * minus any permission time taken inside that window. Leave days are not paid.
 */
class SalaryCalculator
{
    /** Length of a shift in minutes; an end before the start means it runs past midnight. */
    public static function shiftMinutes(string $startTime, string $endTime): int
    {
        $start = Carbon::parse('2000-01-01 '.$startTime);
        $end   = Carbon::parse('2000-01-01 '.$endTime);
        if ($end->lte($start)) {
            $end->addDay();
        }
        return intdiv($start->diffInSeconds($end), 60);
    }

    /**
     * Paid minutes for one attendance day.
     *
     * endReason: 'punch_out' | 'midnight' (punched out after 12 AM) |
     *            'shift_end' (no punch out) | 'in_progress' (today, still working) |
     *            'after_shift' (no punch out and punched in after shift end: nothing paid)
     */
    public static function day(Carbon $in, ?Carbon $out, string $shiftEndTime, ?Carbon $permStart = null, ?Carbon $permEnd = null, ?Carbon $now = null): array
    {
        $now      = $now ?? Carbon::now();
        $midnight = $in->copy()->startOfDay()->addDay();

        if ($out) {
            $end    = $out->gt($midnight) ? $midnight : $out->copy();
            $reason = $out->gt($midnight) ? 'midnight' : 'punch_out';
        } else {
            $shiftEnd = Carbon::parse($in->toDateString().' '.$shiftEndTime);
            if ($shiftEnd->lte($in)) {
                $end    = $in->copy();
                $reason = 'after_shift';
            } else {
                $end    = $shiftEnd->gt($midnight) ? $midnight : $shiftEnd;
                $reason = 'shift_end';
            }
            if ($end->gt($now)) {
                $end    = $now->copy()->max($in);
                $reason = 'in_progress';
            }
        }

        $worked = intdiv(max(0, $in->diffInSeconds($end, false)), 60);

        // Permission time inside the worked window; an unclosed permission runs to the end of the day.
        $permission = 0;
        if ($permStart) {
            $pFrom = $permStart->copy()->max($in);
            $pTo   = ($permEnd ?? $end)->copy()->min($end);
            $permission = min($worked, intdiv(max(0, $pFrom->diffInSeconds($pTo, false)), 60));
        }

        return [
            'in'         => $in,
            'out'        => $out,
            'end'        => $end,
            'endReason'  => $reason,
            'worked'     => $worked,
            'permission' => $permission,
            'paid'       => $worked - $permission,
        ];
    }

    /** Working days set for the month, or null if the admin has not set them yet. */
    public function workingDays(Carbon $month): ?int
    {
        $row = SalaryWorkingDay::where('month', $month->copy()->startOfMonth()->toDateString())->first();
        return $row ? (int) $row->working_days : null;
    }

    /** Salary summary for every active staff member for one month. */
    public function month(Carbon $month, ?int $onlyUserId = null): array
    {
        $from = $month->copy()->startOfMonth();
        $to   = $month->copy()->endOfMonth();
        $workingDays = $this->workingDays($from);

        $staff = DB::table('users as u')
            ->leftJoin('shifttimewithusers as su', 'su.userId', '=', 'u.userId')
            ->leftJoin('shifttime as s', 's.shiftId', '=', 'su.shiftId')
            ->whereNull('u.deleted_at')
            ->when($onlyUserId, fn ($q) => $q->where('u.userId', $onlyUserId))
            ->orderBy('u.userId')
            ->get(['u.userId', 'u.firstName', 'u.lastName', 'u.mobile', 's.shiftName', 's.startTime as shiftStart', 's.endTime as shiftEnd']);

        // Latest salary that was effective on the 1st of this month, per staff.
        $salaries = StaffSalary::where('effective_from', '<=', $from->toDateString())
            ->when($onlyUserId, fn ($q) => $q->where('userId', $onlyUserId))
            ->orderByDesc('effective_from')
            ->get()
            ->unique('userId')
            ->keyBy('userId');

        $attendance = DB::table('attendance as a')
            ->leftJoin('shifttime as s', 's.shiftId', '=', 'a.shiftId')
            ->whereBetween('a.startDate', [$from->toDateString(), $to->toDateString()])
            ->where('a.is_leave', '!=', 1)
            ->when($onlyUserId, fn ($q) => $q->where('a.userId', $onlyUserId))
            ->orderBy('a.startTime')
            ->get(['a.userId', 'a.startDate', 'a.startTime', 'a.endTime', 'a.is_auto_punch_out', 'a.permission_startTime', 'a.permission_endTime', 's.endTime as dayShiftEnd'])
            ->groupBy('userId');

        // Leave days per staff (not paid, shown next to days worked)
        $leaves = DB::table('attendance')
            ->whereBetween('startDate', [$from->toDateString(), $to->toDateString()])
            ->where('is_leave', 1)
            ->when($onlyUserId, fn ($q) => $q->where('userId', $onlyUserId))
            ->select('userId', DB::raw('COUNT(DISTINCT startDate) as days'))
            ->groupBy('userId')
            ->pluck('days', 'userId');

        $rows = [];
        foreach ($staff as $s) {
            $salary       = $salaries->get($s->userId);
            $shiftMinutes = ($s->shiftStart && $s->shiftEnd) ? self::shiftMinutes($s->shiftStart, $s->shiftEnd) : null;

            $days = [];
            foreach ($attendance->get($s->userId, collect()) as $a) {
                $shiftEnd = $a->dayShiftEnd ?? $s->shiftEnd;
                if (!$shiftEnd) {
                    continue;
                }
                // An automatic punch out (attendance:auto-punch-out) is not a real punch
                // out: count it as a missing punch out, which also stops at shift end.
                $auto = (int) $a->is_auto_punch_out === 1;
                $days[] = ['date' => $a->startDate, 'auto' => $auto] + self::day(
                    Carbon::parse($a->startTime),
                    ($a->endTime && !$auto) ? Carbon::parse($a->endTime) : null,
                    $shiftEnd,
                    $a->permission_startTime ? Carbon::parse($a->permission_startTime) : null,
                    $a->permission_endTime ? Carbon::parse($a->permission_endTime) : null
                );
            }

            // Working days set for this staff in exactly this month override the month's value
            $customDays = ($salary && $salary->working_days && $salary->effective_from->isSameDay($from))
                ? (int) $salary->working_days : null;
            $staffDays  = $customDays ?? $workingDays;

            $paid     = array_sum(array_column($days, 'paid'));
            $expected = ($staffDays && $shiftMinutes) ? $staffDays * $shiftMinutes : null;
            $rate     = ($salary && $expected) ? (float) $salary->monthly_salary / $expected : null;

            $rows[] = [
                'userId'            => $s->userId,
                'name'              => trim($s->firstName.' '.$s->lastName),
                'mobile'            => $s->mobile,
                'shiftName'         => $s->shiftName,
                'shiftMinutes'      => $shiftMinutes,
                'salary'            => $salary ? (float) $salary->monthly_salary : null,
                'salaryFrom'        => $salary ? $salary->effective_from : null,
                'workingDays'       => $staffDays,
                'customWorkingDays' => $customDays !== null,
                'daysWorked'        => count($days),
                'leaveDays'         => (int) ($leaves[$s->userId] ?? 0),
                'absentDays'        => $staffDays ? max(0, $staffDays - count($days) - (int) ($leaves[$s->userId] ?? 0)) : null,
                'paidMinutes'       => $paid,
                'permissionMinutes' => array_sum(array_column($days, 'permission')),
                'missingPunchOut'   => count(array_filter($days, fn ($d) => in_array($d['endReason'], ['shift_end', 'after_shift']))),
                'expectedMinutes'   => $expected,
                'rate'              => $rate,
                'earned'            => $rate !== null ? round($paid * $rate, 2) : null,
                'days'              => $days,
            ];
        }

        return ['month' => $from, 'workingDays' => $workingDays, 'rows' => $rows];
    }

    /** "hh:mm" for a number of minutes, e.g. 125 -> "2:05". */
    public static function hm(?int $minutes): string
    {
        if ($minutes === null) {
            return '-';
        }
        return intdiv($minutes, 60).':'.str_pad($minutes % 60, 2, '0', STR_PAD_LEFT);
    }
}
