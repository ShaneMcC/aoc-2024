#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	$start = findCells($map, 'S')[0];
	$end = findCells($map, 'E')[0];

	function getPathCost($map, $start, $end, $max = PHP_INT_MAX) {
		$queue = new SPLPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
		$queue->insert([$start[0], $start[1]], 0);

		$costs = [];

		$adj = getAdjacentDirections();

		while (!$queue->isEmpty()) {
			$q = $queue->extract();
			[$x, $y] = $q['data'];
			$cost = abs($q['priority']);

			if ($cost > $max) { return PHP_INT_MAX; }
			if (isset($costs[$y][$x])) { continue; }
			$costs[$y][$x] = $cost;

			if ([$x, $y] == $end) {
				return $cost;
			}

			foreach ($adj as [$dX, $dY]) {
				[$tX, $tY] = [$x + $dX, $y + $dY];
				if (($map[$tY][$tX] ?? '#') != '#') {
					$queue->insert([$tX, $tY], -($cost + 1));
				}
			}
		}

		return False;
	}

	function getCheats($map) {
		$adj = getAdjacentDirections();
		$cheats = [];

		[$minX, $minY, $maxX, $maxY] = getBoundingBox($map);

		foreach (cells($map) as [$x, $y, $cell]) {
			if ($y == $minY || $x == $minX || $y == $maxY || $x == $maxX) { continue; }

			if ($cell == '#') {
				foreach ($adj as [$dX, $dY]) {
					[$tX, $tY] = [$x + $dX, $y + $dY];

					if ($tY == $minY || $tX == $minX || $tY == $maxY || $tX == $maxX) { continue; }

					if (($map[$tY][$tX] ?? '#') != '#') {
						$cheat = [[$x, $y], [$tX, $tY]];
						sort($cheat);

						[$cX, $cY] = $cheat[0];

						if (($map[$cY][$cX] ?? '@') == '#') {
							$cheats[] = $cheat;
						}
					}
				}
			}
		}

		return array_unique($cheats, SORT_REGULAR);
	}

	$noCheatCost = getPathCost($map, $start, $end);
	$cheats = getCheats($map);

	echo "No Cheat Cost: {$noCheatCost}\n";

	$counters = [];

	$part1 = $part2 = 0;
	$counter = 0;
	$totalCheats = count($cheats);
	foreach ($cheats as [[$tX, $tY], [$tX2, $tY2]]) {
		$counter++;
		echo "\nTrying cheat: {$counter} / {$totalCheats}\n";

		$newMap = $map;
		if (isDebug()) {
			$newMap[$tY][$tX] = '1';
			$newMap[$tY2][$tX2] = '2';
			drawMap($newMap);
		}
		$newMap[$tY][$tX] = '.';
		$newMap[$tY2][$tX2] = '.';

		$cheatCost = getPathCost($newMap, $start, $end, $noCheatCost);

		$cheatDiff = $noCheatCost - $cheatCost;

		echo "\tCheat Cost: {$cheatCost} (Difference: $cheatDiff) \n";

		$counters[$cheatDiff] = ($counters[$cheatDiff] ?? 0) + 1;

		if ($noCheatCost - $cheatCost >= 100) {
			$part1++;
		}
	}

	echo "\n\n";
	ksort($counters);
	foreach ($counters as $d => $c) {
		echo "There are {$c} cheats that save {$d} picoseconds.\n";
	}

	echo 'Part 1: ', $part1, "\n";
