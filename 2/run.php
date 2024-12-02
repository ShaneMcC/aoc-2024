#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$entries = [];
	foreach ($input as $line) {
		$entries[] = explode(" ", $line);
	}

	$part1 = 0;
	foreach ($entries as $e) {
		$forward = $e; sort($forward);
		$backward = $e; rsort($backward);

		if ($e != $forward && $e != $backward) { continue; }

		for ($i = 1; $i < count($e); $i++) {
			$cur = $e[$i];
			$prev = $e[$i - 1];
			$diff = abs($cur - $prev);
			if ($diff == 0 || $diff > 3) {
				continue 2;
			}
		}

		$part1++;
	}
	echo 'Part 1: ', $part1, "\n";

	// $part2 = -1;
	// echo 'Part 2: ', $part2, "\n";
