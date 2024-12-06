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
		$previous = [];
		while (true) {
			[$x, $y, $face] = $guard;
			[$dx, $dy, $nextFace] = $directions[$face];

			$map[$y][$x] = 'x';
			$thisPos = "{$x},{$y},{$face}";

			if (!isset($map[$y + $dy][$x + $dx])) {
				$isLoop = false;
				break;
			}
			$next = $map[$y + $dy][$x + $dx];

			if (isset($previous[$thisPos])) {
				$isLoop = true;
				break;
			}

			$previous[$thisPos] = True;

			if ($next == '#' || $next == 'O') {
				$guard = [$x, $y, $nextFace];
			} else {
				$guard = [$x + $dx, $y + $dy, $face];
			}
		}

		return [$map, $isLoop];
	}

	$visitedMap = getVisitedPositions($map, $guard)[0];
	$part1 = count(findCells($visitedMap, 'x'));
	echo 'Part 1: ', $part1, "\n";

	$part2 = 0;
	foreach (cells($visitedMap) as [$x, $y, $cell]) {
		if ($cell == 'x') {
			$newMap = $map;
			$newMap[$y][$x] = 'O';
			[$visitedMap, $looped] = getVisitedPositions($newMap, $guard);
			if ($looped) {
				$part2++;
			}
		}
	}
	echo 'Part 2: ', $part2, "\n";
