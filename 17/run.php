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

		$tests = 0;
		$useful = 0;

		for ($i = $max; $i >= 0; $i--) {
			$progSlice = array_slice($program, $i);
			$next = $i - 1;

			foreach ($check[$i] as $testA) {
				$startA = $testA * 8;
				$endA = $startA + 7;
				if (isDebug()) { echo "[{$i}, {$testA}, {$startA}...{$endA}] => ", implode(',', $progSlice), "\n"; }
				for ($a = $startA; $a <= $endA; $a++) {
					$tests++;
					$run = $vm->run($a);
					$valid = ($run == $progSlice);

					if (isDebug()) {
						$maxbinlen = ($max + 1) * 3;
						$maxdeclen = strlen($maxdec);
						$maxoctlen = ($max + 1);
						$decbin = decbin($a);
						while (strlen($decbin) % 3 != 0) { $decbin = '0' . $decbin; }
						$bin = sprintf("%{$maxbinlen}s", $decbin);
						$dec = sprintf("%{$maxdeclen}s", $a);
						$oct = sprintf("%-{$maxoctlen}s", base_convert($a, 10, 8));

						echo "\t {{$dec} / {$bin} / {$oct}} => ", implode(',', $run);
						echo ($valid ? " => Valid!" : ""), "\n";
					}

					if ($valid) {
						$useful++;
						$check[$next][] = $a;
					}
				}
			}
		}

		if (isDebug()) {
			$possible = count($check[-1] ?? []);
			echo "\n";
			echo "Tests: {$tests}\n";
			echo "Useful: {$useful}\n";
			echo "Possible: {$possible}\n";
			echo "\n";
		}

		return empty($check[-1] ?? []) ? -1 : min($check[-1]);
	}

	$vm = new Day17VM($register, $program);

	$part1 = implode(',', $vm->run());
	echo 'Part 1: ', $part1, "\n";

	$part2 = findPart2($program);
	echo 'Part 2: ', $part2, "\n";
