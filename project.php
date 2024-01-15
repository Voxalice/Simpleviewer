<?php


# REPLIT DEVELOPMENT TOGGLE!

$proxy_requests = false;

# REPLIT DEVELOPMENT TOGGLE!

function p(string $url_input) {
	if ($GLOBALS['proxy_requests']) {
		return 'https://apis.scratchconnect.eu.org/free-proxy/get?url=' . $url_input;
	} else {
		return $url_input;
	}
}


$project = @file_get_contents(p("https://api.scratch.mit.edu/projects/{$_GET['p']}"));

$page_title = "Simpleviewer - Project {$_GET['p']} Not Found";


if ($project !== false) {

	$project_json = json_decode($project, true);

	if ($project_json["id"] == $_GET["p"]) {

		$page_title = "Simpleviewer - Project {$_GET["p"]} - {$project_json["title"]}";

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
		

			if ($project === false) {

				http_response_code(404);
				
				echo "<h1>404 Project Not Found</h1>Make sure the project ID has been typed correctly!<br><br>(Simpleviewer cannot view unshared projects)";

			} else { # Project exists

							
				$project_json = json_decode($project, true);
							
				if ($project_json["id"] == $_GET["p"]) {

					
					# Title, author, and project ID
					
					$author = $project_json['author']['username'];

					$id = $_GET["p"];

					$title = htmlspecialchars($project_json['title']);

					echo "<h1>{$title}, by <a href='/users/{$author}' class='userlink'>{$author}</a></h1>(Project #{$id})<br><br>";
					

					# TurboWarp embed
					
					echo "<input type='image' src='/turbowarpload.jpg' onclick='twload();' tabindex='-1' alt='Click here to load this project in a TurboWarp embed. JavaScript required'><br><br><a href='https://forkphorus.github.io/sb-downloader/?id={$_GET['p']}'>(or download it as a .sb3 file; JavaScript required)</a><br><br><hr>";


					# Project stats

					$stats = $project_json['stats'];

					echo "<br>
					<b>{$stats['views']}</b> views / 
					<b>{$stats['loves']}</b> loves / 
					<b>{$stats['favorites']}</b> favorites / 
					<b>{$stats['remixes']}</b> remixes
					<br>";


					# Project datetimes

					$dates = $project_json['history'];

					$created = @date('Y-m-d H:i:s', strtotime($dates['created']));

					$created_string = '';
					if (date('Y', strtotime($created)) > 2006) {
						$created_string = "<br>Project created on <b>{$created}</b>";
					}
					
					$modified = @date('Y-m-d H:i:s', strtotime($dates['modified']));

					$modified_string = '';
					if (date('Y', strtotime($modified)) > 2006) {
						$modified_string = "<br>Last modified on <b>{$modified}</b>";
					}

					$shared = @date('Y-m-d H:i:s', strtotime($dates['shared']));

					$shared_string = '';
					if (date('Y', strtotime($shared)) > 2006) {
						$shared_string = "<br><br>Last shared on <b>{$shared}</b>";
					}

					echo "{$created_string}{$modified_string}{$shared_string}";


					# Descriptions

					$instructions = htmlspecialchars($project_json['instructions'], ENT_QUOTES, 'UTF-8');
					
					$description = htmlspecialchars($project_json['description'], ENT_QUOTES, 'UTF-8');
					
					echo nl2br("<br><br><div id='desc'><br><a href='//scratch.mit.edu/projects/{$id}' tabindex='0'>View on Scratch (JS and good internet connection required)</a><br><br><b>Instructions</b><br><br>{$instructions}<br><br><b>Notes and Credits</b><br><br>{$description}<br><br></div><br>");

					
					# Comments
					
					echo "<hr><h1>Comments</h1>";

					if (@round(@$_GET["page"]) > 0) {

						$page = @round(@$_GET["page"]);

					} else {

						$page = 1;

					}

					$page_offset = $page - 1;

					$page_offset = $page_offset * 20;
					
					
					$comments = @file_get_contents(p("https://api.scratch.mit.edu/users/{$author}/projects/{$id}/comments?offset={$page_offset}&limit=20"));
					
					$comment_json = json_decode($comments, true);


					# Check if comments are empty

					if (str_contains($comments, "{")) {
						
					
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
	
								$replies = @file_get_contents(p("https://api.scratch.mit.edu/users/{$project_json['author']['username']}/projects/{$_GET["p"]}/comments/{$value["id"]}/replies?offset=0&limit=40"));
								
								$reply_json = json_decode($replies, true);


								echo "<ul class='replies'>";
								
								
								foreach($reply_json as $key2 => $value2) {
	
									$comment_date2 = date('Y-m-d H:i:s', strtotime($value2['datetime_created']));

									$comment_content2 = preg_replace("#(https:\/\/scratch.mit.edu\/projects(?:\/|\/\w+\/)(\d+)(?:\/|))#is", "<a href='/projects/$2'>$1</a>", $value2['content']);
				
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
						$query_result_previous = "/project.php?" . http_build_query($query);

						# Construct next page link

						$query = $_GET;

						// replace parameter(s)
						$query['page'] = $page_next;

						// rebuild url
						$query_result_next = "/project.php?" . http_build_query($query);

						# Echo page buttons / links

						if ($page > 1) {

							echo "<a href='{$query_result_previous}'>&lt; Previous page</a> | ";

						}

						echo "<a href='{$query_result_next}'>Next page &gt;</a><br><br>";
						

					} else {


						echo "<p>No comments</p><br>";

						
					}

					
				} else {

					http_response_code(404);
					
					echo "<h1>404 Project Not Found</h1>Make sure the project ID has been typed correctly!";
					
				}


			}
		?>

		<script type="text/javascript" language="JavaScript">
		<!--
			id = <?php echo $id; ?>;
			function twload(){
				document.querySelector("input").outerHTML = '<iframe src="https://turbowarp.org/' + id + '/embed?addons=pause,gamepad,mute-project,drag-drop&settings-button" width="499" height="416" allowtransparency="true" frameborder="0" scrolling="no" allowfullscreen alt="TurboWarp embed"></iframe>';
			}
		//-->
		</script>
		
		<?php include "footer.php" ?>
	</font></body>
</html>