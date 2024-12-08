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

	$bounding = getBoundingBox($map);

	function getAntiNodes($types, $bounding, $harmonics = false) {
		[$minX, $minY, $maxX, $maxY] = $bounding;
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

					if ($harmonics) {
						$antiNodes["{$x},{$y}"] = True;
						$antiNodes["{$x2},{$y2}"] = True;
					}

					while ($aX >= $minX && $aX <= $maxX && $aY >= $minY && $aY <= $maxY) {
						$antiNodes["{$aX},{$aY}"] = True;

						$aX += $dX;
						$aY += $dY;

						if (!$harmonics) { break; }
					}
				}
			}
		}

		return $antiNodes;
	}

	$part1 = count(getAntiNodes($types, $bounding));
	echo 'Part 1: ', $part1, "\n";

	$part2 = count(getAntiNodes($types, $bounding, true));
	echo 'Part 2: ', $part2, "\n";
