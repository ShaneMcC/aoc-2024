#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$entries = [];
	foreach ($input as $line) {
		preg_match_all("#(do\(\)|don't\(\)|mul\(([0-9]{1,3},[0-9]{1,3})\))#", $line, $m);
		foreach ($m[1] as $m) {
			$entries[] = $m;
		}
	}

	$part1 = 0;
	$part2 = 0;
	$on = true;
	foreach ($entries as $muls) {
		if ($muls == 'do()') {
			$on = true;
		} else if ($muls == "don't()") {
			$on = false;
		} else {
			preg_match('#mul\(([0-9]{1,3}),([0-9]{1,3})\)#', $muls, $bits);
			$sum = ($bits[1] * $bits[2]);
			$part1 += $sum;
			$part2 += $on ? $sum : 0;
		}
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
