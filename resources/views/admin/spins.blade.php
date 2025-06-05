@php 
use Carbon\Carbon; 
$currentTime = Carbon::now();
use App\Http\Controllers\SpinController;
@endphp

@extends('layouts.base')

@section('content') 

@if($errors->any())
    <div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

        @foreach($errors->all() as $error)
            {{ $error }} <br/>
        @endforeach
    </div>
@endif

@if(session()->has('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        {{ session()->get('success') }}
    </div>
@endif


<div class="container-fluid d-flex  justify-content-between my-2"> 
    
        <div class='text-left justify-content-start flex'>                
                <h3>User Spins List</h3>
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
        <th>Name</th>
        <th>Mobile</th>
        <th>Branch</th>
        <th>Invoive No</th>		
        <th>Prize</th>
		<th>Spin Date</th>
		<th>Collected</th>
        <th>Action</th>
    </tr>
	</thead>
	<tbody>
    @if( !$spins->isEmpty() )
		@php $i= 0; @endphp
        @foreach($spins as $spin)      
			@php $i++;  @endphp
            <tr>
                <td>#{{ $spin->id }}</td>
				<td>{{ $spin->name }}</td> 
                <td>{{ $spin->mobile }}</td>
                <td>{{ $spin->branch }} </td> 
				<td>{{ $spin->invoice_number }} </td>                
                <!--<td>
                @if(isset($spin->invoice_copy) && !empty($spin->invoice_copy))       
                    @php 
                        $invoice_copy = SpinController::getUserSpinMedia($spin->invoice_copy) 
                    @endphp  

                    @if( $invoice_copy['is_exists'] )
                        <a href="{{ $invoice_copy['img_src'] }}" data-toggle="lightbox" data-caption="{{ $spin->name }}" data-size="sm" data-constrain="true" class="col-sm-4" data-gallery="Invoice Copy">
                            <img class="img-fluid" src="{{ $invoice_copy['img_src'] }}" width="48" height="48" />
                        </a>
                    @endif 
                @endif 
                </td>-->
				<td>
					@if( $spin->discount ) 
                    <img src="{{ asset('resources/images/spin/'.$spin->discount.'.png') }}" width="100" />
					@else
					-
                    @endif
				</td>  
				<td>{{ Carbon::parse($spin->created_at)->format('d-M-Y') }} </td> 
				<td>{{ $spin->is_redeemed ? 'Yes' : 'No' }} </td>  
                <td>                    
                    @if( !$spin->is_redeemed ) 
                    <a class="btn btn-info btn-sm my-1" href="{{route('updateInvoiceForm', $spin->id)}}" target="_blank">Mark as Collected</a>
					@else
					-
                    @endif
                </td>
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
