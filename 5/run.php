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

		$rules[$a][] = $b;
	}

	// Using this function ends up being quicker than checking with
	// our arrayIsSorted() function.
	function isInOrder($pages) {
		global $rules;

		for ($i = 0; $i < count($pages); $i++) {
			$now = $pages[$i];

			if (isset($rules[$now])) {
				for ($j = 0; $j < $i; $j++) {
					if (in_array($pages[$j], $rules[$now])) {
						return false;
					}
				}
			}
		}

		return true;
	}

	$comparator = function($a, $b) use ($rules) {
		if (in_array($b, $rules[$a] ?? [])) { return -1; }
		if (in_array($a, $rules[$b] ?? [])) { return 1; }
		return 0;
	};

	$part1 = $part2 = 0;

	foreach ($updates as $update) {
		$pages = explode(',', $update);

		if (isInOrder($pages)) {
			$part1 += $pages[count($pages) / 2];
		} else {
			usort($pages, $comparator);
			$part2 += $pages[count($pages) / 2];
		}
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
