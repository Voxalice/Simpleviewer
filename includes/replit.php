<?php

# Replit development code

$replit_support = filter_var(@file_get_contents("./replit.txt"), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE);

function p(string $url_input) {
	if ($GLOBALS['replit_support']) {
		return 'https://apis.scratchconnect.eu.org/free-proxy/get?url=' . $url_input;
	} else {
		return $url_input;
	}
}

?>