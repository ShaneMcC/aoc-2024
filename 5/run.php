#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$ordering = $input[0];
	$updates = $input[1];

	$rules = [];
	foreach ($ordering as $rule) {
		[$a, $b] = explode('|', $rule, 2);
		if (!isset($rules[$a])) { $rules[$a] = ['before' => [], 'after' => []]; }
		if (!isset($rules[$b])) { $rules[$b] = ['before' => [], 'after' => []]; }

		$rules[$a]['before'][] = $b;
		$rules[$b]['after'][] = $a;
	}

	function isInOrder($rules, $pages) {
		for ($i = 0; $i < count($pages); $i++) {
			$now = $pages[$i];

			if (isset($rules[$now])) {
				$before = array_slice($pages, 0, $i);
				foreach ($before as $b) {
					if (in_array($b, $rules[$now]['before'])) {
						return false;
					}
				}
			}
		}

		return true;
	}

	$part1 = 0;
	foreach ($updates as $update) {
		$pages = explode(',', $update);

		if (isInOrder($rules, $pages)) {
			$part1 += $pages[count($pages) / 2];
		}
	}

	echo 'Part 1: ', $part1, "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
