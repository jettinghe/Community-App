@extends('home')

@section('content')

@if (Session::has('error'))
    {{ trans(Session::get('reason')) }}
@elseif (Session::has('success'))
    An e-mail with the password reset has been sent.
@endif
<h2>Reset Password</h2>
<hr>
{{ Form::open(array('method' => 'post', 'class'=>'form-horizontal', 'role'=>'form')) }}
<div class="form-group">
	<div class="col-sm-8">
	{{ Form::text('email', '', array('class'=>'form-control', 'placeholder' => 'Your Email Address', 'required')) }}
	</div>
    <div class="col-sm-4">
	{{ Form::submit('Send Reminder Email', array('class' => 'btn btn-info')) }}
    </div>
</div>
{{ Form::close()}}

@stop