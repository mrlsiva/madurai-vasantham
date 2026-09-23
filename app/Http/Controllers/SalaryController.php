<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use App\Models\User;
use App\Models\StaffSalary;
use App\Models\SalaryWorkingDay;
use App\Services\SalaryCalculator;

class SalaryController extends Controller
{
    /* ?month=2026-09, defaulting to the current month */
    private function month(Request $request): Carbon
    {
        $month = $request->query('month');
        return ($month && preg_match('/^\d{4}-\d{2}$/', $month))
            ? Carbon::createFromFormat('Y-m-d', $month.'-01')->startOfDay()
            : Carbon::now()->startOfMonth();
    }

    /* Salary summary for all staff for one month */
    public function index(Request $request, SalaryCalculator $calculator)
    {
        $report = $calculator->month($this->month($request));
        return view('admin.salary.index', compact('report'));
    }

    /* Day by day salary for one staff member, plus their salary history */
    public function show(Request $request, SalaryCalculator $calculator, string $id)
    {
        $user = User::findOrFail($id);
        $report = $calculator->month($this->month($request), (int) $id);
        $row = $report['rows'][0] ?? null;
        $history = StaffSalary::where('userId', $id)->orderBy('effective_from')->get();

        return view('admin.salary.show', compact('user', 'report', 'row', 'history'));
    }

    /* Printable salary receipt for one staff member and month (signed link, no login) */
    public function receipt(SalaryCalculator $calculator, string $id, string $month)
    {
        $user = User::findOrFail($id);
        $report = $calculator->month(Carbon::createFromFormat('Y-m-d', $month.'-01')->startOfDay(), (int) $id);
        $row = $report['rows'][0] ?? null;

        return view('admin.salary.receipt', compact('user', 'report', 'row'));
    }

    /* Signed receipt link and WhatsApp click-to-chat link for one salary row */
    public static function whatsapp(array $row, Carbon $month): ?array
    {
        if ($row['earned'] === null) {
            return null;
        }

        $receiptUrl = URL::signedRoute('salary.receipt', ['id' => $row['userId'], 'month' => $month->format('Y-m')]);

        // wa.me needs the number with country code; staff mobiles are stored as 10 digits (India)
        $phone = preg_replace('/\D/', '', (string) $row['mobile']);
        if (strlen($phone) === 10) {
            $phone = '91'.$phone;
        }

        $text = "Dear *{$row['name']}*,\n"
            ."Your salary for *".$month->format('F Y')."*:\n"
            ."Days worked: {$row['daysWorked']}\n"
            ."Paid hours: ".SalaryCalculator::hm($row['paidMinutes'])."\n"
            ."Amount: *Rs. ".number_format($row['earned'], 2)."*\n\n"
            ."Receipt: {$receiptUrl}\n\n"
            ."Thank you.\nVasantham Group Of Companies";

        return [
            'receiptUrl'  => $receiptUrl,
            'whatsappUrl' => strlen($phone) >= 11 ? 'https://wa.me/'.$phone.'?text='.rawurlencode($text) : null,
        ];
    }

    /* Working days for a month (same for all staff) */
    public function saveWorkingDays(Request $request)
    {
        $input = $request->validate([
            'month'        => 'required|date_format:Y-m',
            'working_days' => 'required|integer|min:1|max:31',
        ]);

        SalaryWorkingDay::updateOrCreate(
            ['month' => $input['month'].'-01'],
            ['working_days' => $input['working_days']]
        );

        return redirect()->route('salary.index', ['month' => $input['month']])
            ->with('success', 'Working days saved for '.Carbon::parse($input['month'].'-01')->format('M Y'));
    }

    /* Working days for all 12 months of a year, ?year=2026 */
    public function workingDaysYear(Request $request)
    {
        $year = (int) $request->query('year', Carbon::now()->year);
        if ($year < 2000 || $year > 2100) {
            $year = Carbon::now()->year;
        }

        $saved = SalaryWorkingDay::whereYear('month', $year)->get()
            ->mapWithKeys(fn ($row) => [(int) $row->month->format('n') => (int) $row->working_days]);

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $first = Carbon::create($year, $m, 1);
            $days  = $first->daysInMonth;
            $sundays = $saturdays = 0;
            for ($d = 1; $d <= $days; $d++) {
                $dow = $first->copy()->day($d)->dayOfWeek;
                $sundays   += $dow === Carbon::SUNDAY ? 1 : 0;
                $saturdays += $dow === Carbon::SATURDAY ? 1 : 0;
            }
            $months[$m] = [
                'label'      => $first->format('F'),
                'saved'      => $saved->get($m),
                'all'        => $days,
                'noSunday'   => $days - $sundays,
                'weekdays'   => $days - $sundays - $saturdays,
            ];
        }

