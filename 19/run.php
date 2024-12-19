#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$patterns = getInputLines();
	$towels = array_map(fn($x) => trim($x), explode(',', array_shift($patterns)));

	function producePattern($pattern, $towels) {
		$possible = [];
		$possible[''] = [''];

		while (!empty($possible)) {
			[$current] = array_shift($possible);

			foreach ($towels as $t) {
				$attempt = $current . $t;

				if ($attempt == $pattern) { return TRUE; }

				if (str_starts_with($pattern, $attempt)) {
					$possible[$attempt] = [$attempt];
				}
			}
		}

		return FALSE;
	}

	$part1 = 0;
	foreach ($patterns as $p) {
		echo "Attempting Pattern: {$p}";
		$isPossible = producePattern($p, $towels);

		echo ($isPossible ? ' => Yes!' : ''), "\n";

		if ($isPossible) {
			$part1++;
		}
	}

	echo 'Part 1: ', $part1, "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
