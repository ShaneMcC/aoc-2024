#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	$start = findCells($map, 'S')[0];
	$end = findCells($map, 'E')[0];

	function getPathAndCost($map, $start, $end) {
		$queue = new SPLPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
		$queue->insert([$start[0], $start[1], []], 0);

		$costs = [];

		$adj = getAdjacentDirections();

		while (!$queue->isEmpty()) {
			$q = $queue->extract();
			[$x, $y] = $q['data'];
			$cost = abs($q['priority']);

			if (isset($costs[$y][$x])) { continue; }
			$path["{$x},{$y}"] = True;
			$costs[$y][$x] = $cost;

			if ([$x, $y] == $end) {
				return [$cost, $path];
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

	function getManhattenPoints($x, $y, $wantedMan) {
		$possible = [];

		for ($tX = $x-$wantedMan; $tX <= $x + $wantedMan; $tX++) {
			for ($tY = $y-$wantedMan; $tY <= $y + $wantedMan; $tY++) {
				$man = manhattan($x, $y, $tX, $tY);
				if ($man <= $wantedMan) {
					$possible[] = [$tX, $tY, $man];
				}
			}
		}

		return $possible;
	}

	function findCheatOptions($costMap, $path, $cheatLen = 2, $wantedBenefit = 0) {
		$options = [];

		foreach ($path as $point => $_) {
			[$x, $y] = explode(',', $point);

			$myCost = $costMap[$y][$x];

			foreach (getManhattenPoints($x, $y, $cheatLen) as [$tX, $tY, $man]) {
				$targetCost = $costMap[$tY][$tX] ?? '#';
				if ($targetCost == '#' || $targetCost > $myCost) { continue; }

				$myNewCost = $targetCost + $man;
				$diff = $myCost - $myNewCost;

				if ($diff >= $wantedBenefit) {
					$options["{$x},{$y},{$tX},{$tY}"] = $diff;
				}
			}
		}

		return $options;
	}

	function getOptionCounts($options) {
		$count = [];
		foreach ($options as $diff) {
			$count[$diff] = ($count[$diff] ?? 0) + 1;
		}
		ksort($count);
		return $count;
	}

	$costMap = $map;
	[$baseCost, $basePath] = getPathAndCost($map, $start, $end);

	foreach ($basePath as $point => $_) {
		[$x, $y] = explode(',', $point);
		$costMap[$y][$x] = $baseCost--;
	}

	$wantedBenefit = isTest() ? 1 : 100;
	$options = findCheatOptions($costMap, $basePath, 2, $wantedBenefit);
	$part1 = count($options);

	if (isDebug()) {
		foreach (getOptionCounts($options) as $d => $c) {
			echo "There are {$c} cheats that save {$d} picoseconds.\n";
		}
	}

	echo 'Part 1: ', $part1, "\n";

	$wantedBenefit = isTest() ? 50 : 100;
	$options = findCheatOptions($costMap, $basePath, 20, $wantedBenefit);
	$part2 = count($options);

	if (isDebug()) {
		foreach (getOptionCounts($options) as $d => $c) {
			echo "There are {$c} cheats that save {$d} picoseconds.\n";
		}
	}

	echo 'Part 2: ', $part2, "\n";
