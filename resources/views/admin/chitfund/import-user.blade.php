@extends('layouts.chitfund.base')

@section('content') 

@php 
use Carbon\Carbon; 
@endphp

<!--page-content-wrapper-->
	<div class="page-content-wrapper d-flex justify-content-center align-items-center py-5">
		<div class="page-content">					
			
			<div class="row">
				<div class="col-12 col-lg-12">
					<img src="{{asset('/resources/assets_chitfund/images/logo-icon.png')}}" class="mx-auto d-flex " style="width: 100px;"  alt="">
				</div>
			</div>
			<div class="row" style="padding-top: 10px;">
				<div class="col-12 col-lg-12  text-center">
					<h1 style="font-size: 40px;">Vasantham Siru Semippu Thittam</h1>
				</div>
			</div>
			<div class="row" style="padding-top: 30px;">
					@if(session('success'))
						<div class="alert alert-success">
							{{ session('success') }}
						</div>
					@endif
					
					<div class="container mt-5 text-center">
					<h2 class="mb-4">
						Import Excel to Database
					</h2>
					<form action="{{ route('chitfund.importExcel') }}" method="POST" enctype="multipart/form-data">
						@csrf
						<div class="form-group mb-4" style="max-width: 500px; margin: 0 auto;">
							<div class="custom-file text-left">
								<input type="file" name="file" class="custom-file-input" id="customFile">
								<label class="custom-file-label" for="customFile">Choose file</label>
							</div>
						</div>
						<button class="btn btn-primary">Import data</button>
					</form>
				</div>

			</div>
			<!--end row-->
			
				
			
		</div>
	</div>
	<!--end page-content-wrapper-->
@endsection
