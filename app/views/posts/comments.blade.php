@if($errors->count() > 0)
	@if($errors->count() > 0)
		<div class="alert alert-warning alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
			<ul>
			{{ $errors->first('content', '<li><strong>:message</strong></li>') }}
			</ul>
		</div>
	@endif
@endif
{{ Form::open(array('id' => 'new-comment', 'method' => 'post', 'role' => 'form')) }}
<fieldset>
	{{ Form::token() }}
	<input type="hidden" name ="postid" id="postid" value="{{$post->id}}"/>
	<div class="form-group">
	{{ Form::textarea('content', Input::old('content'), array('required', 'id' => 'comment-textarea','class' => 'form-control', 'novalidate')) }}
	</div>
	{{ Form::submit('Add Comment', array('class' => 'btn btn-default')) }}
</fieldset>
{{ Form::close() }}
{{ HTML::script('/js/summernote.min.js') }}
{{ HTML::style('/css/summernote.css') }}