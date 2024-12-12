#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	$regionid = 'A';
	$regionmap = [];

	foreach (cells($map) as [$x, $y, $cell]) {
		if (!isset($regionmap[$y])) { $regionmap[$y] = []; }
		$regionmap[$y][$x] = null;
	}

	foreach (cells($map) as [$x, $y, $cell]) {
		if ($regionmap[$y][$x] !== null) {
			continue;
		} else {
			$regionid++;
		}

		$checkcells = [];
		$checkcells[] = [$x, $y];

		while (!empty($checkcells)) {
			[$cx, $cy] = array_shift($checkcells);
			$cc = $map[$cy][$cx];

			if ($regionmap[$cy][$cx] !== null) { continue; }

			if ($cc == $cell) {
				$regionmap[$cy][$cx] = $regionid;

				foreach (getAdjacentCells($map, $cx, $cy, false) as [$ax, $ay]) {
					$checkcells[] = [$ax, $ay];
				}
			}
		}
	}

	$regionareas = [];
	$regionperimiters = [];

	foreach (cells($map) as [$x, $y, $cell]) {
		$adjacent = getAdjacentCells($map, $x, $y, false);

		$adiff = 4;
		foreach ($adjacent as [$ax, $ay]) {
			$acell = $map[$ay][$ax] ?? '.';

			if ($acell == $cell) {
				$adiff--;
			}
		}

		$myregion = $regionmap[$y][$x];
		$regionareas[$myregion] = ($regionareas[$myregion] ?? 0) + 1;
		$regionperimiters[$myregion] = ($regionperimiters[$myregion] ?? 0) + $adiff;
	}

	$part1 = 0;

	foreach ($regionareas as $regionid => $area) {
		$perimiter = $regionperimiters[$regionid];

		$part1 += ($area * $perimiter);
	}

	echo 'Part 1: ', $part1, "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
