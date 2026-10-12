<?php

namespace App\Http\Controllers;

use App\Exports\AttendanceReportExport;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public const TYPES = ['daily' => 'Daily', 'weekly' => 'Weekly', 'monthly' => 'Monthly', 'custom' => 'Custom'];

    /* Report page: filters + on-screen preview */
    public function index(Request $request)
    {
        $filters = $this->filters($request);
        $report  = $this->build($filters);
        $users   = User::orderBy('userId')->get(['userId', 'firstName', 'lastName']);
        $types   = self::TYPES;

        return view('admin.report.index', compact('filters', 'report', 'users', 'types'));
    }

    /* Same report as the page, downloaded as Excel (xlsx) or PDF */
    public function download(Request $request, string $format)
    {
        $filters = $this->filters($request);
        $report  = $this->build($filters);
        /* e.g. Monthly-Report-September-2026-100010-M-SIVA, Daily-Report-12-10-2026-All-Staff */
        $fileName = preg_replace('/[^A-Za-z0-9]+/', '-', $report['title'].' '.$report['staff']);
        $fileName = trim($fileName, '-');

        if ($format === 'pdf') {
            return Pdf::loadView('admin.report.pdf', compact('report'))
                ->setPaper('a4', 'landscape')
                ->setOption('isFontSubsettingEnabled', true)
                ->download($fileName.'.pdf');
        }

        return Excel::download(new AttendanceReportExport($report), $fileName.'.xlsx');
    }

    /* Validated filters with defaults: daily report for today */
    private function filters(Request $request): array
    {
        $request->validate([
            'type'    => 'nullable|in:'.implode(',', array_keys(self::TYPES)),
            'date'    => 'nullable|date',
            'week'    => ['nullable', 'regex:/^\d{4}-W\d{2}$/'],
            'month'   => ['nullable', 'regex:/^\d{4}-\d{2}$/'],
            'from'    => 'nullable|required_if:type,custom|date',
            'to'      => 'nullable|required_if:type,custom|date|after_or_equal:from',
            'user_id' => 'nullable|integer',
        ]);

        $today = Carbon::today();

        return [
            'type'    => $request->input('type', 'daily'),
            'date'    => $request->input('date', $today->toDateString()),
            'week'    => $request->input('week', $today->format('o-\WW')),
            'month'   => $request->input('month', $today->format('Y-m')),
            'from'    => $request->input('from', $today->copy()->startOfMonth()->toDateString()),
            'to'      => $request->input('to', $today->toDateString()),
            'user_id' => $request->input('user_id'),
        ];
    }

    /* [from, to] dates and a readable period label for the selected report type */
    private function period(array $filters): array
    {
        switch ($filters['type']) {
            case 'weekly':
                [$year, $week] = explode('-W', $filters['week']);
                $from = Carbon::now()->setISODate((int) $year, (int) $week)->startOfWeek();
                $to   = $from->copy()->endOfWeek()->startOfDay();
                $period = 'Week '.(int) $week.' '.$year.' ('.$from->format('d-m-Y').' to '.$to->format('d-m-Y').')';
                break;
            case 'monthly':
                $from = Carbon::createFromFormat('Y-m-d', $filters['month'].'-01')->startOfDay();
                $to   = $from->copy()->endOfMonth()->startOfDay();
                $period = $from->format('F Y');
                break;
            case 'custom':
                $from = Carbon::parse($filters['from'])->startOfDay();
                $to   = Carbon::parse($filters['to'])->startOfDay();
                $period = $from->format('d-m-Y').' to '.$to->format('d-m-Y');
                break;
            default:
                $from = Carbon::parse($filters['date'])->startOfDay();
                $to   = $from->copy();
                $period = $from->format('d-m-Y');
        }

        return [$from, $to, $period];
    }

    /* Attendance rows for the period, plus a per-staff summary */
    private function build(array $filters): array
    {
        [$from, $to, $period] = $this->period($filters);

        $records = DB::table('attendance as a')
            ->leftJoin('users as u', 'u.userId', '=', 'a.userId')
            ->leftJoin('shifttime as s', 's.shiftId', '=', 'a.shiftId')
            ->whereBetween('a.startDate', [$from->toDateString(), $to->toDateString()])
            ->when($filters['user_id'], fn ($q, $userId) => $q->where('a.userId', $userId))
            ->select(
                'a.userId', 'a.startDate', 'a.startTime', 'a.endTime', 'a.is_auto_punch_out',
                'a.is_permission', 'a.permission_startTime', 'a.permission_endTime', 'a.is_leave', 'a.reason',
                'u.firstName', 'u.lastName', 'u.mobile',
                's.shiftName', 's.startTime as shiftStart', 's.endTime as shiftEnd'
            )
            ->orderBy('a.startDate')
            ->orderBy('a.userId')
            ->get();

        $rows = $records->map(fn ($r) => $this->row($r));

        /* Every active staff member appears in the summary, even with no attendance in the period */
        $staff = User::when($filters['user_id'], fn ($q, $userId) => $q->where('userId', $userId))
            ->orderBy('userId')
            ->get(['userId', 'firstName', 'lastName', 'mobile']);

        $summary = $staff->map(function ($user) use ($rows) {
            $own = $rows->where('userId', $user->userId);
            $present = $own->where('isLeave', false);

            return [
                'userId'            => $user->userId,
                'name'              => trim($user->firstName.' '.$user->lastName),
                'mobile'            => $user->mobile,
                'present'           => $present->count(),
                'leave'             => $own->where('isLeave', true)->count(),
                'permission'        => $present->where('permissionMinutes', '>', 0)->count(),
                'permissionMinutes' => $present->sum('permissionMinutes'),
                'late'              => $present->where('lateMinutes', '>', 0)->count(),
                'lateMinutes'       => $present->sum('lateMinutes'),
                'workedMinutes'     => $present->sum('workedMinutes'),
                'missingOut'        => $present->where('punchOut', '-')->count(),
            ];
        })->values();

        /* Selected staff filter as text: "100010 - M SIVA" or "All Staff" */
        $selected = $filters['user_id'] ? User::withTrashed()->find($filters['user_id']) : null;
        $staffLabel = $selected ? $selected->userId.' - '.trim($selected->firstName.' '.$selected->lastName) : 'All Staff';

        return [
            'title'   => self::TYPES[$filters['type']].' Report - '.$period,
            'period'  => $period,
            'staff'   => $staffLabel,
            'from'    => $from,
            'to'      => $to,
            'days'    => $from->diffInDays($to) + 1,
            'rows'    => $rows->values(),
            'summary' => $summary,
            'totals'  => [
                'present'       => $summary->sum('present'),
                'leave'         => $summary->sum('leave'),
                'permission'    => $summary->sum('permission'),
                'late'          => $summary->sum('late'),
                'workedMinutes' => $summary->sum('workedMinutes'),
            ],
        ];
    }

    /* One attendance record, with worked / late / permission time in minutes */
    private function row(object $r): array
    {
        $isLeave = $r->is_leave == 1;
        $in  = $r->startTime ? Carbon::parse($r->startTime) : null;
        $out = $r->endTime ? Carbon::parse($r->endTime) : null;

        $worked = (!$isLeave && $in && $out && $out->gt($in)) ? $in->diffInMinutes($out) : 0;

        $late = 0;
        if (!$isLeave && $in && $r->shiftStart) {
            $shiftStart = $in->copy()->setTimeFromTimeString($r->shiftStart);
            $late = $in->gt($shiftStart) ? $shiftStart->diffInMinutes($in) : 0;
        }

        $permission = 0;
        if ($r->is_permission == 1 && $r->permission_startTime && $r->permission_endTime) {
            $permission = max(0, Carbon::parse($r->permission_startTime)->diffInMinutes(Carbon::parse($r->permission_endTime), false));
        }

        return [
            'userId'            => $r->userId,
            'name'              => trim($r->firstName.' '.$r->lastName),
            'date'              => Carbon::parse($r->startDate)->format('d-m-Y'),
            'day'               => Carbon::parse($r->startDate)->format('D'),
            'shift'             => $r->shiftStart ? Carbon::parse($r->shiftStart)->format('h:i A').' - '.Carbon::parse($r->shiftEnd)->format('h:i A') : '-',
            'punchIn'           => (!$isLeave && $in) ? $in->format('h:i A') : '-',
            'punchOut'          => (!$isLeave && $out) ? $out->format('h:i A').($r->is_auto_punch_out ? ' (Auto)' : '') : '-',
            'isLeave'           => $isLeave,
            'status'            => $isLeave ? 'Leave' : 'Present',
            'workedMinutes'     => $worked,
            'lateMinutes'       => $late,
            'permissionMinutes' => $permission,
            'reason'            => $r->reason ?? '',
        ];
    }

    /* 135 -> "2:15" (hours:minutes); blank for zero when $dashForZero */
    public static function hm(int $minutes, bool $dashForZero = true): string
    {
        if ($minutes <= 0 && $dashForZero) {
            return '-';
        }
        return intdiv($minutes, 60).':'.str_pad($minutes % 60, 2, '0', STR_PAD_LEFT);
    }
}
