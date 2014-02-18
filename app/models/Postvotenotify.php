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
}
