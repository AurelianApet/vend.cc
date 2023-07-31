<?php

require_once('countries.php');
require_once('bins2cc.php');

$country_renames = [
	'U.K.' => 'UK',
	'Great Britain' => 'UK',
];

$cards = $_REQUEST['cards'];

$card_lines = explode("\n", $cards);

$results = [];

foreach ($card_lines as $card) {
	$card = trim($card);
	// $card = preg_replace('/\D/', '', $card);
	$bin = substr($card, 0, 6);

	$cc = isset($bins2cc[$bin]) ? $bins2cc[$bin] : null;
	if ($cc && isset($countries[$cc])) {
		
		$country = $countries[$cc];
		
		if (isset($country_renames[$country])) {
			$country = $country_renames[$country];
		}

		$results[$country][] = "$card|$country";
	}
}
