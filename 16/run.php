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
					$locations = [$end];
				}

				if ($cost == $maxCost) {
					$locations = array_unique(array_merge($locations, $steps), SORT_REGULAR);
				}
			}

			[$dX, $dY, $possibleDirections] = $directions[$direction];
			[$tX, $tY] = [$x + $dX, $y + $dY];

			if ($map[$tY][$tX] != '#') {
				$queue->insert([$tX, $tY, $direction, array_merge($steps, [[$x, $y]])], -($cost + 1));
			}
			foreach ($possibleDirections as $pd) {
				$queue->insert([$x, $y, $pd, $steps], -($cost + 1000));
			}
		}

		return [$maxCost, count($locations)];
	}

	[$part1, $part2] = getPathCost($map, $start, $end);
	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
