<?php

$uri_split = explode(".", $_SERVER['REQUEST_URI']);

# Add page buttons

$page_previous = $page - 1;
$page_next = $page + 1;

# Construct previous page link

$query = $_GET;

// replace parameter(s)
$query['page'] = $page_previous;

// rebuild url
$query_result_previous = $uri_split[0] . '.php?' . http_build_query($query);

# Construct next page link

$query = $_GET;

// replace parameter(s)
$query['page'] = $page_next;

// rebuild url
$query_result_next = $uri_split[0] . '.php?' . http_build_query($query);

# Echo page buttons / links

if ($page > 1) {

	echo "<a href='{$query_result_previous}'>&lt; Previous page</a> | ";

}

echo "<a href='{$query_result_next}'>Next page &gt;</a><br><br>";

?>