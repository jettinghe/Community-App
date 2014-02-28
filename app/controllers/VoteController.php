<?php

class VoteController extends BaseController {

    public function getVoteUp($id){
        $data = array(
            "html" => '<span class="vote-btn upvotecount orange-span" data-refresh-url="'. URL::to("post-vote/remove-vote-up/$id") . '"><i class="fa fa-fw fa-thumbs-up"></i>' . Post::find($id)->upvotes . '</span>'
        );
        
        return Response::json($data);
    }

    public function getRemoveVoteUp($id){
        $data = array(
            "html" => '<span class="vote-btn upvotecount" data-refresh-url="'. URL::to("post-vote/vote-up/$id") . '"><i class="fa fa-fw fa-thumbs-o-up"></i>' . Post::find($id)->upvotes . '</span>'
        );
        
        return Response::json($data);
    }

    public function postVoteUp($id){
        if( Auth::check() ){
            Auth::user()->vote($id, 'up');
        }
        $data = array(
            "html" => '<a href="'. URL::to("post-vote/vote-down/$id") .'" class="downvote-button ajax-button btn btn-xs btn-default pull-right" data-method="post" data-refresh=".downvotecount"  data-replace=".upvote-button"><span class="vote-btn downvotecount" data-refresh-url="'. URL::to("post-vote/vote-down/$id") . '"><i class="fa fa-fw fa-thumbs-o-down"></i>' . Post::find($id)->downvotes . '</span></a>'
        );
        return Response::json($data);
    }

    public function getVoteDown($id){
        $data = array(
            "html" => '<span class="vote-btn downvotecount orange-span" data-refresh-url="'. URL::to("post-vote/remove-vote-down/$id") . '"><i class="fa fa-fw fa-thumbs-down"></i>' . Post::find($id)->downvotes . '</span>'
        );
        
        return Response::json($data);
    }

    public function getRemoveVoteDown($id){
        $data = array(
            "html" => '<span class="vote-btn downvotecount" data-refresh-url="'. URL::to("post-vote/vote-down/$id") . '"><i class="fa fa-fw fa-thumbs-o-down"></i>' . Post::find($id)->downvotes . '</span>'
        );
        
        return Response::json($data);
    }

    public function postVoteDown($id){
        if( Auth::check() ){
            Auth::user()->vote($id, 'down');
        }
        $data = array(
            "html" => '<a href="'. URL::to("post-vote/vote-up/$id") .'" class="upvote-button ajax-button btn btn-xs btn-default pull-right small-margin-right" data-method="post" data-refresh=".upvotecount"  data-replace=".downvote-button"><span class="vote-btn upvotecount" data-refresh-url="'. URL::to("post-vote/vote-up/$id") . '"><i class="fa fa-fw fa-thumbs-o-up"></i>' . Post::find($id)->upvotes . '</span></a>'
        );
        
        return Response::json($data);
    }

}