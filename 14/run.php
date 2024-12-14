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

		$px = wrapmod($px + ($vx * $seconds), $mapMaxX);
		$py = wrapmod($py + ($vy * $seconds), $mapMaxY);

		return [$px, $py];
	}

	function getRobotMap($robots, $position = 100, $failOnOverlap = false) {
		global $mapMaxX, $mapMaxY;

		$map = array_fill(0, $mapMaxY+1, array_fill(0, $mapMaxX+1, null));

		$q0 = $q1 = $q2 = $q3 = 0;
		$halfX = (int)floor($mapMaxX / 2);
		$halfY = (int)floor($mapMaxY / 2);

		foreach ($robots as $robot) {
			[$px, $py] = getRobotPosition($robot, $position);

			$map[$py][$px]++;
			if ($map[$py][$px] > 1) {
				if ($failOnOverlap) { return [false, 0]; }
			}

			if ($px < $halfX && $py < $halfY) { $q0++; }
			if ($px < $halfX && $py > $halfY) { $q1++; }
			if ($px > $halfX && $py < $halfY) { $q2++; }
			if ($px > $halfX && $py > $halfY) { $q3++; }
		}

		$safetyCode = $q0 * $q1 * $q2 * $q3;

		return [$map, $safetyCode];
	}

	function hasTree($map) {
		if (is_array($map)) {
			// Terrible check for a tree.
			foreach ($map as $row) {
				if (strstr(implode(',', $row), '1,1,1,1,1,1,1,1,1,1,1')) {
					return true;
				}
			}
		}

		return false;
	}

	$part1 = getRobotMap($robots)[1];
	echo 'Part 1: ', $part1, "\n";

	for ($i = 0; $i < 10000; $i++) {
		[$map, $safetyCode] = getRobotMap($robots, $i, true);

		if (hasTree($map)) {
			$part2 = $i;
			break;
		}
	}

	echo 'Part 2: ', $part2, "\n";
