<?php
use Carbon\Carbon;
define("SiteTitle", "Laravel Community");
/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the Closure to execute when that URI is requested.
|
*/

Route::get('/', array('as'=>'home', function(){
	return View::make('posts.allposts')->with('posts', Post::orderBy('created_at', 'desc')->paginate(8))->with('pageTitle', SiteTitle);
}));

Route::get('topics', function(){
	return View::make('posts.allposts')->with('posts', Post::orderBy('created_at', 'desc')->paginate(8))->with('pageTitle', 'Topics | ' . SiteTitle);
});

Route::get('followed-topics', array('before'=>'auth', function(){
	if ( !empty(Auth::user()->followed_categories) && Auth::user()->followed_categories !== ''){
		$followed_categories = Category::whereIn('category_name', explode(',', Auth::user()->followed_categories))->lists('id');
		$posts = Post::whereIn('category_id', $followed_categories)->orderBy('created_at', 'desc')->paginate(8);
		return View::make('posts.allposts')->with('posts', $posts)->with('pageTitle', 'My Followed Topics | ' . SiteTitle);
	}else{
		return Redirect::back()->with('warningMessage', "You haven't followed any topics yet. Explore and follow your favourites." );
	}
}));

Route::get('search', array('as'=>'search', 'uses'=>'PostsController@search'));

Route::get('my-posts', array('as'=>'myposts', 'before'=>'auth', function(){
	return View::make('users.myposts')->with('posts', Auth::user()->posts()->orderBy('created_at', 'desc')->paginate(5))
				->with('pageTitle', 'My Posts | ' . SiteTitle);
}));

Route::get('my-comments', array('as'=>'mycomments', 'before'=>'auth', function(){
	return View::make('users.mycomments')->with('comments', Auth::user()->comments()->orderBy('created_at', 'desc')->paginate(5))
				->with('pageTitle', 'My Comments | ' . SiteTitle);
}));

Route::get('notifications/mark-all-as-read', array('before'=>'auth', 'uses'=>'UsersController@markRead'));
Route::get('notifications/votes-read', array('before'=>'auth', 'uses'=>'UsersController@votesRead'));

Route::get('post/{id}/{postTitle}', 'PostsController@show');
Route::post('post/{id}/{postTitle}', 'CommentsController@store');
Route::post('notifications', 'CommentsController@store');

Route::get('tag/{tag}', 'PostsController@postsByTag');

Route::get('category/{category}', function($category){
	$matched_category = Category::where('category_uri', '=', $category)->first();
	if( count($matched_category) > 0 ){
		$posts = $matched_category->posts()->paginate(8);
		return View::make('posts.allposts')
        		->with('posts', $posts)->with('category', $matched_category)
        		->with('pageTitle', $matched_category->category_name. ' | ' . SiteTitle);
    }else{
    	return Redirect::route('home')->with('warningMessage', "Could not find category: $category" );
    }
});

Route::get('topic/{parentcategory}', function($parentcategory){
	$matched_parent_category = Parentcategory::where('parent_category_uri', '=', $parentcategory)->first();
	if( count($matched_parent_category) > 0 ){
		$posts = $matched_parent_category->posts()->paginate(8);
		return View::make('posts.allposts')
        		->with('posts', $posts)->with('parentcategory', $matched_parent_category)
        		->with('pageTitle', $matched_parent_category->parent_category_name. ' | ' . SiteTitle);
    }else{
    	return Redirect::route('home')->with('warningMessage', "Cound not find topic: $parentcategory" );
    }
});

Route::get('post/{id}/votes/up', function($id)
{	
	
	$data = array(
		"html" => '<span class="vote-btn votecount grey-span"><i class="fa fa-thumbs-up"></i> ' . Post::find($id)->upvotes . '</span>'
	);
	
	return Response::json($data);
});

Route::post('post/{id}/votes/up', function($id)
{	
	if( Auth::check() ){
		$post = Post::find($id);
		$voters_id_queue = $post->voters_id;
		$voters_id_array = explode(',', $voters_id_queue);
		$current_voter_id = Auth::user()->id;
		if( ! in_array($current_voter_id, $voters_id_array)){
			$voters_id_queue .= $voters_id_queue == '' ? $current_voter_id : ',' . $current_voter_id;
			$post->upvotes += 1; 
			$post->voters_id = $voters_id_queue;
			$post->save();

			if( count($post->postvotenotify) > 0 && $post->postvotenotify->is_read == 0 ) {
				$post->postvotenotify->upvoted += 1;
				$post->postvotenotify->save();
			}else{
				if( count($post->postvotenotify) > 0 && $post->postvotenotify->is_read == 1 ) {
					$post->postvotenotify->delete();
				}
				$postvotenotify = new Postvotenotify;
				$postvotenotify->user_id = $post->user->id;
				$postvotenotify->post_id = $id;
				$postvotenotify->is_read = 0;
				$postvotenotify->upvoted += 1;
				$postvotenotify->save();
			}
		}
	}
	$data = array(
		"html" => '<a href="#" class="btn btn-xs btn-default" disabled="disabled"><span class="vote-btn votecount grey-span"><i class="fa fa-thumbs-up"></i> ' . Post::find($id)->upvotes . '</span></a>'
	);
	
	return Response::json($data);
});

