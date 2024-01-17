<?php


require './includes/replit.php';
require './includes/page.php';
require './includes/functions.php';


# Page setup

$q_query = $_GET['q'] ?? null;

if (str_contains($_SERVER['REQUEST_URI'], "+")) {

	$uri_search_fixed = implode(" ", explode("+", $_SERVER['REQUEST_URI']));

	header("Location: {$uri_search_fixed}");
	
	die();
	
}


?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Simpleviewer - Searching for <?= $q_query ?></title>
		<link rel="stylesheet" href="/style.css">
		<meta charset="UTF-8">
		<meta name="description" content="View Scratch without JavaScript or a modern browser">
		<meta name="keywords" content="Scratch, MIT, Legacy, Compatibility, Accessibility">
		<meta name="author" content="Voxalice">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body bgcolor="#222"><font color="white" face="sans-serif">
		<?php


			# Fix page offset

			$page_offset = $page - 1;

			$page_offset = $page_offset * 16;

	
			# Get search results

			$search_query = @urlencode($q_query);

			$search = @file_get_contents(p("https://api.scratch.mit.edu/search/projects?limit=16&offset={$page_offset}&language=en&mode=popular&q={$search_query}"));

			$search_json = json_decode($search, true);

			$search_query_echo = str_replace('+', ' ', $search_query);

			echo "<h1>Search: {$search_query_echo}</h1>";
		
		
			# Check if search results were returned

			if ($q_query !== NULL && str_contains($search, "comments_allowed")) {


				foreach($search_json as $key => $value) {

					
					# Echo search results
					
					echo sview_result($value['id'], $value['title'], $value['author']['username']);
					

				}


				require './includes/page_links.php';


			} else {


				echo "<p>No results :(</p><br>";


			}

					
		?>

		<form action="/search.php">
			<label for="simpleviewer-search"><b>Search for another project:</b></label> <input type="search" id="simpleviewer-search" name="q" value="<?= $q_query ?>"> <input type="submit" value="Submit">
		</form>

		<?php include "footer.php"; ?>
	</font></body>
</html>