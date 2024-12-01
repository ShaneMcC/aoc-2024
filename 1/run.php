#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$list1 = [];
	$list2 = [];
	foreach ($input as $line) {
		preg_match('#(.*) (.*)#SADi', $line, $m);
		[$all, $foo, $bar] = $m;
		$list1[] = $foo;
		$list2[] = $bar;
	}

	sort($list1);
	sort($list2);

	$part1 = 0;
	for ($i = 0; $i < count($list1); $i++) {
		$part1 += abs($list1[$i] - $list2[$i]);
	}

	echo 'Part 1: ', $part1, "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
