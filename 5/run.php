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

	function isInOrder($rules, $pages) {
		for ($i = 0; $i < count($pages); $i++) {
			$now = $pages[$i];

			if (isset($rules[$now])) {
				$before = array_slice($pages, 0, $i);
				foreach ($before as $b) {
					if (in_array($b, $rules[$now])) {
						return false;
					}
				}
			}
		}

		return true;
	}

	$part1 = 0;
	$part2 = 0;
	foreach ($updates as $update) {
		$pages = explode(',', $update);

		if (isInOrder($rules, $pages)) {
			$part1 += $pages[count($pages) / 2];
		} else {
			usort($pages, function($a, $b) use ($rules) {
				if (!isset($rules[$a])) { return 0; }

				if (in_array($b, $rules[$a])) { return -1; }
				else { return 1; }
			});

			$part2 += $pages[count($pages) / 2];
		}
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
