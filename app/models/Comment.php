<?php

class Comment extends Basemodel {
	protected $guarded = array();

	public static $rules = array(
        'content'=>'required|max:500',
    );

    public function user(){
        return $this->belongsTo('User');
    }

    public function post(){
        return $this->belongsTo('Post');
    }
}
