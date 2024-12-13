#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$prizes = [];

	foreach ($input as $prizedata) {
		$prize = [];
		foreach ($prizedata as $line) {
			if (preg_match('#^Button ([AB]): X\+([\d]+), Y\+([\d]+)$#', $line, $m)) {
				$prize[$m[1]] = ['x' => $m[2], 'y' => $m[3]];
			} else if (preg_match('#^Prize: X=([\d]+), Y=([\d]+)$#', $line, $m)) {
				$prize['x'] = $m[1];
				$prize['y'] = $m[2];
			}
		}
		$prizes[] = $prize;
	}

	function calculateTokens($prize) {
		[$tX, $tY] = [$prize['x'], $prize['y']];
		[$aX, $aY] = [$prize['A']['x'], $prize['A']['y']];
		[$bX, $bY] = [$prize['B']['x'], $prize['B']['y']];

		$aCost = 3;
		$bCost = 1;

		$minCost = FALSE;

		for ($i = 1; $i <= 100; $i++) {
			for ($j = 1; $j <= 100; $j++) {
				$thisX = ($aX * $i) + ($bX * $j);
				$thisY = ($aY * $i) + ($bY * $j);
				$thisCost = ($aCost * $i) + ($bCost * $j);

				if ([$thisX, $thisY] == [$tX, $tY]) {
					if ($minCost === FALSE) { $minCost = $thisCost; }
					else { $minCost = min($minCost, $thisCost); }
				}
			}
		}

		return $minCost;
	}

	function calculateTokensZ3($prizes, $part2 = false) {
		// Fuck maths.
		$prizelines = [];

		foreach ($prizes as $prize) {
			[$tX, $tY] = [$prize['x'], $prize['y']];
			[$aX, $aY] = [$prize['A']['x'], $prize['A']['y']];
			[$bX, $bY] = [$prize['B']['x'], $prize['B']['y']];

			if ($part2) {
				$tX += 10000000000000;
				$tY += 10000000000000;
			}

			$prizelines[] = "prizes.append(({$tX},{$tY},{$aX},{$aY},{$bX},{$bY}))\n";
		}

		$prizelines = implode("\n", $prizelines);

		$lines = [];
		$lines[] = <<<Z3CODE
		#!/usr/bin/python
		from z3 import Int, Solver

		total = 0

		prizes = []

		{$prizelines}

		for (tX,tY,aX,aY,bX,bY) in prizes:
		  solver = Solver()
		  countA = Int('countA')
		  countB = Int('countB')

		  # solver.add(countA <= 100)
		  # solver.add(countB <= 100)

		  solver.add((aX * countA) + (bX * countB) == tX)
		  solver.add((aY * countA) + (bY * countB) == tY)

		  try:
		    solver.check()
		    total += int(str(solver.model().eval(countA * 3 + countB)))
		  except:
		    pass

		print(total)
		Z3CODE;

		$code = implode("\n", $lines);
		$tempFile = tempnam(sys_get_temp_dir(), 'AOC24');
		file_put_contents($tempFile, $code);
		chmod($tempFile, 0700);
		$minCost = exec($tempFile);
		unlink($tempFile);

		return $minCost;
	}

	$part1 = 0;

	foreach ($prizes as $prize) {
		$part1 += calculateTokens($prize);
	}

	echo 'Part 1: ', $part1, "\n";

	$part2 = calculateTokensZ3($prizes, true);
	echo 'Part 2: ', $part2, "\n";
