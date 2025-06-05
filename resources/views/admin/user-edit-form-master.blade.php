@php
use Carbon\Carbon;
@endphp
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
	
		<input type="hidden" name="status" value="A" />
		<input type="hidden" name="userId" value="{{ $user->userId }}" />

		<div class="row">
			<div class="col-sm-6">      
				<label for="firstName" class="mb-1">First Name</label>
				<div class="form-group {{ $errors->has('firstName') ? 'has-error' : ''}}">
					<input id="firstName" type="text" class="form-control @error('firstName') is-invalid @enderror" name="firstName" placeholder="First Name" value="{{ $user->firstName }}" required />
					{!! $errors->first('firstName', '<p class="help-block">:message</p>') !!}
				</div>
			</div>
			<div class="col-sm-6">
				<label for="lastName" class="mb-1">Last Name</label>
				<div class="form-group {{ $errors->has('lastName') ? 'has-error' : ''}}">
					<input id="lastName" type="text" class="form-control @error('lastName') is-invalid @enderror" name="lastName" placeholder="First Name" value="{{ $user->lastName }}" required />
					{!! $errors->first('lastName', '<p class="help-block">:message</p>') !!}
				</div>
			</div>
		</div>
		
		<div class="row mt-4">
			<div class="col-sm-6">      
				<label for="email" class="mb-1">Email</label>
				<div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
					<input id="email" type="text" class="form-control @error('email') is-invalid @enderror" name="email" placeholder="Email" value="{{ $user->email }}" required />				
					{!! $errors->first('email', '<p class="help-block">:message</p>') !!}				  
				</div>
			</div>
			
			<div class="col-sm-6">      
				<label for="mobile" class="mb-1">Mobile</label>
				<div class="form-group {{ $errors->has('mobile') ? 'has-error' : ''}}">
					<input id="mobile" type="text" class="form-control @error('mobile') is-invalid @enderror" name="mobile" placeholder="Mobile" value="{{ $user->mobile }}" required />				
					{!! $errors->first('mobile', '<p class="help-block">:message</p>') !!}				  
				</div>
			</div>
		</div>

		<div class="row mt-4">
			<div class="col-sm-6">      
				<label for="DOB" class="mb-1">DOB ( YYYY-MM-DD )</label>
				<div class="form-group {{ $errors->has('DOB') ? 'has-error' : ''}}">
					<input id="DOB" type="text" class="form-control @error('DOB') is-invalid @enderror" name="DOB" placeholder="2000-05-10" value="{{ $user->DOB }}" required />				
					{!! $errors->first('DOB', '<p class="help-block">:message</p>') !!}				  
				</div>
			</div>
			
			<div class="col-sm-6">      
				<label for="gender" class="mb-1">Gender</label>
				<div class="form-group {{ $errors->has('gender') ? 'has-error' : ''}}">
					<label for="male" class="mx-2"><input id="male" type="radio" class="" name="gender" value="M" {{ ( $user->gender == "M" ) ? "checked":"" }} />
					Male</label>
					
					<label for="Female" class="mx-2"><input id="Female" type="radio" class="" name="gender" value="F" {{ ( $user->gender == "F") ? "checked":"" }} />
					Female</label>
					
					<label for="Transgender" class="mx-2"><input id="Transgender" type="radio" class="" name="gender" value="T" {{ ( $user->gender == "T" ) ? "checked":"" }} />
					Transgender</label>			
							
					{!! $errors->first('gender', '<p class="help-block">:message</p>') !!}	
				</div>
			</div>
		</div>	
		
		<div class="row mt-4">
			<div class="col-sm-6">      
				<label for="address" class="mb-1">Address</label>
				<div class="form-group {{ $errors->has('address') ? 'has-error' : ''}}">
					<textarea id="address" type="text" class="form-control @error('address') is-invalid @enderror" name="address" placeholder="Address" value="" required >{{ $user->address }}</textarea>				
					{!! $errors->first('address', '<p class="help-block">:message</p>') !!}				  
				</div>
			</div>
		</div>
		
		<div class="row mt-4">
			<div class="col-sm-6">      
				<label for="password" class="mb-1">Password</label>
				<div class="form-group {{ $errors->has('password') ? 'has-error' : ''}}">
					<input id="password" type="text" class="form-control @error('password') is-invalid @enderror" name="password" placeholder="Password" value="vasantham123" required />				
					{!! $errors->first('password', '<p class="help-block">:message</p>') !!}				  
				</div>
			</div>
		</div>

		<div class="row mt-4">
			<div class="col-sm-6">      
				<label for="role" class="mb-1">Role</label>
				<div class="form-group {{ $errors->has('role') ? 'has-error' : ''}}">			
					
					@if(!empty($roles))
						 <select name="role" id="role" class="form-control @error('role') is-invalid @enderror" required >
							<option value="">Select Role</option>
							@foreach($roles as $i => $value)
								   <option value="{{$value->roleId}}" {{ ( $user->roleId == $value->roleId ) ? "selected":"" }} >{{$value->name}}</option>
							@endforeach 
						</select>	
                    @endif
					
					{!! $errors->first('role', '<p class="help-block">:message</p>') !!}				  
				</div>
			</div>
		</div>	
		
		<div class="row mt-4">
			<div class="col-sm-6">
				<label for="lastName" class="mb-1">Select Shift</label>
				<div class="form-group {{ $errors->has('shiftId') ? 'has-error' : ''}}">
					@if(!empty($shifts))
						 <select name="shiftId" id="shiftId" class="form-control @error('shiftId') is-invalid @enderror" required >
							<option value="">Select Shift</option>
							@foreach($shifts as $i => $value)
								<option value="{{$value->shiftId}}" {{ ( $user->shid == $value->shiftId ) ? "selected":"" }} >
								{{$value->shiftName}} - {{ Carbon::parse($value->startTime)->format('h:iA') }}-{{ Carbon::parse($value->endTime)->format('h:iA') }}</option>
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
					Update User
				</button>
			</div>		
		</div>   

		
	</div>
</div>

