#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = explode(' ', getInputLine());
	$input = array_map(fn($x) => intval(($x)), $input);

	function blinkCount($stone, $count) {
		$key = json_encode([__FILE__, __LINE__, func_get_args()]);

		return storeCachedResult($key, function() use ($stone, $count) {
			if ($count == 0) {
				return 1;
			} if ($stone === 0) {
				return blinkCount(1, $count - 1);
			} else if (strlen($stone) % 2 == 0) {
				$left = intval(substr($stone, 0, strlen($stone) / 2));
				$right = intval(substr($stone, strlen($stone) / 2));

				return blinkCount($left, $count - 1) + blinkCount($right, $count - 1);
			} else {
				return blinkCount($stone * 2024, $count - 1);
			}
		});
	}

	$stones = $input;
	$part1 = $part2 = 0;
	foreach ($stones as $stone) {
		$part1 += blinkCount($stone, 25);
		$part2 += blinkCount($stone, 75);
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
