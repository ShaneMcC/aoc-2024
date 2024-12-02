#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$entries = [];
	foreach ($input as $line) {
		$entries[] = explode(" ", $line);
	}

	function checkSafe($report) {
		$lastDirection = null;
		for ($i = 1; $i < count($report); $i++) {
			$diff = $report[$i] - $report[$i - 1];

			$direction = ($diff < 0);
			if ($lastDirection !== null && $direction !== $lastDirection) { return false; }
			$lastDirection = $direction;

			if ($diff < -3 || $diff == 0 || $diff > 3) {
				return false;
			}
		}

		return true;
	}

	$part1 = 0;
	$part2 = 0;
	foreach ($entries as $e) {
		if (checkSafe($e)) {
			$part1++;
			$part2++;
		} else {
			// Try removing bits...
			for ($i = 0; $i < count($e); $i++) {
				$testReport = $e;
				unset($testReport[$i]);
				$testReport = array_values($testReport);

				if (checkSafe($testReport)) {
					$part2++;
					break;
				}
			}
		}
	}
	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
