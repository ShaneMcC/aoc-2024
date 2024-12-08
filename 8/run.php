#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	$types = [];
	foreach (cells($map) as [$x, $y, $cell]) {
		if ($cell == '.') { continue; }

		if (!isset($types[$cell])) { $types[$cell] = []; }

		$types[$cell][] = [$x, $y];
	}

	[$minX, $minY, $maxX, $maxY] = getBoundingBox($map);

	$antiNodes = [];
	foreach ($types as $type => $coords) {
		// Look at all other locations from us and store as appropraite

		foreach ($coords as [$x, $y]) {
			foreach ($coords as [$x2, $y2]) {
				if ([$x, $y] == [$x2, $y2]) { continue; }

				$dX = $x2 - $x;
				$dY = $y2 - $y;

				$aX = $x2 + $dX;
				$aY = $y2 + $dY;

				if ($aX >= $minX && $aX <= $maxX && $aY >= $minY && $aY <= $maxY) {
					$antiNodes["{$aX},{$aY}"] = True;
				}
			}
		}
	}

	$part1 = $part2 = 0;

	$part1 = count($antiNodes);
	echo 'Part 1: ', $part1, "\n";

	// echo 'Part 2: ', $part2, "\n";
