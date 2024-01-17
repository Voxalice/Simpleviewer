<?php


require './includes/replit.php';
require './includes/page.php';
require './includes/functions.php';


# Page setup

$u_query = $_GET['u'] ?? null;

$user = @file_get_contents(p("https://api.scratch.mit.edu/users/{$u_query}"));

$page_title = "Simpleviewer - User {$u_query} Not Found";


if ($user !== false) {

	$user_json = json_decode($user, true);

	if (@strcasecmp(@$user_json["username"], $u_query) == 0) {

		$page_title = "Simpleviewer - User {$user_json["username"]}";

	}

}


?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title><?= $page_title; ?></title>
		<link rel="stylesheet" href="/style.css">
		<meta charset="UTF-8">
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		<meta name="description" content="View Scratch without JavaScript or a modern browser">
		<meta name="keywords" content="Scratch, MIT, Legacy, Compatibility, Accessibility">
		<meta name="author" content="Voxalice">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body bgcolor="#222"><font color="white" face="sans-serif">
		<?php 

			
			if ($user === false) {
				
				http_response_code(404);

				echo "<h1>404 User Not Found</h1>Make sure the username has been typed correctly!<br>(Don't include the * next to Scratch Team usernames)<br><br>";

			} else {


				$user_json = json_decode($user, true);

				if (@strcasecmp(@$user_json["username"], $u_query) == 0) {


					# Username (including *) and user ID

					$username = $user_json["username"];

					if ($user_json["scratchteam"] === true) {

						$star = '*';

					} else {

						$star = '';

					}

					echo "<h1>@{$username}{$star} <img src='{$user_json["profile"]["images"]["32x32"]}' width='32'></h1>
					(User #{$user_json["id"]})<br><br>";


					# User statuses

					$about_me = htmlspecialchars($user_json['profile']['bio']);

					$wiwo = htmlspecialchars($user_json['profile']['status']);

					echo nl2br("<div id='desc'><br><a href='//scratch.mit.edu/users/{$username}' tabindex='0'>View on Scratch (JS required)</a><br><br><b>About Me</b><br><br>{$about_me}<br><br><b>What I'm Working On</b><br><br>{$wiwo}<br><br></div>");


					# Join date

					$join_date = date('Y-m-d H:i:s', strtotime($user_json['history']['joined']));

					echo "<p>Joined <b>{$join_date}</b></p>";
					

					# Ocular status

					$ocular_status = @file_get_contents("https://my-ocular.jeffalo.net/api/user/{$username}");

					$ocular_status_json = json_decode($ocular_status, true);

					if (@$ocular_status_json['name'] == $username) {

						$ocular_status_string = htmlentities($ocular_status_json['status']);

						echo "<p>Ocular status: <i>\"{$ocular_status_string}\"</i></p>";
						
					}


					# Comments

					echo "<hr><h1>Comments</h1>";

					$comment_html = nl2br(@file_get_contents(p("https://scratch.mit.edu/site-api/comments/user/{$username}/?page={$page}")));


					# Check if comments are empty

					if (str_contains($comment_html, "div")) {


						# Remove all tags except <div>'s, <ul>'s, and <span>'s

						$final_comments = strip_tags($comment_html, ['div', 'ul', 'span']);

						# Remove [data-content="reply-form"]

						$final_comments = preg_replace('#<div data-content="reply-form">(.*?)</div>#is', '', $final_comments);

						# Remove element IDs (all elements are selected by class)

						$final_comments = preg_replace('#id="(.*?)"#is', '', $final_comments);

						# Remove data-comment- attributes

						$final_comments = preg_replace('# data-comment-#is', '', $final_comments);

						# Remove data-thread attributes

						$final_comments = preg_replace('# data-thread="(.*?)"#is', '', $final_comments);

						# Remove unnecessary line-breaks in source code

						$final_comments = str_replace(array("\r", "\n"), '', $final_comments);

						# Add <br><br> after comment content

						$final_comments = preg_replace("#<div class=\"content\">(.*?)</div>#is", "<div class='content'>$1</div><br><br>", $final_comments);

						# Remove excess whitespace

						$final_comments = preg_replace('/\s+/', ' ', $final_comments);

						# Full comment dates

						function full_date(array $sview_date_input) {

							return date('Y-m-d H:i:s', strtotime($sview_date_input[1]));
							
						}

						$final_comments = preg_replace_callback('#<span class="time" title="(.*?)">(.*?)</span>#is', "full_date", $final_comments);

						# Bold usernames and add links

						$final_comments = preg_replace("#<div class=\"name\">\s(.*?)\s</div>#is", "<a href='/users/$1' class='userlink'><b>$1</b></a><br><br>", $final_comments);

						# 1.1.0: Add links to project URLs

						$final_comments = preg_replace("#(https:\/\/scratch.mit.edu\/projects(?:\/|\/\w+\/)(\d+)(?:\/|))#is", "<a href='/projects/$2'>$1</a>", $final_comments);

						# Remove random unwanted strings

						$final_comments = str_replace('<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN" "http://www.w3.org/TR/REC-html40/loose.dtd">', '', $final_comments);

						$final_comments = str_replace('<html><body>', '', $final_comments);

						$final_comments = str_replace('</body></html>', '', $final_comments);

						$final_comments = str_replace(' <span>Reply</span> ', '', $final_comments);

						$final_comments = str_replace(' <ul class="replies"> </ul> ', '<br>', $final_comments);

						$final_comments = str_replace('</ul>', '</ul><br>', $final_comments);

						$final_comments = str_replace('<div class="actions-wrap"> </div>', '', $final_comments);

						$final_comments = preg_replace('#<div class="more-replies"(.*?)</div>#is', '', $final_comments);

						$final_comments = preg_replace('#<div class="button grey"(.*?)</div>#is', '', $final_comments);

						# Make date small and add "Posted " before it

						$final_comments = preg_replace("#<div class=\"info\">(.*?)<div class='content'>(.*?)<br><br> <div>(.*?)</div>#is", "<div class=\"info\">$1<div class='content'>$2<br><small>Posted $3</small>", $final_comments);

						# We're done here (finally)

						echo $final_comments;


						require './includes/page_links.php';


					} else {


						echo '<p>No comments</p><br>';


					}


				} else {

					http_response_code(404);

					echo "<h1>404 User Not Found</h1>Make sure the username has been typed correctly!<br>(Don't include the * next to Scratch Team usernames)";

				}


			}
		

		?>

		<?php include "footer.php"; ?>
	</font></body>
</html>