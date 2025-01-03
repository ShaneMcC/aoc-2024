#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$mainKeypad = [[7, 8, 9],
	               [4, 5, 6],
	               [1, 2, 3],
	               ['#', 0, 'A']];

    $arrowsPad = [['#', '^', 'A'],
	              ['<', 'v', '>']];

	function getPathFromAtoB($keypad, $start, $end) {
		if ($start == $end) { return 'A'; }

		[$sX, $sY] = findCells($keypad, $start)[0];
		[$eX, $eY] = findCells($keypad, $end)[0];
		[$dX, $dY] = [$eX - $sX, $eY - $sY];

		$vdir = ($sY == $eY) ? '' : (($sY > $eY) ? '^' : 'v');
		$hdir = ($sX == $eX) ? '' : (($sX > $eX) ? '<' : '>');

		$vertical = str_repeat($vdir, abs($dY));
		$horizontal = str_repeat($hdir, abs($dX));

		// The rule here is essentially:
		// Going left is expensive (higher up the chain), we want to do that as little as possible and as early as possible.
		//
		// So:
		// - If we have to go left, and can do it without hitting '#', do it first. (As this requries 2 lefts from the starting 'A')
		// - If not, do a middle button (which requires only 1-left from our starting 'A')
		// - Otherwise, right button involves 0 lefts from our starting 'A'
		//
		// I think.
		//
		// More detailed explainers at: https://www.reddit.com/r/adventofcode/comments/1hj7f89/2024_day_21_part_1_found_a_rule_to_make_it_work/
		//
		// This however seems to work though and does the right thing.

		$path = $horizontal . $vertical;
		if ($hdir == '<' && $keypad[$sY][$eX] != '#') {
			$path = $horizontal . $vertical;
		} else if ($hdir == '>' && $keypad[$eY][$sX] != '#' || $keypad[$eY][$sX] != '#') {
			$path = $vertical . $horizontal;
		}

		return $path . 'A';
	}

	function getSequence($keypad, $sequence) {
		$current = 'A';
		$result = '';

		foreach (str_split($sequence) as $c) {
			$result .= getPathFromAtoB($keypad, $current, $c);
			$current = $c;
		}

		return $result;
	}

	function getSequenceCounts($keypad, $sequence) {
		$bits = explode('A', getSequence($keypad, $sequence));

		for ($i = 0; $i < count($bits) - 1; $i++) {
			$path = $bits[$i] . 'A';
			$result[$path] = ($result[$path] ?? 0) + 1;
		}

		return $result;
	}

	function getNestedSequenceCounts($sequence, $chain = 2) {
		global $arrowsPad, $mainKeypad;

		$counts = [getSequence($mainKeypad, $sequence) => 1];

		for ($i = 0; $i < $chain; $i++) {
			$newCounts = [];
			foreach ($counts as $seq => $count) {
				foreach (getSequenceCounts($arrowsPad, $seq) as $nseq => $ncount) {
					$newCounts[$nseq] = ($newCounts[$nseq] ?? 0) + ($ncount * $count);
				}
			}

			$counts = $newCounts;
		}

		$final = 0;
		foreach ($counts as $s => $c) {
			$final += strlen($s) * $c;
		}

		return $final;
	}

	$part1 = $part2 = 0;
	foreach ($input as $line) {
		$seqLen1 = getNestedSequenceCounts($line, 2);
		$seqLen2 = getNestedSequenceCounts($line, 25);
		$numVal = (int)str_replace('A', '', $line);
		$calc1 = $numVal * $seqLen1;
		$calc2 = $numVal * $seqLen2;

		if (isDebug()) {
			echo $line, ': ', "=> {$seqLen1} * {$numVal} = {$calc1} // {$seqLen2} * {$numVal} = {$calc2}", "\n";
		}

		$part1 += $calc1;
		$part2 += $calc2;
	}

	if (isDebug()) { echo "\n"; }
	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
