@php
use App\Services\SalaryCalculator;
$month = $report['month'];
$ym = $month->format('Y-m');
$reasons = [
    'punch_out'   => ['Punch out', 'bg-success'],
    'midnight'    => ['Stopped at 12 AM', 'bg-info text-dark'],
    'shift_end'   => ['No punch out - shift end', 'bg-warning text-dark'],
    'after_shift' => ['No punch out - after shift', 'bg-danger'],
    'in_progress' => ['Working now', 'bg-secondary'],
];
@endphp
@extends('layouts.base')

@section('content')
<div class="card">
<div class="card-body">

@if($errors->any())
    <div class="alert alert-danger alert-dismissible" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        @foreach($errors->all() as $error) {{ $error }} <br/> @endforeach
    </div>
@endif
@if(session()->has('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        {{ session()->get('success') }}
    </div>
@endif

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
    <div>
        <a href="{{ route('salary.index', ['month' => $ym]) }}" class="small">&laquo; All staff</a>
        <h3 class="mb-0">{{ $user->firstName }} {{ $user->lastName }} - Salary {{ $month->format('F Y') }}</h3>
    </div>
    @if($row)
        @php $links = \App\Http\Controllers\SalaryController::whatsapp($row, $month); @endphp
        @if($links)
        <div class="ms-auto d-flex gap-2">
            <a href="{{ $links['receiptUrl'] }}" target="_blank" class="btn btn-outline-secondary btn-sm"><i class="fa-solid fa-receipt"></i> Receipt</a>
            @if($links['whatsappUrl'])
                <a href="{{ $links['whatsappUrl'] }}" target="_blank" rel="noopener" class="btn btn-success btn-sm"><i class="fa-brands fa-whatsapp"></i> Send on WhatsApp</a>
            @else
                <span class="btn btn-outline-secondary btn-sm disabled">No valid mobile number</span>
            @endif
        </div>
        @else
        <div class="ms-auto d-flex align-items-center gap-2">
            <span class="btn btn-outline-secondary btn-sm disabled"><i class="fa-solid fa-receipt"></i> Receipt</span>
            <span class="btn btn-outline-secondary btn-sm disabled"><i class="fa-brands fa-whatsapp"></i> Send on WhatsApp</span>
            <span class="small text-danger">
                @if(!$report['workingDays'])
                    Set <a href="{{ route('salary.workingDaysYear', ['year' => $month->year]) }}">working days for {{ $month->format('M Y') }}</a> first
                @else
                    Set the salary for {{ $month->format('M Y') }} first
                @endif
            </span>
        </div>
        @endif
    @endif
    <form method="get" action="{{ route('salary.show', $user->userId) }}">
        <label class="form-label mb-0 small">Month</label>
        <input type="month" name="month" value="{{ $ym }}" class="form-control form-control-sm" onchange="this.form.submit()">
    </form>
</div>

@if($row)
<div class="row g-2 mb-4">
    <div class="col-6 col-md-3 col-xl">
        <div class="border rounded p-2 h-100 {{ $row['customWorkingDays'] ? 'border-primary' : '' }}">
            <div class="small text-muted">
                Working days
                <span class="badge {{ $row['customWorkingDays'] ? 'bg-primary' : 'bg-light text-dark' }}">{{ $row['customWorkingDays'] ? 'This staff' : 'Default' }}</span>
            </div>
            <form method="post" action="{{ route('salary.staffWorkingDays', $user->userId) }}" class="d-flex gap-1 mt-1">
                @csrf
                <input type="hidden" name="month" value="{{ $ym }}">
                <input type="number" name="working_days" min="1" max="{{ $month->daysInMonth }}"
                    value="{{ $row['customWorkingDays'] ? $row['workingDays'] : '' }}"
                    placeholder="{{ $report['workingDays'] ?? 'Not set' }}"
                    class="form-control form-control-sm" style="max-width: 80px"
                    title="Working days for this staff in {{ $month->format('M Y') }}. Leave empty to use the default ({{ $report['workingDays'] ?? 'not set' }}).">
                <button class="btn btn-outline-primary btn-sm" title="Save"><i class="fa-solid fa-check"></i></button>
            </form>
            <div class="small text-muted mt-1">
                Default {{ $report['workingDays'] ?? 'not set' }}@if($row['customWorkingDays']) &middot; clear to reset @endif
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 col-xl">
        <div class="border rounded p-2 h-100">
            <div class="small text-muted">Days worked</div>
            <div class="fw-bold">{{ $row['daysWorked'] }}{{ $row['workingDays'] ? ' of '.$row['workingDays'] : '' }}</div>
            <div class="small text-muted">
                Leave {{ $row['leaveDays'] }}
                @if($row['absentDays'] !== null) &middot; Absent {{ $row['absentDays'] }} @endif
            </div>
        </div>
    </div>
    @foreach([
        'Monthly salary' => $row['salary'] !== null ? '₹'.number_format($row['salary'], 2) : 'Not set',
        'Shift'          => ($row['shiftName'] ?? '-').' ('.SalaryCalculator::hm($row['shiftMinutes']).' h)',
        'Per minute'     => $row['rate'] !== null ? '₹'.number_format($row['rate'], 4) : '-',
        'Paid hours'     => SalaryCalculator::hm($row['paidMinutes']).' of '.SalaryCalculator::hm($row['expectedMinutes']),
        'Earned'         => $row['earned'] !== null ? '₹'.number_format($row['earned'], 2) : '-',
    ] as $label => $value)
        <div class="col-6 col-md-3 col-xl">
            <div class="border rounded p-2 h-100">
                <div class="small text-muted">{{ $label }}</div>
                <div class="fw-bold">{{ $value }}</div>
            </div>
        </div>
    @endforeach
</div>
@endif

<div class="row g-4">
    <div class="col-lg-8">
        <h5>Day by day</h5>
        <table class="table table-sm table-striped align-middle">
            <thead>
            <tr>
                <th>Date</th>
                <th>Punch In</th>
                <th>Punch Out</th>
                <th>Counted Until</th>
                <th class="text-end">Worked</th>
                <th class="text-end">Permission</th>
                <th class="text-end">Paid</th>
                <th class="text-end">Amount</th>
            </tr>
            </thead>
            <tbody>
            @forelse($row['days'] ?? [] as $day)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($day['date'])->format('d M, D') }}</td>
                    <td>{{ $day['in']->format('h:i A') }}</td>
                    <td>{{ $day['out'] ? $day['out']->format('h:i A') : ($day['auto'] ? 'Auto' : '-') }}</td>
                    <td>
                        {{ $day['end']->format('h:i A') }}
                        <span class="badge {{ $reasons[$day['endReason']][1] }}">{{ $day['auto'] ? 'Auto punch out - shift end' : $reasons[$day['endReason']][0] }}</span>
                    </td>
                    <td class="text-end">{{ SalaryCalculator::hm($day['worked']) }}</td>
                    <td class="text-end">{{ $day['permission'] ? '-'.SalaryCalculator::hm($day['permission']) : '-' }}</td>
                    <td class="text-end">{{ SalaryCalculator::hm($day['paid']) }}</td>
                    <td class="text-end">{{ $row['rate'] !== null ? '₹'.number_format($day['paid'] * $row['rate'], 2) : '-' }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center">No attendance in {{ $month->format('F Y') }}</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="col-lg-4">
        <h5>Monthly salary</h5>
        <form method="post" action="{{ route('salary.save', $user->userId) }}" class="border rounded p-3 mb-3">
            @csrf
            <input type="hidden" name="month" value="{{ $ym }}">
            <div class="mb-2">
                <label class="form-label mb-0 small">Monthly salary (₹)</label>
                <input type="number" name="monthly_salary" step="0.01" min="0" class="form-control" required value="{{ old('monthly_salary') }}">
            </div>
            <div class="mb-2">
                <label class="form-label mb-0 small">From month</label>
                <input type="month" name="effective_from" class="form-control" required value="{{ old('effective_from', $ym) }}">
            </div>
            <input type="hidden" name="apply" value="year">
            <button class="btn btn-primary btn-sm">Save for 12 months</button>
            <div class="small text-muted mt-2">Fills this month and the next 11 months. Edit any single month in the list below.</div>
        </form>

        <table class="table table-sm align-middle">
            <thead><tr><th>Month</th><th>Salary (₹) &middot; Working days</th><th></th></tr></thead>
            <tbody>
            @forelse($history as $h)
                @php $hm = $h->effective_from->format('Y-m'); @endphp
                <tr class="{{ $hm === $ym ? 'table-primary' : '' }}">
                    <td>{{ $h->effective_from->format('M Y') }}</td>
                    <td>
                        <form method="post" action="{{ route('salary.save', $user->userId) }}" class="d-flex gap-1">
                            @csrf
                            <input type="hidden" name="month" value="{{ $ym }}">
                            <input type="hidden" name="effective_from" value="{{ $hm }}">
                            <input type="hidden" name="apply" value="month">
                            <input type="number" name="monthly_salary" step="0.01" min="0" required value="{{ $h->monthly_salary }}" class="form-control form-control-sm" style="max-width: 120px">
                            <input type="number" name="working_days" min="1" max="{{ $h->effective_from->daysInMonth }}" value="{{ $h->working_days }}"
                                placeholder="Default" title="Working days for this staff in {{ $h->effective_from->format('M Y') }}. Empty = the month's default."
                                class="form-control form-control-sm" style="max-width: 80px">
                            <button class="btn btn-outline-primary btn-sm" title="Save {{ $h->effective_from->format('M Y') }} only"><i class="fa-solid fa-check"></i></button>
                        </form>
                    </td>
                    <td class="text-end">
                        <form method="post" action="{{ route('salary.delete', [$user->userId, $h->id]) }}" onsubmit="return confirm('Remove the salary for {{ $h->effective_from->format('M Y') }}?')">
                            @csrf
                            <input type="hidden" name="month" value="{{ $ym }}">
                            <button class="btn btn-link btn-sm text-danger p-0" title="Remove"><i class="fa-solid fa-trash"></i></button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr><td colspan="3" class="text-muted">No salary set yet</td></tr>
            @endforelse
            </tbody>
        </table>
        <div class="small text-muted">After the last month in this list, the last salary keeps applying until a new one is set.</div>
    </div>
</div>

</div></div>
@endsection
