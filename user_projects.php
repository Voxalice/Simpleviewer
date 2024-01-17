<?php


require './includes/replit.php';
require './includes/page.php';
require './includes/functions.php';


# Page setup

$u_query = $_GET['u'] ?? null;

$u_query = htmlspecialchars($u_query);


?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Simpleviewer - Projects by <?= $u_query ?></title>
		<link rel="stylesheet" href="/style.css">
		<meta charset="UTF-8">
		<meta name="description" content="View Scratch without JavaScript or a modern browser">
		<meta name="keywords" content="Scratch, MIT, Legacy, Compatibility, Accessibility">
		<meta name="author" content="Voxalice">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body bgcolor="#222"><font color="white" face="sans-serif">
		<?php


			# Get project results

			$user_p = @file_get_contents(p("https://api.scratch.mit.edu/users/{$u_query}/projects?limit=20&offset={$page_offset}"));

			$user_p_json = json_decode($user_p, true);

			echo "<h1>Projects by <a href='/users/{$u_query}' class='userlink'>{$u_query}</a></h1>";


			# Check if search results were returned

			if ($u_query !== NULL && str_contains($user_p, "comments_allowed")) {


				foreach($user_p_json as $key => $value) {


					# Echo search results

					echo sview_result($value['id'], $value['title'], $u_query);


				}


				require './includes/page_links.php';


			} else {


				echo "<p>No projects</p><br>";


			}


		?>

		<?php include "footer.php"; ?>
	</font></body>
</html>