#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	function getScore($map, $start) {

		$visited = [];
		$points = [$start];

		$score = 0;

		while (!empty($points)) {
			[$x, $y] = array_pop($points);
			if (isset($visited["{$x},{$y}"])) { continue; }

			$visited["{$x},{$y}"] = True;

			$me = $map[$y][$x];

			if ($me == 9) {
				$score++;
			} else {
				foreach (getAdjacentCells($map, $x, $y, false, false) as [$aX, $aY]) {
					$adj = $map[$aY][$aX];

					if ($adj > $me && $adj-$me == 1) {
						$points[] = [$aX, $aY];
					}
				}
			}
		}

		return $score;
	}

	$startingPoints = findCells($map, 0);

	$part1 = 0;
	foreach ($startingPoints as $start) {
		$score = getScore($map, $start);

		$part1 += $score;
	}

	echo 'Part 1: ', $part1, "\n";

	// $part2 = 0
	// echo 'Part 2: ', $part2, "\n";
