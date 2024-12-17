#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$register = [];
	$program = [];
	$ip = 0;
	foreach ($input as $line) {
		if (preg_match('#^Register (.*): (.*)$#ADi', $line, $m)) {
			$register[$m[1]] = $m[2];
		} else if (preg_match('#^Program: (.*)$#ADi', $line, $m)) {
			$program = explode(',', $m[1]);
		}
	}

	function literal($value) {
		return $value;
	}

	function combo($value) {
		global $register;

		if ($value == 0) { return $value; }
		if ($value == 1) { return $value; }
		if ($value == 2) { return $value; }
		if ($value == 3) { return $value; }
		if ($value == 4) { return $register['A']; }
		if ($value == 5) { return $register['B']; }
		if ($value == 6) { return $register['C']; }
		if ($value == 7) { die('Invalid'); }

		die('Invalid 2');
	}

	$out = [];

	while ($ip < count($program) - 1) {
		$op = $program[$ip];
		$arg = $program[$ip + 1];
		$ip += 2;

		switch ($op) {

			case 0: // adv
				$register['A'] = (int)($register['A'] / pow(2, combo($arg)));
				break;

			case 1: // bxl
				$register['B'] = $register['B'] ^ literal($arg);
				break;

			case 2: // bst
				$register['B'] = combo($arg) % 8;
				break;

			case 3: // jnz
				if ($register['A'] != 0) {
					$ip = literal($arg);
				}
				break;

			case 4: // bxc
				$register['B'] = $register['B'] ^ $register['C'];
				break;

			case 5: // out
				$out[] = combo($arg) % 8;
				break;

			case 6: // bdv
				$register['B'] = (int)($register['A'] / pow(2, combo($arg)));
				break;

			case 7: // cdv
				$register['C'] = (int)($register['A'] / pow(2, combo($arg)));
				break;

			default:
				die('Invalid?');
				break;
		}
	}

	var_dump($register);
	echo implode(',', $out), "\n";

	$part1 = 0;
	echo 'Part 1: ', $part1, "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
