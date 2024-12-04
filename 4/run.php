#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	function hasXMAS($map, $x, $y) {
		$directions = [[1, 0],  // Forwards
		               [-1, 0], // Backwards
		               [0, -1], // Up
		               [0, 1], // Down
		               [1, -1], // Diagonal Up Forwards
		               [1, 1], // Diagonal Down Forwards
		               [-1, -1], // Diagonal Up Backwards
		               [-1, 1], // Diagonal Down Backwards
	    ];

		$count = 0;
		foreach ($directions as $dir) {
			$word = '';
			for ($i = 0; $i < 4; $i++) {
				$word .= $map[$y + $dir[1] * $i][$x + $dir[0] * $i] ?? '.';
			}
			if ($word == 'XMAS') {
				$count++;
			}
		}

		return $count;
	}

	function hasX_MAS($map, $x, $y) {
		$tl = $map[$y - 1][$x - 1] ?? '.';
		$tr = $map[$y - 1][$x + 1] ?? '.';
		$bl = $map[$y + 1][$x - 1] ?? '.';
		$br = $map[$y + 1][$x + 1] ?? '.';

		if ((($tl == 'M' && $br == 'S') || ($tl == 'S' && $br == 'M')) && (($tr == 'M' && $bl == 'S') || ($tr == 'S' && $bl == 'M'))) {
			return 1;
		}

		return 0;
	}


	$part1 = 0;
	$part2 = 0;

	foreach (cells($map) as [$x, $y, $cell]) {
		if ($cell == 'X') {
			$part1 += hasXMAS($map, $x, $y);
		}

		if ($cell == 'A') {
			$part2 += hasX_MAS($map, $x, $y);
		}
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
