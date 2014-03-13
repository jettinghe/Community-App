<?php
define("SiteTitle", "Laravel Community");

/*
|--------------------------------------------------------------------------
| The root content and topics content route.
|--------------------------------------------------------------------------
| In default, return all posts at index level and in topics.
|
*/
Route::get('/', array('as'=>'home', function(){
	return View::make('posts.allposts')->with('posts', Post::orderBy('created_at', 'desc')->paginate(8))->with('pageTitle', SiteTitle);
}));

Route::get('topics', function(){
	return View::make('posts.allposts')->with('posts', Post::orderBy('created_at', 'desc')->paginate(8))->with('pageTitle', 'Topics | ' . SiteTitle);
});


/*
|--------------------------------------------------------------------------
| All controller level routes are listed below
|--------------------------------------------------------------------------
*/

//Controller route for all user actions
Route::controller('user', 'UsersController');
//Controller route for all post vote actions
Route::controller('post-vote', 'VoteController');
//Controller route for all password remind/reset actions
Route::controller('password', 'RemindersController');


/*
|--------------------------------------------------------------------------
| All posts related routes are listed below.
|--------------------------------------------------------------------------
| Including search, single post show/edit, posts by tags/categories/topics.
|
*/

//Display search form
Route::get('search', array('as'=>'search', 'uses'=>'PostsController@search'));
//Display posts by tag
Route::get('tag/{tag}', 'PostsController@postsByTag');
//Dislay posts by category
Route::get('category/{category}', 'PostsController@postsByCategory');
//Display posts by topic
Route::get('topic/{parentcategory}', 'PostsController@postsByTopic');
//Display single post
Route::get('post/{id}/{postTitle}', 'PostsController@show');
Route::get('post/{id}', 'PostsController@show');
//Post comment on a single post
Route::post('post/{id}/{postTitle}', 'CommentsController@store');
Route::post('post/{id}', 'CommentsController@store');
//Edit single post view
Route::get('edit/post/{id}', array('as'=>'edit', 'before'=>'auth', 'uses'=>'PostsController@edit'));
//Update single post
Route::put('edit/post/{id}', array('before' => 'csrf', 'uses' => 'PostsController@update'));
//Full topics page
Route::get('explore', 'PostsController@explore');

/*
|--------------------------------------------------------------------------
| All notification related routes are listed below
|--------------------------------------------------------------------------
*/

//Display all notifications for current logged in user
Route::get('notifications', array('as'=>'messages', 'before' => 'auth', 'uses'=>'NotificationController@getNotifications'));
//Mark read of comments notifications
Route::get('notifications/mark-all-as-read', array('before'=>'auth', 'uses'=>'NotificationController@getCommentsRead'));
//Mark read of votes notifications
Route::get('notifications/votes-read', array('before'=>'auth', 'uses'=>'NotificationController@getVotesRead'));
//Mark read of single comment notification
Route::post('notifications/{messageId}', array('as'=>'singlemessage', 'before' => 'auth', 'uses'=>'NotificationController@postSingleNotification'));
//Return Ajax view (Big tick sign) of read single comment notification
Route::get('notifications/{messageId}', 'NotificationController@postSingleNotification');
//Save comments when user reply comments on notification page
Route::post('notifications/reply-comment/{post_id}/{comment_id}', array('before'=>'auth', 'uses'=>'CommentsController@store'));
//Get comment form for each notification
Route::get('notifications/reply-comment/{post_id}/{comment_id}', 'CommentsController@replyCommentInNotification');
