@php 
use Carbon\Carbon; 
$currentTime = Carbon::now();
use App\Http\Controllers\SpinController;
@endphp

@extends('layouts.base')

@section('content') 


<div class="container-fluid d-flex  justify-content-between my-2"> 
    
        <div class='text-left justify-content-start flex'>                
                <h3>Spin Invoice List</h3>
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
				<td>{{ $invoice->iyerbungalow }}</td> 
                <td>{{ $invoice->alanganallur }}</td>
                <td>{{ $invoice->palamedu }} </td> 
				<td>{{ $invoice->valasai }} </td> 
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

@endsection
