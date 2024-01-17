<?php

if (@round(@$_GET["page"]) > 0) {

	$page = @round(@$_GET["page"]);

} else {

	$page = 1;

}

$page_offset = $page - 1;

$page_offset = $page_offset * 20;

?>