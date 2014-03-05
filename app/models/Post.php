<?php
use Carbon\Carbon;

class Post extends Basemodel {
	protected $guarded = array();

	public static $rules = array(
        'title'=>'required|between:5, 50',
        'content'=>'required',
    );

	public static function genId(){ 
        $uniqid = ''; 
        do{
            for ( $i = 0; $i < 9; $i++ ) 
            { 
            $uniqid .= mt_rand ( 1, 9 ); 
            } 
        }while(Post::find($uniqid));

        return $uniqid; 
    } 

    public static function seoLink($id){ 
        $post = Post::find($id);
        $title = $post->title;
        $seoTitle = strtolower($title);
        $seoTitle = preg_replace("/[^a-z0-9_\s-]/", "", $seoTitle);
        $seoTitle = preg_replace("/[\s-]+/", " ", $seoTitle);
        $seoTitle = preg_replace("/[\s_]/", "-", $seoTitle);
        $seolink = URL::to("post/$post->id/" . $seoTitle);
        return $seolink; 
    } 

    public static function hotPosts($hotPeriod){
        $now = new Carbon();
        $period = new Carbon();
        $period = $period->subDays($hotPeriod);

        $hotPosts = DB::table('posts')->whereBetween('posts.created_at', array($period, $now))
                            ->select(array('posts.*', DB::raw('COUNT(post_id)+upvotes AS response_count')))
                            ->leftJoin('comments', 'posts.id', '=', 'comments.post_id')
                            ->groupBy('posts.id')
                            ->orderBy('response_count', 'desc')
                            ->get();
        return $hotPosts;
    }

    public function category(){
        return $this->belongsTo('Category');
    }

    public function user(){
        return $this->belongsTo('User');
    }

    public function postvotenotify(){
        return $this->hasOne('Postvotenotify');
    }

    public function comments(){
        return $this->hasMany('Comment');
    }
}
