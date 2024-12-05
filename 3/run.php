#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$entries = [];
	foreach ($input as $line) {
		preg_match_all("#(do\(\)|don't\(\)|mul\((\d{1,3}),(\d{1,3})\))#", $line, $m);
		for ($i = 0; $i < count($m[0]); $i++) {
			$entries[] = empty($m[2][$i]) ? $m[1][$i] : [$m[2][$i], $m[3][$i]];
		}
	}

	$part1 = $part2 = 0;
	$on = true;

	foreach ($entries as $entry) {
		if ($entry == 'do()') {
			$on = true;
		} else if ($entry == "don't()") {
			$on = false;
		} else {
			$sum = array_product($entry);
			$part1 += $sum;
			$part2 += $on ? $sum : 0;
		}
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
