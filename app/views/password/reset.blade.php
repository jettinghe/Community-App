@extends('home')

@section('content')


@if (Session::has('error'))
    {{ trans(Session::get('reason')) }}
@endif

{{ Form::open(array('method' => 'post')) }}
<fieldset>
<input type="hidden" name="token" value="{{ $token }}">
<div class="form-group">
		{{ Form::label('email', 'Email') }}
		{{ Form::email('email', Input::old('email'), array('required', 'class' => 'form-control')) }}
	</div>
	<div class="form-group">
		{{ Form::label('password', 'Password') }}
		{{ Form::password('password', array('required', 'class' => 'form-control')) }}
	</div>
	<div class="form-group">
		{{ Form::label('password_confirmation', 'Confirm Password') }}
		{{ Form::password('password_confirmation', array('required', 'class' => 'form-control')) }}
	</div>
	{{ Form::submit('Reset Password', array('class' => 'btn btn-default')) }}

</fieldset>
{{ Form::close() }}
@stop