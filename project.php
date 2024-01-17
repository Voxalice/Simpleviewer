<?php


require './includes/replit.php';
require './includes/page.php';
require './includes/functions.php';


# Page setup

$p_query = $_GET['p'] ?? null;

$project = @file_get_contents(p("https://api.scratch.mit.edu/projects/{$p_query}"));

$page_title = "Simpleviewer - Project {$p_query} Not Found";


if ($project !== false) {

	$project_json = json_decode($project, true);

	if (@$project_json["id"] == $p_query) {

		$page_title = "Simpleviewer - Project {$p_query} - {$project_json["title"]}";

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
				
				echo "<h1>404 Project Not Found</h1>Make sure the project ID has been typed correctly!<br><br>(Simpleviewer cannot view unshared projects)<br><br>";

			} else { # Project exists

							
				$project_json = json_decode($project, true);
							
				if (@$project_json["id"] == $p_query) {

					
					# Title, author, and project ID
					
					$author = $project_json['author']['username'];

					$title = htmlspecialchars($project_json['title']);

					echo "<h1>{$title}, by <a href='/users/{$author}' class='userlink'>{$author}</a></h1>(Project #{$p_query})<br><br>";
					

					# TurboWarp embed
					
					echo "<input type='image' src='/turbowarpload.jpg' onclick='twload();' tabindex='-1' alt='Click here to load this project in a TurboWarp embed. JavaScript required'><br><br><a href='https://forkphorus.github.io/sb-downloader/?id={$p_query}'>(or download it as a .sb3 file; JavaScript required)</a><br><br><hr>";


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
					
					echo nl2br("<br><br><div id='desc'><br><a href='//scratch.mit.edu/projects/{$p_query}' tabindex='0'>View on Scratch (JS and good internet connection required)</a><br><br><b>Instructions</b><br><br>{$instructions}<br><br><b>Notes and Credits</b><br><br>{$description}<br><br></div><br>");

					
					# Comments
					
					echo "<hr><h1>Comments</h1>";
					
					
					$comments = @file_get_contents(p("https://api.scratch.mit.edu/users/{$author}/projects/{$p_query}/comments?offset={$page_offset}&limit=20"));
					
					$comment_json = json_decode($comments, true);


					# Check if comments are empty

					if (str_contains($comments, "{")) {
						
					
						foreach($comment_json as $key => $value) {
							
							# Echo top-level comment
	
							$comment_date = date('Y-m-d H:i:s', strtotime($value['datetime_created']));

							# 1.1.0: Add links to project URLs

							$comment_content = preg_replace("#(https:\/\/scratch.mit.edu\/projects(?:\/|\/\w+\/)(\d+)(?:\/|))#is", "<a href='/projects/$2'>$1</a>", $value['content']);

							echo sview_comment($value["author"]["username"], $comment_content, $comment_date, false);
							
							
							if ($value["reply_count"] > 0) {
	
								
								# Echo reply comments if applicable
	
								$replies = @file_get_contents(p("https://api.scratch.mit.edu/users/{$project_json['author']['username']}/projects/{$p_query}/comments/{$value["id"]}/replies?offset=0&limit=40"));
								
								$reply_json = json_decode($replies, true);


								echo "<ul class='replies'>";
								
								
								foreach($reply_json as $key2 => $value2) {
	
									$comment_date2 = date('Y-m-d H:i:s', strtotime($value2['datetime_created']));

									$comment_content2 = preg_replace("#(https:\/\/scratch.mit.edu\/projects(?:\/|\/\w+\/)(\d+)(?:\/|))#is", "<a href='/projects/$2'>$1</a>", $value2['content']);

									echo sview_comment($value2["author"]["username"], $comment_content2, $comment_date2, true);
									
								}


								echo "</ul>";
	
	
				 			}
	
							# End top-level comment foreach
							
							echo "<br>";
							
						}
						

						require './includes/page_links.php';
						

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
			id = <?php echo $p_query; ?>;
			function twload(){
				document.querySelector("input").outerHTML = '<iframe src="https://turbowarp.org/' + id + '/embed?addons=pause,gamepad,mute-project,drag-drop&settings-button" width="499" height="416" allowtransparency="true" frameborder="0" scrolling="no" allowfullscreen alt="TurboWarp embed"></iframe>';
			}
		//-->
		</script>
		
		<?php include "footer.php" ?>
	</font></body>
</html>