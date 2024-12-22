#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$part1 = 0;

	function mix($input, $mixer) {
		return $input ^ $mixer;
	}

	function prune($input) {
		return $input % 16777216;
	}

	function getNextSecretNumber($secret) {
		$secret = prune(mix($secret, ($secret * 64)));
		$secret = mix($secret, (int)floor($secret / 32));
		$secret = prune(mix($secret, ($secret * 2048)));

		return $secret;
	}

	$part1 = 0;
	foreach ($input as $num) {
		$result = $num;
		for ($i = 0; $i < 2000; $i++) { $result = getNextSecretNumber($result); }

		$part1+= $result;
		if (isDebug()) { echo $num, ': ', $result, "\n"; }
	}
	echo 'Part 1: ', $part1, "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
