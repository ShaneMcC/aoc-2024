#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$entries = [];
	foreach ($input as $line) {
		preg_match('#(.*): (.*)#ADi', $line, $m);
		[$all, $score, $values] = $m;
		$entries[$score] = explode(' ', $values);
	}

	function checkValid($score, $values, $allowConcat = false) {
		$attempts[-1] = True;
		foreach ($values as $v) {
			$newAttempts = [];

			foreach ($attempts as $a => $_) {
				$a1 = ($a === -1 ? $v : $a * $v);
				if ($a1 <= $score) { $newAttempts[$a1] = True; }

				$a2 = ($a === -1 ? $v : $a + $v);
				if ($a2 <= $score) { $newAttempts[$a2] = True; }

				if ($allowConcat) {
					$a3 = 0 + (($a === -1 ? $v : $a.$v));
					if ($a3 <= $score) { $newAttempts[$a3] = True; }
				}
			}

			if (empty($newAttempts)) { return False; }

			$attempts = $newAttempts;
		}

		foreach ($attempts as $a => $_) {
			if ($a == $score) {
				return True;
			}
		}

		return False;
	}

	$part1 = $part2 = 0;

	foreach ($entries as $score => $values) {
		// echo $score, ': ', json_encode($values), ' => ', checkValid($score, $values), "\n";

		if (checkValid($score, $values)) {
			$part1 += $score;
			$part2 += $score;
		} else if (checkValid($score, $values, true)) {
			$part2 += $score;
		}
	}

	echo 'Part 1: ', $part1, "\n";
	echo 'Part 2: ', $part2, "\n";
