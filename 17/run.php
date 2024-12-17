#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$register = [];
	$program = [];
	foreach ($input as $line) {
		if (preg_match('#^Register (.*): (.*)$#ADi', $line, $m)) {
			$register[$m[1]] = $m[2];
		} else if (preg_match('#^Program: (.*)$#ADi', $line, $m)) {
			$program = explode(',', $m[1]);
		}
	}

	class Day17VM {
		private $register = [];
		private $program = [];

		function __construct($register, $program) {
			$this->register = $register ?? ['A' => 0, 'B' => 0, 'C' => 0];
			$this->program = $program;
		}

		private function literal($value) {
			return $value;
		}

		private function combo($value) {

			if ($value == 0) { return $value; }
			if ($value == 1) { return $value; }
			if ($value == 2) { return $value; }
			if ($value == 3) { return $value; }

			if ($value == 4) { return $this->register['A']; }
			if ($value == 5) { return $this->register['B']; }
			if ($value == 6) { return $this->register['C']; }
			die('Invalid');
		}

		public function run($A = False) {
			if ($A !== False) {
				$this->register = ['A' => $A, 'B' => 0, 'C' => 0];
			}

			$ip = 0;
			$out = [];

			while ($ip < count($this->program) - 1) {
				$op = $this->program[$ip];
				$arg = $this->program[$ip + 1];
				$ip += 2;

				switch ($op) {
					case 0: // adv
						$this->register['A'] = (int)($this->register['A'] / pow(2, $this->combo($arg)));
						break;

					case 1: // bxl
						$this->register['B'] = $this->register['B'] ^ $this->literal($arg);
						break;

					case 2: // bst
						$this->register['B'] = $this->combo($arg) % 8;
						break;

					case 3: // jnz
						if ($this->register['A'] != 0) {
							$ip = $this->literal($arg);
						}
						break;

					case 4: // bxc
						$this->register['B'] = $this->register['B'] ^ $this->register['C'];
						break;

					case 5: // out
						$out[] = $this->combo($arg) % 8;
						break;

					case 6: // bdv
						$this->register['B'] = (int)($this->register['A'] / pow(2, $this->combo($arg)));
						break;

					case 7: // cdv
						$this->register['C'] = (int)($this->register['A'] / pow(2, $this->combo($arg)));
						break;

					default:
						die('Invalid?');
						break;
				}
			}

			return $out;
		}

		public function debug($a) {
			echo $a, ': ', implode(',', $this->run($a)), "\n";
		}
	}

	function findPart2($program) {
		$vm = new Day17VM(NULL, $program);

		$max = count($program) - 1;
		$maxbinlen = ($max + 1) * 3;
		$maxdeclen = strlen(pow(8, $max + 1));

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
