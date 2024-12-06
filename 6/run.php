#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	$guard = findCells($map, '^')[0];
	$guard[2] = '^';

	$directions = [];
	$directions['>'] = [1, 0, 'v'];
	$directions['<'] = [-1, 0, '^'];
	$directions['v'] = [0, 1, '<'];
	$directions['^'] = [0, -1, '>'];

	function getVisitedPositions($map, $guard, $trackVisits = true, $obstacle = null) {
		global $directions;

		$isLoop = false;
		$previousState = [];
		$visited = [];
		while (true) {
			[$x, $y, $face] = $guard;
			[$dx, $dy, $nextFace] = $directions[$face];

			if ($trackVisits) { $visited["{$x},{$y}"] = [$x, $y]; }

			$nx = $x + $dx;
			$ny = $y + $dy;

			if (!isset($map[$ny][$nx])) {
				$isLoop = false;
				break;
			}

			if ($map[$ny][$nx] == '#' || $obstacle === [$nx, $ny]) {
				$guard = [$x, $y, $nextFace];

				$thisState = "{$x},{$y},{$face}";
				if (isset($previousState[$thisState])) {
					$isLoop = true;
					break;
				}
				$previousState[$thisState] = True;
			} else {
				$guard = [$nx, $ny, $face];
			}
		}

		return [$visited, $isLoop];
	}

	$visitedMap = getVisitedPositions($map, $guard)[0];
	$part1 = count($visitedMap);
	echo 'Part 1: ', $part1, "\n";

	$part2 = 0;
	foreach ($visitedMap as $pos) {
		$looped = getVisitedPositions($map, $guard, false, $pos)[1];
		if ($looped) {
			$part2++;
		}
	}
	echo 'Part 2: ', $part2, "\n";
