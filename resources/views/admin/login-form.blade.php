@extends('layouts.chitfund.base-login')

@section('content') 

<style>	
	h1 {
		font-size: 40px;
		color: #ed1c24; 
		text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);					
		padding: 10px; 
		border-radius: 5px; 
		font-weight: 800;
		font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
	}
</style>

 <!--page-content-wrapper-->
	<div class="page-content-wrapper d-flex justify-content-center align-items-center py-3">
		<div class="page-content">	            
	<div class="container">
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
		<div class="row">
			<div class="col-md-6 mx-auto login-form-wrapper">
				<h2 class="py-3 text-center">Admin Login Form</h2>

				@if ($errors->any())
					<div class="alert alert-danger">
						<ul>
							@foreach ($errors->all() as $error)
								<li>{{ $error }}</li>
							@endforeach
						</ul>
					</div>
				@endif

				@if(Session::has('error-message'))
					<p class="alert alert-info">{{ Session::get('error-message') }}</p>
				@endif

				<form action="{{ route('login.validate') }}" method="post">
					@csrf
					<div class="mb-3">
						<label for="username" class="form-label">UserName</label>
						<input type="text" class="form-control" name="username" id="username" placeholder="Enter UserName" autocomplete="off" />
					</div>
					<div class="mb-4">
						<label for="password" class="form-label">Password</label>
						<input type="password" class="form-control" name="password" id="password" placeholder="Enter Password" autocomplete="off" />
					</div>
					<div class="col-12 col-lg-12 text-center">
						<input type="submit" class="btn btn-primary " value="Login">
					</div>
					
				</form>
			</div>
		</div>
	</div>
	</div>
	</div>

@endsection
