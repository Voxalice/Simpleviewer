<?php

# Project redirect

if (str_contains($_SERVER['REQUEST_URI'], '/projects/')) {

	$uri_split = explode("/", $_SERVER['REQUEST_URI']);

	header("Location: /project.php?p={$uri_split[2]}");
	die();

# User redirect
	
} else if (str_contains($_SERVER['REQUEST_URI'], '/users/')) {

	$uri_split = explode("/", $_SERVER['REQUEST_URI']);

	header("Location: /user.php?u={$uri_split[2]}");
	die();

# Studio redirect
	
} else if (str_contains($_SERVER['REQUEST_URI'], '/studios/')) {

	$uri_split = explode("/", $_SERVER['REQUEST_URI']);

	header("Location: /studio.php?s={$uri_split[2]}");
	die();

# Project search redirect
	
} else if (str_contains($_SERVER['REQUEST_URI'], '/search/projects')) {

	$uri_q_split = explode("?", $_SERVER['REQUEST_URI']);

	header("Location: /search.php?{$uri_q_split[1]}");
	die();

# Index page
	
} else if ($_SERVER['REQUEST_URI'] == '/' or $_SERVER['REQUEST_URI'] == '') {

	$footer = @file_get_contents("./footer.php");

	echo <<<EOD
	<!DOCTYPE html>
	<html lang="en-US">
		<head>
			<title>Simpleviewer - Homepage</title>
			<link rel="stylesheet" href="/style.css">
			<meta charset="UTF-8">
			<meta name="description" content="View Scratch without JavaScript or a modern browser">
			<meta name="keywords" content="Scratch, MIT, Legacy, Compatibility, Accessibility">
			<meta name="author" content="Voxalice">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
		</head>
		<body bgcolor="#222"><font color="white" face="sans-serif">
			<h1>Simpleviewer</h1>
			<p>Simpleviewer is a website that allows you to view pages on Scratch without requiring JavaScript or loading large resources.<br><br>This website was made for people with low-end devices, legacy browsers, and poor internet connections.</p>

			<hr><br>

			<form action="/project.php">
				<label for="p"><b>Input a Scratch project ID:</b></label><br><br>
				<input type="text" id="p" name="p" value="104"> <input type="submit" value="Submit">
			</form><br>or...<br><br>
			<form action="/user.php">
				<label for="u"><b>Input a Scratch username:</b></label><br><br>
				<input type="text" id="u" name="u" value="griffpatch"> <input type="submit" value="Submit">
			</form><br>or...<br><br>
			<form action="/search.php">
				<label for="simpleviewer-search"><b>Search for a Scratch project by name:</b></label><br><br>
				<input type="search" id="simpleviewer-search" name="q" value="Scratchnapped"> <input type="submit" value="Submit">
			</form><br>or...<br><br>
		<form action="/studio.php">
			<label for="s"><b>Input a Scratch studio ID:</b></label><br><br>
			<input type="text" id="s" name="s" value="31478892"> <input type="submit" value="Submit">
		</form>

			<p>Click <a href="//github.com/Voxalice/Simpleviewer">here</a> to view this website's source code on GitHub.</p>

			$footer
		</font></body>
	</html>
	EOD;

# 404 (no matching pages were found)
	
} else {

	http_response_code(404);

	$footer = @file_get_contents("./footer.php");

	echo <<<EOD
	<!DOCTYPE html>
	<html lang="en-US">
		<head>
			<title>Simpleviewer - 404</title>
			<link rel="stylesheet" href="/style.css" />
			<meta charset="UTF-8">
			<meta name="description" content="View Scratch without JavaScript or a modern browser">
			<meta name="keywords" content="Scratch, MIT, Legacy, Compatibility, Accessibility">
			<meta name="author" content="Voxalice">
			<meta name="viewport" content="width=device-width, initial-scale=1.0">
		</head>
		<body bgcolor="#222"><font color="white" face="sans-serif">
			<h1>404 Page Not Found</h1>
			<p>Sorry, Simpleviewer couldn't find the page you were looking for.<br><br>(Make sure you didn't misspell anything in the URL!)</p>

			$footer
		</font></body>
	</html>
	EOD;

}

?>