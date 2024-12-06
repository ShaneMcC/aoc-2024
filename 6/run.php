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

	function getVisitedPositions($map, $guard, $trackVisits = true, $obstacle = null, $previousState = []) {
        global $DODEBUG;
		global $directions;

		$isLoop = false;
		$visited = [];
		while (true) {
			[$x, $y, $face] = $guard;
			[$dx, $dy, $nextFace] = $directions[$face];
			[$nx, $ny] = [$x + $dx, $y + $dy];

			if ($trackVisits) { $visited["{$x},{$y},{$face}"] = [$x, $y, $face]; }

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
	$uniquePositions = [];
	foreach ($visitedMap as $pos) {
		[$x, $y, $face] = $pos;
		$uniquePositions["{$x},{$y}"] = true;
	}
	$part1 = count($uniquePositions);
	echo 'Part 1: ', $part1, "\n";

	$part2 = 0;
	$previousPositions = [];
    $previousSteps = [];
	$uniqueObstaclePositions = [];
	foreach ($visitedMap as $posKey => $pos) {
		[$x, $y, $face] = $pos;
		[$dx, $dy, $nextFace] = $directions[$face];
		[$nx, $ny] = [$x + $dx, $y + $dy];

        if (isset($previousSteps["{$nx},{$ny}"])) { continue; }
		$looped = getVisitedPositions($map, $pos, false, [$nx, $ny], $previousPositions)[1];
		if ($looped) {
			$uniqueObstaclePositions["{$nx},{$ny}"] = True;
		}
		$previousPositions[$posKey] = True;
        $previousSteps["{$nx},{$ny}"] = True;
	}
	$part2 = count($uniqueObstaclePositions);
	echo 'Part 2: ', $part2, "\n";
