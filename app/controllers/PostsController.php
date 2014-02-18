<?php

class PostsController extends BaseController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        return View::make('posts.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        return View::make('posts.create');
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
        return View::make('posts.show')->with('post', Post::findOrFail($id))->with('pageTitle', Post::findOrFail($id)->title . ' | ' . SiteTitle);;
	}

	/**
     * Display posts under tag
     * @param  string $tag tag name of a tag
     * @return objects      posts under tag
     */
    public function postsByTag($tag)
    {
        return View::make('posts.allposts')
        			->with('posts', Post::where('tags', 'LIKE', '%'.$tag.'%')->paginate(5))
        			->with('tagname', $tag)
        			->with('pageTitle', "Tag: $tag | " . SiteTitle);
    }

    /**
     * Search posts with keywords
     * @param  String $keywords 
     * @return Post objects array
     */
    public function search()
    {
    	$keywords = Input::get('q');
    	if( trim($keywords) == '' ){
    		return Redirect::back()->with('warningMessage', 'Please Specify A Search Query!');
    	}else{
	    	$keyword_tokens = explode(' ', preg_replace('!\s+!', ' ', $keywords));
		    
		    $matched_posts = Post::where('title', 'LIKE', '%'. $keyword_tokens[0] .'%');

		    if ( count($keyword_tokens) > 1 ){
			    foreach(array_slice($keyword_tokens, 1) as $keyword_token){
			        $matched_posts = $matched_posts->orWhere('title', 'LIKE', '%'. $keyword_token .'%');
			    }
			}

		    $matched_posts = $matched_posts->paginate(8);

		    if ( count($matched_posts) > 0 ){
		        return View::make('posts.allposts')
	        			->with('posts', $matched_posts)->with('keywords', $keywords)
	        			->with('pageTitle', "Search Results For: $keywords | " . SiteTitle);
	        }else{
	        	return Redirect::back()->with('warningMessage', "No results found for: $keywords");
	        }	
	    }
    }

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		$post = Post::findOrFail($id);
		if ( $post->user->id == Auth::user()->id ){
        	return View::make('posts.edit')->with('post', $post)->with('pageTitle', 'Edit Post | ' . SiteTitle);
		}else {
        	return Redirect::route('home')->with('warningMessage', 'Get the heck outta here!');
        }
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		$validation = Post::validate(Input::all());  

        if ($validation->passes()) {

        	$post = Post::find($id);

            $post->category_id = Input::get('categoryid');
            $post->tags = Input::get('tags');
            $post->title = Input::get('title');
            $post->content = Input::get('content');
            $post->updated_at = new DateTime();
            
            $post->save();
            
            return Redirect::route('home')
                ->with('successMessage', "Your Post Has Been Updated!");
        
        } else {
            return Redirect::back()->withErrors($validation)->withInput();;
        }
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
