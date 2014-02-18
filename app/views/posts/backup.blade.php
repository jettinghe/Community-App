@if($comment->has_sub_comments)
					@foreach($post->comments as $subcomment)
						@if($subcomment->reply_comment_id == $comment->id)
						<div class="sub-comments well">
							{{ $subcomment->content }}
							{{ $subcomment->user->username }}
							{{ $subcomment->created_at->diffForHumans() }}
						</div>
						@endif
					@endforeach
				@endif