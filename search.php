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


if (str_contains($_SERVER['REQUEST_URI'], "+")) {

	$uri_search_fixed = implode(" ", explode("+", $_SERVER['REQUEST_URI']));

	header("Location: {$uri_search_fixed}");
	
	die();
	
}


?>
<!DOCTYPE html>
<html lang="en-US">
	<head>
		<title>Simpleviewer - Searching for <?= @$_GET['q'] ?></title>
		<link rel="stylesheet" href="/style.css">
		<meta charset="UTF-8">
		<meta name="description" content="View Scratch without JavaScript or a modern browser">
		<meta name="keywords" content="Scratch, MIT, Legacy, Compatibility, Accessibility">
		<meta name="author" content="Voxalice">
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
	</head>
	<body bgcolor="#222"><font color="#fff" face="sans-serif">
		<?php


			# Read 'page' as query parameter

			if (@round(@$_GET["page"]) > 0) {

				$page = @round(@$_GET["page"]);

			} else {

				$page = 1;

			}

			$page_offset = $page - 1;

			$page_offset = $page_offset * 16;

	
			# Get search results

			$search_query = urlencode($_GET['q']); # >:(

			$search = @file_get_contents(p("https://api.scratch.mit.edu/search/projects?limit=16&offset={$page_offset}&language=en&mode=popular&q={$search_query}"));

			$search_json = json_decode($search, true);

			$search_query_echo = str_replace('+', ' ', $search_query);

			echo "<h1>Search: {$search_query_echo}</h1>";
		
		
			# Check if search results were returned

			if (str_contains($search, "{")) {


				foreach($search_json as $key => $value) {

					
					# Echo search results
					
					echo "<div class='result'><a href='/projects/{$value['id']}'><img src='{$value['images']['100x80']}' width='100' height='80'><br><br><b>\"{$value['title']}\"</b></a>, by <a href='/users/{$value['author']['username']}'>{$value['author']['username']}</a> (Project #{$value['id']})</div><br>";
					

				}


				# Add page buttons

				$page_previous = $page - 1;
				$page_next = $page + 1;

				# Construct previous page link
				
				$query = $_GET;

				// replace parameter(s)
				$query['page'] = $page_previous;

				// rebuild url
				$query_result_previous = "/search.php?" . http_build_query($query);

				# Construct next page link

				$query = $_GET;

				// replace parameter(s)
				$query['page'] = $page_next;

				// rebuild url
				$query_result_next = "/search.php?" . http_build_query($query);
				

				# Echo page buttons / links

				if ($page > 1) {

					echo "<a href='{$query_result_previous}'>&lt; Previous page</a> | ";

				}

				echo "<a href='{$query_result_next}'>Next page &gt;</a><br><br>";


			} else {


				echo "<p>No results :(</p><br>";


			}

					
		?>

		<form action="/search.php">
			<label for="simpleviewer-search"><b>Search for another project:</b></label> <input type="search" id="simpleviewer-search" name="q" value="<?= $_GET['q'] ?>"> <input type="submit" value="Submit">
		</form>

		<?php include "footer.php"; ?>
	</font></body>
</html>