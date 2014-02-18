<?php

class CommentsController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        return View::make('comments.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        return View::make('comments.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validation = Comment::validate(Input::all());

        if ($validation->passes()) {
            
            $comment = new Comment;
            $comment_content = Input::get('content');
            $reply_to_content = '';

            $comment->user_id = Auth::user()->id;

            $replying_post_id = Input::get('postid');

            $comment->post_id = $replying_post_id;

            $reply_comment_ids = Input::get('reply-comment-id');
            $reply_comment_ids = ( !empty($reply_comment_ids) && $reply_comment_ids !== '' && $reply_comment_ids !== null ) ? $reply_comment_ids : 0;
            $comment->reply_comment_id = $reply_comment_ids;

            $comment->content = $comment_content;
            $comment->created_at = new DateTime();
            $comment->updated_at = new DateTime();
            
            $comment->save();

            $current_comment_id = $comment->id;

            if ( $reply_comment_ids !== 0 ) {
            	foreach (explode(',', $reply_comment_ids) as $reply_comment_id) {
            		$parent_comment = Comment::findOrFail($reply_comment_id);
	            	$parent_comment->has_sub_comments = true;
	            	$parent_comment_user = $parent_comment->user->username;
	            	$parent_comment->save();

	            	//save notify information to database
	            	$comment_notify = new Commentnotify;
	            	//save reply to user id from current comment so the user gets reply notify from current saving comment
	        
	            	$comment_notify->user_id = $parent_comment->user->id;
	            	$comment_notify->post_id = $replying_post_id;
	            	$comment_notify->comment_id = $current_comment_id;
	            	$comment_notify->reply_comment_id = $reply_comment_id;
	            	$comment_notify->is_read = false;
	            	$comment->created_at = new DateTime();
	        		$comment->updated_at = new DateTime();
	            	$comment_notify->save();
            	}            	
            }else{
            	//save notify information to database
            	$comment_notify = new Commentnotify;
            	//save reply to user id from current comment so the user gets reply notify from current saving comment
        
            	$comment_notify->user_id = Post::findOrFail($replying_post_id)->user->id;
            	$comment_notify->post_id = $replying_post_id;
            	$comment_notify->comment_id = $current_comment_id;
            	$comment_notify->reply_comment_id = 0;
            	$comment_notify->is_read = false;
            	$comment->created_at = new DateTime();
        		$comment->updated_at = new DateTime();
            	$comment_notify->save();
            }
            
            return Redirect::back()->with('successMessage', 'Comment Added, Go To <a href="#comment-'.$current_comment_id.'" class="alert-link"><u>Your Comment</u></a>')->with('commentMade', $comment_content);
        
        } else {
        	return Redirect::back()->withErrors($validation)->withInput();
        }
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        return View::make('comments.show');
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        return View::make('comments.edit');
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		//
	}

}
