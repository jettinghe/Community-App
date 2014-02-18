@extends('home')

@section('content')
	@if($errors->count() > 0)
		<div class="alert alert-warning alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<ul>
			{{ $errors->first('email', '<li><strong>:message</strong></li>') }}
			{{ $errors->first('username', '<li><strong>:message</strong></li>') }}
			{{ $errors->first('password', '<li><strong>:message</strong></li>') }}
			{{ $errors->first('password_confirmation', '<li><strong>:message</strong></li>') }}
			</ul>
		</div>
	@endif
	<h2>Register</h2>
	<hr>

	{{ Form::open(array('url' => 'register', 'method' => 'post', 'role' => 'form')) }}
		<fieldset>
		{{ Form::token() }}

		<div class="form-group">
			{{ Form::label('email', 'Email') }}
			{{ Form::email('email', Input::old('email'), array('required', 'class' => 'form-control')) }}
		</div>
		<div class="form-group">
			{{ Form::label('username', 'User Name') }}
			{{ Form::text('username', Input::old('username'), array('required', 'class' => 'form-control')) }}
		</div>
		<div class="form-group">
			{{ Form::label('password', 'Password') }}
			{{ Form::password('password', array('required', 'class' => 'form-control')) }}
		</div>
		<div class="form-group">
			{{ Form::label('password_confirmation', 'Confirm Password') }}
			{{ Form::password('password_confirmation', array('required', 'class' => 'form-control')) }}
		</div>
		<hr>
		{{ Form::submit('Register', array('class' => 'btn btn-default')) }}
		</fieldset>
	{{ Form::close() }}
@stop