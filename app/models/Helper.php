<?php

class Helper extends Eloquent {
	protected $guarded = array();

	public static $rules = array();

	/**
	 * return active class for current category or parentcategory(topic)
	 * @param  string $identifier      the identifier of category uri
	 * @param  string $type            is it an identifier for category or parentcategory?
	 * @param  boolean $highlightParent is it for highlight parent category when currently viewing category page
	 * @param  model $category        a category model
	 * @return string                  li active class html
	 */
	public static function getActiveClass($identifier, $type, $slash, $highlightParent, $category){
		//the default active class for all
		if( Request::is( $type . $slash . $identifier )){
			return '<li class="active">';
		}
		//display active class for current category's parent category
		//check if parent category id in parent category loop that's equal with current category uri's parent category id, if yes, show current active category's parent category as active.
		elseif( $highlightParent && $type === 'topic' && Parentcategory::where('parent_category_uri', '=', $identifier)->first()->id 
			=== $category->parentcategory->id ){
			return '<li class="active active-topic-for-child-category">';
		}
		else{
			return '<li>';
		}
	}
}
