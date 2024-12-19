#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$patterns = getInputLines();
	$towels = array_map(fn($x) => trim($x), explode(',', array_shift($patterns)));

	function producePattern($pattern, $towels) {
		$key = json_encode([__FILE__, __LINE__, func_get_args()]);

		return storeCachedResult($key, function() use ($pattern, $towels) {
			$final = 0;

			foreach ($towels as $t) {
				if ($t == $pattern) {
					$final += 1;
				} else if (str_starts_with($pattern, $t)) {
					$final += producePattern(substr($pattern, strlen($t)), $towels);
				}
			}

			return $final;
		});
	}

	$part1 = $part2 = 0;
	foreach ($patterns as $p) {
		$isPossible = producePattern($p, $towels);

		$part1 += ($isPossible > 0);
		$part2 += $isPossible;
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
