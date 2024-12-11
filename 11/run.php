#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = explode(' ', getInputLine());
	$input = array_map(fn($x) => intval(($x)), $input);

	function blink($stones) {
		$new = [];

		foreach ($stones as $stone) {
			if ($stone === 0) {
				$new[] = 1;
			} else if (strlen($stone) % 2 == 0) {
				$new[] = intval(substr($stone, 0, strlen($stone) / 2));
				$new[] = intval(substr($stone, strlen($stone) / 2));
			} else {
				$new[] = $stone * 2024;
			}
		}

		return $new;
	}

	$stones = $input;

	for ($i = 0; $i < 25; $i++) {
		$stones = blink($stones);
	}

	$part1 = count($stones);

	// foreach

	echo 'Part 1: ', $part1, "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
