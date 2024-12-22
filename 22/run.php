#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	// Magic number for maximum changes to remember in our key
	// this is used to remove older options.
	//
	// We store each set of change as a set of 5 bits in the key.
	// ie `00000 00000 00000 10010` for 0 0 0 9
	// `10010 10000 10000 01110` for 9 8 7 6
	// `10000 10000 01110 01110` for 8 7 6 5
	// etc.
	$maxChangeKey = 1024 * 1024;

	$part1 = 0;

	$allSetsOfFour = [];
	$part1 = $part2 = 0;
	foreach ($input as $m) {
		$secret = $m;
		$bananas = abs($secret % 10);
		$changeKey = 0;

		$setsOfFour = [];
		$changes = [];
		for ($i = 0; $i < 2000; $i++) {
			$secret = ($secret ^ ($secret << 6)) & 16777215; // Multiply by 64 and XOR with input, then mod 16777216
			$secret = ($secret ^ ($secret >> 5)); // Divide by 32 and XOR with input
			$secret = ($secret ^ ($secret << 11)) & 16777215; // Multiply by 2048 and XOR with input, then mod 16777216

			$newBananas = abs($secret % 10);
			$diff = $newBananas - $bananas;
			// Shift the key over 5 to make space for the new number
			// OR it into the number
			// then mask off the upper bits to remove older values
			$changeKey = (($changeKey << 5) | ($diff + 9)) & ($maxChangeKey - 1);

			$bananas = $newBananas;

			if (!isset($setsOfFour[$changeKey])) {
				$setsOfFour[$changeKey] = $bananas;
				$allSetsOfFour[$changeKey] = ($allSetsOfFour[$changeKey] ?? 0) + $bananas;
				$part2 = max($part2, $allSetsOfFour[$changeKey]);
			}
		}

		$part1 += $secret;
	}
	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
