<?php
	class Day17VM {
		private $register = [];
		private $program = [];

		function __construct($register, $program) {
			$this->register = $register ?? ['A' => 0, 'B' => 0, 'C' => 0];
			$this->program = $program;
		}

		private function combo($value) {
			if ($value >= 0 && $value <= 3) { return $value; }
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
						$this->register['B'] = $this->register['B'] ^ $arg;
						break;

					case 2: // bst
						$this->register['B'] = $this->combo($arg) % 8;
						break;

					case 3: // jnz
						if ($this->register['A'] != 0) {
							$ip = $arg;
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
	}
