@extends('layouts.base')

@section('content') 
<div class="container-fluid">
  <div class="row mb-4">
      <div class=" d-flex justify-content-between">
          <div class="">
              <h3>Create User</h3>
          </div>
         
      </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-12">
      <form action="{{route('users.store')}}" method="POST" name="create-user" enctype="multipart/form-data">
        @csrf
        @include('admin.user-form-master')
      </form>
    </div>
  </div>
</div>


@endsection