Route::post('user/follow/category/{category}', function($category)
{	
	
	if ( Auth::check() ){
		$current_user = Auth::user();
		$followed_category_queue = $current_user->followed_categories;
		$followed_category_queue .= $current_user->followed_categories == '' ? $category : ',' . $category;
		$current_user->followed_categories = $followed_category_queue;
		$current_user->save();
	}

	$data = array(
		"html" => '<a href="'. URL::to('user/unfollow/category/'. $category) . '" class="unfollow-category ajax-button btn btn-xs btn-warning pull-right" data-method="post" data-replace=".unfollow-category"><span><i class="fa fa-times-circle-o"></i> UnFollow ' . $category .'</a>'
	);
	
	return Response::json($data);
});

Route::post('user/unfollow/category/{category}', function($category)
{	
	
	if ( Auth::check() ){
		$current_user = Auth::user();
		$followed_categories_array = explode(',', $current_user->followed_categories);
		$key = array_search($category,$followed_categories_array);
		if($key!==false){
		    unset($followed_categories_array[$key]);
		}
		$current_user->followed_categories = implode(',', $followed_categories_array);
		$current_user->save();
	}

	$data = array(
		"html" => '<a href="'. URL::to('user/follow/category/'.$category) . '" class="follow-category ajax-button btn btn-xs btn-default pull-right" data-method="post" data-replace=".follow-category"><span><i class="fa fa-check-circle-o"></i> Follow ' .$category .'</a>'
	);
	
	return Response::json($data);
});


View::composer(array('users.newpost', 'posts.edit'), function($view)
{
	$catmodel = Category::get(array('id','category_name'));
	
	$catsHTML = '<select id="categoryid" name="categoryid" class="form-control selectpicker" data-live-search="true">';
	
	foreach ($catmodel as $key => $value)
	{
    	// Create the options array
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

Route::get('activate', array('as'=>'activate', 'uses'=>'UsersController@activate'));

Route::get('forgot-password', function()
{
    return View::make('password.pwremind')->with('pageTitle', 'Forgot Password | ' . SiteTitle);
});

Route::post('forgot-password', 'RemindersController@postRemind');

Route::get('register', array('as'=>'register', function()
{
     return View::make('users.create')
            ->with('pageTitle', 'Register | ' . SiteTitle);
}));

Route::get('password/reset/{token}', 'RemindersController@getReset');

Route::post('password/reset/{token}', 'RemindersController@postReset');

Route::get('logout', array('as'=>'logout', 'uses'=>'UsersController@logout'));

Route::get('login', array('as'=>'login', function()
{	
	if ( !Auth::check() ){
    	return View::make('users.login')
			->with('pageTitle', 'Login | ' . SiteTitle);
	}else{
		return Redirect::route('home');
	}
}));

Route::get('new-post', array('as'=>'new-post', 'before' => 'auth', function()
{
    return View::make('users.newpost')
            ->with('pageTitle', 'New Post | ' . SiteTitle);
}));

Route::get('edit/post/{id}', array('as'=>'edit', 'before'=>'auth', 'uses'=>'PostsController@edit'));
Route::put('edit/post/{id}', array('before' => 'csrf', 'uses' => 'PostsController@update'));

Route::get('user/{username}', 'UsersController@show');

Route::get('notifications', array('as'=>'messages', 'before' => 'auth', 'uses'=>'UsersController@notifications'));
Route::post('notifications/{messageId}', array('as'=>'singlemessage', 'before' => 'auth', 'uses'=>'UsersController@singleNotification'));
Route::get('notifications/{messageId}', function()
{	
	
	$data = array(
		"html" => '<blockquote class="single-notification"></blockquote>'
	);
	
	return Response::json($data);
});

Route::post('notifications/reply-comment/{post_id}/{comment_id}', array('before'=>'auth', 'uses'=>'CommentsController@store'));

Route::get('notifications/reply-comment/{post_id}/{comment_id}', function($post_id, $comment_id)
{	
	
	$data = array(
		"html" => Auth::user()->commentFormHtml($post_id, $comment_id)
	);
	
	return Response::json($data);
});

Route::post('login', array('before'=>'csrf', 'uses'=>'UsersController@login'));
Route::post('register', array('before'=>'csrf', 'uses'=>'UsersController@store'));
Route::post('new-post', array('before'=>'csrf', 'uses'=>'UsersController@newPost'));

Route::resource('posts', 'PostsController');

Route::resource('users', 'UsersController');

Route::resource('categories', 'CategoriesController');

Route::resource('comments', 'CommentsController');

Route::resource('commentnotifies', 'CommentnotifiesController');

Route::resource('postvotenotifies', 'PostvotenotifiesController');

Route::resource('parentcategories', 'ParentcategoriesController');