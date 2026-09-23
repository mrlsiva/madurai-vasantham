@php 
use Carbon\Carbon; 
$currentTime = Carbon::now();
use App\Http\Controllers\SpinController;
@endphp

@extends('layouts.spin-admin')

@section('content') 


<div class="container-fluid d-flex  justify-content-between my-2"> 
    
        <div class='text-left justify-content-start flex'>                
                <h3>Spin Invoice List</h3>
                <small class="text-muted">Click <i class="fa-regular fa-copy"></i> to copy an unused invoice. Struck-out invoices are already used.</small>
        </div>
        <div class='text-right justify-content-end flex'>                
             <span class="px-2"> {{ $currentTime->format('D') .' - '. $currentTime->format('d-M-Y'); }} </span>
			 <a class="btn btn-primary btn-sm my-1" href="{{route('importInvoice')}}" target="_blank" title='Import Invoice'><i class="fa-solid fa-file-import"></i> Import Invoice</a>
        </div>         
   
</div>

<div class="container-fluid"> 
<table id="spins-table" class="table table-striped spins-table">
    <thead>
	<tr>
        <th>ID</th>
        <th>Iyer Bungalow</th>
        <th>Alanganallur</th>
        <th>Palamedu</th>
        <th>Valasai</th>
    </tr>
	</thead>
	<tbody>
    @if( !$invoices->isEmpty() )
		@php $i= 0; @endphp
        @foreach($invoices as $invoice)      
			@php $i++;  @endphp
            <tr>
                <td>#{{ $invoice->id }}</td>
				@foreach(['iyerbungalow', 'alanganallur', 'palamedu', 'valasai'] as $branch)
					@php $number = $invoice->$branch; @endphp
					<td>
					@if( $number !== null && $number !== '' && strtolower($number) !== 'null' )
						@if( isset($used[strtolower($branch.'|'.$number)]) )
							<span class="text-muted text-decoration-line-through" title="Already used">{{ $number }}</span>
						@else
							{{ $number }}
							<button type="button" class="btn btn-link btn-sm p-0 ms-1 copy-invoice" data-invoice="{{ $number }}" title="Copy {{ $number }}"><i class="fa-regular fa-copy"></i></button>
						@endif
					@endif
					</td>
				@endforeach
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="10" class="text-center">No Records Found</td>           
        </tr>
    @endif
	</tbody>
</table>
</div>

<script>
	/* Copy an unused invoice number to the clipboard, then show a tick briefly */
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('.copy-invoice');
		if (!btn) return;

		var text = btn.dataset.invoice;
		var done = function () {
			var icon = btn.querySelector('i');
			icon.className = 'fa-solid fa-check text-success';
			setTimeout(function () { icon.className = 'fa-regular fa-copy'; }, 1200);
		};

		if (navigator.clipboard && window.isSecureContext) {
			navigator.clipboard.writeText(text).then(done);
		} else {
			var tmp = document.createElement('textarea');
			tmp.value = text;
			document.body.appendChild(tmp);
			tmp.select();
			document.execCommand('copy');
			document.body.removeChild(tmp);
			done();
		}
	});
</script>

@endsection
