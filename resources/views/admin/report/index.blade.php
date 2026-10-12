@php
use App\Http\Controllers\ReportController;
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

<div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-3">
    <div>
        <h3 class="mb-1">Attendance Report</h3>
        <small class="text-muted">{{ $report['title'] }} ({{ $report['days'] }} {{ $report['days'] > 1 ? 'days' : 'day' }}) &nbsp;|&nbsp; Staff: {{ $report['staff'] }}</small>
    </div>
    <div class="d-flex gap-2">
        <button type="submit" form="report-filters" formaction="{{ route('report.download', 'excel') }}" class="btn btn-success btn-sm"><i class="fa-solid fa-file-excel"></i> Excel</button>
        <button type="submit" form="report-filters" formaction="{{ route('report.download', 'pdf') }}" class="btn btn-danger btn-sm"><i class="fa-solid fa-file-pdf"></i> PDF</button>
    </div>
</div>

<form method="get" action="{{ route('report.index') }}" id="report-filters" class="row g-2 align-items-end mb-4">
    <div class="col-auto">
        <label class="form-label mb-0 small">Report</label>
        <div class="btn-group btn-group-sm d-flex" role="group">
            @foreach($types as $key => $label)
                <input type="radio" class="btn-check" name="type" id="type-{{ $key }}" value="{{ $key }}" @checked($filters['type'] === $key)>
                <label class="btn btn-outline-primary" for="type-{{ $key }}">{{ $label }}</label>
            @endforeach
        </div>
    </div>
    <div class="col-auto" data-for="daily">
        <label class="form-label mb-0 small">Date</label>
        <input type="date" name="date" value="{{ $filters['date'] }}" class="form-control form-control-sm">
    </div>
    <div class="col-auto" data-for="weekly">
        <label class="form-label mb-0 small">Week</label>
        <input type="week" name="week" value="{{ $filters['week'] }}" class="form-control form-control-sm">
    </div>
    <div class="col-auto" data-for="monthly">
        <label class="form-label mb-0 small">Month</label>
        <input type="month" name="month" value="{{ $filters['month'] }}" class="form-control form-control-sm">
    </div>
    <div class="col-auto" data-for="custom">
        <label class="form-label mb-0 small">From</label>
        <input type="date" name="from" value="{{ $filters['from'] }}" class="form-control form-control-sm">
    </div>
    <div class="col-auto" data-for="custom">
        <label class="form-label mb-0 small">To</label>
        <input type="date" name="to" value="{{ $filters['to'] }}" class="form-control form-control-sm">
    </div>
    <div class="col-auto">
        <label class="form-label mb-0 small">Staff</label>
        <select name="user_id" class="form-select form-select-sm">
            <option value="">All Staff</option>
            @foreach($users as $u)
                <option value="{{ $u->userId }}" @selected($filters['user_id'] == $u->userId)>{{ $u->userId }} - {{ $u->firstName }} {{ $u->lastName }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fa-solid fa-magnifying-glass"></i> View</button>
    </div>
</form>

<div class="row g-2 mb-4 text-center">
    <div class="col"><div class="border rounded p-2"><div class="fs-4 fw-bold text-success">{{ $report['totals']['present'] }}</div><small>Present</small></div></div>
    <div class="col"><div class="border rounded p-2"><div class="fs-4 fw-bold text-danger">{{ $report['totals']['leave'] }}</div><small>Leave</small></div></div>
    <div class="col"><div class="border rounded p-2"><div class="fs-4 fw-bold text-warning">{{ $report['totals']['permission'] }}</div><small>Permission</small></div></div>
    <div class="col"><div class="border rounded p-2"><div class="fs-4 fw-bold text-secondary">{{ $report['totals']['late'] }}</div><small>Late</small></div></div>
    <div class="col"><div class="border rounded p-2"><div class="fs-4 fw-bold">{{ ReportController::hm($report['totals']['workedMinutes'], false) }}</div><small>Worked Hours</small></div></div>
</div>

<h5>Summary</h5>
<div class="table-responsive mb-4">
<table class="table table-sm table-bordered table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>User ID</th><th>Name</th><th>Mobile</th><th>Present</th><th>Leave</th>
            <th>Permission</th><th>Permission Hrs</th><th>Late</th><th>Late Hrs</th><th>Worked Hrs</th><th>Missing Punch Out</th>
        </tr>
    </thead>
    <tbody>
    @forelse($report['summary'] as $s)
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
    @empty
        <tr><td colspan="11" class="text-center">No Records Found</td></tr>
    @endforelse
    </tbody>
</table>
</div>

<h5>Details</h5>
<div class="table-responsive">
<table class="table table-sm table-bordered table-hover align-middle">
    <thead class="table-light">
        <tr>
            <th>Date</th><th>User ID</th><th>Name</th><th>Shift</th><th>Punch In</th><th>Punch Out</th>
            <th>Status</th><th>Worked Hrs</th><th>Late By</th><th>Permission Hrs</th><th>Reason</th>
        </tr>
    </thead>
    <tbody>
    @forelse($report['rows'] as $r)
        <tr>
            <td>{{ $r['date'] }} <small class="text-muted">{{ $r['day'] }}</small></td>
            <td>{{ $r['userId'] }}</td>
            <td>{{ $r['name'] }}</td>
            <td>{{ $r['shift'] }}</td>
            <td>{{ $r['punchIn'] }}</td>
            <td>{{ $r['punchOut'] }}</td>
            <td><span class="badge {{ $r['isLeave'] ? 'bg-danger' : 'bg-success' }}">{{ $r['status'] }}</span></td>
            <td>{{ ReportController::hm($r['workedMinutes']) }}</td>
            <td>{{ ReportController::hm($r['lateMinutes']) }}</td>
            <td>{{ ReportController::hm($r['permissionMinutes']) }}</td>
            <td>{{ $r['reason'] ?: '-' }}</td>
        </tr>
    @empty
        <tr><td colspan="11" class="text-center">No attendance records for this period</td></tr>
    @endforelse
    </tbody>
</table>
</div>

</div></div>

<script>
/* Show only the date inputs for the selected report type; reload on change */
(function () {
    var form = document.getElementById('report-filters');
    function toggle() {
        var type = form.querySelector('input[name=type]:checked').value;
        form.querySelectorAll('[data-for]').forEach(function (el) {
            var show = el.dataset.for === type;
            el.classList.toggle('d-none', !show);
            el.querySelectorAll('input').forEach(function (i) { i.disabled = !show; });
        });
    }
    form.querySelectorAll('input[name=type]').forEach(function (r) { r.addEventListener('change', toggle); });
    toggle();
})();
</script>
@endsection
