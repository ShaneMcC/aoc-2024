#!/usr/bin/php
<?php
	$__CLI['long'] = ['interactive', 'draw'];
	$__CLI['extrahelp'][] = '      --interactive        Run interactively and ignore instructions.';
	$__CLI['extrahelp'][] = '      --draw               Draw final state.';

	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$interactiveMode = isset($__CLIOPTS['interactive']) && function_exists("pcntl_signal") && function_exists("pcntl_async_signals") && file_exists('/bin/stty');
	if ($interactiveMode) { initFluff(); }

	$drawMap = !$interactiveMode && isset($__CLIOPTS['draw']);

	$wideMap = $map = [];
	foreach ($input[0] as $row) {
		$map[] = str_split(str_replace([' '], ['.'], $row));
		$wideMap[] = str_split(str_replace(['#', 'O', '.', ' ', '@'], ['##', '[]', '..', '..', '@.'], $row));
	}

	$isWideMap = !empty(findCells($map, '['));
	if ($isWideMap) {
		echo 'Map is already wide, skipping part 1.', "\n";
		$wideMap = $map;
	}

	$intrs = $input[1];

	$directions['^'] = [0, -1];
	$directions['<'] = [-1, 0];
	$directions['>'] = [1, 0];
	$directions['v'] = [0, 1];

	function candoMoveItem($map, $cell, $direction) {
		global $directions;

		[$tX, $tY] = $cell;
		[$dX, $dY] = $directions[$direction];

		while (true) {
			$tX += $dX;
			$tY += $dY;
			$nextCell = $map[$tY][$tX];

			if ($nextCell === '#') {
				return False;
			} else if ($nextCell === '.') {
				return True;
			} else if ($nextCell === '[' && ($direction == '^' || $direction == 'v')) {
				return candoMoveItem($map, [$tX, $tY], $direction) && candoMoveItem($map, [$tX + 1, $tY], $direction);
			} else if ($nextCell === ']' && ($direction == '^' || $direction == 'v')) {
				return candoMoveItem($map, [$tX - 1, $tY], $direction) && candoMoveItem($map, [$tX, $tY], $direction);
			}
		}
	}

	function doMoveItem(&$map, $cell, $direction) {
		global $directions;

		[$x, $y] = $cell;
		[$dX, $dY] = $directions[$direction];
		[$tX, $tY] = [$x + $dX, $y + $dY];

		$myItem = $map[$y][$x];
		$targetItem = $map[$tY][$tX];

		// echo "Moving item {$myItem} at {$x},{$y} in {$direction} to {$tX},{$tY} onto {$targetItem}\n";

		if ($myItem == '#') { return false; }

		if ($targetItem === '.') {
			$map[$tY][$tX] = $myItem;
			$map[$y][$x] = '.';

			return [$tX, $tY];
		} else if ($targetItem === 'O' || $targetItem === '[' || $targetItem === ']') {
			$moveResult = doMoveItem($map, [$tX, $tY], $direction);
			if ($moveResult !== false) {
				$map[$tY][$tX] = $myItem;
				$map[$y][$x] = '.';

				return [$tX, $tY];
			}
		}

		return false;
	}

	function moveItem(&$map, $cell, $direction) {
		global $directions;

		if ($direction == '<' || $direction == '>') {
			return doMoveItem($map, $cell, $direction);
		} else {
			[$x, $y] = $cell;
			[$dX, $dY] = $directions[$direction];
			[$tX, $tY] = [$x + $dX, $y + $dY];

			$myItem = $map[$y][$x];
			$targetItem = $map[$tY][$tX];

			if ($targetItem == '.' || $targetItem == '#' || $targetItem == 'O') {
				return doMoveItem($map, $cell, $direction);
			} else {
				if ($targetItem == '[') {
					$leftCell = [$tX, $tY];
					$rightCell = [$tX + 1, $tY];
				} else if ($targetItem == ']') {
					$leftCell = [$tX - 1, $tY];
					$rightCell = [$tX, $tY];
				} else {
					return False;
				}

				$moveLeftResult = candoMoveItem($map, $leftCell, $direction);
				$moveRightResult = candoMoveItem($map, $rightCell, $direction);

				if ($moveLeftResult && $moveRightResult) {
					moveItem($map, $leftCell, $direction);
					moveItem($map, $rightCell, $direction);

					$map[$tY][$tX] = $myItem;
					$map[$y][$x] = '.';

					return [$tX, $tY];
				}
			}
		}

		return False;
	}

	function moveAround($map, $instructions) {
		global $interactiveMode, $drawMap;

		[$rX, $rY] = findCells($map, '@')[0];

		if ($interactiveMode|| isDebug()) {
			fluffMap($map, "Begin State", false);
		}

		foreach ($instructions as $ins) {
			$moveResult = moveItem($map, [$rX, $rY], $ins);
			if ($moveResult != false) {
				[$rX, $rY] = $moveResult;
			}

			if ($interactiveMode || isDebug()) {
				fluffMap($map, "Moved: {$ins}", $interactiveMode);
			}
		}

		if ($interactiveMode || $drawMap || isDebug()) {
			fluffMap($map, "End State", $interactiveMode);
		}

		return $map;
	}

	if (!$interactiveMode) {
		function getInstructions($instructions) {
			foreach ($instructions as $instructionLine) {
				foreach (str_split($instructionLine) as $ins) {
					yield $ins;
				}
			}
		}
	}

	if (!$isWideMap) {
		$finalMap = moveAround($map, getInstructions($intrs));
		$part1 = 0;
		foreach (findCells($finalMap, 'O') as [$x, $y]) {
			$part1 += (100 * $y) + $x;
		}
		echo 'Part 1: ', $part1, "\n";
	}

	$finalWideMap = moveAround($wideMap, getInstructions($intrs));
	$part2 = 0;
	foreach (findCells($finalWideMap, '[') as [$x, $y]) {
		$part2 += (100 * $y) + $x;
	}
	echo 'Part 2: ', $part2, "\n";
