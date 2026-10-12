@php use App\Http\Controllers\ReportController; @endphp
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>{{ $report['title'] }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 9px; color: #222; }
        h1 { font-size: 15px; margin: 0; }
        h2 { font-size: 11px; margin: 14px 0 4px; }
        .muted { color: #666; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #bbb; padding: 3px 4px; text-align: left; }
        th { background: #e9ecef; }
        tr.total td { font-weight: bold; background: #f5f5f5; }
        .leave { color: #c0392b; font-weight: bold; }
        .stats td { border: none; text-align: center; font-size: 10px; }
        .stats b { font-size: 13px; display: block; }
        .details tr { page-break-inside: avoid; }
    </style>
</head>
<body>
    <h1>Vasantham Group Of Companies - {{ $report['title'] }}</h1>
    <div class="muted">
        Period: {{ $report['from']->format('d M Y') }}@if($report['days'] > 1) to {{ $report['to']->format('d M Y') }} ({{ $report['days'] }} days)@endif
        &nbsp;|&nbsp; Staff: {{ $report['staff'] }}
        &nbsp;|&nbsp; Generated: {{ now()->format('d M Y h:i A') }}
    </div>

    <table class="stats" style="margin-top:8px">
        <tr>
            <td><b>{{ $report['totals']['present'] }}</b>Present</td>
            <td><b>{{ $report['totals']['leave'] }}</b>Leave</td>
            <td><b>{{ $report['totals']['permission'] }}</b>Permission</td>
            <td><b>{{ $report['totals']['late'] }}</b>Late</td>
            <td><b>{{ ReportController::hm($report['totals']['workedMinutes'], false) }}</b>Worked Hours</td>
        </tr>
    </table>

    <h2>Summary</h2>
    <table>
        <thead>
            <tr>
                <th>User ID</th><th>Name</th><th>Mobile</th><th>Present</th><th>Leave</th>
                <th>Permission</th><th>Permission Hrs</th><th>Late</th><th>Late Hrs</th><th>Worked Hrs</th><th>Missing Punch Out</th>
            </tr>
        </thead>
        <tbody>
        @foreach($report['summary'] as $s)
            <tr>
                <td>{{ $s['userId'] }}</td>
                <td>{{ $s['name'] }}</td>
                <td>{{ $s['mobile'] }}</td>
                <td>{{ $s['present'] }}</td>
                <td>{{ $s['leave'] }}</td>
                <td>{{ $s['permission'] }}</td>
                <td>{{ ReportController::hm($s['permissionMinutes']) }}</td>
                <td>{{ $s['late'] }}</td>
                <td>{{ ReportController::hm($s['lateMinutes']) }}</td>
                <td>{{ ReportController::hm($s['workedMinutes']) }}</td>
                <td>{{ $s['missingOut'] ?: '-' }}</td>
            </tr>
        @endforeach
            <tr class="total">
                <td></td><td>Total</td><td></td>
                <td>{{ $report['totals']['present'] }}</td>
                <td>{{ $report['totals']['leave'] }}</td>
                <td>{{ $report['totals']['permission'] }}</td>
                <td></td>
                <td>{{ $report['totals']['late'] }}</td>
                <td></td>
                <td>{{ ReportController::hm($report['totals']['workedMinutes']) }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>

    <h2>Details</h2>
    <table class="details">
        <thead>
            <tr>
                <th>Date</th><th>User ID</th><th>Name</th><th>Shift</th><th>Punch In</th><th>Punch Out</th>
                <th>Status</th><th>Worked Hrs</th><th>Late By</th><th>Permission Hrs</th><th>Reason</th>
            </tr>
        </thead>
        <tbody>
        @forelse($report['rows'] as $r)
            <tr>
                <td>{{ $r['date'] }} {{ $r['day'] }}</td>
                <td>{{ $r['userId'] }}</td>
                <td>{{ $r['name'] }}</td>
                <td>{{ $r['shift'] }}</td>
                <td>{{ $r['punchIn'] }}</td>
                <td>{{ $r['punchOut'] }}</td>
                <td class="{{ $r['isLeave'] ? 'leave' : '' }}">{{ $r['status'] }}</td>
                <td>{{ ReportController::hm($r['workedMinutes']) }}</td>
                <td>{{ ReportController::hm($r['lateMinutes']) }}</td>
                <td>{{ ReportController::hm($r['permissionMinutes']) }}</td>
                <td>{{ $r['reason'] }}</td>
            </tr>
        @empty
            <tr><td colspan="11" style="text-align:center">No attendance records for this period</td></tr>
        @endforelse
        </tbody>
    </table>
</body>
</html>
