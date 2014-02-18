@extends('master')
@section('main-content')

@if(Session::has('successMessage'))
	<div class="alert alert-success alert-dismissable">
	  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	  <strong>{{ Session::get('successMessage') }}</strong>
	@if(Session::has('countNotifications'))
		  <strong>You have {{ Session::get('countNotifications') }} new <a href="{{ URL::to('notifications') }}" class="alert-link"><u>notifications</u></a></strong>.
	@endif
	</div>
@endif
@if(Session::has('warningMessage'))
	<div class="alert alert-warning alert-dismissable">
	  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	  <strong>{{ Session::get('warningMessage') }}</strong>
	</div>
@endif
@if(Session::has('infoMessage'))
	<div class="alert alert-info alert-dismissable">
	  <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
	  <strong>{{ Session::get('infoMessage') }}</strong>
	</div>
@endif


<!-- <div class="panel panel-default"> -->

	@yield('content')

<!-- </div> -->
@stop

@section('sidebar')
	
	<h4>Topics</h4>
	@foreach($parentCats as $parentCat)
		<a href="{{ URL::to("topic/$parentCat->parent_category_uri") }}" class="label-tags"><span class="label label-default label-tag">{{ $parentCat->parent_category_name}}</span></a>
	@endforeach

	<hr>
	
	<h4>Categories</h4>
	@foreach($hotCats as $hotCat)
		<a href="{{ URL::to("category/$hotCat->category_uri") }}" class="label-tags"><span class="label label-default label-tag">{{ $hotCat->category_name}}</span></a>
	@endforeach

	<hr>

	<h4>Featured Posts Today</h4>
	<div class="list-group">
	@foreach($hotPostsToday as $hotPostToday)
		<a href="{{ Post::seolink($hotPostToday->id) }}" class="list-group-item">{{ $hotPostToday->title }}<span class="badge">{{ $hotPostToday->response_count }}</span></a>
	@endforeach
	</div>
	<hr>

	<h4>Featured Posts This Week</h4>
	<div class="list-group">
	@foreach($hotPostsWeek as $hotPostWeek)
		<a href="{{ Post::seolink($hotPostWeek->id) }}" class="list-group-item">{{ $hotPostWeek->title }}<span class="badge">{{ $hotPostWeek->response_count }}</span></a>
	@endforeach
	</div>
	<hr>
	
	<h4>Featured Posts This Month</h4>
	<div class="list-group">
	@foreach($hotPostsMonth as $hotPostMonth)
		<a href="{{ Post::seolink($hotPostMonth->id) }}" class="list-group-item">{{ $hotPostMonth->title }}<span class="badge">{{ $hotPostMonth->response_count }}</span></a>
	@endforeach
	</div>
@stop
