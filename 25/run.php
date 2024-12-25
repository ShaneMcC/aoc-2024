#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$locks = [];
	$keys = [];

	foreach ($input as $group) {
		$isLock = ($group[0] == '#####');

		$code = [-1, -1, -1, -1, -1];
		for ($i = 0; $i < 5; $i++) {
			foreach ($group as $line) {
				if ($line[$i] == '#') {
					$code[$i]++;
				}
			}
		}

		if ($isLock) {
			$locks[] = $code;
		} else {
			$keys[] = $code;
		}
	}

	$part1 = 0;

	foreach ($locks as $lock) {
		foreach ($keys as $key) {
			$overlapped = false;
			for ($i = 0; !$overlapped && $i < 5; $i++) {
				$overlapped = ($lock[$i] + $key[$i]) >= 6;
			}

			if (!$overlapped) {
				$part1++;
			}
		}
	}

	echo 'Part 1: ', $part1, "\n";
