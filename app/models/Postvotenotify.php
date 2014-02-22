<?php

class Postvotenotify extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public function post(){
        return $this->belongsTo('Post');
    }

    public function user(){
        return $this->belongsTo('User');
    }

    public static function saveVoteNotify( $post, $upVoteDiff, $downVoteDiff ){
    	if( count($post->postvotenotify) > 0 && $post->postvotenotify->is_read == 0 ) {
			$post->postvotenotify->upvoted += $upVoteDiff;
			$post->postvotenotify->downvoted += $downVoteDiff;
			$post->postvotenotify->save();
			if ( $post->postvotenotify->upvoted == 0 && $post->postvotenotify->downvoted == 0) {
				$post->postvotenotify->delete();
			}
		}else{
			if( count($post->postvotenotify) > 0 && $post->postvotenotify->is_read == 1 ) {
				$post->postvotenotify->delete();
			}
			$postvotenotify = new Postvotenotify;
			$postvotenotify->user_id = $post->user->id;
			$postvotenotify->post_id = $post->id;
			$postvotenotify->is_read = 0;
			$postvotenotify->upvoted = $upVoteDiff;
			$postvotenotify->downvoted = $downVoteDiff;
			$postvotenotify->save();
		}
    }
}
