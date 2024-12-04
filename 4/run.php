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

	$part1 = 0;

	foreach (cells($map) as [$x, $y, $cell]) {
		if ($cell == 'X') {
			$part1 += hasXMAS($map, $x, $y);
		}
	}

	echo 'Part 1: ', $part1, "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
