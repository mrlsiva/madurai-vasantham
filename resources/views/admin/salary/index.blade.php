@php
use App\Services\SalaryCalculator;
use App\Http\Controllers\SalaryController;
$month = $report['month'];
$ym = $month->format('Y-m');
$totalEarned = array_sum(array_map(fn ($r) => $r['earned'] ?? 0, $report['rows']));
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
        <h3 class="mb-1">Salary - {{ $month->format('F Y') }}</h3>
        <small class="text-muted">Paid by the minute: monthly salary / (working days x shift minutes). Missing punch out stops at shift end; punch out after midnight stops at 12 AM; permission time is deducted.</small>
    </div>

    <form method="get" action="{{ route('salary.index') }}" class="d-flex align-items-end gap-2">
        <div>
            <label class="form-label mb-0 small">Month</label>
            <input type="month" name="month" value="{{ $ym }}" class="form-control form-control-sm" onchange="this.form.submit()">
        </div>
    </form>

    <form method="post" action="{{ route('salary.workingDays') }}" class="d-flex align-items-end gap-2">
        @csrf
        <input type="hidden" name="month" value="{{ $ym }}">
        <div>
            <label class="form-label mb-0 small">Working days in {{ $month->format('M Y') }}</label>
            <input type="number" name="working_days" min="1" max="31" value="{{ $report['workingDays'] }}" class="form-control form-control-sm" style="width: 120px" required>
        </div>
        <button class="btn btn-primary btn-sm">Save</button>
        <a href="{{ route('salary.workingDaysYear', ['year' => $month->year]) }}" class="btn btn-outline-secondary btn-sm">All months</a>
    </form>
</div>

@if(!$report['workingDays'])
    <div class="alert alert-warning py-2">Set the <b>working days</b> for {{ $month->format('F Y') }} to calculate salaries, above or for the whole year on the <a href="{{ route('salary.workingDaysYear', ['year' => $month->year]) }}">Working Days</a> page.</div>
@endif

<table class="table table-striped align-middle">
    <thead>
    <tr>
        <th>User ID</th>
        <th>Name</th>
        <th>Shift</th>
        <th class="text-end">Monthly Salary</th>
        <th class="text-end">Days Worked</th>
        <th class="text-end">Paid Hours</th>
        <th class="text-end">Expected Hours</th>
        <th class="text-end">Permission</th>
        <th class="text-end">No Punch Out</th>
        <th class="text-end">Per Minute</th>
        <th class="text-end">Earned</th>
        <th></th>
    </tr>
    </thead>
    <tbody>
    @forelse($report['rows'] as $row)
        <tr>
            <td>{{ $row['userId'] }}</td>
            <td>{{ $row['name'] }}</td>
            <td>{{ $row['shiftName'] ?? '-' }}</td>
            <td class="text-end">
                @if($row['salary'] !== null)
                    ₹{{ number_format($row['salary'], 2) }}
                @else
                    <a href="{{ route('salary.show', ['id' => $row['userId'], 'month' => $ym]) }}" class="badge bg-warning text-dark text-decoration-none">Set salary</a>
                @endif
            </td>
            <td class="text-end" title="Leave {{ $row['leaveDays'] }}{{ $row['absentDays'] !== null ? ', absent '.$row['absentDays'] : '' }}">
                {{ $row['daysWorked'] }}{{ $row['workingDays'] ? ' / '.$row['workingDays'] : '' }}
                @if($row['leaveDays'])<br><small class="text-muted">{{ $row['leaveDays'] }} leave</small>@endif
            </td>
            <td class="text-end">{{ SalaryCalculator::hm($row['paidMinutes']) }}</td>
            <td class="text-end">
                {{ SalaryCalculator::hm($row['expectedMinutes']) }}
                @if($row['customWorkingDays'])<br><span class="badge bg-primary" title="Working days set for this staff only">{{ $row['workingDays'] }} days</span>@endif
            </td>
            <td class="text-end">{{ $row['permissionMinutes'] ? SalaryCalculator::hm($row['permissionMinutes']) : '-' }}</td>
            <td class="text-end">{{ $row['missingPunchOut'] ?: '-' }}</td>
            <td class="text-end">{{ $row['rate'] !== null ? '₹'.number_format($row['rate'], 4) : '-' }}</td>
            <td class="text-end fw-bold">{{ $row['earned'] !== null ? '₹'.number_format($row['earned'], 2) : '-' }}</td>
            <td class="text-nowrap">
                <a href="{{ route('salary.show', ['id' => $row['userId'], 'month' => $ym]) }}" class="btn btn-outline-primary btn-sm">Details</a>
                @include('admin.salary.partials.send-buttons', [
                    'links'  => SalaryController::whatsapp($row, $month),
                    'reason' => !$report['workingDays'] ? 'Set working days for '.$month->format('M Y').' first' : 'Set the salary for '.$month->format('M Y').' first',
                ])
            </td>
        </tr>
    @empty
        <tr><td colspan="12" class="text-center">No staff found</td></tr>
    @endforelse
    </tbody>
    @if($report['rows'])
    <tfoot>
        <tr>
            <th colspan="10" class="text-end">Total</th>
            <th class="text-end">₹{{ number_format($totalEarned, 2) }}</th>
            <th></th>
        </tr>
    </tfoot>
    @endif
</table>

</div></div>
@endsection
