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

	function getNextPosition($map, $guard, $obstacle = null) {
		global $directions;

		[$x, $y, $face] = $guard;
		[$dx, $dy, $nextFace] = $directions[$face];
		[$nx, $ny] = [$x + $dx, $y + $dy];

		if (($map[$ny][$nx] ?? false) === '#' || $obstacle === [$nx, $ny]) {
			return [$x, $y, $nextFace, true];
		}

		return [$nx, $ny, $face, false];
	}

	function getVisitedPositions($map, $guard) {
		$visitedAll = [];
		$visitedUnique = [];
		while (isset($map[$guard[1]][$guard[0]])) {
			[$x, $y, $face] = $guard;

			$visitedAll["{$x},{$y},{$face}"] = [$x, $y, $face];
			$visitedUnique["{$x},{$y}"] = True;

			$guard = getNextPosition($map, $guard);
		}

		return [$visitedAll, $visitedUnique];
	}

	function willLoop($map, $guard, $obstacle = null, $previousState = []) {
		while (isset($map[$guard[1]][$guard[0]])) {
			[$x, $y, $face] = $guard;

			$guard = getNextPosition($map, $guard, $obstacle);
			if ($guard[3]) {
				$thisState = "{$x},{$y},{$face}";
				if (isset($previousState[$thisState])) {
					return True;
				}
				$previousState[$thisState] = True;
			}
		}

		return false;
	}

	function checkObstacleLocations($map, $knownRoute) {
		global $directions;

		$previousPositions = [];
		$previousLocations = [];
		$uniqueObstaclePositions = [];
		foreach ($knownRoute as $posKey => $pos) {
			[$x, $y, $face] = $pos;
			[$dx, $dy] = $directions[$face];
			[$nx, $ny] = [$x + $dx, $y + $dy];

			if (isset($previousLocations["{$nx},{$ny}"])) { continue; }
			$looped = willLoop($map, $pos, [$nx, $ny], $previousPositions);
			if ($looped) {
				$uniqueObstaclePositions["{$nx},{$ny}"] = True;
			}
			$previousPositions[$posKey] = True;
			$previousLocations["{$nx},{$ny}"] = True;
		}

		return $uniqueObstaclePositions;
	}

	[$visitedAll, $visitedUnique] = getVisitedPositions($map, $guard);
	$part1 = count($visitedUnique);
	echo 'Part 1: ', $part1, "\n";

	$part2 = count(checkObstacleLocations($map, $visitedAll));
	echo 'Part 2: ', $part2, "\n";
