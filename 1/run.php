#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$left = [];
	$right = [];
	foreach ($input as $line) {
		preg_match('#(\d+)\s+(\d+)#ADi', $line, $m);
		[$all, $left[], $right[]] = $m;
	}

	sort($left);
	sort($right);

	$rightacv = array_count_values($right);

	$part1 = 0;
	$part2 = 0;
	for ($i = 0; $i < count($left); $i++) {
		$part1 += abs($left[$i] - $right[$i]);
		$part2 += ($rightacv[$left[$i]] ?? 0) * $left[$i];
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
