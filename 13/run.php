#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$prizes = [];

	foreach ($input as $prizedata) {
		$prize = [];
		foreach ($prizedata as $line) {
			if (preg_match('#^Button ([AB]): X\+([\d]+), Y\+([\d]+)$#', $line, $m)) {
				$prize[$m[1]] = ['x' => $m[2], 'y' => $m[3]];
			} else if (preg_match('#^Prize: X=([\d]+), Y=([\d]+)$#', $line, $m)) {
				$prize['x'] = $m[1];
				$prize['y'] = $m[2];
			}
		}
		$prizes[] = $prize;
	}

	function calculateTokens($prize) {
		[$tX, $tY] = [$prize['x'], $prize['y']];
		[$aX, $aY] = [$prize['A']['x'], $prize['A']['y']];
		[$bX, $bY] = [$prize['B']['x'], $prize['B']['y']];

		$aCost = 3;
		$bCost = 1;

		$minCost = FALSE;

		for ($i = 1; $i <= 100; $i++) {
			for ($j = 1; $j <= 100; $j++) {
				$thisX = ($aX * $i) + ($bX * $j);
				$thisY = ($aY * $i) + ($bY * $j);
				$thisCost = ($aCost * $i) + ($bCost * $j);

				if ([$thisX, $thisY] == [$tX, $tY]) {
					if ($minCost === FALSE) { $minCost = $thisCost; }
					else { $minCost = min($minCost, $thisCost); }
				}
			}
		}

		return $minCost;
	}

	$part1 = 0;

	foreach ($prizes as $prize) {
		$part1 += calculateTokens($prize);
	}

	echo 'Part 1: ', $part1, "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
