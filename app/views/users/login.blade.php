@extends('home')

@section('content')
	<h2>Login</h2>
	{{ Form::open(array('url' => 'user/login', 'method' => 'post', 'role' => 'form')) }}
	<fieldset>
	{{ Form::token() }}
	<div class="form-group">
		{{ Form::label('email', 'Email') }}
		{{ Form::email('email', Input::old('email'), array('required', 'class' => 'form-control')) }}
	</div>
	<div class="form-group">
		{{ Form::label('password', 'Password') }}
		{{ Form::password('password', array('required', 'class' => 'form-control')) }}
	</div>
	{{ Form::submit('Login', array('class' => 'btn btn-default')) }}
	<p>{{ HTML::link('password/remind', 'Forgot Your Password?')}}</p>
	{{ Form::close() }}
@stop