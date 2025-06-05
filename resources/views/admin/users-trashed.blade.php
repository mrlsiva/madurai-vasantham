@php 
use Carbon\Carbon; 
$currentTime = Carbon::now();
use App\Http\Controllers\AttendanceController;
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


<div class="container d-flex  justify-content-between my-2"> 
    
        <div class='text-left justify-content-start flex'>                
                <h3>Trashed Users List</h3>
        </div>
        <div class='text-right justify-content-end flex'>                
             <span> {{ $currentTime->format('D') .' - '. $currentTime->format('d-M-Y'); }} </span>
        </div>
          
   
</div>

<table class="table">
    <tr>
        <th>User ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Mobile</th>        
        <th>Action</th>
    </tr>
    @if( !$users->isEmpty() )
        @foreach($users as $user)
            <tr>
                <td>{{ $user->userId }}</td>
                <td>{{ $user->firstName }} {{ $user->lastName }}</td>               
                <td>{{ $user->email }}</td>
                <td>{{ $user->mobile }}</td>
                <td>     
					<div class="action-btns">
						<form method="POST" action="{{route('users.restore', $user->userId)}}">
							@csrf
							<input name="userId" type="hidden" value="{{$user->userId}}">
							<button type="submit" class="btn btn-sm btn-info show_confirm_delete my-1" data-toggle="tooltip" data-method="restore" title='Restore'><i class="fa-regular fa-window-restore"></i></button>
						</form>	
						<form method="POST" action="{{route('users.deleteforever', $user->userId)}}">
							@csrf
							<input name="userId" type="hidden" value="{{$user->userId}}">
							<button type="submit" class="btn btn-sm btn-danger show_confirm_delete" data-toggle="tooltip" data-method="delete" title='Delete'><i class="fa-solid fa-trash"></i></button>
						</form>	
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
@endsection
