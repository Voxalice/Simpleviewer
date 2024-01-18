<?php


# Comment component

function sview_comment(string $comment_username, string $comment_data, string $comment_datetime, bool $comment_reply_on) {

	
	if ($comment_reply_on) {
		
		$comment_reply_marker = '<small>reply: </small>';
		
	} else {
		
		$comment_reply_marker = '';
		
	}

	return "<div class='comment'><b>{$comment_reply_marker}<a href='/users/{$comment_username}' class='userlink'>{$comment_username}</a></b><br><br>{$comment_data}<br><br><small>Posted {$comment_datetime}</small></div>";

	
}


# Project display component

function sview_result(int $result_id, string $result_title, string $result_username) {


	$result_title2 = htmlspecialchars($result_title);	

	return "<div class='result'><a href='/projects/{$result_id}'><img src='//uploads.scratch.mit.edu/get_image/project/{$result_id}_100x80.png?v=sview' width='100' height='80'><br><br><b>\"{$result_title2}\"</b></a>, by <a href='/users/{$result_username}'>{$result_username}</a> (Project #{$result_id})</div><br>";
	
	
}


# Replace project and studio URLs in comments

function sview_replace_links(string $comment_with_links) {


	$final_comment_with_links = preg_replace("#(http(?:s|)://scratch.mit.edu/projects(?:/|/\w+/)(\d+)(?:/editor(?:/|)|/fullscreen(?:/|)|/|))#is", "<a href='/projects/$2'>$1</a>", $comment_with_links);

	$final_comment_with_links = preg_replace("#(http(?:s|)://scratch.mit.edu/studios(?:/|/\w+/)(\d+)(?:/comments(?:/|)|/curators(?:/|)|/activity(?:/|)|/|))#is", "<a href='/studios/$2'>$1</a>", $final_comment_with_links);

	return $final_comment_with_links;


}


?>