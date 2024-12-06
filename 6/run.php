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

	function getVisitedPositions($map, $guard) {
		global $directions;

		$isLoop = false;
		$previousState = [];
		$visited = [];
		while (true) {
			[$x, $y, $face] = $guard;
			[$dx, $dy, $nextFace] = $directions[$face];

			$visited["{$x},{$y}"] = true;

			if (!isset($map[$y + $dy][$x + $dx])) {
				$isLoop = false;
				break;
			}
			$next = $map[$y + $dy][$x + $dx];

			if ($next == '#' || $next == 'O') {
				$guard = [$x, $y, $nextFace];

				$thisState = "{$x},{$y},{$face}";
				if (isset($previousState[$thisState])) {
					$isLoop = true;
					break;
				}
				$previousState[$thisState] = True;
			} else {
				$guard = [$x + $dx, $y + $dy, $face];
			}
		}

		return [$visited, $isLoop];
	}

	$visitedMap = getVisitedPositions($map, $guard)[0];
	$part1 = count($visitedMap);
	echo 'Part 1: ', $part1, "\n";

	$part2 = 0;
	foreach ($visitedMap as $v => $_) {
		[$x, $y] = explode(',', $v, 2);
		$newMap = $map;
		$newMap[$y][$x] = 'O';
		[$visitedMap, $looped] = getVisitedPositions($newMap, $guard);
		if ($looped) {
			$part2++;
		}
	}
	echo 'Part 2: ', $part2, "\n";
