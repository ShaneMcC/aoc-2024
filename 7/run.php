#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$entries = [];
	foreach ($input as $line) {
		preg_match('#(.*): (.*)#ADi', $line, $m);
		[$all, $foo, $bar] = $m;
		$entries[$foo] = explode(' ', $bar);
	}

	function checkValid($score, $values, $allowConcat = false) {
		$attempts = [null];
		foreach ($values as $v) {
			$newAttempts = [];

			foreach ($attempts as $a) {
				$a1 = ($a === null ? 1 : $a) * $v;
				$a2 = ($a === null ? 0 : $a) + $v;
				if ($a1 <= $score) { $newAttempts[] = $a1; }
				if ($a2 <= $score) { $newAttempts[] = $a2; }

				if ($allowConcat) {
					$a3 = ($a === null ? '' : $a) . $v;
					if ($a3 <= $score) { $newAttempts[] = $a3; }
				}
			}

			$attempts = $newAttempts;
		}

		foreach ($attempts as $a) {
			if ($a == $score) {
				return True;
			}
		}

		return False;
	}

	$part1 = $part2 = 0;

	foreach ($entries as $score => $values) {
		// echo $score, ': ', json_encode($values), ' => ', checkValid($score, $values, true), "\n";

		if (checkValid($score, $values)) {
			$part1 += $score;
			$part2 += $score;
		} else if (checkValid($score, $values, true)) {
			$part2 += $score;
		}
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
