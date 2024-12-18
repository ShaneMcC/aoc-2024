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

	function getPathCost($map, $start, $end) {
		$queue = new SPLPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
		$queue->insert([$start[0], $start[1]], 0);

		$costs = [];

		$adj = getAdjacentDirections();

		while (!$queue->isEmpty()) {
			$q = $queue->extract();
			[$x, $y] = $q['data'];
			$cost = abs($q['priority']);

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

	$size = (isTest() ? 6 : 70);
	$start = [0, 0];
	$end = [$size, $size];
	$map = array_fill(0, $size + 1, array_fill(0, $size + 1, '.'));

	$startPoint = (isTest() ? 12 : 1024);

	for ($i = 0; $i < count($entries); $i++) {
		[$x, $y] = $entries[$i];
		$map[$y][$x] = '#';
		if ($i < $startPoint) { continue; }

		$valid = getPathCost($map, $start, $end);

		if ($i == $startPoint) {
			echo 'Part 1: ', $valid, "\n";
		}

		if ($valid === FALSE) {
			$part2 = [$x, $y];
			break;
		}
	}

	echo 'Part 2: ', implode(',', $part2), "\n";
