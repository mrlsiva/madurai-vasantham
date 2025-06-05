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
								@if( !$user->isEmpty() )
									<a href="{{route('chitfund.showPlan', $user[0]->plan_id)}}"><i class="lni lni-arrow-left-circle" style="font-size: 30px; float: right;"></i></a>								
								
									<h4 class="mb-0">#{{ $user[0]->user_id }} -  {{ $user[0]->user_name }} </h4>
								@endif 	
							</div>
							<hr/>
							<div class="table-responsive">					
								<table id="example2" class="table table-striped table-bordered" style="width:100%">
									<thead>
										<tr>
											<th>SL.No</th>
											<th>Bill.No</th>
											<th>Month</th>
											<th>Status</th>
											<th>Update Status</th>
											<th>Print</th>
										</tr>
									</thead>
									<tbody>
										@if( !$user->isEmpty() )
											@php
												$i = 0;
												$is_disabled = false;
										    @endphp 
											@foreach($user as $u)
												@php
													$i = $i+1;													
												@endphp 
												<tr class="{{ $is_disabled ? 'table-light' : ''}}">
													<td>{{ $i }}</td>
													<td>#{{$u->due_id}}</td>
													<td>{{ Carbon::parse($u->due_date)->format('M Y') }}</td>
													<td>
														@php 
															$color = ChitFundController::getColorCode($u->due_status);
														@endphp
														<a href="javascript:;" class="btn btn-sm btn-light-{{ $color }} btn-block radius-30">
														{{ ChitFundController::getDueStatus($u->due_status, $key='string') }}
													</a>
													</td>
													<td>
														<form class="formm" action="{{route('chitfund.updateDueStatus')}}" method="POST">
															@csrf
															<input type="hidden" name="due_id" value="{{ $u->due_id }}">															
															<select name="due_status" class="single-select select2-hidden-accessible" data-select2-id="1" tabindex="-1" aria-hidden="true">
																<option value="0" data-select2-id="0" {{ $u->due_status == 0 ? 'selected' : '' }}>Un Paid</option>
																<option value="1" data-select2-id="1" {{ $u->due_status == 1 ? 'selected' : '' }}>Paid</option>
																<option value="2" data-select2-id="2" {{ $u->due_status == 2 ? 'selected' : '' }}>Closed</option>
																<option value="3" data-select2-id="3" {{ $u->due_status == 3 ? 'selected' : '' }}>Winner 1</option>
																<option value="4" data-select2-id="4" {{ $u->due_status == 4 ? 'selected' : '' }}>Winner 2</option>
																<option value="5" data-select2-id="5" {{ $u->due_status == 5 ? 'selected' : '' }}>Winner 3</option>
															</select>
															<button type="submit" class="btn btn-primary btn-sm">Update</button>
														</form>
													</td>
													<td>
														<a href="{{route('chitfund.printInvoice', [$u->user_id, $u->due_id])}}" target="_blank"> <i class="lni lni-printer" style="font-size: 18px;"></i> </a>
														&nbsp;
														
														@php 
															$whatsapp_message = '';
															$whatsapp_url = '#';
															$target = "_self";
															if( $u->due_status == 1 ){		
																$date = Carbon::parse($u->due_date_paid )->format('d-m-Y');
																$whatsapp_message = urlencode("Dear *$u->user_name*, Token Number *$u->user_id* you have paid Rs:$u->plan_amount for the date $date of vasantham chit fund $u->plan_name. \n \n Thank you. \n Vasantham Group Of Companies");
																$whatsapp_url = "https://wa.me/$u->mobile_no?text=$whatsapp_message";
																$target = "_blank";
															}
														@endphp
														
														<a href="{{$whatsapp_url}}" target="{{$target}}"><i class="lni lni-whatsapp" style="font-size: 18px;"></i></a>
													</td>
													@php 													
														if ( $u->due_status >= 2 ) {
															$is_disabled = true;
														}	
													@endphp
												</tr>
											@endforeach
										@else
										<tr>
											<td colspan="6" class="text-center">No Dues Paid</td>
										<tr>
									@endif	
								
									</tbody>
									<tfoot>
										<tr>
											<th>SL.No</th>
											<th>Bill.No</th>
											<th>Month</th>
											<th>Status</th>
											<th>Update Status</th>
											<th>Print</th>
										</tr>
									</tfoot>
								</table>
							</div>
							
						</div>
						
					</div>
					<!--end card-->			
			
			
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
