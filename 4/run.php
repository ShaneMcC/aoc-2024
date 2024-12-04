#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	function hasWORD($map, $x, $y, $wantedWord = 'XMAS') {
		$count = 0;
		foreach (getAdjacentDirections(true, false) as $dir) {
			$word = '';
			for ($i = 0; $i < strlen($wantedWord); $i++) {
				$word .= $map[$y + ($dir[1] * $i)][$x + ($dir[0] * $i)] ?? '.';
			}
			if ($word == $wantedWord) {
				$count++;
			}
		}

		return $count;
	}

	function hasCrossedMAS($map, $x, $y) {
		$first = ($map[$y - 1][$x - 1] ?? '.') . $map[$y][$x] . ($map[$y + 1][$x + 1] ?? '.'); // TL + BR
		$second = ($map[$y - 1][$x + 1] ?? '.') . $map[$y][$x] . ($map[$y + 1][$x - 1] ?? '.'); // TR + BL

		if (in_array($first, ['MAS', 'SAM']) && in_array($second, ['MAS', 'SAM'])) {
			return true;
		}

		return false;
	}


	$part1 = 0;
	$part2 = 0;
	$part1Word = 'XMAS';

	foreach (cells($map) as [$x, $y, $cell]) {
		if ($cell == $part1Word[0]) {
			$part1 += hasWORD($map, $x, $y, $part1Word);
		}

		if ($cell == 'A' && hasCrossedMAS($map, $x, $y)) {
			$part2++;
		}
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
