<?php

class UsersController extends BaseController {

    public function __construct() {
        $this->beforeFilter('csrf', array('on'=>'post', 'only'=>array('postLogin', 'postRegister')));
        $this->beforeFilter('auth', array('on'=>'get', 'except'=>array('getLogin', 'getActivate', 'getRegister', 'getProfile')));
    }


    public function getLogin(){
        if ( !Auth::check() ){
            return View::make('users.login')
                ->with('pageTitle', 'Login | ' . SiteTitle);
        }else{
            return Redirect::route('home');
        }
    }
    /**
     * User login function
     * @return Return Response/Redirect 
     */
    public function postLogin() {
        $email = Input::get('email');
        $user = User::where('email', '=', $email)->first();
        if($user){
            $isActive = User::where('email', '=', $email)->first()->active;
            if($isActive === '0'){
                return Redirect::action('UsersController@getLogin')
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
                return Redirect::action('UsersController@getLogin')
                    ->with('warningMessage', 'Your username/password combination was incorrect.')
                    ->withInput();
            }
        }else{
            return Redirect::action('UsersController@getLogin')
                    ->with('warningMessage', 'We could not find your email.')
                    ->withInput();
        }
    }

    public function getNewPost(){
        return View::make('users.newpost')
            ->with('pageTitle', 'New Post | ' . SiteTitle);
    }

    /**
     * User add new post
     * @return Response 
     */
    public function postNewPost(){

        $validation = Post::validate(Input::all());                                       
        $postid = Input::get('postid');
        $isEmpty = trim(strip_tags(Input::get('content'))) == '';

        if ($validation->passes() && !$isEmpty) {
            
            $post = new Post;
            $post->id = $postid;
            $post->user_id = Auth::user()->id;
            $post->category_id = Input::get('categoryid');
            $post->parentcategory_id = Category::find(Input::get('categoryid'))->parentcategory_id;
            $post->tags = Input::get('tags');
            $post->title = Input::get('title');
            $post->content = Input::get('content');

            $post->created_at = new DateTime();
            $post->updated_at = new DateTime();
            
            $post->save();
            
            return Redirect::route('home')
                ->with('successMessage', "Your Ad Has Been Posted!");
        
        } else {
            return $isEmpty ? Redirect::action('UsersController@getNewPost')->withErrors($validation)->with('warningMessage', '<ul><li>Content is empty.</li></ul>')->withInput()
                             : Redirect::action('UsersController@getNewPost')->withErrors($validation)->withInput();
        }
    }

