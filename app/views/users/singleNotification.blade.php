@extends('home')

@section('content')
	
	@if( !empty($singleNotification) && $singleNotification !== null )
		<div class="well">
			You've got a new reply by {{ Comment::find($singleNotification->comment_id)->user->username }}<hr>
			in this post: {{ Post::find($singleNotification->post_id)->title }} <hr>
			comment content: {{ Comment::find($singleNotification->comment_id)->content }} <hr>
		</div>
	@endif

@stop