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

	function getPathCost($map, $start, $end) {
		$queue = new SPLPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
		$queue->insert([$start[0], $start[1]], 0);

		$costs = [];

		while (!$queue->isEmpty()) {
			$q = $queue->extract();
			[$x, $y] = $q['data'];
			$cost = abs($q['priority']);

			if (isset($costs[$y][$x])) { continue; }
			$costs[$y][$x] = $cost;

			if ([$x, $y] == $end) {
				return $cost;
			}

			$adj = getAdjacentDirections();

			foreach ($adj as [$dX, $dY]) {
				[$tX, $tY] = [$x + $dX, $y + $dY];
				if (($map[$tY][$tX] ?? '#') != '#') {
					$queue->insert([$tX, $tY], -($cost + 1));
				}
			}
		}

		return False;
	}


	$size = (isTest() ? 6 : 70);
	$start = [0, 0];
	$end = [$size, $size];

	$map = getMapAtTime(isTest() ? 12 : 1024);
	$part1 = getPathCost($map, $start, $end);
	echo 'Part 1: ', $part1, "\n";

	$time = 0;
	$map = array_fill(0, $size + 1, array_fill(0, $size + 1, '.'));
	for ($i = 0; $i < count($entries); $i++) {
		[$x, $y] = $entries[$i];
		$map[$y][$x] = '#';
		$valid = getPathCost($map, $start, $end);

		if ($valid === FALSE) {
			$part2 = [$x, $y];
			break;
		}
	}

	echo 'Part 2: ', implode(',', $part2), "\n";
