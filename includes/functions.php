<?php


function sview_comment(string $comment_username, string $comment_data, string $comment_datetime, bool $comment_reply_on) {

	
	if ($comment_reply_on) {
		
		$comment_reply_marker = '<small>reply: </small>';
		
	} else {
		
		$comment_reply_marker = '';
		
	}

	return "<div class='comment'><b>{$comment_reply_marker}<a href='/users/{$comment_username}' class='userlink'>{$comment_username}</a></b><br><br>{$comment_data}<br><br><small>Posted {$comment_datetime}</small></div>";

	
}


function sview_result(int $result_id, string $result_title, string $result_username) {
	

	return "<div class='result'><a href='/projects/{$result_id}'><img src='//uploads.scratch.mit.edu/get_image/project/{$result_id}_100x80.png?v=sview' width='100' height='80'><br><br><b>\"{$result_title}\"</b></a>, by <a href='/users/{$result_username}'>{$result_username}</a> (Project #{$result_id})</div><br>";
	
	
}


?>