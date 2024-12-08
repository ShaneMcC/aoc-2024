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

	function getAntiNodes($types, $bounding) {
		[$minX, $minY, $maxX, $maxY] = $bounding;
		$antiNodes = [];
		$harmonicAntiNodes = [];
		foreach ($types as $coords) {
			if (count($coords) == 1) { continue; }

			foreach ($coords as [$x, $y]) {
				$harmonicAntiNodes["{$x},{$y}"] = True;

				foreach ($coords as [$x2, $y2]) {
					if ([$x, $y] == [$x2, $y2]) { continue; }

					$dX = $x2 - $x;
					$dY = $y2 - $y;

					$aX = $x2 + $dX;
					$aY = $y2 + $dY;

					$first = True;
					while ($aX >= $minX && $aX <= $maxX && $aY >= $minY && $aY <= $maxY) {
						if ($first) { $antiNodes["{$aX},{$aY}"] = True; }
						$first = False;
						$harmonicAntiNodes["{$aX},{$aY}"] = True;

						$aX += $dX;
						$aY += $dY;
					}
				}
			}
		}

		return [$antiNodes, $harmonicAntiNodes];
	}

	[$antiNodes, $harmonicAntiNodes] = getAntiNodes($types, $bounding);
	$part1 = count($antiNodes);
	echo 'Part 1: ', $part1, "\n";

	$part2 = count($harmonicAntiNodes);
	echo 'Part 2: ', $part2, "\n";
