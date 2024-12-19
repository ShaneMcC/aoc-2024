#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$patterns = getInputLines();
	$towels = array_map(fn($x) => trim($x), explode(',', array_shift($patterns)));

	function producePattern($pattern, $towels) {
		$possible = [];
		$possible[''] = ['', 1];

		$final = 0;

		while (!empty($possible)) {
			[$current, $count] = array_shift($possible);

			foreach ($towels as $t) {
				$attempt = $current . $t;

				if ($attempt == $pattern) { $final += $count; continue; }

				if (str_starts_with($pattern, $attempt)) {
					if (isset($possible[$attempt])) {
						$possible[$attempt][1] += $count;
					} else {
						$possible[$attempt] = [$attempt, $count];
					}
				}
			}
		}

		return $final;
	}

	$part1 = $part2 = 0;
	foreach ($patterns as $p) {
		$isPossible = producePattern($p, $towels);

		$part1 += ($isPossible > 0);
		$part2 += $isPossible;
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
