@php 
use Carbon\Carbon; 
$currentTime = Carbon::now();
use App\Http\Controllers\AttendanceController;
@endphp

@extends('layouts.base')

@section('content') 
<div class="card">
<div class="card-body">
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
                <h3>Users List</h3>
        </div>
        <div class='text-right justify-content-end flex'>                
             <span> {{ $currentTime->format('D') .' - '. $currentTime->format('d-M-Y'); }} </span>
        </div>
          
   
</div>

<table class="table users-listing">
    <tr>
        <th>User ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Mobile</th>
        <th>Punch InTime</th>
        <th>Punch OutTime</th>		
		<th>Today's Photo</th>
		<th>Permission</th>
		<th>Permission Photo</th>
		<th>Leave</th>
		<th>Late</th>	
		<th>Login DeviceInfo</th>	
        <th>Action</th>
    </tr>
    @if( !$users->isEmpty() )
        @foreach($users as $user)

        @php 
        $startTime = '-'; 
        $endTime = '-'; 
        $imageUrl = ''; 
		$permission =  '-';
		$permission_imageUrl = '';
		$leave =  '-';
		$late =  [];
        @endphp

        @if( !$attendance->isEmpty() )
             @foreach($attendance as $attend)
                @if( $attend->userId == $user->userId ) 
                    @php $startTime = $attend->startTime ? $attend->startTime : '-'; @endphp             
                    @php $endTime = $attend->endTime ? $attend->endTime : '-'; @endphp
                    @php $imageUrl = $attend->imageUrl ? $attend->imageUrl : ''; @endphp					
					@php $permission = $attend->is_permission == 1 ? "<span class='btn bg-warning pl-wrapper'>P </span><br>$attend->permissionInHours <br><span class='reason-hvr'>$attend->reason</span>" : '-' @endphp
					@php $permission_imageUrl = $attend->permission_imageUrl ? $attend->permission_imageUrl : ''; @endphp					
					@php $leave = $attend->is_leave == 1 ? "<span class='btn bg-danger pl-wrapper'>L</span> <br><span class='reason-hvr'>$attend->reason</span>" : '-' @endphp 
					@if( $attend->is_leave != 1 )
						@php $late = AttendanceController::getPunchInLateTime($user->userId); @endphp 					
					@endif	
                @endif
             @endforeach
        @endif       

            <tr>
                <td>{{ $user->userId }}</td>
                <td>
					{{ $user->firstName }} {{ $user->lastName }} <br/>
					{{ Carbon::parse($user->shiftstartTime)->format('h:iA') }}-{{ Carbon::parse($user->shiftendTime)->format('h:iA') }}				
				</td>               
                <td>{{ $user->email }}</td>
                <td>{{ $user->mobile }}</td>                            
                <td>
                   {{--  @if(isset($user->startTime) && !empty($user->startTime)) 
                    {{ $user->startTime ? $user->startTime : '-' }}
                    @endif --}}

                    {{ $startTime }}
                </td>
                <td>
                {{-- @if(isset($user->endTime) && !empty($user->endTime)) 
                    {{ $user->endTime ? $user->endTime : '-' }}
                    @endif --}}

                    {{ $endTime }}
                </td>
                <td>
                {{-- @if(isset($user->imageUrl) && !empty($user->imageUrl)) 

                    @if( $user->imageUrl != '' && file_exists(public_path('/uploads/staffs/'.$user->imageUrl)) )
                        @php $img_src = URL::to('/public/uploads/staffs/'.$user->imageUrl ); @endphp
                    @else
                        @php $img_src = URL::to('/public/uploads/thumb/user-thumb.png'); @endphp
                    @endif    
                            
                    <a href="{{ $img_src }}" data-toggle="lightbox" data-caption="{{ $user->firstName }} {{ $user->lastName }}" data-size="sm" data-constrain="true" class="col-sm-4" data-gallery="User Thumb">
                        <img class="img-fluid" src="{{ $img_src }}" width="48" height="48" />
                    </a>
                @endif   --}}
                
                {{-- @if(isset($imageUrl) && !empty($imageUrl))               
                
                    @if( $imageUrl != '' && file_exists(public_path('/uploads/staffs/'.$user->userId.'/'.$imageUrl)) )
                        @php $img_src = URL::to('/public/uploads/staffs/'.$user->userId.'/'.$imageUrl ); @endphp
                      
                        <a href="{{ $img_src }}" data-toggle="lightbox" data-caption="{{ $user->firstName }} {{ $user->lastName }}" data-size="sm" data-constrain="true" class="col-sm-4" data-gallery="User Thumb">
                            <img class="img-fluid" src="{{ $img_src }}" width="48" height="48" />
                        </a>
                    @endif 
                @endif --}}

                @if(isset($imageUrl) && !empty($imageUrl))       
                    @php 
                        $selfie = AttendanceController::getUserMedia($imageUrl) 
                    @endphp  

                    @if( $selfie['is_exists'] )
                        <a href="{{ $selfie['img_src'] }}" data-toggle="lightbox" data-caption="{{ $user->firstName }} {{ $user->lastName }}" data-size="sm" data-constrain="true" class="col-sm-4" data-gallery="User Thumb">
                            <img class="img-fluid" src="{{ $selfie['img_src'] }}" width="48" height="48" />
                        </a>					
                    @endif 
				@else
					 - 	
                @endif 
                </td>				
				<td>{!! $permission !!} </td>
				<td>                
                @if(isset($permission_imageUrl) && !empty($permission_imageUrl))       
                    @php 
                        $selfie = AttendanceController::getUserMedia($permission_imageUrl) 
                    @endphp  

                    @if( $selfie['is_exists'] )
                        <a href="{{ $selfie['img_src'] }}" data-toggle="lightbox" data-caption="{{ $user->firstName }} {{ $user->lastName }}" data-size="sm" data-constrain="true" class="col-sm-4" data-gallery="User Thumb">
                            <img class="img-fluid" src="{{ $selfie['img_src'] }}" width="48" height="48" />
                        </a>						
                    @endif 
				@else
					 - 	
                @endif 
                </td>	
                <td> {!! $leave !!} </td>
				<td>{{ $late ? $late['lateBy'] : '-' }}</td>
				<td> {{ $user->deviceInfo ? $user->deviceInfo : '-' }} </td>
                <td>
					<div class="action-btns d-flex gap-1">
						<a class="btn btn-info btn-sm my-1" href="{{route('userlog', $user->userId)}}" target="_blank" title='View'><i class="fa-solid fa-eye"></i></a>
						<a class="btn btn-info btn-sm my-1" href="{{route('users.edit', $user->userId)}}" target="_blank" title='Edit'><i class="fas fa-edit"></i></a>
						<form class="my-1" method="POST" action="{{route('users.delete', $user->userId)}}">
							@csrf
							<input name="userId" type="hidden" value="{{$user->userId}}">
							<button type="submit" class="btn btn-sm btn-danger btn-flat show_confirm_delete" data-toggle="tooltip" data-method="trash" title='Delete'><i class="fa-solid fa-trash-can"></i></i></button>
						</form>
						@if( empty($user->shiftName) ) 
						<a class="btn btn-info btn-sm my-1" href="{{route('shift.create', $user->userId)}}" target="_blank" title='Shift Time'><i class="fa-solid fa-business-time"></i></a>
						@endif
					</div>
                </td>
            </tr>
        @endforeach
    @else
        <tr>
            <td colspan="9" class="text-center">No Records Found</td>           
        </tr>
    @endif
</table>

<div class="pagination-nav flex items-center justify-between">     
    {{ $users->links() }} 
</div>

</div></div>
@endsection
