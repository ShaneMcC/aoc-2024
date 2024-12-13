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

	function calculateTokens($prize, $positionModifier = 0) {
		[$tX, $tY] = [$prize['x'] + $positionModifier, $prize['y'] + $positionModifier];
		[$aX, $aY] = [$prize['A']['x'], $prize['A']['y']];
		[$bX, $bY] = [$prize['B']['x'], $prize['B']['y']];

		// https://www.reddit.com/r/adventofcode/comments/1hd7irq/2024_day_13_an_explanation_of_the_mathematics/
		$det = ($aX * $bY) - ($aY * $bX);
		$pressA = (($tX * $bY) - ($tY * $bX)) / $det;
		$pressB = (($aX * $tY) - ($aY * $tX)) / $det;

		if (is_int($pressA) && is_int($pressB)) {
			return ($pressA * 3) + $pressB;
		}

		return 0;
	}

	$part1 = $part2 = 0;

	foreach ($prizes as $prize) {
		$part1 += calculateTokens($prize);
		$part2 += calculateTokens($prize, 10000000000000);
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
