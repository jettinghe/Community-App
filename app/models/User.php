<?php

use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Basemodel implements UserInterface, RemindableInterface {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password');

	/**
	 * User rules
	 */
	public static $rules = array(
		'email'=>'required|unique:users|email|min:4',
		'username'=>'required|unique:users|alpha_num|between:2,20',
		'password'=>'required|alpha_num|min:6|confirmed',
		'password_confirmation'=>'required|alpha_num|min:6'
	);

	/**
	 * Get the unique identifier for the user.
	 *
	 * @return mixed
	 */
	public function getAuthIdentifier()
	{
		return $this->getKey();
	}

	/**
	 * Get the password for the user.
	 *
	 * @return string
	 */
	public function getAuthPassword()
	{
		return $this->password;
	}

	/**
	 * Get the e-mail address where password reminders are sent.
	 *
	 * @return string
	 */
	public function getReminderEmail()
	{
		return $this->email;
	}

	public function posts(){
        return $this->hasMany('Post');
    }

    public function comments(){
        return $this->hasMany('Comment');
    }

	public function commentnotifies(){
        return $this->hasMany('Commentnotify');
    }

    public function postvotenotifies(){
        return $this->hasMany('Postvotenotify');
    }

    public static function vote($postId, $type){
    	$post = Post::find($postId);
    	$beforeDownVotes = $post->downvotes;
		$upvoters_id_queue = $post->upvoters_id;
		$upvoters_id_array = explode(',', $upvoters_id_queue);
		$downvoters_id_queue = $post->downvoters_id;
		$downvoters_id_array = explode(',', $downvoters_id_queue);
		$current_voter_id = Auth::user()->id;
		$isUserUpVoted = in_array($current_voter_id, $upvoters_id_array);
		$isUserDownVoted = in_array($current_voter_id, $downvoters_id_array);

		if ( $type === 'up' ){
			if ( ! $isUserUpVoted && ! $isUserDownVoted) {
				$upvoters_id_queue .= $upvoters_id_queue == '' ? $current_voter_id : ',' . $current_voter_id;
				$post->upvotes += 1;
				$post->upvoters_id = $upvoters_id_queue;
				$post->save();
				Postvotenotify::saveVoteNotify($post, 1, 0);
			}elseif ( $isUserUpVoted ) {
				$key = array_search($current_voter_id, $upvoters_id_array);
				if( $key !== false ){
			    	unset($upvoters_id_array[$key]);
					$post->upvotes -= 1;
					$post->upvoters_id = implode(',', $upvoters_id_array);
					$post->save();
					Postvotenotify::saveVoteNotify($post, -1, 0);
				}
			}elseif ( $isUserDownVoted ){
				$key = array_search($current_voter_id, $downvoters_id_array);
				if( $key !== false ){
			    	unset($downvoters_id_array[$key]);
					$post->downvoters_id = implode(',', $downvoters_id_array);
					$post->downvotes -= 1;
					$upvoters_id_queue .= $upvoters_id_queue == '' ? $current_voter_id : ',' . $current_voter_id;
					$post->upvotes += 1;
					$post->upvoters_id = $upvoters_id_queue;
					$post->save();
					Postvotenotify::saveVoteNotify($post, 1, -1);
				}
			}
		}elseif ( $type === 'down' ){
			if ( ! $isUserUpVoted && ! $isUserDownVoted) {
				$downvoters_id_queue .= $downvoters_id_queue == '' ? $current_voter_id : ',' . $current_voter_id;
				$post->downvotes += 1;
				$post->downvoters_id = $downvoters_id_queue;
				$post->save();
				Postvotenotify::saveVoteNotify($post, 0, 1);
			}elseif ( $isUserDownVoted ) {
				$key = array_search($current_voter_id, $downvoters_id_array);
				if( $key !== false ){
			    	unset($downvoters_id_array[$key]);
					$post->downvotes -= 1;
					$post->downvoters_id = implode(',', $downvoters_id_array);
					$post->save();
					Postvotenotify::saveVoteNotify($post, 0, -1);
				}
			}elseif ( $isUserUpVoted ){
				$key = array_search($current_voter_id, $upvoters_id_array);
				if( $key !== false ){
			    	unset($upvoters_id_array[$key]);
			    	$post->upvoters_id = implode(',', $upvoters_id_array);
					$post->upvotes -= 1;
					$downvoters_id_queue .= $downvoters_id_queue == '' ? $current_voter_id : ',' . $current_voter_id;
					$post->downvotes += 1;
					$post->downvoters_id = $downvoters_id_queue;
					$post->save();
					Postvotenotify::saveVoteNotify($post, -1, 1);
				}
			}
		}
    }

	public static function loginFormHtml(){
		return '<div class="modal fade" id="login-form" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">'.
		'<div class="modal-dialog">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
		        <h4 class="modal-title" id="myModalLabel">Login</h4>
		      </div>
		      <div class="modal-body">'.
				Form::open(array('url' => 'login', 'method' => 'post', 'role' => 'form')).
				'<fieldset>'.
				Form::token().
				'<div class="form-group">' . 
					Form::label('email', 'Email') .
					Form::email('email', Input::old('email'), array('required', 'class' => 'form-control')).
				'</div>
				<div class="form-group">' . 
					Form::label('password', 'Password'). 
					Form::password('password', array('required', 'class' => 'form-control')).
				'</div>'.
				Form::submit('Login', array('class' => 'btn btn-default')).
				'<p>' . HTML::link('forgot-password', 'Forgot Your Password?') . '</p>' . 
				Form::close() .
			  '</div></div></div></div>';
	}

	public static function commentFormHtml($post_id, $reply_comment_id) {
		return Form::open(array('id' => 'new-comment', 'method' => 'post', 'role' => 'form')) . 
			'<fieldset>' .
				Form::token() .
				'<input type="hidden" name="reply-comment-id" id="reply-comment-id" value="' . $reply_comment_id . '">' .
				'<input type="hidden" name ="postid" id="postid" value="' . $post_id . '"/>
				<div class="form-group">' .
				Form::textarea('content', Input::old('content'), array('required', 'class' => 'form-control', 'placeholder'=>'@'. Comment::find($reply_comment_id)->user->username)) .
				'</div>' .
				Form::submit('Submit Reply', array('class' => 'btn btn-default')) .
			'</fieldset>' .
			Form::close();
	}

	/**
	 * Check if given comment has been replied in a notification loop
	 * @param  Integer  $comment_id 
	 * @return Boolean  
	 */
	public function isReplied($comment_id){
		foreach( Auth::user()->comments()->where('reply_comment_id', 'Like', '%'. $comment_id . '%')->get() as $matched_replies ){
			if( in_array( $comment_id, explode(',', $matched_replies->reply_comment_id))){
				return true;
			}
		}
		return false;
	}

	/**
	 * Count how many unread notifications for logged in user
	 * @return Integer Number of unread notifications
	 */
	public static function countNotifications(){
		$countNotifications = Auth::user()->commentnotifies()->where('is_read', '=', '0')->count() + Auth::user()->postvotenotifies()->where('is_read', '=', '0')->count();
		return $countNotifications > 0 ? $countNotifications : '';
	}

}