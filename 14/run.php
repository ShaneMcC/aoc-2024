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

	if (isDebug()) {
		$map = array_fill(0, $mapMaxY, array_fill(0, $mapMaxX, '.'));
		drawMap($map, true);
	}

	$q0 = 0;
	$q1 = 0;
	$q2 = 0;
	$q3 = 0;

	$halfX = (int)floor($mapMaxX / 2);
	$halfY = (int)floor($mapMaxY / 2);

	foreach ($robots as $robot) {
		[$px, $py] = getRobotPosition($robot, 100);

		if ($px < $halfX && $py < $halfY) { $q0++; }
		if ($px < $halfX && $py > $halfY) { $q1++; }
		if ($px > $halfX && $py < $halfY) { $q2++; }
		if ($px > $halfX && $py > $halfY) { $q3++; }

		if (isDebug()) {
			if ($map[$py][$px] == '.') { $map[$py][$px] = 0; }
			$map[$py][$px]++;
		}
	}

	if (isDebug()) { drawMap($map, false); }

	$part1 = $q0 * $q1 * $q2 * $q3;

	// foreach

	echo 'Part 1: ', $part1, "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
