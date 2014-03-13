@extends('home')

@section('content')

@foreach($parentCats as $parentCat)
<div class="panel panel-default">
	<div class="panel-heading">
    	{{ $parentCat->parent_category_name }}
    	<span class="badge pull-right">{{ $parentCat->categories->count() }}</span>
	</div>
	<div class="panel-body">
    	@foreach($parentCat->categories as $category)
			<a href="{{ URL::to('category/'. $category->category_uri) }}"><span class="label label-default">{{ $category->category_name }}
			<i class="fa fa-times grey-icon"></i><span class="grey-span">{{ $category->postsCount() }}</span></span></a>
    	@endforeach
	</div>
</div>
@endforeach

@stop
