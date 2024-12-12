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

	$regiontypes = [];
	$regionareas = [];
	$regionperimiters = [];
	$sides = [];

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
		$regiontypes[$myregion] = $map[$y][$x];
	}


	foreach ($regionareas as $checkregion => $_) {
		for ($y = 0; $y < count($map); $y++) {
			$lastCellBefore = Null;
			$lastCellAfter = Null;
			for ($x = 0; $x < count($map[0]); $x++) {
				$myregion = $regionmap[$y][$x];
				$before = $regionmap[$y - 1][$x] ?? '.';
				$after = $regionmap[$y + 1][$x] ?? '.';

				if ($myregion == $checkregion) {
					$cellBefore = ($before == $myregion);
					$cellAfter = ($after == $myregion);

					if ($cellBefore !== $lastCellBefore && $before != $myregion) {
						$sides[$myregion] = ($sides[$myregion] ?? 0) + 1;
					}

					if ($cellAfter !== $lastCellAfter && $after != $myregion) {
						$sides[$myregion] = ($sides[$myregion] ?? 0) + 1;
					}
				} else { $cellBefore = Null; $cellAfter = Null; }

				$lastCellBefore = $cellBefore;
				$lastCellAfter = $cellAfter;
			}
		}

		for ($x = 0; $x < count($map[0]); $x++) {
			$lastCellBefore = Null;
			$lastCellAfter = Null;
			for ($y = 0; $y < count($map); $y++) {
				$myregion = $regionmap[$y][$x];
				$before = $regionmap[$y][$x - 1] ?? '.';
				$after = $regionmap[$y][$x + 1] ?? '.';

				if ($myregion == $checkregion) {
					$cellBefore = ($before == $myregion);
					$cellAfter = ($after == $myregion);

					if ($cellBefore !== $lastCellBefore && $before != $myregion) {
						$sides[$myregion] = ($sides[$myregion] ?? 0) + 1;
					}

					if ($cellAfter !== $lastCellAfter && $after != $myregion) {
						$sides[$myregion] = ($sides[$myregion] ?? 0) + 1;
					}
				} else { $cellBefore = Null; $cellAfter = Null; }

				$lastCellBefore = $cellBefore;
				$lastCellAfter = $cellAfter;
			}
		}
	}

	$part1 = $part2 = 0;

	foreach ($regionareas as $regionid => $area) {
		$perimiter = $regionperimiters[$regionid] ?? 0;
		$side = ($sides[$regionid] ?? 0);
		$type = $regiontypes[$regionid] ?? 0;

		if (isDebug()) {
			echo "Region: {$regionid} of plant type {$type} has area {$area}, perimiter {$perimiter} and sides {$side}\n";
		}

		$part1 += ($area * $perimiter);
		$part2 += ($area * $side);
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
