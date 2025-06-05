@php
use Carbon\Carbon;
use App\Http\Controllers\AttendanceController;
@endphp
@extends('layouts.base')

@section('content') 
<div class="card">
<div class="card-body">

@if ($errors->any())
    <div class="alert alert-danger  alert-dismissible">
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if( !$attendancelogs->isEmpty() )
    <h3>{{ $attendancelogs[0]->firstName }} {{ $attendancelogs[0]->lastName }} Attendance Logs</h3> 
@endif   

<div class="container d-flex text-right justify-content-end my-2">
    @if( !$attendancelogs->isEmpty() )
    <div class="col-lg-3">
    <form action="{{ route('attendancelogs.export') }}" method="post">
        @csrf
        <div class="form-group">
            <div class='input-group text-right justify-content-end flex'>
            <input type='hidden' name="id" value="{{$attendancelogs[0]->userId}}" />
                {{----}}
                 <input type='text' name="date" class="form-control date  mx-2" id='userLogDatePicker' />
               <!-- <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                </span>  -->              
                <button class=" mt-8 text-center btn btn-success py-2">Download Report</button>
            </div>
        </div>         
        </form>    
        </div>    
    @endif
</div>



<table class="table">
    <tr>
        <th>SL No</th>
        <th>User</th>        
        <th>Shift Time</th>
        <th>Punch In Time</th>
        <th>Punch Out Time</th>         
		<th>Total Hours</th>        
        <th>Permission</th>
		<th>Permission Photo</th>
        <th>Leave</th>
        <th>Late</th>
        <th>OverTime</th>
    </tr>

    @if( !$attendancelogs->isEmpty() )

        @foreach($attendancelogs as $key => $attendancelog)
            <tr>
                <td>{{ $attendancelogs->firstItem() + $key}}</td>
                <td>                    
                   {{-- @if( $attendancelog->imageUrl != '' && file_exists(public_path('/uploads/staffs/'.$attendancelog->userId.'/'.$attendancelog->imageUrl)) )
                        @php $img_src = URL::to('/public/uploads/staffs/'.$attendancelog->userId.'/'.$attendancelog->imageUrl ); @endphp
                    @else
                        @php $img_src = URL::to('/public/uploads/thumb/user-thumb.png'); @endphp                        
                    @endif --}}
                    
                    @php 
                        $selfie = AttendanceController::getUserMedia($attendancelog->imageUrl) 
                    @endphp
                    <a href="{{ $selfie['img_src'] }}" data-toggle="lightbox" data-caption="{{ $attendancelog->firstName }} {{ $attendancelog->lastName }}" data-size="sm" data-constrain="true" class="col-sm-4" data-gallery="User Thumb">
                        <img class="img-fluid" src="{{ $selfie['img_src'] }}" width="48" height="48" />
                    </a>
                </td>    
                <td>{{ Carbon::parse($attendancelog->shiftstartTime)->format('h:iA') }}-{{ Carbon::parse($attendancelog->shiftendTime)->format('h:iA') }}</td>            
                <td>{{ $attendancelog->startTime ? $attendancelog->startTime : '-' }}</td>
                <td>{{ $attendancelog->endTime ? $attendancelog->endTime : '-' }}</td>           
                <td>
					@if( $attendancelog->is_leave != 1 )
						{{ $attendancelog->total_hours }}
					@else
						 -	
					@endif				
				</td>
                <td> 				 
				{!! $attendancelog->is_permission == 1 ? "<span class='btn bg-warning pl-wrapper'>P </span><br>$attendancelog->permissionInHours <br><span class='reason-hvr'>$attendancelog->reason</span>" : '-' !!} 
				</td>
				<td>                
                @if(isset($attendancelog->permission_imageUrl) && !empty($attendancelog->permission_imageUrl))       
                    @php 
                        $selfie = AttendanceController::getUserMedia($attendancelog->permission_imageUrl) 
                    @endphp  

                    @if( $selfie['is_exists'] )
                        <a href="{{ $selfie['img_src'] }}" data-toggle="lightbox" data-caption="{{ $attendancelog->firstName }} {{ $attendancelog->lastName }}" data-size="sm" data-constrain="true" class="col-sm-4" data-gallery="User Thumb">
                            <img class="img-fluid" src="{{ $selfie['img_src'] }}" width="48" height="48" />
                        </a>						
                    @endif 
				@else
					 - 	
                @endif 
                </td>	
                <td> {!! $attendancelog->is_leave == 1 ? "<span class='btn bg-danger pl-wrapper'>L</span> <br><span class='reason-hvr'>$attendancelog->reason</span>" : '-' !!} </td>
                <td> 

				@if( $attendancelog->is_leave != 1 )
					@php $late = AttendanceController::getPunchInLateTime($attendancelog->userId, $attendancelog->startTime); @endphp 	
					{{ $late['lateBy'] ? $late['lateBy'] : '-' }}
				@else
					 -	
				@endif	

				</td>
                <td> - </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="12" class="text-center">No Records Found</td>           
        </tr>
    @endif
</table>

<div class="pagination-nav flex items-center justify-between">        
	{{ $attendancelogs->links() }} 
</div>

</div></div>
@endsection

