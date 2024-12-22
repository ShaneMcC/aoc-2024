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

	$maxCount = 2000;

	$allSetsOfFour = [];
	$part1 = 0;
	foreach ($input as $m) {
		$secret = $m;
		$bananas = abs($secret % 10);

		$setsOfFour = [];
		$changes = [];
		for ($i = 0; $i < $maxCount; $i++) {
			$secret = getNextSecretNumber($secret);

			$newBananas = abs($secret % 10);
			$diff = $newBananas - $bananas;
			$bananas = $newBananas;

			$changes[] = $diff;
			if (count($changes) == 4) {
				$implode = implode(',', $changes);

				if (!isset($setsOfFour[$implode])) {
					$setsOfFour[$implode] = $bananas;
					$allSetsOfFour[$implode] = ($allSetsOfFour[$implode] ?? 0) + $bananas;
				}

				array_shift($changes);
			}
		}

		$part1 += $secret;
	}
	echo 'Part 1: ', $part1, "\n";

	$part2 = max($allSetsOfFour);
	echo 'Part 2: ', $part2, "\n";
