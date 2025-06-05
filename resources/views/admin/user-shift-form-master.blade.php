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
  
<div class="row">
	<div class="col-lg-8">
	
		<input type="hidden" name="userId" value="{{ !empty($user) ? $user[0]->userId : '' }}" />

		<div class="row">
			
			<div class="col-sm-6">
				<label for="lastName" class="mb-1">Select Shift</label>
				<div class="form-group {{ $errors->has('shiftId') ? 'has-error' : ''}}">
					@if(!empty($shifts))
						 <select name="shiftId" id="shiftId" class="form-control @error('shiftId') is-invalid @enderror" required >
							<option value="">Select Shift</option>
							@foreach($shifts as $i => $value)
								<option value="{{$value->shiftId}}" {{ ( old("shiftId") == $value->shiftId ) ? "selected":"" }} >{{$value->shiftName}}</option>
							@endforeach 
						</select>	
                    @endif

					{!! $errors->first('shiftId', '<p class="help-block">:message</p>') !!}
				</div>
			</div>
		</div>
		
		
		
		<div class="row">
			<div class="form-group pt-5 pb-5">   
				<button type="submit" class="btn btn-success">
					{{ isset($users) ? 'Update User Shift' : 'Create User Shift'}}
				</button>
			</div>		
		</div>   

		
	</div>
</div>

