@php
use App\Services\SalaryCalculator;
use Carbon\Carbon;
$month = $report['month'];
$partial = $month->isSameMonth(Carbon::now());
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Salary Receipt - {{ $user->firstName }} {{ $user->lastName }} - {{ $month->format('F Y') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f3f4f6; }
        .receipt { max-width: 760px; margin: 24px auto; background: #fff; border-radius: 12px; padding: 28px; box-shadow: 0 1px 4px rgba(0,0,0,.08); }
        .brand { color: #b3121b; font-weight: 800; }
        .label { color: #6b7280; font-size: 13px; }
        .amount { font-size: 28px; font-weight: 800; }
        .table td, .table th { font-size: 14px; }
        @media print {
            body { background: #fff; }
            .receipt { box-shadow: none; margin: 0; max-width: none; padding: 0; }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>
<div class="receipt">
    <div class="d-flex justify-content-between align-items-start border-bottom pb-3 mb-3">
        <div>
            <div class="brand fs-4">Vasantham Group Of Companies</div>
            <div class="text-muted">Salary Receipt</div>
        </div>
        <div class="text-end">
            <div class="fw-bold">{{ $month->format('F Y') }}</div>
            <div class="label">Generated {{ Carbon::now()->format('d M Y, h:i A') }}</div>
        </div>
    </div>

    @if(!$row || $row['earned'] === null)
        <div class="alert alert-warning">The salary for {{ $month->format('F Y') }} is not ready yet. Please contact the office.</div>
    @else
        @if($partial)
            <div class="alert alert-info py-2">This month is still in progress. The amount covers attendance up to {{ Carbon::now()->format('d M Y') }}.</div>
        @endif

        <div class="row g-3 mb-3">
            <div class="col-6">
                <div class="label">Staff</div>
                <div class="fw-bold">{{ $row['name'] }}</div>
                <div class="label">ID {{ $row['userId'] }}{{ $row['shiftName'] ? ' - Shift '.$row['shiftName'] : '' }}</div>
            </div>
            <div class="col-6 text-end">
                <div class="label">Amount earned</div>
                <div class="amount">₹{{ number_format($row['earned'], 2) }}</div>
            </div>
        </div>

        <table class="table table-sm mb-4">
            <tbody>
                <tr><td>Monthly salary</td><td class="text-end">₹{{ number_format($row['salary'], 2) }}</td></tr>
                <tr><td>Working days in the month</td><td class="text-end">{{ $row['workingDays'] }}</td></tr>
                <tr><td>Expected hours ({{ $row['workingDays'] }} days x {{ SalaryCalculator::hm($row['shiftMinutes']) }})</td><td class="text-end">{{ SalaryCalculator::hm($row['expectedMinutes']) }}</td></tr>
                <tr><td>Days worked</td><td class="text-end">{{ $row['daysWorked'] }} of {{ $row['workingDays'] }}</td></tr>
                <tr><td>Leave days</td><td class="text-end">{{ $row['leaveDays'] ?: '-' }}</td></tr>
                <tr><td>Absent days</td><td class="text-end">{{ $row['absentDays'] ?: '-' }}</td></tr>
                <tr><td>Permission time deducted</td><td class="text-end">{{ $row['permissionMinutes'] ? SalaryCalculator::hm($row['permissionMinutes']) : '-' }}</td></tr>
                <tr><td>Paid hours</td><td class="text-end">{{ SalaryCalculator::hm($row['paidMinutes']) }}</td></tr>
                <tr><td>Rate per minute</td><td class="text-end">₹{{ number_format($row['rate'], 4) }}</td></tr>
                <tr class="table-light fw-bold"><td>Amount earned</td><td class="text-end">₹{{ number_format($row['earned'], 2) }}</td></tr>
            </tbody>
        </table>

        <h6>Day by day</h6>
        <table class="table table-sm table-striped">
            <thead>
                <tr><th>Date</th><th>Punch In</th><th>Counted Until</th><th class="text-end">Paid</th><th class="text-end">Amount</th></tr>
            </thead>
            <tbody>
            @foreach($row['days'] as $day)
                <tr>
                    <td>{{ Carbon::parse($day['date'])->format('d M, D') }}</td>
                    <td>{{ $day['in']->format('h:i A') }}</td>
                    <td>
                        {{ $day['end']->format('h:i A') }}
                        @if($day['auto'] || in_array($day['endReason'], ['shift_end', 'after_shift']))
                            <small class="text-muted">(no punch out)</small>
                        @endif
                    </td>
                    <td class="text-end">{{ SalaryCalculator::hm($day['paid']) }}</td>
                    <td class="text-end">₹{{ number_format($day['paid'] * $row['rate'], 2) }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <p class="label mb-0">Salary is paid by the minute from punch in to punch out. Without a punch out, time is counted until the shift end; permission time is not paid.</p>
    @endif

    <div class="text-center mt-4 no-print">
        <button class="btn btn-primary" onclick="window.print()">Print / Save as PDF</button>
    </div>
</div>
</body>
</html>
