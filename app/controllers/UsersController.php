<?php

class UsersController extends BaseController {
	
	/**
	 * User login function
	 * @return Return Response/Redirect 
	 */
	public function login() {
        $email = Input::get('email');
        $user = User::where('email', '=', $email)->first();
        if($user){
            $isActive = User::where('email', '=', $email)->first()->active;
            if($isActive === '0'){
                return Redirect::route('login')
                    ->with('warningMessage', "Your Account $email has not been activated yet.")
                    ->withInput();
            }

            $user = array(
                'email' => $email,
                'password' => Input::get('password'),
                'active' => $isActive
            );

            if (Auth::attempt($user)) {
                return Redirect::back()->with('successMessage', 'Welcome back, '. Auth::user()->username . '. ')->with('countNotifications', Auth::user()->commentnotifies()->where('is_read', '=', 0)->count() + Auth::user()->postvotenotifies()->where('is_read', '=', 0)->count());
            } else {
                return Redirect::route('login')
                    ->with('warningMessage', 'Your username/password combination was incorrect.')
                    ->withInput();
            }
        }else{
            return Redirect::route('login')
                    ->with('warningMessage', 'We could not find your email.')
                    ->withInput();
        }
    }

    /**
     * User add new post
     * @return Response 
     */
    public function newPost(){

        $validation = Post::validate(Input::all());                                       
        $postid = Input::get('postid');

        if ($validation->passes()) {
            
            $post = new Post;
            $post->id = $postid;
            $post->user_id = Auth::user()->id;
            $post->category_id = Input::get('categoryid');
            $post->parentcategory_id = Category::find(Input::get('categoryid'))->parentcategory_id;
            $post->tags = Input::get('tags');
            $post->title = Input::get('title');
            // $htmlTagFilter = array("~<p[^>]*>\s?</p>~", "~<div[^>]*>\s?</div>~", "~<li[^>]*>\s?</li>~", "~<ul[^>]*>\s?</ul>~", "~<ol[^>]*>\s?</ol>~", "~<a[^>]*>\s?</a>~", "~<font[^>]*>~", "~<\/font>~", "~style\=\"[^\"]*\"~", "~<span[^>]*>\s?</span>~");
            // $filteredContent  = preg_replace($htmlTagFilter, '', Input::get('content'));
            //$post->content = strip_tags(Input::get('content'), '<h1><h2><h3><h4><h5><h6><span><u><ul><li><ol><p><a><blockquote><pre>');
            $post->content = Input::get('content');

            $post->created_at = new DateTime();
            $post->updated_at = new DateTime();
            
            $post->save();
            
            return Redirect::route('home')
                ->with('successMessage', "Your Ad Has Been Posted!");
        
        } else {
            return Redirect::route('new-post')->withErrors($validation)->withInput();;
        }
    }

    /**
     * Display messages for logged in user, eg. comment notification, post commented notification
     * @return Response
     */
    public function notifications() {
        if( Auth::check()) {
            $notifications_for_user = Auth::user()->commentnotifies()->where('is_read', '=', '0')->orderBy('created_at', 'desc')->get();
            $postvotenotifications = Auth::user()->postvotenotifies()->where('is_read', '=', '0')->orderBy('created_at', 'desc')->get();
            return View::make('users/notifications')->with('notifications', $notifications_for_user)
                                                    ->with('postvotenotifications', $postvotenotifications)
                                                    ->with('pageTitle', 'Notifications | ' . SiteTitle);;
        }else{
            return Redirect::route('home');
        }
    }

    /**
     * Display messages for logged in user, eg. comment notification, post commented notification
     * @return Response
     */
    public function singleNotification($messageId) {
        $singleNotification = Commentnotify::find($messageId);
        if( Auth::check() && Auth::user()->id == $singleNotification->user->id ) {
            $singleNotification->is_read = true;
            $singleNotification->save();
            $data = array(
                "html" => '<div class="read-notification"><i class="fa fa-check fa-5x pull-right white-icon"></i></div>'
            );
            return Response::json($data);
        }else{
            return Redirect::route('home');
        }
    }

    /**
     * Mark all notifications as read
     * @return Response 
     */
    public function markRead(){
        if ( Auth::check() ){
            DB::table('commentnotifies')
            ->where('user_id', '=', Auth::user()->id)
            ->where('is_read', 0)
            ->update(array('is_read' => 1));
            return Redirect::route('messages');
        }else{
            return Redirect::route('home');
        }
    }

    /**
     * Mark all vote notifications as read
     * @return Response 
     */
    public function votesRead(){
        if ( Auth::check() ){
            DB::table('postvotenotifies')
            ->where('is_read', 0)
            ->update(array('is_read' => 1));
            return Redirect::route('messages');
        }else{
            return Redirect::route('home');
        }
    }

    /**
     * User logs out function
     * @return Response Redirect 
     */
    public function logout() {
        if (Auth::check()) {
            Auth::logout();
            return Redirect::route('home')->with('successMessage', 'Your are now logged out!');
        } else {
            return Redirect::route('home');
        }
    }

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
        return View::make('users.index');
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
        //
	}

	/**
     * Activate user registration.
     *
     * @return Response
     */
    public function activate()
    {
        $activation_key = Input::get('uid');
        $user = User::where('activation_key', '=', $activation_key)->first();

        if($user){
            $user->active = true;
            $user->activation_key = '';
            $user->save();
            Auth::login($user);
            return Redirect::route('home')
                ->with('successMessage', 'Your Account Has been Activated, You are now logged in.');
        }else{
            return Redirect::route('home');
        }
    }

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		$validation = User::validate(Input::all());

        if ($validation->passes()) {
            
            $email = Input::get('email');
            $username = Input::get('username');
            $user = new User;
            if(User::where('email', '=', $email)->count()>0)
                return Redirect::route('register')
                ->with('warningMessage', 'This email has been registered! Please choose another one');
            else
                $user->email = $email;
            $user->password = Hash::make(Input::get('password'));
            $activation_key = sha1(mt_rand(10000,99999).time().$email);
            $user->activation_key = $activation_key;
            $user->active = false;
            $user->username = $username;
            $user->created_at = new DateTime();
            $user->updated_at = new DateTime();
            $user->save();

            //send registration confirmation email to user
            $data['email'] = $email;
            $data['activate_link'] = URL::to('activate?uid=' . $activation_key); 
            $activation = array();
            $activation['email'] = $email;
            $activation['subject'] = 'Confirm Account Registration on Laravel Community';

            Mail::send('emails.auth.activate', $data, function($message) use ($activation)
            {
                $message->to($activation['email'])->subject($activation['subject']);
            });

            return Redirect::route('home')
                ->with('infoMessage', 'Thanks for registering! Please Activate Your Account via Link We Sent to Your Email');
            }else {
                return Redirect::route('register')->withErrors($validation)->withInput();
            }
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($username)
	{
        $user = User::where('username', '=', $username)->first();
        $userPosts = $user->posts()->orderBy('created_at', 'desc')->take(3)->get();
        $userComments = $user->comments()->orderBy('created_at', 'desc')->take(3)->get();

        return View::make('users.show')->with('username', $username)
                    ->with('userPosts', $userPosts)
                    ->with('userComments', $userComments)
                    ->with('pageTitle', "Member Profile: $username | " . SiteTitle);;
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
        return View::make('users.edit');
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
