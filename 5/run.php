#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$ordering = $input[0];
	$updates = $input[1];

	$rules = [];
	foreach ($ordering as $rule) {
		[$a, $b] = explode('|', $rule, 2);
		if (!isset($rules[$a])) { $rules[$a] = []; }

		$rules[$a][$b] = True;
	}

	$comparator = function($a, $b) use ($rules) {
		if (isset($rules[$a][$b])) { return -1; }
		if (isset($rules[$b][$a])) { return 1; }
		return 0;
	};

	$part1 = $part2 = 0;

	foreach ($updates as $update) {
		$sortedPages = $pages = explode(',', $update);

		usort($sortedPages, $comparator);

		$mid = $sortedPages[count($sortedPages) / 2];

		if ($pages == $sortedPages) {
			$part1 += $mid;
		} else {
			$part2 += $mid;
		}
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";