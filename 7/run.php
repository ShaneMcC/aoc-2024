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
		$attempts = [$values[0] => True];

		for ($i = 1; $i < count($values); $i++) {
			$v = $values[$i];

			$newAttempts = [];
			foreach ($attempts as $a => $_) {
				if ($a > $score) { continue; }

				$newAttempts[$a * $v] = True;
				$newAttempts[$a + $v] = True;
				if ($allowConcat) { $newAttempts[(int)($a . $v)] = True; }
			}

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
