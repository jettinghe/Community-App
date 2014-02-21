@extends('home')

@section('content')

@if( count($posts) > 0)
	<div class="panel panel-default">
<<<<<<< HEAD
		<div class="panel-heading">Your Posts</div>
=======
		<div class="panel-heading">{{ $userRelatePostsTitle }}</div>
>>>>>>> 1765f9683be80a5130867bf5862b032032af32c9
		<ul class="list-group">
		@foreach($posts as $post)
		<li class="list-group-item">
			<div class="post-entry">
				<h2 class="panel-title">
					<a href="{{ Post::seolink($post->id) }}">{{ $post->title }}</a>
					<span class="badge pull-right">{{ $post->comments()->count() }}</span>
				</h2>
				<hr>
				<span><i class="fa fa-user fa-large grey-icon"></i>
				{{ $post->user->username }}</span>
				<span class="grey-span">&#8226;</span>
				<i class="fa fa-clock-o fa-large grey-icon"></i>
				<span>{{ $post->created_at->diffForHumans() }}</span>
				<span class="grey-span">&#8226;</span>
				<a href="{{ URL::to('category/'. $post->category->category_name) }}"><span class="label label-default">{{ $post->category->category_name }}</span></a>
<<<<<<< HEAD
				<a href="{{URL::to("edit/post/$post->id")}}" class="btn btn-xs btn-info pull-right">Edit Post</a>
=======
				@if($post->user->id == Auth::user()->id)
				<a href="{{URL::to("edit/post/$post->id")}}" class="btn btn-xs btn-info pull-right">Edit Post</a>
				@endif
>>>>>>> 1765f9683be80a5130867bf5862b032032af32c9
			</div>
		</li>
		@endforeach
		</ul>
	</div>

	{{ $posts->links()}}
@else
	You have not posted anything yet.
@endif

@stop