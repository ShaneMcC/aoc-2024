#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$register = ['A' => 0, 'B' => 0, 'C' => 0];
	$program = [];
	foreach ($input as $line) {
		if (preg_match('#^Register (.*): (.*)$#ADi', $line, $m)) {
			$register[$m[1]] = (int)($m[2]);
		} else if (preg_match('#^Program: (.*)$#ADi', $line, $m)) {
			$program = array_map(fn($x) => (int)$x, explode(',', $m[1]));
		}
	}

	require_once(dirname(__FILE__) . '/Day17VM.php');

	function findPart2($program) {
		$vm = new Day17VM(NULL, $program);

		$max = count($program) - 1;
		$maxdec = pow(8, $max + 1);

		if ($maxdec > PHP_INT_MAX) { die('Unable to calculate'); }

		$check = array_fill(0, $max, []);
		$check[$max][] = 0;

		for ($i = $max; $i >= 0; $i--) {
			$progSlice = array_slice($program, $i);
			$next = $i - 1;

			foreach ($check[$i] as $testA) {
				if (isDebug()) { echo "[{$i}, {$testA}] => ", implode(',', $progSlice), "\n"; }
				for ($a = $testA * 8; $a < ($testA * 8) + 8; $a++) {
					$run = $vm->run($a);
					$valid = ($run == $progSlice);

					if (isDebug()) {
						$maxbinlen = ($max + 1) * 3;
						$maxdeclen = strlen($maxdec);
						$bin = sprintf("%{$maxbinlen}s", decbin($a));
						$dec = sprintf("%{$maxdeclen}s", $a);

						echo "\t {{$dec} / {$bin}} => ", implode(',', $run);
						echo ($valid ? " => Valid!" : ""), "\n";
					}

					if ($valid) {
						$check[$next][] = $a;
					}
				}
			}
		}

		return empty($check[-1] ?? []) ? -1 : min($check[-1]);
	}

	$vm = new Day17VM($register, $program);

	$part1 = implode(',', $vm->run());
	echo 'Part 1: ', $part1, "\n";

	$part2 = findPart2($program);
	echo 'Part 2: ', $part2, "\n";
