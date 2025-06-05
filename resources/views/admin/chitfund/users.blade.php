@extends('layouts.chitfund.base')

@section('content') 

@php 
use App\Http\Controllers\ChitFund\ChitFundController;
use Carbon\Carbon; 
@endphp

<!--page-content-wrapper-->
	<div class="page-content-wrapper mt-4">
		<div class="page-content">					
			
			<div class="card">
				<div class="card-body">
					<div class="card-title">
						<a href="{{route('chitfundIndex')}}"><i class="lni lni-arrow-left-circle" style="font-size: 30px; float: right;"></i></a>
						@if( !$users->isEmpty() )							
							@php 
								$start_date = Carbon::parse($users[0]->start_date);
								$end_date = Carbon::parse($users[0]->end_date);
							@endphp
						
						<h4 class="mb-0">{{ $users[0]->plan_name }} ({{ (int)round($start_date->floatDiffInMonths($end_date)); }} Months Scheme) </h4>
						<h5>{{ Carbon::parse($users[0]->start_date)->format('M Y') }} To {{ Carbon::parse($users[0]->end_date)->format('M Y') }}</h5>
						@endif
					</div>
					<hr/>
					<div class="table-responsive">					
						<table id="example2" class="table table-striped table-bordered" style="width:100%">
							<div class="ms-auto">
								<button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" style="float: inline-start;"><i class="lni lni-circle-plus"></i> Add User</button>
							</div>
							<thead>
								<tr>
									<th>User Id</th>
									<th>User Name</th>
									<th>Phone Number</th>
									<th>Address</th>
									<th>This Month Due</th>
									<th>Action</th>
								</tr>
							</thead>
							<tbody>
								@if( !$users->isEmpty() )
									@if( isset($users[0]->user_id) && $users[0]->user_id != 'null' )
										@foreach($users as $user)
											<tr>
												<td>#{{ $user->user_id }}</td>
												<td>{{ $user->user_name }}</td>
												<td>{{ $user->mobile_no }}</td>
												<td>{{ $user->address }}</td>
												<td>
												
												@php 
													$status = ChitFundController::getUserDueStatus($user->user_id);              
													$status = $status['success'] == true ? $status['data'][0]->due_status : 0;
													$color = ChitFundController::getColorCode($status);
												@endphp
													<a href="javascript:;" class="btn btn-sm btn-light-{{ $color }} btn-block radius-30">
														{{ ChitFundController::getDueStatus($status, $key='string') }} 
													</a> 	
												</td>
												<td>
													<a href="{{route('chitfund.userDetails', [$user->user_id])}}" title="View Bills"><i class="lni lni-eye" style="font-size: 18px; font-weight: 800;"></i></a>
													&nbsp;
													<a href="#" data-bs-toggle="modal" data-bs-target="#edituser_{{ $user->user_id }}"><i class="lni lni-pencil-alt" style="font-size: 18px; font-weight: 800;"></i></a>
													&nbsp;
													<!--<a href="{{route('chitfund.addDue', [$user->plan_id, $user->user_id])}}" title="Generate Bill"><i class="lni lni-checkmark" style="font-size: 18px; font-weight: 800;"></i></a>-->
													
													<!-- Add User Popup Model -->
														<div class="col">
															<div class="modal fade" id="edituser_{{ $user->user_id }}" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
																<div class="modal-dialog">
																	<div class="modal-content">
																		<div class="modal-header" style="background-color:#673ab7;">
																			<h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Edit User - #{{ $user->user_id }} {{ $user->user_name }} </h5>
																			<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
																		</div>
																		<div class="modal-body">
																			<form class="formm" action="{{route('chitfund.editUser')}}" method="POST">  	@csrf		
																				<input type="hidden" name="user_id" value="{{ $user->user_id }}">
																				<input type="hidden" name="plan_id" value="{{ $user->plan_id }}">
																					
																				<label for="user_name" class="labell">Name</label>
																				<div class="form-group">
																					<input id="user_name" type="text" class="form-control inputt @error('user_name') is-invalid @enderror" name="user_name" placeholder="Enter Name" value="{{ $user->user_name }}" required />
																				</div>
																		
																				<label for="mobile_no" class="labell">Phone Number</label>
																				<div class="form-group">
																					<input id="mobile_no" type="tel" class="form-control inputt @error('mobile_no') is-invalid @enderror" name="mobile_no" placeholder="Enter Phone Number" value="{{ $user->mobile_no }}" pattern="[0-9]{10}" required />
																				</div>	
																				<label for="address" class="labell">Address</label>
																				<div class="form-group">
																					<textarea id="address" type="tel" class="form-control inputt @error('address') is-invalid @enderror" name="address" placeholder="Enter Address" value="{{ $user->address }}"  required>{{ $user->address }}</textarea>
																				</div>
																				
																				<div class="modal-footer">
																					<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
																					<button type="submit" class="btn btn-primary">Update User</button>
																				</div>
																			</form>
																		</div>																		
																	</div>
																</div>
															</div>
														</div>
														<!-- End Add User Popup Model -->
												</td>
											</tr>
											
										@endforeach
									@else
										<tr>
											<td colspan="6" class="text-center">No Records Found</td>
										<tr>
									@endif							
								@endif
						
							</tbody>
							<tfoot>
								<tr>
									<th>User Id</th>
									<th>User Name</th>
									<th>Phone Number</th>
									<th>Address</th>
									<th>Due</th>
									<th>Action</th>
								</tr>
							</tfoot>
						</table>
					</div>
					
				</div>
				
			</div>
			<!--end card-->
			
			<!-- Add User Popup Model -->
			<div class="col">
				<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header" style="background-color:#673ab7;">
								<h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Add User</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<form class="formm" action="{{route('chitfund.createUser')}}" method="POST">
									@csrf	
									@php 
										$plan_id = null;
									@endphp									
									@if( !$users->isEmpty() )										
										@php 
											$plan_id = $users[0]->plan_id;
										@endphp
									@endif		
									<input type="hidden" name="plan_id" value="{{ $plan_id }}">	
										
									<label for="user_name" class="labell">Name</label>
									<div class="form-group {{ $errors->has('user_name') ? 'has-error' : ''}}">
										<input id="user_name" type="text" class="form-control inputt @error('user_name') is-invalid @enderror" name="user_name" placeholder="Enter Name" value="{{ old('user_name') }}" required />
										{!! $errors->first('user_name', '<p class="help-block">:message</p>') !!}
									</div>
							
									<label for="mobile_no" class="labell">Phone Number</label>
									<div class="form-group {{ $errors->has('mobile_no') ? 'has-error' : ''}}">
										<input id="mobile_no" type="tel" class="form-control inputt @error('mobile_no') is-invalid @enderror" name="mobile_no" placeholder="Enter Phone Number" value="{{ old('mobile_no') }}" pattern="[0-9]{10}" required />
										{!! $errors->first('mobile_no', '<p class="help-block">:message</p>') !!}
									</div>	
									
									<label for="address" class="labell">Address</label>
									<div class="form-group {{ $errors->has('address') ? 'has-error' : ''}}">
									
										<textarea id="address" type="tel" class="form-control inputt @error('address') is-invalid @enderror" name="address" placeholder="Enter Address" value="{{ old('address') }}"  required></textarea>
										{!! $errors->first('address', '<p class="help-block">:message</p>') !!}
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
							
									.labell {
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
			<!-- End Add User Popup Model -->

			<!-- Edit Due Status Popup Model -->
			<div class="col">
				<div class="modal fade" id="editDueStatus" tabindex="-1" aria-labelledby="editDueStatusLabel" aria-hidden="true">
					<div class="modal-dialog">
						<div class="modal-content">
							<div class="modal-header" style="background-color:#673ab7;">
								<h5 class="modal-title" id="exampleModalLabel" style="color: aliceblue;">Edit Status</h5>
								<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
							</div>
							<div class="modal-body">
								<form class="formm" action="#" method="POST">
									@csrf	
									@php 
										$plan_id = null;
									@endphp									
									@if( !$users->isEmpty() )										
										@php 
											$plan_id = $users[0]->plan_id;
										@endphp
									@endif		
									<input type="hidden" name="plan_id" value="{{ $plan_id }}">	
							
									<label for="due_status" class="labell">Status</label>
									<div class="form-group">
										<select id="due_status" class="form-group single-select select2-hidden-accessible" data-select2-id="1" tabindex="-1" aria-hidden="true">
											<option value="0" data-select2-id="0">Un Paid</option>
											<option value="1" data-select2-id="1">Paid</option>
											<option value="2" data-select2-id="2">Closed</option>
											<option value="3" data-select2-id="3">Winner 1</option>
											<option value="4" data-select2-id="4">Winner 2</option>
											<option value="5" data-select2-id="5">Winner 3</option>
										</select>
									</div>	
									
									<div class="modal-footer mt-4">
										<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
										<button type="submit" class="btn btn-primary">Change Status</button>
									</div>
								</form>
								<style>	
									.formm {
										
										padding: 20px;
										border-radius: 10px;
										width: 450px;
									}
							
									.labell {
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
			<!-- Edit Due Status Popup Model -->	
			
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
	
	<!-- Redirect to whatsapp -->	
	@php
		$sendNotifi = Config::get('app.chit_send_useradd_watsapp_notifi');
	@endphp	
	@if ( $sendNotifi && session()->has('url') )
		<a id="userRegNotifi" href="{{session()->get('url')}}" target="_blank" style="visibility:hidden; opacity:0; height:0; font-size:0;"></a>
		<script src="{{asset('/resources/assets_chitfund/js/jquery.min.js')}}"></script>
		<script type="text/javascript">
			$(document).ready(function(){
				document.getElementById('userRegNotifi').click();
			});
		</script>
	@endif
	<!--end Redirect to whatsapp -->	
	
@endsection
