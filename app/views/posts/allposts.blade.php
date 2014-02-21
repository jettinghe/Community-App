@extends('home')

@section('content')

{{-- Breadcrumb Section--}}
@if ( !empty($tagname) && $tagname !== '' )
<h3>Posts Tagged by {{ $tagname}} </h3><hr>
@endif
@if ( !empty($keywords) && $keywords !== '' )
<h3>Search Results For: {{ $keywords}} </h3><hr>
@endif
{{-- End Breadcrumb Section--}}

{{-- Display Posts --}}
@if( count($posts) > 0 )
	<div id="post-entries" class="panel panel-default">
		<div class="panel-heading">
			<ul class="nav nav-pills topic-nav">
				@foreach($parentCats as $parentCat)
				{{-- Return active highlight li class from helper class function --}}
				@if ( !empty($category) && $category !== '' && $category !== null )
				{{ Helper::getActiveClass($parentCat->parent_category_uri, 'topic', '/', true, $category) }}
				@else
				{{ Helper::getActiveClass($parentCat->parent_category_uri, 'topic', '/', false, '') }}
				@endif
				{{ HTML::link('topic/'.$parentCat->parent_category_uri, $parentCat->parent_category_name)}}</li>
				@endforeach
<<<<<<< HEAD
				{{ Helper::getActiveClass('topics', '', '', false, '') }}<a href="{{ URL::to('topics') }}">All Topics</a></li>
=======
				{{-- End return topic links(parent category links)--}}
				{{ Helper::getActiveClass('topics', '', '', false, '') }}<a href="{{ URL::to('topics') }}">All Topics</a></li>
				@if ( Auth::check() )
				{{ Helper::getActiveClass('followed-topics', '', '', false, '') }}<a href="{{ URL::to('followed-topics') }}">Followed Topics</a></li>
				@endif
>>>>>>> 1765f9683be80a5130867bf5862b032032af32c9
			</ul>
		</div>
		
		@if ( !empty($parentcategory) && $parentcategory !== '' )
		<div class="panel-body panel-body-cats">
			<ul class="nav nav-pills cat-nav">
				@foreach($parentcategory->categories as $subcategory)
					<li><a href="{{ URL::to('category/'.$subcategory->category_uri) }}"><span>&#8226;</span> {{ $subcategory->category_name}}</a></li>
				@endforeach
			</ul>
		</div>
		@endif

		@if ( !empty($category) && $category !== '' )
		<div class="panel-body panel-body-cats">
			<ul class="nav nav-pills cat-nav">
				@foreach($category->parentcategory->categories as $subcategory)
					{{ Helper::getActiveClass($subcategory->category_uri, 'category', '/', false, '') }}<a href="{{ URL::to('category/'.$subcategory->category_uri) }}"><span>&#8226;</span> {{ $subcategory->category_name}}</a></li>
				@endforeach
				@if ( Auth::check() )
					@if ( ! in_array( $category->category_name, explode(',', Auth::user()->followed_categories)) )
					<a href="{{ URL::to('user/follow/category/'.$category->category_name)}}" class="follow-category ajax-button btn btn-xs btn-default pull-right" data-method="post" data-replace=".follow-category"><span><i class="fa fa-check-circle-o"></i> Follow {{ $category->category_name }}</a>
					@else
					<a href="{{ URL::to('user/unfollow/category/'.$category->category_name) }}" class="unfollow-category ajax-button btn btn-xs btn-warning pull-right" data-method="post" data-replace=".unfollow-category"><span><i class="fa fa-times-circle-o"></i> UnFollow {{ $category->category_name }} </a>
					@endif
				@else
				<a href="#" class="btn btn-xs btn-default pull-right" data-toggle="modal" data-target="#login-form"><i class="fa fa-check-circle-o"></i>
					Follow {{ $category->category_name }}</a>
				@endif
			</ul>
		</div>
		@endif

		<ul class="list-group">

		@foreach($posts as $post)
		
		<li class="list-group-item">
			<div class="post-entry">
				<h2 class="panel-title">
					<?php $post_link = Post::seolink($post->id); ?>
					<a href="{{ $post_link }}">{{ $post->title }}</a>
					<a href="{{ $post_link.'#comments' }}"><span class="badge pull-right">{{ $post->comments()->count() }}</span></a>
				</h2>
				<hr>
				<span><i class="fa fa-user fa-large grey-icon"></i>
				{{ $post->user->username }}</span>
				<span class="grey-span">&#8226;</span>
				<i class="fa fa-clock-o fa-large grey-icon"></i>
				<span>
					{{ $post->created_at->diffForHumans() }}
				</span>
				<span class="grey-span">&#8226;</span>
				<a href="{{ URL::to('category/'. $post->category->category_uri) }}"><span class="label label-default">{{ $post->category->category_name }}</span></a>
			</div>
		</li>

		@endforeach
		
		</ul>
	</div>
<<<<<<< HEAD

=======
>>>>>>> 1765f9683be80a5130867bf5862b032032af32c9
	{{ $posts->appends(array('q' => Input::get('q')))->links()}}

{{-- End Display Posts --}}
@else
	No posts found
@endif

@stop