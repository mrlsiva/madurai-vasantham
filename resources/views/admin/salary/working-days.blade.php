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
        <a href="{{ route('salary.index') }}" class="small">&laquo; Salary</a>
        <h3 class="mb-1">Working Days - {{ $year }}</h3>
        <small class="text-muted">Working days per month, the same for all staff. Used for the per-minute salary rate.</small>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('salary.workingDaysYear', ['year' => $year - 1]) }}" class="btn btn-outline-secondary btn-sm">&laquo; {{ $year - 1 }}</a>
        <a href="{{ route('salary.workingDaysYear', ['year' => $year + 1]) }}" class="btn btn-outline-secondary btn-sm">{{ $year + 1 }} &raquo;</a>
    </div>
</div>

<form method="post" action="{{ route('salary.workingDaysYear.save') }}">
    @csrf
    <input type="hidden" name="year" value="{{ $year }}">

    <div class="d-flex flex-wrap align-items-center gap-2 mb-3">
        <span class="small text-muted">Fill all months with:</span>
        <button type="button" class="btn btn-outline-primary btn-sm fill-all" data-key="all">All days</button>
        <button type="button" class="btn btn-outline-primary btn-sm fill-all" data-key="noSunday">All days except Sundays</button>
        <button type="button" class="btn btn-outline-primary btn-sm fill-all" data-key="weekdays">Monday to Friday</button>
        <span class="small text-muted ms-2">or type a number for any month (e.g. after removing holidays)</span>
    </div>

    <table class="table table-striped align-middle" style="max-width: 900px">
        <thead>
        <tr>
            <th>Month</th>
            <th class="text-center">Days in month</th>
            <th class="text-center">Except Sundays</th>
            <th class="text-center">Mon - Fri</th>
            <th style="width: 160px">Working days</th>
            <th class="text-center">Status</th>
        </tr>
        </thead>
        <tbody>
        @foreach($months as $m => $month)
            <tr>
                <td>{{ $month['label'] }}</td>
                @foreach(['all', 'noSunday', 'weekdays'] as $key)
                    <td class="text-center">
                        <button type="button" class="btn btn-link btn-sm p-0 fill-one" data-month="{{ $m }}" data-value="{{ $month[$key] }}" title="Use {{ $month[$key] }}">{{ $month[$key] }}</button>
                    </td>
                @endforeach
                <td>
                    <input type="number" name="days[{{ $m }}]" min="1" max="{{ $month['all'] }}"
                        value="{{ old('days.'.$m, $month['saved']) }}"
                        data-all="{{ $month['all'] }}" data-no-sunday="{{ $month['noSunday'] }}" data-weekdays="{{ $month['weekdays'] }}"
                        class="form-control form-control-sm wd-input @error('days.'.$m) is-invalid @enderror">
                </td>
                <td class="text-center">
                    @if($month['saved'])
                        <span class="badge bg-success">Saved</span>
                    @else
                        <span class="badge bg-secondary">Not set</span>
                    @endif
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <button class="btn btn-primary">Save working days</button>
    <span class="small text-muted ms-2">Months left empty stay unset.</span>
</form>

<script>
    /* Quick fill: one month from a suggested number, or all months at once */
    document.addEventListener('click', function (e) {
        var one = e.target.closest('.fill-one');
        if (one) {
            document.querySelector('input[name="days[' + one.dataset.month + ']"]').value = one.dataset.value;
            return;
        }
        var all = e.target.closest('.fill-all');
        if (all) {
            var attr = { all: 'all', noSunday: 'noSunday', weekdays: 'weekdays' }[all.dataset.key];
            document.querySelectorAll('.wd-input').forEach(function (input) {
                input.value = input.dataset[attr];
            });
        }
    });
</script>

</div></div>
@endsection
