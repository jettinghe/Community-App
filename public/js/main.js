$(document).ready(function() {

	$('.popover-button').click(function(e) {
	    e.preventDefault();
	});

	$('.popover-button').popover();

	var currentReplyingId = '',
		currentReplyingUser = '';

	$('.reply-comment-button').each(function(){
		var $this = $(this),
			replyCommentId = $this.attr('id').substr(14),
			replyCommentUser = $this.data('at-name'),
			$currentCommentForm = $('#comment-id-' + replyCommentId),
			currentLevelNumber = $currentCommentForm.data('counter'),
			replyToCommentId = $currentCommentForm.data('reply-comment-id');

		//generate anchor links to list of comments that this comment replied to
		if (replyToCommentId !== 0) {
			var index = 0,
				replyToCommentHTML = '',
				replyToCommentIdArray = replyToCommentId.toString().split(","),
				currentCommentContent = $currentCommentForm.find('.comment-content').html();

			for (index = 0; index < replyToCommentIdArray.length; ++index) {
				var	repliedCommentId = replyToCommentIdArray[index],
					$commentThread = $('#comments .comment-container[data-comment-id=' + repliedCommentId +']');
			    //replyToCommentHTML = replyToCommentHTML.concat('<a href="#comment-' + repliedCommentId + '"><span class="label label-default">@' + $commentThread.find('.reply-comment-button').data('at-name') + ' ( ' + $commentThread.data('counter') + ' 楼 )</span></a> ');
				currentCommentContent = currentCommentContent.replace('@' + $commentThread.find('.reply-comment-button').data('at-name') + '(#' + $commentThread.data('counter') + ')', 
												'<a href="#comment-' + repliedCommentId + '"><span class="label label-default">@' + $commentThread.find('.reply-comment-button').data('at-name') + ' (#' + $commentThread.data('counter') + ')</span></a> ');
			}

			console.log(currentCommentContent);
			// $currentCommentContent = $currentCommentForm.find('.comment-content');
			$currentCommentForm.find('.comment-content').html(currentCommentContent);
			//$currentCommentFormColumn.prepend(replyToCommentIdArray.length > 1 ? '<i class="fa fa-reply-all grey-icon"></i> ' : '<i class="fa fa-reply grey-icon"></i> ')	
		}

		if ($('#comments').data('user-login') == 'no'){
			$this.popover();
			return;
		}

		$this.on('click', function(e){
			e.stopPropagation();
			e.preventDefault();
			$('#comments .cancel-reply-button').css('visibility', 'hidden');

			if($('#comments').data('user-login') == 'yes'){
				$currentCommentForm.find(' > .cancel-reply-button').css('visibility', 'visible');
			}

			$('#new-comment').appendTo($currentCommentForm);

			//currentReplyingUser = currentReplyingUser.concat()
			var currentReplyingUserArray = currentReplyingUser.toString().split(",  ");
			var currentReplyingPlaceholder = '@' + replyCommentUser + '(#' + currentLevelNumber + ')';
			if( currentReplyingUserArray.indexOf(currentReplyingPlaceholder) == -1 ) {
				currentReplyingUser = currentReplyingUser.concat( (currentReplyingUser == '') ? currentReplyingPlaceholder : ',  '+ currentReplyingPlaceholder );
			}

			//reset comment form for new comment
			$('#reply-comment-id').remove();
			var currentReplyingIdArray = currentReplyingId.toString().split(",");

			if( currentReplyingIdArray.indexOf(replyCommentId) == -1 ) {
				currentReplyingId = currentReplyingId.concat( (currentReplyingId == '') ? replyCommentId : ',' + replyCommentId );
			}
			
			$currentCommentForm.find('> #new-comment > fieldset').prepend('<input type="hidden" name="reply-comment-id" id="reply-comment-id" value="'+currentReplyingId+'">');
			
			$('#new-comment textarea').text('');
			$('#new-comment .note-editable').html('');

			//$currentCommentForm.find('> #new-comment > fieldset').prepend('<input type="hidden" name="reply-comment-id" id="reply-comment-id" value="'+replyCommentId+'">');
			
			$('#new-comment textarea').text(currentReplyingUser.toString().split(",  ").join('<br/><br/>'));
			$('#new-comment .note-editable').html(currentReplyingUser.toString().split(",  ").join('<br/><br/>'));
		});
	});

	$('.cancel-reply-button').on('click', function(){
		var $this = $(this),
			$commentForm = $this.parent().find('#new-comment');
		$('#new-comment textarea').text('');
		$('#new-comment .note-editable').html('');
		$commentForm.find('#reply-comment-id').remove();
		currentReplyingUser = currentReplyingId = '';
		$commentForm.appendTo('#comments');
		$this.css('visibility', 'hidden');
	});

	$('.selectpicker').selectpicker();

	$('li.dropdown .dropdown-animation').on('click', function(){
		$(this).find('i').toggleClass('fa-angle-down').toggleClass('fa-angle-up');
	});

	if( $('#new-post-wysiwyg').length > 0){
		$('#new-post-wysiwyg').summernote({
		  height: 250,
		  toolbar: [
		    ['fontsize', ['fontsize']],
		    ['color', ['color']],
		    ['para', ['ul', 'ol', 'paragraph']],
		    ['height', ['height']],
		    ['insert', ['picture', 'link']], // no insert buttons
		    //['table', ['table']], // no table button
		    ['help', ['help']], //no help button
		    ['fullscreen', ['fullscreen']]
		  ]
		});
	}

	if( $('#comment-textarea').length > 0){
		$('#comment-textarea').summernote({
		  height: 150,
		  toolbar: [
		    ['style', ['bold', 'italic', 'underline', 'clear']],
		    ['para', ['ul', 'ol']],
		    ['insert', ['link']], // no insert buttons
		    ['help', ['help']], //no help button
		  ]
		});
	}
	
});