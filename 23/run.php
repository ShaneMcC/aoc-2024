#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$computers = [];
	foreach ($input as $line) {
		preg_match('#(.*)-(.*)#ADi', $line, $m);
		[$all, $a, $b] = $m;
		if (!isset($computers[$a])) { $computers[$a] = ['name' => $a, 'links' => []]; }
		if (!isset($computers[$b])) { $computers[$b] = ['name' => $b, 'links' => []]; }

		$computers[$a]['links'][] = $b;
		$computers[$b]['links'][] = $a;
	}

	$setsOfThree = [];

	foreach ($computers as $a => $adata) {
		foreach ($adata['links'] as $b) {
			foreach ($computers[$b]['links'] as $c) {
				foreach ($computers[$c]['links'] as $d) {
					if ($d == $a) {
						$set = [$a, $b, $c];
						sort($set);
						$set = implode(',', $set);

						$startsWithT = ($a[0] == 't' || $b[0] == 't' || $c[0] == 't');

						$setsOfThree[$set] = $startsWithT;
					}
				}
			}
		}
	}

	$part1 = array_sum($setsOfThree);
	echo 'Part 1: ', $part1, "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
