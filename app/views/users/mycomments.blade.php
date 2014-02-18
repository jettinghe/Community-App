@extends('home')

@section('content')

@if( count($comments) > 0)

	@foreach($comments as $comment)

		@if ( $comment->reply_comment_id == 0)
		<blockquote><a href="{{ Post::seolink($comment->post_id) }}#comment-{{$comment->id}}"><i class="fa fa-link grey-icon"></i></a> {{ $comment->content }}
			<hr>
			Replied to post <a href="{{ Post::seolink($comment->post_id) }}">{{ Post::find($comment->post_id)->title }}</a>
		</blockquote>
		@else

		<blockquote><a href="{{ Post::seolink($comment->post_id) }}#comment-{{$comment->id}}"><i class="fa fa-link grey-icon"></i></a> {{ $comment->content }}
			<hr>
			Replied to 
			<?php 
				$replyCommentIdArray = explode(',' , $comment->reply_comment_id); 
				$countReplyComments = count($replyCommentIdArray);
			?>
			@foreach( $replyCommentIdArray as $key => $reply_comment_id)
				<?php $replied_comment = Comment::find($reply_comment_id); ?>
				<strong>{{ $replied_comment->user->username }} : "{{ $replied_comment->content }}"</strong>
				@if( $key + 1 !== $countReplyComments) 
				,
				@endif
			@endforeach

			under post: <a href="{{ Post::seolink($comment->post_id) }}">{{ Post::find($comment->post_id)->title }}</a>
		</blockquote>

		@endif

	@endforeach

	{{ $comments->links()}}
@else
	You have not made any comments yet.
@endif

@stop