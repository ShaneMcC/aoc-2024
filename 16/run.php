#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	$directions = [];
	$directions['N'] = [0, -1, ['W', 'E']];
	$directions['E'] = [1, 0, ['N', 'S']];
	$directions['S'] = [0, 1, ['E', 'W']];
	$directions['W'] = [-1, 0, ['S', 'N']];

	$start = findCells($map, 'S')[0];
	$end = findCells($map, 'E')[0];

	function getPathCost($map, $start, $end) {
		global $directions;

		$queue = new SPLPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
		$queue->insert([$start[0], $start[1], 'E', []], 0);

		$locations = [];
		$costs = [];

		$maxCost = PHP_INT_MAX;

		while (!$queue->isEmpty()) {
			$q = $queue->extract();
			[$x, $y, $direction, $steps] = $q['data'];
			$cost = abs($q['priority']);

			$costs[$y][$x][$direction] = min($cost, $costs[$y][$x][$direction] ?? PHP_INT_MAX);
			if ($cost > $costs[$y][$x][$direction]) { continue; }

			if ([$x, $y] == $end) {
				if ($cost < $maxCost) {
					$maxCost = min($cost, $maxCost);
					$locations["{$end[0]},{$end[1]}"] = True;
				}

				if ($cost == $maxCost) {
					$locations = array_merge($locations, $steps);
				}
			}

			[$dX, $dY, $possibleDirections] = $directions[$direction];
			[$tX, $tY] = [$x + $dX, $y + $dY];

			$steps["{$x},{$y}"] = True;

			if ($map[$tY][$tX] != '#') {
				if (!array_key_exists("{$tX},{$tY}", $steps)) {
					$queue->insert([$tX, $tY, $direction, $steps], -($cost + 1));
				}
			}
			foreach ($possibleDirections as $pd) {
				[$pdX, $pdY] = $directions[$pd];
				[$ptX, $ptY] = [$x + $pdX, $y + $pdY];

				if ($map[$ptY][$ptX] != '#') {
					if (!array_key_exists("{$ptX},{$ptY}", $steps)) {
						$queue->insert([$ptX, $ptY, $pd, $steps], -($cost + 1001));
					}
				}
			}
		}

		return [$maxCost, count($locations)];
	}

	[$part1, $part2] = getPathCost($map, $start, $end);
	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
