@extends('layouts.base')

@section('content') 
<div class="container-fluid">
  <div class="row mb-4">
      <div class=" d-flex justify-content-between">
          <div class="">
              <h3>Import Spin Invoice</h3>
          </div>
         
      </div>
  </div>  
  <div class="row" style="padding-top: 30px;">
	@if(session('success'))
		<div class="alert alert-success">
			{{ session('success') }}
		</div>
	@endif
	
	@if($errors->any())
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>

			@foreach($errors->all() as $error)
				{{ $error }} <br/>
			@endforeach
		</div>
	@endif
	
	<div class="container mt-5 text-center">
		<div class="row justify-content-center">
			<div class="col-6 align-self-center">
				<div class="border border-2 p-5">
					<h2 class="mb-4">
						Import CSV file to Database
					</h2>
				
					<form action="{{ route('importInvoiceExcel') }}" method="POST" enctype="multipart/form-data">
						@csrf
						<div class="form-group mb-4" style="max-width: 500px; margin: 0 auto;">
							<div class="custom-file text-left">
								<input type="file" name="invoice" class="custom-file-input" id="customFile">
								<label class="custom-file-label" for="customFile">Choose file</label>
							</div>
						</div>
						<button class="btn btn-primary">Import data</button>
					</form>
				</div>
			</div>
		</div>	
	</div>

</div>
<!--end row-->
</div>


@endsection