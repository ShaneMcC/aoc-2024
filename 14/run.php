#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$robots = [];
	foreach ($input as $line) {
		preg_match('#^p=(.*),(.*) v=(.*),(.*)$#ADi', $line, $m);
		[$all, $px, $py, $vx, $vy] = $m;
		$robots[] = [$px, $py, $vx, $vy];
	}

	$mapMaxX = isTest() ? 11 : 101;
	$mapMaxY = isTest() ? 7 : 103;

	function getRobotPosition($robot, $seconds) {
		global $mapMaxX, $mapMaxY;

		[$px, $py, $vx, $vy] = $robot;

		$px += ($vx * $seconds);
		$py += ($vy * $seconds);

		if ($px > $mapMaxX) { $px = $px % $mapMaxX; }
		if ($py > $mapMaxY) { $py = $py % $mapMaxY; }

		while ($px < 0) { $px += $mapMaxX; }
		while ($py < 0) { $py += $mapMaxY; }

		return [$px, $py];
	}

	$q0 = $q1 = $q2 = $q3 = 0;

	$halfX = (int)floor($mapMaxX / 2);
	$halfY = (int)floor($mapMaxY / 2);

	$part1 = $part2 = 0;

	for ($i = 0; $i < 10000; $i++) {
		$map = array_fill(0, $mapMaxY+1, array_fill(0, $mapMaxX+1, null));

		// Secret property of the inputs...
		// The easter-egg is the first frame with no overlaps.
		// I hate it.
		$hasOverlaps = false;
		foreach ($robots as $robot) {
			[$px, $py] = getRobotPosition($robot, $i);

			if ($i == 100) {
				if ($px < $halfX && $py < $halfY) { $q0++; }
				if ($px < $halfX && $py > $halfY) { $q1++; }
				if ($px > $halfX && $py < $halfY) { $q2++; }
				if ($px > $halfX && $py > $halfY) { $q3++; }
			}

			$map[$py][$px]++;
			if ($map[$py][$px] > 1) {
				$hasOverlaps = True;
				if ($i != 100 && !isDebug()) {
					break;
				}
			}
		}

		if (isDebug()) {
			drawSparseMap($map, '.', true, $i);
		}

		if ($i == 100) {
			$part1 = $q0 * $q1 * $q2 * $q3;
			echo 'Part 1: ', $part1, "\n";
		}

		if (!$hasOverlaps) {
			$part2 = $i;
			break;
		}
	}

	echo 'Part 2: ', $part2, "\n";
