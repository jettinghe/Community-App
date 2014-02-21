@extends('home')

@section('content')

@if ( ! empty($post) && $post !== null )

	<h1 class="post-header">{{ $post->title }}</h1>
	<span class="dark-span"><i class="fa fa-user"></i> <a href="{{URL::to('user/'.$post->user->username)}}" class="dark-link">{{ $post->user->username }}</a></span>
	<span class="grey-span">&#8226;</span>
	<span class="dark-span">{{ $post->created_at->diffForHumans()}}</span>
	<span class="grey-span">&#8226;</span>
	<a href="{{URL::to('category/'.$post->category->category_uri)}}" class="dark-link"><span class="label label-default">{{ $post->category->category_name }}</span></a>
	
	{{--Post Like--}}
	@if ( Auth::check() )
		@if ( ! in_array( Auth::user()->id, explode(',', $post->voters_id)) && Auth::user()->id !== $post->user->id )
		<a href="{{ URL::to("post/$post->id/votes/up")}}" class="ajax-button btn btn-xs btn-default pull-right" data-method="post" data-refresh=".votecount"  data-replace=".voted-button"><span class="vote-btn votecount" data-refresh-url="{{ URL::to("post/$post->id/votes/up")}}"><i class="fa fa-thumbs-o-up"></i> {{ $post->upvotes }}</span></a>
		@elseif(Auth::user()->id == $post->user->id)
		<a href="#" class="btn btn-xs btn-default popover-button pull-right" data-content="You cannot vote on your own post" data-placement="top"><i class="fa fa-thumbs-o-up"></i> {{ $post->upvotes }}</a>
		@else
		<a href="#" class="btn btn-xs btn-default pull-right" disabled="disabled"><span class="grey-span"><i class="fa fa-thumbs-o-up"></i> {{ $post->upvotes }}</span></a>
		@endif
	@else
	<a href="#" class="btn btn-xs btn-default pull-right" data-toggle="modal" data-target="#login-form"><i class="fa fa-thumbs-o-up"></i> {{ $post->upvotes }}</a>
	@endif
	{{--End Post Like--}}

	{{--Post Comment Form Anchor--}}
	<a href="#comment-form" class="btn btn-xs btn-default pull-right small-margin-right"><i class="fa fa-comments-o"></i> {{ $post->comments()->count() }}</a>
	{{--End Post Comment Form Anchor--}}



	{{--Post Favourite--}}
	@if ( Auth::check() )
		@if ( ! in_array( $post->id, explode(',', Auth::user()->favourite_posts)) )
		<a href="{{ URL::to('user/favourite/post/'.$post->id)}}" class="favourite-post ajax-button btn btn-xs btn-default pull-right small-margin-right" data-method="post" data-replace=".favourite-post"><span><i class="fa fa-star-o"></i></a>
		@else
		<a href="{{ URL::to('user/unfavourite/post/'.$post->id) }}" class="unfavourite-post ajax-button btn btn-xs btn-warning pull-right small-margin-right" data-method="post" data-replace=".unfavourite-post"><span><i class="fa fa-star"></i></a>
		@endif
	@else
	<a href="#" class="btn btn-xs btn-default pull-right small-margin-right" data-toggle="modal" data-target="#login-form"><i class="fa fa-star-o"></i></a> 
	@endif
	{{--End Post Favourite--}}

	<hr>
	
	<p>{{ $post->content }}</p>

	<hr>
	<div class="post-tags">
	@if ( ! empty( $post->tags) && $post->tags !== '')
		@foreach( explode(',', $post->tags) as $tag)
			<a href="{{ URL::to("tag/$tag")}}"><span class="label label-default"><i class="fa fa-tag"></i> {{ $tag }}</span></a>
		@endforeach
	@endif
	</div>
	<a name="comments" class="fixed-header-anchor"></a>
	<div id="comments" data-user-login="{{ Auth::check() ? 'yes': 'no'}}">
		<div class="panel panel-default">
		  <div class="panel-heading">Comments</div>
		<ul class="list-group">
		@foreach ($post->comments as $key => $comment)
			<li class="list-group-item">
				<div class="comment-container" id="comment-id-{{ $comment->id }}" data-comment-id="{{ $comment->id }}" data-counter="{{ $key + 1 }}" data-reply-comment-id="{{ $comment->reply_comment_id }}">
					<a name="comment-{{ $comment->id }}" class="fixed-header-anchor"></a>
					<?php $comment_user_name = $comment->user->username; ?>
						<a href="#comment-{{$comment->id }}"><span class="label label-default pull-right">#{{ $key + 1 }}</span></a>
						<div class="comment-meta medium-margin-bottom">
							<span class="dark-span"><i class="fa fa-user"></i> <a href="{{URL::to('user/'.$comment_user_name)}}" class="dark-link">{{ $comment_user_name }}</a></span>
							<span class="grey-span">&#8226;</span>
							<span class="dark-span">{{ $comment->created_at->diffForHumans() }}</span>
						</div>
						<!-- the comment content -->
						<div class="comment-content">
							{{ $comment->content }}
						</div>
						<hr>
						@if (Auth::check())
						{{ link_to("#reply-comment-$comment->id", 'Reply', array('class'=> 'btn btn-default btn-xs reply-comment-button', 'id' => "reply-comment-$comment->id", 'data-at-name' => "$comment_user_name")) }}
						{{ link_to("#cancel-reply-comment-$comment->id", 'Cancel Reply', array('class'=> 'btn btn-default btn-xs cancel-reply-button', 'id' => "reply-comment-$comment->id")) }}
						@else
						{{ link_to("#reply-comment-$comment->id", 'Reply', array('class'=> 'btn btn-default btn-xs reply-comment-button popover-button', 'id' => "reply-comment-$comment->id", 'data-at-name' => "$comment_user_name", 'data-toggle' => 'modal', 'data-target' => '#login-form')) }}
						@endif
				</div>
			</li>
		@endforeach
		</ul>
		</div>
		@if (Auth::check())
			<a name="comment-form"></a>
			@include('posts.comments')
		@endif
		</div><!-- commments -->
@else

<div class="well">
	<h2 class="panel-title">Not Found</h2>
</div>

@endif

@stop