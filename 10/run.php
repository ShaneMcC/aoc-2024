#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	function getScoreAndRating($map, $start) {
		$visited = [];
		$points = [$start];
		$nines = [];
		while (!empty($points)) {
			[$x, $y] = array_pop($points);
			if (!isset($visited["{$x},{$y}"])) { $visited["{$x},{$y}"] = 0; }
			$visited["{$x},{$y}"]++;

			$me = $map[$y][$x];

			if ($me == 9) {
				$nines["{$x},{$y}"] = True;
			} else {
				foreach (getAdjacentCells($map, $x, $y, false, false) as [$aX, $aY]) {
					$adj = $map[$aY][$aX];

					if ($adj > $me && $adj-$me == 1) {
						$points[] = [$aX, $aY];
					}
				}
			}
		}

		$rating = 0;
		foreach ($nines as $nine => $_) {
			$rating += $visited[$nine];
		}
		$score = count($nines);

		return [$score, $rating];
	}

	$startingPoints = findCells($map, 0);

	$part1 = $part2 = 0;
	foreach ($startingPoints as $start) {
		[$score, $rating] = getScoreAndRating($map, $start);
		$part1 += $score;
		$part2 += $rating;
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
