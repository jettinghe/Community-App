@extends('home')

@section('content')

@if( count($userPosts) > 0)

	<h3>Posts made by {{ $username }}</h3>

	@foreach($userPosts as $userPost)

	<div class="well">
		<h2 class="panel-title"><span class="label label-default">{{ $userPost->category->category_name }}</span> <a href="{{ Post::seolink($userPost->id) }}">{{ $userPost->title }}</a> </h2>
		<hr>
		
		<span>{{ $userPost->created_at->diffForHumans()}}</span>
		<span>&#8226;</span>
		<span>by {{ $userPost->user->username }}</span>
	</div>

	@endforeach
@else
	This user hasn't posted anything yet.
@endif

<!-- <hr> -->

@if( count($userComments) > 0)
	<h3 class="page-header">Comments made by {{ $username }}</h3>
	@foreach($userComments as $userComment)

		@if ( $userComment->reply_comment_id == 0)
		<blockquote><a href="{{ Post::seolink($userComment->post_id) }}#comment-{{$userComment->id}}"><i class="fa fa-link grey-icon"></i></a> {{ $userComment->content }}
			<hr>
			Replied to post <a href="{{ Post::seolink($userComment->post_id) }}">{{ Post::find($userComment->post_id)->title }}</a>
		</blockquote>
		@else

		<blockquote><a href="{{ Post::seolink($userComment->post_id) }}#comment-{{$userComment->id}}"><i class="fa fa-link grey-icon"></i></a> {{ $userComment->content }}
			<hr>
			Replied to 
			<?php 
				$replyCommentIdArray = explode(',' , $userComment->reply_comment_id); 
				$countReplyComments = count($replyCommentIdArray);
			?>
			@foreach( $replyCommentIdArray as $key => $reply_comment_id)
				<?php $replied_comment = Comment::find($reply_comment_id); ?>
				<strong>{{ $replied_comment->user->username }} : "{{ $replied_comment->content }}"</strong>
				@if( $key + 1 !== $countReplyComments) 
				,
				@endif
			@endforeach

			under post: <a href="{{ Post::seolink($userComment->post_id) }}">{{ Post::find($userComment->post_id)->title }}</a>
		</blockquote>

		@endif

	@endforeach

@else
	This user hasn't made any comments yet.
@endif

@stop