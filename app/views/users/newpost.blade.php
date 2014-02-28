@extends('home')
@section('content')

@if($errors->count() > 0)
	<div class="alert alert-warning alert-dismissable">
		<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
		<ul>
		{{ $errors->first('title', '<li><strong>:message</strong></li>') }}
		{{ $errors->first('content', '<li><strong>:message</strong></li>') }}
		</ul>
	</div>
@endif

	{{ Form::open(array('id' => 'new-post', 'url' => 'user/new-post', 'method' => 'post', 'role' => 'form')) }}
	<fieldset>
		{{ Form::token() }}
		<input type="hidden" name ="postid" id="postid" value="{{$uniqid}}"/>
		<div class="form-group">
		{{ Form::label('title', 'Title') }}
		{{ Form::text('title', Input::old('title'), array('required', 'class' => 'form-control')) }}
		</div>
		<div class="form-group">
		{{ Form::label('content', 'Content') }}
		{{ Form::textarea('content', Input::old('content'), array('required', 'class' => 'form-control', 'id'=>'new-post-wysiwyg')) }}
		</div>
		<div class="form-group">
		{{ Form::label('category', 'Category') }}
		{{ $catsHTML }}
		</div>
		<div class="form-group">
		{{ Form::label('tags', 'Tags') }}
		{{ Form::text('tags', Input::old('tags'), array('id' => 'add-post-tags', 'class' => 'form-control', 'data-role' => 'tagsinput')) }}
		<span class="help-block">Add post tags, separate them using comma, hit enter or space key. Maximum 5 tags per post.</span>
		</div>
		<hr>
		{{ Form::submit('Add New Post', array('class' => 'btn btn-default')) }}
	</fieldset>
	{{ Form::close() }}
	
      
	{{ HTML::script('/js/summernote.min.js') }}
	{{ HTML::style('/css/summernote.css') }}
@stop