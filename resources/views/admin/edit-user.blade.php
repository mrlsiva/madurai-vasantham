@extends('layouts.base')

@section('content') 
<div class="container-fluid">
  <div class="row mb-4">
      <div class=" d-flex justify-content-between">
          <div class="">
              <h3>Edit User: <b>#{{$user->userId}} - {{$user->firstName}} {{$user->lastName}}</b></h3>
          </div>
         
      </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-12">
      <form action="{{route('users.update')}}" method="POST" name="edit-user" enctype="multipart/form-data">
        @csrf
        @include('admin.user-edit-form-master')
      </form>
    </div>
  </div>
</div>


@endsection