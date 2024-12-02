#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$entries = [];
	foreach ($input as $line) {
		$entries[] = explode(" ", $line);
	}

	function checkSafe($report) {
		$forward = $report;
		sort($forward);
		$backward = $report;
		rsort($backward);

		if ($report != $forward && $report != $backward) {
			return false;
		}

		for ($i = 1; $i < count($report); $i++) {
			$cur = $report[$i];
			$prev = $report[$i - 1];
			$diff = abs($cur - $prev);
			if ($diff == 0 || $diff > 3) {
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
