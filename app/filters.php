<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::action('UsersController@getLogin');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

/*
|--------------------------------------------------------------------------
| All View Composers
|--------------------------------------------------------------------------
|
|Generate category options, topic options and a unique id 
|for new post page.
|
*/

View::composer(array('users.newpost', 'posts.edit'), function($view)
{
	$catmodel = Category::get(array('id','category_name'));
	
	$catsHTML = '<select id="categoryid" name="categoryid" class="form-control selectpicker" data-live-search="true">';
	
	foreach ($catmodel as $key => $value){
    	$catoptions[$value->id] = $value->category_name;
	}

	foreach( Parentcategory::all() as $parentcategory){
		$catsHTML .= '<optgroup label="' . $parentcategory->parent_category_name . '">';
		foreach ($parentcategory->categories as $subcategory) {
			$catsHTML .= '<option value="' . $subcategory->id . '">' . $subcategory->category_name . '</option>';
		}
		$catsHTML .= '</optgroup>';
	}

	$catsHTML .= '</select>';
	
	$view->with('catsHTML', $catsHTML)->with('catoptions', $catoptions);
});

View::composer('users.newpost', function($view){
    $view->with('uniqid', Post::genId());
});

View::composer('home', function($view){
	$parentCats = Parentcategory::all();
	$hotCats = Category::all();
	$hotPostsToday= Post::hotPosts(1);
	$hotPostsWeek = Post::hotPosts(7);
	$hotPostsMonth = Post::hotPosts(31);

    $view->with('hotCats', $hotCats)
    	 ->with('hotPostsToday', $hotPostsToday)
    	 ->with('hotPostsWeek', $hotPostsWeek)
    	 ->with('hotPostsMonth', $hotPostsMonth)
    	 ->with('parentCats', $parentCats);
});

View::composer('posts.allposts', function($view){
	$parentCats = Parentcategory::all();
    $view->with('parentCats', $parentCats);
});