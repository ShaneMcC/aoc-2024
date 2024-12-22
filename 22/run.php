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

	$monkeys = [];
	foreach ($input as $m) { $monkeys[$m] = []; }

	$allSetsOfFour = [];
	$part1 = 0;
	foreach ($monkeys as $m => $_) {
		$secret = $m;
		$bananas = abs($secret % 10);

		$changes = [];
		for ($i = 0; $i < $maxCount; $i++) {
			$secret = getNextSecretNumber($secret);
			$newBananas = abs($secret % 10);
			$diff = $newBananas - $bananas;
			$bananas = $newBananas;
			$changes[] = [$diff, $bananas];
		}

		$setsOfFour = [];
		for ($i = 0; $i < count($changes) - 3; $i++) {
			$test = [$changes[$i][0], $changes[$i + 1][0], $changes[$i + 2][0], $changes[$i + 3][0]];
			$implode = implode(',', $test);

			if (!isset($setsOfFour[$implode])) {
				$setsOfFour[$implode] = $changes[$i + 3][1];
				$allSetsOfFour[$implode] = ($allSetsOfFour[$implode] ?? 0) + $setsOfFour[$implode];
			}
		}

		$monkey = [];
		// $monkey['changes'] = $changes;
		$monkey['final'] = $secret;
		$monkey['setsOfFour'] = $setsOfFour;
		$monkeys[$m] = $monkey;

		$part1 += $secret;
	}
	echo 'Part 1: ', $part1, "\n";

	$part2 = max($allSetsOfFour);
	echo 'Part 2: ', $part2, "\n";
