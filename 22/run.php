#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$part1 = 0;

	function getNextSecretNumber($secret) {
		// Multiply by 64 and XOR with input
		// mod 16777216
		$secret = ($secret ^ ($secret << 6)) & 16777215;

		// Divide by 32 and XOR with input
		$secret = ($secret ^ ($secret >> 5));

		// Multiply by 2048 and XOR with input
		// mod 16777216
		$secret = ($secret ^ ($secret << 11)) & 16777215;

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

	//
