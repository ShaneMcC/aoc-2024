#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$entries = [];
	foreach ($input as $line) {
		preg_match('#^(.*),(.*)$#ADi', $line, $m);
		[$all, $x, $y] = $m;
		$entries[] = [$x, $y];
	}

	function getMapAtTime($time) {
		global $entries;

		$key = json_encode([__FILE__, __LINE__, func_get_args()]);

		return storeCachedResult($key, function() use ($time, $entries) {
			$size = (isTest() ? 6 : 70);
			$map = array_fill(0, $size + 1, array_fill(0, $size + 1, '.'));

			for ($i = 0; $i < min($time, count($entries) - 1); $i++) {
				[$x, $y] = $entries[$i];

				$map[$y][$x] = '#';
			}

			return $map;
		});
	}

	function getPathCost($start, $end) {
		$queue = new SPLPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
		$queue->insert([$start[0], $start[1], 0, []], 0);

		$costs = [];

		$map = getMapAtTime(isTest() ? 12 : 1024);

		while (!$queue->isEmpty()) {
			$q = $queue->extract();
			[$x, $y, $time, $steps] = $q['data'];
			$cost = abs($q['priority']);

			if (isset($costs[$y][$x])) { continue; }
			$costs[$y][$x] = [$cost, $time, $steps];

			$steps["{$x},{$y}"] = True;

			$adj = getAdjacentDirections();

			foreach ($adj as [$dX, $dY]) {
				[$tX, $tY] = [$x + $dX, $y + $dY];
				if (($map[$tY][$tX] ?? '#') != '#') {
					$queue->insert([$tX, $tY, $time + 1, $steps], -($cost + 1));
				}
			}
		}

		return $costs[$end[1]][$end[0]] ?? False;
	}


	$size = (isTest() ? 6 : 70);
	$start = [0, 0];
	$end = [$size, $size];

	$part1 = getPathCost($start, $end);
	echo 'Part 1: ', count($part1[2] ?? []), "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
