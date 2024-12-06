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

		$count = 0;
		while (true) {
			[$x, $y, $face] = $guard;
			[$dx, $dy, $nextFace] = $directions[$face];

			$map[$y][$x] = 'x';

			if (!isset($map[$y + $dy][$x + $dx])) { break; }
			$next = $map[$y + $dy][$x + $dx];

			if ($next == '#') {
				$guard = [$x, $y, $nextFace];
			} else {
				$guard = [$x + $dx, $y + $dy, $face];
			}
		}

		return findCells($map, 'x');
	}

	$visitedMap = getVisitedPositions($map, $guard);
	$part1 = count($visitedMap);
	echo 'Part 1: ', $part1, "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
