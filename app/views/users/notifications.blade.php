@extends('home')

@section('content')
	
	@if( count( $postvotenotifications ) !== 0 )
		<div class="panel panel-success">
			<div class="panel-heading"><i class="fa fa-thumbs-up"></i> Vote Notification <a href="{{ URL::to('notifications/votes-read') }}" class="btn btn-default btn-xs pull-right">Mark All As Read</a></div>
			<div class="panel-body">
			</div>
			<ul class="list-group">
			@foreach( $postvotenotifications as $postvotenotification )
				<a href="{{ Post::seolink($postvotenotification->post_id) }}" class="list-group-item">
				    <blockquote><span class="badge"> + {{ $postvotenotification->upvoted }} </span> {{ Post::find($postvotenotification->post_id)->title }}</blockquote>
				</a>
			@endforeach
			</ul>
		</div>
	@endif

	@if( count($notifications) !== 0 )
		<div class="panel panel-success">
			<div class="panel-heading"><i class="fa fa-comment-o"></i> Comment Notification <a href="{{ URL::to('notifications/mark-all-as-read') }}" class="btn btn-default btn-xs pull-right">Mark All As Read</a></div>
			<div class="panel-body">
			</div>
			<ul class="list-group">
		@foreach($notifications as $notification)
			<li class="list-group-item single-notification" id="notification-{{$notification->id}}">
				<blockquote>{{ Comment::find($notification->comment_id)->user->username }} : &quot;{{ Comment::find($notification->comment_id)->content }}&quot;
					<hr>
					@if( $notification->reply_comment_id == 0)
					Replied to Your Post: <strong>{{ Post::find($notification->post_id)->title }}</strong>
					@else
					Replied to Your Comment: <strong><u>{{ Comment::find($notification->reply_comment_id)->content }}</u></strong>
					in post: <strong><u>{{ Post::find($notification->post_id)->title }}</u></strong>
					@endif
					<span class="label label-info pull-right">{{$notification->created_at->diffForHumans()}}</span>
				</blockquote>
				<a href="{{ URL::to('notifications/' . $notification->id) }}" class="ajax-button btn btn-xs btn-default" data-method="post" data-prepend="#notification-{{$notification->id}}" data-remove="#notification-{{$notification->id}} .ajax-button">Mark As Read</a> 
				@if(Auth::user()->isReplied($notification->comment_id))
				<a class="btn btn-xs btn-default" disabled="disabled"><span class="grey-span">Replied</span></a>
				@else
				<!-- <a href="#" class="btn btn-xs btn-default popover-button" data-content='{{ User::commentFormHtml($notification->post_id, $notification->comment_id) }}' data-placement="right" data-html="true">Reply Comment</a> -->
				<a href="{{ URL::to('notifications/reply-comment/' . $notification->post_id . '/' .$notification->comment_id ) }}" class="ajax-button btn btn-xs btn-default" data-method="get" data-replace="#comment-reply-{{ $notification->comment_id }}">Reply Comment</a>
				<div id="comment-reply-{{ $notification->comment_id }}" class="reply-comment-form"></div>
				@endif
			</li>
		@endforeach
			</ul>
		</div>
	@endif

	@if ( count( $postvotenotifications ) == 0 && count( $notifications ) == 0 ) 
		<div class="panel panel-success">
			<div class="panel-heading">Notification Board</div>
			<div class="panel-body">
				<p>Congrats! You have no new notifications!</p>
			</div>
		</div>
	@endif

@stop