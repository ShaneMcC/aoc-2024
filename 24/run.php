#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLineGroups();

	$wireInput = $input[0];
	$gateInput = $input[1];

	$wires = [];
	foreach ($wireInput as $line) {
		preg_match('#(.*): (.*)#ADi', $line, $m);
		[$all, $wire, $value] = $m;
		$wires[$wire] = (int)$value;
	}

	$gates = [];
	foreach ($gateInput as $line) {
		echo $line, "\n";
		preg_match('#(.*) (.*) (.*) -> (.*)#ADi', $line, $m);
		[$all, $a, $op, $b, $out] = $m;
		$gates[] = ['action' => [$a, $op, $b, $out], 'processed' => false];
	}

	$hasProcessed = true;
	while ($hasProcessed) {
		$hasProcessed = false;
		foreach ($gates as $gateid => $gate) {
			if (!$gate['processed']) {
				[$a, $op, $b, $out] = $gate['action'];

				if (isset($wires[$a]) && isset($wires[$b])) {
					[$a, $b] = [(int)$wires[$a], (int)$wires[$b]];

					if ($op == 'AND') {
						$wires[$out] = $a & $b;
					} else if ($op == 'OR') {
						$wires[$out] = $a | $b;
					} else if ($op == 'XOR') {
						$wires[$out] = $a ^ $b;
					} else {
						die('Unknown: ' . $op);
					}

					$gates[$gateid]['processed'] = true;
					$hasProcessed = true;
				}
			}
		}
	}

	ksort($wires);

	$part1 = '';
	foreach ($wires as $wire => $value) {
		if ($wire[0] == 'z') {
			$part1 = $value . $part1;
		}
	}

	echo 'Part 1: ', bindec($part1), "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
