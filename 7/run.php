#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$entries = [];
	foreach ($input as $line) {
		preg_match('#(.*): (.*)#ADi', $line, $m);
		[$all, $score, $values] = $m;
		$entries[] = [$score, explode(' ', $values)];
	}

	function checkValid($score, $values, $allowConcat = false) {
		$attempts = [$score => True];

		for ($i = count($values) - 1; $i >= 0; $i--) {
			$v = $values[$i];

			$newAttempts = [];
			foreach ($attempts as $a => $_) {
				if ($a < 0) { continue; }

				$t = $a / $v;
				if (is_int($t)) { $newAttempts[$t] = True; }
				$newAttempts[$a - $v] = True;
				if ($allowConcat && str_ends_with($a, $v)) {
					$newAttempts[substr($a, 0, 0 - strlen($v))] = True;
				}
			}

			$attempts = $newAttempts;
		}

		foreach ($attempts as $a => $_) {
			if ($a == 0) {
				return True;
			}
		}

		return False;
	}

	$part1 = $part2 = 0;

	foreach ($entries as [$score, $values]) {
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
