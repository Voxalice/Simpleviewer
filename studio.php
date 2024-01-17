<?php


# Replit development code

$replit_support = filter_var(@file_get_contents("./replit.txt"), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

function p(string $url_input) {
	if ($GLOBALS['replit_support']) {
		return 'https://apis.scratchconnect.eu.org/free-proxy/get?url=' . $url_input;
		#return 'https://api.allorigins.win/raw?url=' . $url_input;
	} else {
		return $url_input;
	}
}


# Page setup

$s_query = $_GET['s'] ?? null;

if ($s_query === NULL) {
	$studio = false;
} else {
	$studio = @file_get_contents(p("https://api.scratch.mit.edu/studios/{$s_query}"));
}

$page_title = "Simpleviewer - Studio {$s_query} Not Found";


if ($studio !== false) {

	$studio_json = json_decode($studio, true);

	if (@$studio_json["id"] == $s_query) {

		$page_title = "Simpleviewer - Studio {$s_query} - {$studio_json["title"]}";

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


			if ($studio === false) {

				http_response_code(404);

				echo "<h1>404 Studio Not Found</h1>Make sure the studio ID has been typed correctly!<br><br>";

			} else { # Studio exists


				$studio_json = json_decode($studio, true);

				if (@$studio_json["id"] == $s_query) {


					# Title, author, and studio ID

					$id = $s_query;

					$title = htmlspecialchars($studio_json['title']);

					echo "<h1>{$title}</h1>(Studio #{$id})<br>";


					# Descriptions

					$description = htmlspecialchars($studio_json['description'], ENT_QUOTES, 'UTF-8');

					echo nl2br("<br><div id='desc'><br><a href='//scratch.mit.edu/studios/{$id}' tabindex='0'>View on Scratch (JS and good internet connection required)</a><br><br><b>Studio Description</b><br><br>{$description}<br><br></div>");


					# Host username

					$host_req = @file_get_contents(p("https://api.scratch.mit.edu/studios/{$s_query}/managers/?limit=1&offset=0"));

					$host_json = json_decode($host_req, true);

					$host_name = $host_json[0]['username'];

					echo "<br>Hosted by <b><a href='/users/{$host_name}' class='userlink'>{$host_name}</a></b><br>";


					# Studio datetimes

					$dates = $studio_json['history'];

					/* $created = @date('Y-m-d H:i:s', strtotime($dates['created']));

					$created_string = '';
					if (date('Y', strtotime($created)) > 2006) {
						$created_string = "<br>Studio created on <b>{$created}</b>";
					} */

					$modified = @date('Y-m-d H:i:s', strtotime($dates['modified']));

					$modified_string = '';
					if (date('Y', strtotime($modified)) > 2006) {
						$modified_string = "<br>Last modified on <b>{$modified}</b>";
					}

					# echo "{$created_string}{$modified_string}";

					echo "{$modified_string}";


					# Studio stats

					$stats = $studio_json['stats'];

					$projects_number = intval($studio_json['stats']['projects']);

					if ($projects_number == 100) {
						$projects_number = "100+";
					}

					$comments_number = intval($studio_json['stats']['comments']);

					if ($comments_number == 100) {
						$comments_number = "100+";
					}

					echo "<br><br>
					<b>{$projects_number}</b> projects / 
					<b>{$comments_number}</b> comments /
					<b>{$stats['followers']}</b> followers /
					<b>{$stats['managers']}</b> managers
					<br><br>";


					# Declare page offset number, for projects and comments

					if (@round(@$_GET["page"]) > 0) {

						$page = @round(@$_GET["page"]);

					} else {

						$page = 1;

					}

					$page_offset = $page - 1;

					$page_offset = $page_offset * 20;


					# Mode switch: comments link

					$query = $_GET;

					// replace parameter(s)
					$query['page'] = 1;
					$query['mode'] = NULL;

					// rebuild url
					$mode_comments = "/studio.php?" . http_build_query($query);

					# Mode switch: projects link

					$query = $_GET;

					// replace parameter(s)
					$query['page'] = 1;
					$query['mode'] = 'projects';

					// rebuild url
					$mode_projects = "/studio.php?" . http_build_query($query);

					# Echo mode buttons / links

					echo "<hr><br><a href='{$mode_comments}'>View Comments</a> | <a href='{$mode_projects}'>View Projects</a><br><br>";


					# Projects / Comments
					
					if (@$_GET['mode'] == 'projects') {
						

						# Projects
						
						echo "<hr><h1>Projects</h1>";

						$projects = @file_get_contents(p("https://api.scratch.mit.edu/studios/{$id}/projects?offset={$page_offset}&limit=20"));

						$projects_json = json_decode($projects, true);
						

						# Check if no projects were returned
						
						if (str_contains($projects, "creator_id")) {
							

							foreach($projects_json as $key => $value) {
								
	
								# Echo search results
	
								echo "<div class='result'><br><a href='/projects/{$value['id']}'><b>\"{$value['title']}\"</b></a>, by <a href='/users/{$value['username']}'>{$value['username']}</a> (Project #{$value['id']})<br><br></div><br>";

								
							}


							# Add page buttons

							$page_previous = $page - 1;
							$page_next = $page + 1;

							# Construct previous page link

							$query = $_GET;

							// replace parameter(s)
							$query['page'] = $page_previous;

							// rebuild url
							$query_result_previous = "/studio.php?" . http_build_query($query);

							# Construct next page link

							$query = $_GET;

							// replace parameter(s)
							$query['page'] = $page_next;

							// rebuild url
							$query_result_next = "/studio.php?" . http_build_query($query);


							# Echo page buttons / links

							if ($page > 1) {

								echo "<a href='{$query_result_previous}'>&lt; Previous page</a> | ";

							}

							echo "<a href='{$query_result_next}'>Next page &gt;</a><br><br>";
							

						} else {


							echo "<p>No projects</p><br>";


						}
						
						
					} else {

						# Comments

						echo "<hr><h1>Comments</h1>";
	
						$comments = @file_get_contents(p("https://api.scratch.mit.edu/studios/{$id}/comments?offset={$page_offset}&limit=20"));
	
						$comment_json = json_decode($comments, true);
	
	
						# Check if comments are empty
	
						if (str_contains($comments, "reply_count")) {
	
	
							foreach($comment_json as $key => $value) {
								
	
								# Echo top-level comment
	
								$comment_date = date('Y-m-d H:i:s', strtotime($value['datetime_created']));
	
								# 1.1.0: Add links to project URLs
	
								$comment_content = preg_replace("#(https:\/\/scratch.mit.edu\/projects(?:\/|\/\w+\/)(\d+)(?:\/|))#is", "<a href='/projects/$2'>$1</a>", $value['content']);
	
								echo "<div class='comment'><a href='/users/{$value["author"]["username"]}' class='userlink'><b>{$value["author"]["username"]}</b></a>
								<br><br>{$comment_content}
								<br><br><small>Posted {$comment_date}</small></div>";
	
	
								if ($value["reply_count"] > 0) {
	
	
									# Echo reply comments if applicable
	
									$replies = @file_get_contents(p("https://api.scratch.mit.edu/studios/{$s_query}/comments/{$value["id"]}/replies?offset=0&limit=40"));
	
									$reply_json = json_decode($replies, true);
	
	
									echo "<ul class='replies'>";
	
	
									foreach($reply_json as $key2 => $value2) {
										
	
										$comment_date2 = date('Y-m-d H:i:s', strtotime($value2['datetime_created']));
	
										$comment_content2 = preg_replace("#(https:\/\/scratch.mit.edu\/studios(?:\/|\/\w+\/)(\d+)(?:\/|))#is", "<a href='/studios/$2'>$1</a>", $value2['content']);
	
										echo "<div class='comment'>
										<b><small>reply: </small><a href='/users/{$value2["author"]["username"]}' class='userlink'>{$value2["author"]["username"]}</a></b>
										<br><br>{$comment_content2}
										<br><br><small>Posted {$comment_date2}</small></div>";

										
									}
	
	
									echo "</ul>";
	
	
								}
	
								# End top-level comment foreach
	
								echo "<br>";
								
	
							}
	
	
							# Add page buttons
	
							$page_previous = $page - 1;
							$page_next = $page + 1;
	
							# Construct previous page link
	
							$query = $_GET;
	
							// replace parameter(s)
							$query['page'] = $page_previous;
	
							// rebuild url
							$query_result_previous = "/studio.php?" . http_build_query($query);
	
							# Construct next page link
	
							$query = $_GET;
	
							// replace parameter(s)
							$query['page'] = $page_next;
	
							// rebuild url
							$query_result_next = "/studio.php?" . http_build_query($query);
	
							# Echo page buttons / links
	
							if ($page > 1) {
	
								echo "<a href='{$query_result_previous}'>&lt; Previous page</a> | ";
	
							}
	
							echo "<a href='{$query_result_next}'>Next page &gt;</a><br><br>";
						

						} else {
	
	
							echo "<p>No comments</p><br>";
	
	
						}
						

					}


				} else {

					http_response_code(404);

					echo "<h1>404 Studio Not Found</h1>Make sure the studio ID has been typed correctly!<br><br>";

				}


			}
		?>

		<script type="text/javascript" language="JavaScript">
		<!--
			id = <?php echo $id; ?>;
			function twload(){
				document.querySelector("input").outerHTML = '<iframe src="https://turbowarp.org/' + id + '/embed?addons=pause,gamepad,mute-studio,drag-drop&settings-button" width="499" height="416" allowtransparency="true" frameborder="0" scrolling="no" allowfullscreen alt="TurboWarp embed"></iframe>';
			}
		//-->
		</script>

		<?php include "footer.php" ?>
	</font></body>
</html>