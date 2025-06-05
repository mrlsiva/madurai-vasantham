@extends('layouts.chitfund.base')

@section('content') 

@php 
use Carbon\Carbon; 
@endphp

<!--page-content-wrapper-->
	<div class="page-content-wrapper d-flex justify-content-center align-items-center py-3">
		<div class="page-content">					
			<style>	
				h1 {
					font-size: 40px;
					color: #ed1c24; 
					text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
					background-color: #f8f8f8;
					padding: 10px; 
					border-radius: 5px; 
					font-weight: 800;
					font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif;
				}
			</style>
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
				@if( !$schemes->isEmpty() )
					@foreach($schemes as $scheme)
						<div class="col-12 col-lg-6">
							<div class="card radius-15 bg-voilet">
								<div class="card-body">
									<div class="d-flex align-items-center">
										<div>
											<h2 class="mb-0 text-white">{{ $scheme->plan_name }} </h2>
										</div>
									</div>
									<div class="d-flex align-items-center">
										<div>
											<p class="mb-0 text-white">
												{{ Carbon::parse($scheme->start_date)->format('M Y') }} - 
												{{ Carbon::parse($scheme->end_date)->format('M Y') }}
											</p>
											@php 
												$start_date = Carbon::parse($scheme->start_date);
												$end_date = Carbon::parse($scheme->end_date);
											@endphp  
											<p class="mb-0 text-white">{{ (int)round($start_date->floatDiffInMonths($end_date)); }} Months Scheme</p>
											<br>
											<a href="{{route('chitfund.showPlan', $scheme->plan_id)}}" class="card-link text-white">View More <i class="lni lni-arrow-right"></i></a>
										</div>
									</div>
								</div>
							</div>
						</div>
					@endforeach   
				@endif
				
				<div class="col-12 col-lg-6">
					<div class="card radius-15 bg-rose">
						<div class="card-body text-center">
							
							<a href="#" data-bs-toggle="modal" data-bs-target="#exampleModal"> 
								<div class="widgets-icons mx-auto bg-white rounded-circle">
									<i class="lni lni-plus"></i>
								</div>
								<h4 class="mb-0 font-weight-bold mt-3 text-white">Add <br> Scheme</h4> 
							</a>
							
						</div>
					</div>
				</div>

				<div class="col">
					<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
						<div class="modal-dialog">
							<div class="modal-content">
								<div class="modal-header" style="background-color:#673ab7;">
									<h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Add Plan</h5>
									<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
								</div>
								<div class="modal-body">						

									<form class="formm" action="{{route('chitfund.createPlan')}}" method="POST">
										@csrf							
										
										<label for="plan_name">Plan Name</label>
										<div class="form-group {{ $errors->has('plan_name') ? 'has-error' : ''}}">
											<input id="plan_name" type="text" class="form-control inputt @error('plan_name') is-invalid @enderror" name="plan_name" placeholder="Enter Plan Name" value="{{ old('plan_name') }}" required />
											{!! $errors->first('plan_name', '<p class="help-block">:message</p>') !!}
										</div>	
										
										<label for="plan_amount">Plan Amount</label>
										<div class="form-group {{ $errors->has('plan_amount') ? 'has-error' : ''}}">
											<input id="plan_amount" type="text" class="form-control inputt @error('plan_amount') is-invalid @enderror" name="plan_amount" placeholder="Enter Plan Amount" value="{{ old('plan_amount') }}" required />
											{!! $errors->first('plan_amount', '<p class="help-block">:message</p>') !!}
										</div>	

										<label for="start_date">Start Date</label>
										<div class="form-group {{ $errors->has('start_date') ? 'has-error' : ''}}">
											<input id="start_date" type="text" class="form-control datepicker inputt @error('start_date') is-invalid @enderror" name="start_date" placeholder="Enter Start Date" value="{{ old('start_date') }}" required />
											{!! $errors->first('start_date', '<p class="help-block">:message</p>') !!}
										</div>	
										
										
										<label for="end_date">End Date</label>
										<div class="form-group {{ $errors->has('end_date') ? 'has-error' : ''}}">
											<input id="end_date" type="text" class="form-control datepicker inputt @error('end_date') is-invalid @enderror" name="end_date" placeholder="Enter End Date" value="{{ old('end_date') }}" required />
											{!! $errors->first('end_date', '<p class="help-block">:message</p>') !!}
										</div>	

										<div class="modal-footer">
											<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
											<button type="submit" class="btn btn-primary">Save changes</button>
										</div>
									</form>
									<style>
										.formm {													
											padding: 20px;
											border-radius: 10px;
											width: 450px;
										}
								
										label {
											display: block;
											margin-bottom: 8px;
											font-weight: bold;
										}
								
										.inputt {
											width: 100%;
											padding: 8px;
											margin-bottom: 15px;
											box-sizing: border-box;
											border: 2px solid #673ab7;
											border-radius: 4px;
										}												
									</style>											
								</div>										
							</div>
						</div>
					</div>
				</div>

			</div>
			<!--end row-->
			
			<!-- Return Response Popup Model -->
			<div class="modal fade" id="ResponseMsgModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
				<div class="modal-dialog">
					<div class="modal-content">						
						<div class="modal-body">
							@if($errors->any())
								<div class="alert alert-danger alert-dismissible" role="alert">
									@foreach($errors->all() as $error)
										{{ $error }} <br/>
									@endforeach
								</div>
							@endif

							@if(session()->has('success'))
								<div class="alert alert-success alert-dismissible" role="alert">
									{{ session()->get('success') }}
								</div>
							@endif					
																	
						</div>	
						<div class="modal-footer">
							<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
						</div>						
					</div>					
				</div>
			</div>	
			<!-- End of Return Response Popup Model -->			
			
		</div>
	</div>
	<!--end page-content-wrapper-->
@endsection
