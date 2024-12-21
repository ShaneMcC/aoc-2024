#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$mainKeypad = [[7, 8, 9],
	               [4, 5, 6],
	               [1, 2, 3],
	               [null, 0, 'A']];

    $arrowsPad = [[null, '^', 'A'],
	              ['<', 'v', '>']];

	  function getPaths($map, $start, $end) {
		$queue = new SPLPriorityQueue();
		$queue->setExtractFlags(SplPriorityQueue::EXTR_BOTH);
		$queue->insert([$start[0], $start[1], [], []], 0);

		$adj = [];
		$adj['^'] = [0, -1];
		$adj['<'] = [-1, 0];
		$adj['>'] = [1, 0];
		$adj['v'] = [0, 1];

		$paths = [];

		$bestLen = PHP_INT_MAX;

		while (!$queue->isEmpty()) {
			$q = $queue->extract();
			[$x, $y, $dirs, $prev] = $q['data'];
			$cost = abs($q['priority']);

			if (isset($prev[$y][$x])) { continue; }
			$prev[$y][$x] = True;

			$myLen = count($dirs);
			if ($myLen > $bestLen) { continue; }

			if ([$x, $y] == $end) {
				if ($myLen < $bestLen) {
					$paths = [];
				}
				$bestLen = min($bestLen, $myLen);
				$paths[] = $dirs;
				continue;
			}

			foreach ($adj as $dir => [$dX, $dY]) {
				[$tX, $tY] = [$x + $dX, $y + $dY];
				if (isset($map[$tY][$tX]) && $map[$tY][$tX] !== null) {
					$queue->insert([$tX, $tY, array_merge($dirs, [$dir]), $prev], -($cost + 1));
				}
			}
		}

		return $paths;
	}

	function getPathsFromAtoB($keypad, $start, $end) {
		return getPaths($keypad, findCells($keypad, $start)[0], findCells($keypad, $end)[0]);
	}

	function getSequence($keypad, $sequence, $all = False) {
		$current = 'A';

		$results = [''];

		foreach (str_split($sequence) as $c) {
			$paths = getPathsFromAtoB($keypad, $current, $c);

			$newResults = [];
			foreach ($results as $r) {
				foreach ($paths as $p) {
					$newResults[] = $r . implode('', $p) . 'A';
				}
			}
			$results = $newResults;

			$current = $c;
		}

		$len = PHP_INT_MAX;
		$path = '';
		foreach ($results as $r) {
			if (strlen($r) < $len) {
				$path = $r;
				$len = strlen($r);
			}
		}

		return $all ? $results : $path;
	}

	function getNestedSequence($sequence) {
		global $arrowsPad, $mainKeypad;

		$possible = [];

		$bestLen = PHP_INT_MAX;

		foreach (getSequence($mainKeypad, $sequence, true) as $inner) {
			foreach (getSequence($arrowsPad, $inner, true) as $mid) {
				foreach (getSequence($arrowsPad, $mid, true) as $outer) {
					$myLen = strlen($outer);
					if ($myLen < $bestLen) {
						$possible = [];
						$bestLen = min($bestLen, $myLen);
					}
					if ($myLen <= $bestLen) {
						$possible[] = $outer;
					}
				}
			}
		}

		return $possible[0];
	}

	function debugSequence($keypad, $sequence) {
		$adj = [];
		$adj['^'] = [0, -1];
		$adj['<'] = [-1, 0];
		$adj['>'] = [1, 0];
		$adj['v'] = [0, 1];

		[$x, $y] = findCells($keypad, 'A')[0];
		$pressed = '';

		foreach (str_split($sequence) as $c) {
			if ($c == 'A') {
				$pressed .= $keypad[$y][$x];
			} else {
				[$dX, $dY] = $adj[$c];
				[$x, $y] = [$x + $dX, $y + $dY];
			}
		}

		return $pressed;
	}

	function debugNestedSequence($sequence) {
		global $arrowsPad, $mainKeypad;

		return debugSequence($mainKeypad, debugSequence($arrowsPad, debugSequence($arrowsPad, $sequence)));
	}


	$part1 = 0;
	foreach ($input as $line) {
		$seq = getNestedSequence($line);
		$debug = debugNestedSequence($seq);
		$numVal = (int)str_replace('A', '', $line);
		$seqLen = strlen($seq);
		$calc = $numVal * $seqLen;

		echo $line, ': ', $seq, " => {$seqLen} * {$numVal} = {$calc} [{$debug}]", "\n";

		$part1 += $calc;
	}

	echo 'Part 1: ', $part1, "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
