#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$map = [];
	foreach ($input[0] as $row) { $map[] = str_split($row); }
	$intrs = $input[1];

	$directions['^'] = [0, -1];
	$directions['<'] = [-1, 0];
	$directions['>'] = [1, 0];
	$directions['v'] = [0, 1];

	function moveItem(&$map, $cell, $direction) {
		global $directions;

		[$x, $y] = $cell;
		[$dX, $dY] = $directions[$direction];
		[$tX, $tY] = [$x + $dX, $y + $dY];

		$myItem = $map[$y][$x];
		$targetItem = $map[$tY][$tX];

		// echo "Moving item {$myItem} at {$x},{$y} in {$direction} to {$tX},{$tY} onto {$targetItem}\n";

		if ($myItem == '#') { return false; }

		if ($targetItem === '.') {
			$map[$tY][$tX] = $myItem;
			$map[$y][$x] = '.';

			return [$tX, $tY];
		} else if ($targetItem === 'O') {
			$moveResult = moveItem($map, [$tX, $tY], $direction);
			if ($moveResult !== false) {
				$map[$tY][$tX] = $myItem;
				$map[$y][$x] = '.';

				return [$tX, $tY];
			}
		}

		return false;
	}

	function moveAround($map, $instructions) {
		[$rX, $rY] = findCells($map, '@')[0];

		foreach ($instructions as $instructionLine) {
			foreach (str_split($instructionLine) as $ins) {
				$moveResult = moveItem($map, [$rX, $rY], $ins);
				if ($moveResult != false) {
					[$rX, $rY] = $moveResult;
				}
			}
		}

		return $map;
	}

	$finalMap = moveAround($map, $intrs);

	$part1 = 0;

	foreach (findCells($finalMap, 'O') as [$x, $y]) {
		$part1 += (100 * $y) + $x;
	}

	echo 'Part 1: ', $part1, "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
