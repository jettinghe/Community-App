<?php

class Parentcategory extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

	public function categories(){
        return $this->hasMany('Category');
    }

    public function posts(){
        return $this->hasMany('Post');
    }
}
