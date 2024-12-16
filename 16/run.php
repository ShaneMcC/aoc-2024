#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$map = getInputMap();

	$directions = [];
	$directions['N'] = [0, -1, ['W', 'E'], '^'];
	$directions['E'] = [1, 0, ['N', 'S'], '>'];
	$directions['S'] = [0, 1, ['E', 'W'], 'v'];
	$directions['W'] = [-1, 0, ['S', 'N'], '<'];

	$start = findCells($map, 'S')[0];
	$end = findCells($map, 'E')[0];

    function fluffMap($map, $title = '') {
        foreach (cells($map) as [$x, $y, $cell]) {
            if ($cell == '#') {
                $map[$y][$x] = "\033[91m" . $cell . "\033[0m";
            }

            if ($cell == '.') {
                $map[$y][$x] = ' ';
            }

            if ($cell == '^' || $cell == '>' || $cell == 'v' || $cell == '<') {
                $map[$y][$x] = "\033[92m" . $cell . "\033[0m";
            }
        }
        drawMap($map, false, $title);
    }


	function getPathCost($map, $start, $end) {
		global $directions;

		$queue = new SPLPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
		$queue->insert([$start[0], $start[1], 'E', []], 0);

		$costs = [];

		while (!$queue->isEmpty()) {
			$q = $queue->extract();
			[$x, $y, $direction, $steps] = $q['data'];
			$cost = abs($q['priority']);

			if (isset($costs[$y][$x][$direction])) { continue; }
			$costs[$y][$x][$direction] = $cost;

			[$dX, $dY, $possibleDirections, $shape] = $directions[$direction];
			[$tX, $tY] = [$x + $dX, $y + $dY];

			if ($map[$tY][$tX] != '#') {
				$queue->insert([$tX, $tY, $direction, array_merge($steps, [[$x, $y, $shape]])], -($cost + 1));
			}
			foreach ($possibleDirections as $pd) {
				$queue->insert([$x, $y, $pd, $steps], -($cost + 1000));
			}
		}

		[$x, $y] = $end;
		if (isset($costs[$y][$x])) {
			return min(array_values($costs[$y][$x]));
		}

		return PHP_INT_MAX;
	}

	$part1 = getPathCost($map, $start, $end);
	echo 'Part 1: ', $part1, "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
