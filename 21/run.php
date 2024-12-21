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
		if ($start == $end) { return ''; }

		[$sX, $sY] = findCells($keypad, $start)[0];
		[$eX, $eY] = findCells($keypad, $end)[0];

		[$dX, $dY] = [$eX - $sX, $eY - $sY];

		$vdir = $hdir = '';

		if ($sY > $eY) {
			$vdir = '^';
		} else if ($sY < $eY) {
			$vdir = 'v';
		}

		if ($sX > $eX) {
			$hdir = '<';
		} else if ($sX < $eX) {
			$hdir = '>';
		}

		$vertical = str_repeat($vdir, abs($dY));
		$horizontal = str_repeat($hdir, abs($dX));

		if ($hdir == '<' && $keypad[$sY][$eX] != '#') {
			return $horizontal . $vertical;
		}

		if ($hdir == '>' && $keypad[$eY][$sX] != '#' || $keypad[$eY][$sX] != '#') {
			return $vertical . $horizontal;
		}

		return $horizontal . $vertical;
	}

	function getSequence($keypad, $sequence) {
		$current = 'A';
		$result = '';

		foreach (str_split($sequence) as $c) {
			$path = getPathFromAtoB($keypad, $current, $c);
			$result .= $path . 'A';
			$current = $c;
		}

		return $result;
	}

	function getNestedSequence($sequence, $chain = 2) {
		global $arrowsPad, $mainKeypad;

		$last = getSequence($mainKeypad, $sequence);

		for ($i = 0; $i < $chain; $i++) {
			$last = getSequence($arrowsPad, $last);
		}

		return $last;
	}

	function getSequenceCounts($keypad, $sequence) {
		$current = 'A';
		$result = [];

		foreach (str_split($sequence) as $c) {
			$path = getPathFromAtoB($keypad, $current, $c) . 'A';
			$result[$path] = ($result[$path] ?? 0) + 1;
			$current = $c;
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


	$part1 = 0;
	foreach ($input as $line) {
		$seq = getNestedSequence($line);
		$numVal = (int)str_replace('A', '', $line);
		$seqLen = strlen($seq);
		$calc = $numVal * $seqLen;

		if (isDebug()) {
			echo $line, ': ', $seq, " => {$seqLen} * {$numVal} = {$calc}", "\n";
		}

		$part1 += $calc;
	}
	echo 'Part 1: ', $part1, "\n";

	if (isDebug()) { echo "\n"; }

	$part2 = 0;
	foreach ($input as $line) {
		$seqLen = getNestedSequenceCounts($line, 25);
		$numVal = (int)str_replace('A', '', $line);
		$calc = $numVal * $seqLen;

		if (isDebug()) {
			echo $line, ': ', "=> {$seqLen} * {$numVal} = {$calc}", "\n";
		}

		$part2 += $calc;
	}
	echo 'Part 2: ', $part2, "\n";
