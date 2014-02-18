<?php

class Basemodel extends Eloquent {
	

	public static function validate($data) {
	$messages = array(
		'suburbad.required' => 'The suburb field is required.'
	);
		return Validator::make($data, static::$rules, $messages);
	}
}