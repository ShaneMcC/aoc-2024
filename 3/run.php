#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$entries = [];
	foreach ($input as $line) {
		preg_match_all('#mul\(([0-9]{1,3},[0-9]{1,3})\)#', $line, $m);
		foreach ($m[1] as $m) {
			$entries[] = $m;
		}
	}

	$part1 = 0;
	foreach ($entries as $muls) {
		$bits = explode(',', $muls);
		$part1 += ($bits[0] * $bits[1]);
	}
	echo 'Part 1: ', $part1, "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
