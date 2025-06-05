@extends('layouts.base')

@section('content') 
<div class="container-fluid">
  <div class="row mb-4">
      <div class=" d-flex justify-content-between">
          <div class="">            
              <h3>Create Shift to {{ !empty($user) ? $user[0]->firstName.' '.$user[0]->lastName : '' }}</h3>
          </div>
         
      </div>
  </div>

  <div class="row mt-4">
    <div class="col-md-12">
      <form action="{{route('shift.store')}}" method="POST" name="create-shift">
        @csrf
        @include('admin.user-shift-form-master')
      </form>
    </div>
  </div>
</div>


@endsection