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
				{{ Helper::getActiveClass('topics', '', '', false, '') }}<a href="{{ URL::to('topics') }}">All Topics</a></li>
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

	{{ $posts->appends(array('q' => Input::get('q')))->links()}}

{{-- End Display Posts --}}
@else
	No posts found
@endif

@stop