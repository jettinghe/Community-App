<?php

class NotificationController extends BaseController {

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

}