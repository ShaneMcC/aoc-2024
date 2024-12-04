#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	function hasXMAS($map, $x, $y) {
		$count = 0;
		foreach (getAdjacentDirections(true, false) as $dir) {
			$word = '';
			for ($i = 0; $i < 4; $i++) {
				$word .= $map[$y + ($dir[1] * $i)][$x + ($dir[0] * $i)] ?? '.';
			}
			if ($word == 'XMAS') {
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

	foreach (cells($map) as [$x, $y, $cell]) {
		if ($cell == 'X') {
			$part1 += hasXMAS($map, $x, $y);
		}

		if ($cell == 'A' && hasCrossedMAS($map, $x, $y)) {
			$part2++;
		}
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