    /**
     * Display messages for logged in user, eg. comment notification, post commented notification
     * @return Response
     */
    public function getNotifications() {
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

    public function getSingleNotification($messageId){
        $data = array(
            "html" => '<blockquote class="single-notification"></blockquote>'
        );
        
        return Response::json($data);
    }

    /**
     * Display messages for logged in user, eg. comment notification, post commented notification
     * @return Response
     */
    public function postSingleNotification($messageId) {
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
    public function getCommentsRead(){
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
    public function getVotesRead(){
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
    public function getLogout() {
        if (Auth::check()) {
            Auth::logout();
            return Redirect::route('home')->with('successMessage', 'Your are now logged out!');
        } else {
            return Redirect::route('home');
        }
    }

    /**
     * Activate user registration.
     *
     * @return Response
     */
    public function getActivate()
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

    public function getRegister(){
        return View::make('users.create')
            ->with('pageTitle', 'Register | ' . SiteTitle);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function postRegister()
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
            $data['activate_link'] = URL::to('user/activate?uid=' . $activation_key); 
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
                return Redirect::action('UsersController@getRegister')->withErrors($validation)->withInput();
            }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function getProfile($username){
        $user = User::where('username', '=', $username)->first();
        $userPosts = $user->posts()->orderBy('created_at', 'desc')->take(3)->get();
        $userComments = $user->comments()->orderBy('created_at', 'desc')->take(3)->get();

        return View::make('users.show')->with('username', $username)
                    ->with('userPosts', $userPosts)
                    ->with('userComments', $userComments)
                    ->with('pageTitle', "Member Profile: $username | " . SiteTitle);;
    }

    /**
     * Return logged in user's own posted posts.
     * @return collection user's posts
     */
    public function getMyPosts(){
        return View::make('users.myposts')->with('posts', Auth::user()->posts()->orderBy('created_at', 'desc')->paginate(5))
                ->with('pageTitle', 'My Posts | ' . SiteTitle)
                ->with('userRelatePostsTitle', 'My Own Posts');
    }

    /**
     * return user's favourite posts
     * @return collection user's favourite posts
     */
    public function getFavouritePosts(){
        $favourite_posts = Post::whereIn('id', explode(',', Auth::user()->favourite_posts))->paginate(5);
        if (count($favourite_posts) > 0){
            return View::make('users.myposts')->with('posts', Post::whereIn('id', explode(',', Auth::user()->favourite_posts))->paginate(5))
                    ->with('pageTitle', 'Favourite Posts | ' . SiteTitle)
                    ->with('userRelatePostsTitle', 'Favourite Posts');
        }else{
            return Redirect::back()->with('warningMessage', 'You have no favourite posts yet.');
        }
    }

    public function getFollowedTopics(){
        if ( !empty(Auth::user()->followed_categories) && Auth::user()->followed_categories !== ''){
            $followed_categories = Category::whereIn('category_name', explode(',', Auth::user()->followed_categories))->lists('id');
            $posts = Post::whereIn('category_id', $followed_categories)->orderBy('created_at', 'desc')->paginate(8);
            return View::make('posts.allposts')->with('posts', $posts)->with('pageTitle', 'My Followed Topics | ' . SiteTitle);
        }else{
            return Redirect::back()->with('warningMessage', "You haven't followed any topics yet. Explore and follow your favourites." );
        }
    }

    /**
     * return user's comments on others' posts
     * @return collection user's own comments
     */
    public function getMyComments(){
        return View::make('users.mycomments')->with('comments', Auth::user()->comments()->orderBy('created_at', 'desc')->paginate(5))
                ->with('pageTitle', 'My Comments | ' . SiteTitle);
    }

    public function postFollowCategory($category){
        if ( Auth::check() ){
            $current_user = Auth::user();
            $followed_category_queue = $current_user->followed_categories;
            $followed_category_queue .= $current_user->followed_categories == '' ? $category : ',' . $category;
            $current_user->followed_categories = $followed_category_queue;
            $current_user->save();
        }

        $data = array(
            "html" => '<a href="'. URL::to('user/unfollow-category/'. $category) . '" class="unfollow-category ajax-button btn btn-xs btn-warning pull-right" data-method="post" data-replace=".unfollow-category"><span><i class="fa fa-times-circle-o"></i> UnFollow ' . $category .'</a>'
        );
        
        return Response::json($data);
    }

    public function postUnfollowCategory($category){
        if ( Auth::check() ){
            $current_user = Auth::user();
            $followed_categories_array = explode(',', $current_user->followed_categories);
            $key = array_search($category,$followed_categories_array);
            if($key!==false){
                unset($followed_categories_array[$key]);
            }
            $current_user->followed_categories = implode(',', $followed_categories_array);
            $current_user->save();
        }

        $data = array(
            "html" => '<a href="'. URL::to('user/follow-category/'.$category) . '" class="follow-category ajax-button btn btn-xs btn-default pull-right" data-method="post" data-replace=".follow-category"><span><i class="fa fa-check-circle-o"></i> Follow ' .$category .'</a>'
        );
        
        return Response::json($data);
    }

    public function postFavouritePost($id){
        if ( Auth::check() ){
            $current_user = Auth::user();
            $favourite_post_queue = $current_user->favourite_posts;
            $favourite_post_queue .= $current_user->favourite_posts == '' ? $id : ',' . $id;
            $current_user->favourite_posts = $favourite_post_queue;
            $current_user->save();
        }

        $data = array(
            "html" => '<a href="'. URL::to('user/unfavourite-post/'. $id) . '" class="unfavourite-post ajax-button btn btn-xs btn-warning pull-right small-margin-right" data-method="post" data-replace=".unfavourite-post"><span><i class="fa fa-star"></i></a>'
        );
        
        return Response::json($data);
    }

    public function postUnfavouritePost($id){
        if ( Auth::check() ){
            $current_user = Auth::user();
            $favourtie_posts_array = explode(',', $current_user->favourite_posts);
            $key = array_search($id, $favourtie_posts_array);
            if($key!==false){
                unset($favourtie_posts_array[$key]);
            }
            $current_user->favourite_posts = implode(',', $favourtie_posts_array);
            $current_user->save();
        }

        $data = array(
            "html" => '<a href="'. URL::to('user/favourite-post/'.$id) . '" class="favourite-post ajax-button btn btn-xs btn-default pull-right small-margin-right" data-method="post" data-replace=".favourite-post"><span><i class="fa fa-star-o"></i></a>'
        );
        
        return Response::json($data);
    }

}