        return view('admin.salary.working-days', compact('year', 'months'));
    }

    /* Save working days for any of the 12 months; empty boxes are left unset */
    public function saveWorkingDaysYear(Request $request)
    {
        $input = $request->validate([
            'year'   => 'required|integer|min:2000|max:2100',
            'days'   => 'array',
            'days.*' => 'nullable|integer|min:1|max:31',
        ]);

        foreach ($input['days'] ?? [] as $m => $days) {
            if ($days !== null && (int) $m >= 1 && (int) $m <= 12 && (int) $days > Carbon::create($input['year'], (int) $m, 1)->daysInMonth) {
                return back()->withInput()->withErrors([
                    'days.'.$m => Carbon::create($input['year'], (int) $m, 1)->format('F Y').' has only '.Carbon::create($input['year'], (int) $m, 1)->daysInMonth.' days',
                ]);
            }
        }

        $saved = 0;
        foreach ($input['days'] ?? [] as $m => $days) {
            if ($days === null || $days === '' || (int) $m < 1 || (int) $m > 12) {
                continue;
            }
            SalaryWorkingDay::updateOrCreate(
                ['month' => Carbon::create($input['year'], (int) $m, 1)->toDateString()],
                ['working_days' => $days]
            );
            $saved++;
        }

        return redirect()->route('salary.workingDaysYear', ['year' => $input['year']])
            ->with('success', 'Working days saved for '.$saved.' month(s) of '.$input['year']);
    }

    /*
     * Monthly salary for one staff member. By default the amount is filled for
     * 12 months from the chosen month (one row per month), so any single month
     * can later be edited on its own with apply=month.
     */
    public function saveSalary(Request $request, string $id)
    {
        User::findOrFail($id);
        $input = $request->validate([
            'monthly_salary' => 'required|numeric|min:0|max:99999999',
            'effective_from' => 'required|date_format:Y-m',
            'apply'          => 'nullable|in:month,year',
        ]);

        $from   = Carbon::createFromFormat('Y-m-d', $input['effective_from'].'-01')->startOfDay();
        $months = ($input['apply'] ?? 'year') === 'month' ? 1 : 12;

        // Editing one month can also set that month's working days for this staff (empty = default)
        $values = ['monthly_salary' => $input['monthly_salary']];
        if ($months === 1 && $request->has('working_days')) {
            $request->validate(['working_days' => 'nullable|integer|min:1|max:'.$from->daysInMonth]);
            $values['working_days'] = $request->input('working_days') ?: null;
        }

        for ($i = 0; $i < $months; $i++) {
            StaffSalary::updateOrCreate(
                ['userId' => $id, 'effective_from' => $from->copy()->addMonths($i)->toDateString()],
                $values
            );
        }

        $message = $months === 1
            ? 'Salary saved for '.$from->format('M Y')
            : 'Salary saved for '.$from->format('M Y').' to '.$from->copy()->addMonths(11)->format('M Y');

        return redirect()->route('salary.show', ['id' => $id, 'month' => $request->input('month', $input['effective_from'])])
            ->with('success', $message);
    }

    /* Working days for one staff in one month; empty = use the month's working days */
    public function saveStaffWorkingDays(Request $request, string $id)
    {
        User::findOrFail($id);
        $request->validate(['month' => 'required|date_format:Y-m']);
        $month = Carbon::createFromFormat('Y-m-d', $request->input('month').'-01')->startOfDay();
        $input = $request->validate(['working_days' => 'nullable|integer|min:1|max:'.$month->daysInMonth]);

        // The salary valid for this month; a carried-forward salary gets its own row for this month
        $salary = StaffSalary::where('userId', $id)->where('effective_from', '<=', $month->toDateString())
            ->orderByDesc('effective_from')->first();
        if (!$salary) {
            return back()->withErrors(['working_days' => 'Set the salary for '.$month->format('M Y').' first']);
        }

        StaffSalary::updateOrCreate(
            ['userId' => $id, 'effective_from' => $month->toDateString()],
            ['monthly_salary' => $salary->monthly_salary, 'working_days' => $input['working_days'] ?? null]
        );

        return redirect()->route('salary.show', ['id' => $id, 'month' => $month->format('Y-m')])
            ->with('success', !empty($input['working_days'])
                ? 'Working days for '.$month->format('M Y').' set to '.$input['working_days'].' for this staff'
                : 'Working days for '.$month->format('M Y').' reset to the default');
    }

    /* Remove one salary history entry */
    public function deleteSalary(Request $request, string $id, string $salaryId)
    {
        StaffSalary::where('userId', $id)->where('id', $salaryId)->delete();

        return redirect()->route('salary.show', ['id' => $id, 'month' => $request->input('month')])
            ->with('success', 'Salary entry removed');
    }
}
