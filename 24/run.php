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
		preg_match('#(.*) (.*) (.*) -> (.*)#ADi', $line, $m);
		[$all, $a, $op, $b, $out] = $m;
		$gates[$out] = ['a' => $a, 'op' => $op, 'b' => $b];
	}

	$__WIRECACHE = [];

	function getWireValue($gateid) {
		global $gates, $wires, $__WIRECACHE;

		if (!isset($__WIRECACHE[$gateid])) {
			if (isset($wires[$gateid])) {
				$result = $wires[$gateid];
			} else if (isset($gates[$gateid])) {
				$gate = $gates[$gateid];

				$a = $wires[$gate['a']] ?? getWireValue($gate['a']);
				$op = $gate['op'];
				$b = $wires[$gate['b']]  ?? getWireValue($gate['b']);

				if ($op == 'AND') {
					$result = $a & $b;
				} else if ($op == 'OR') {
					$result = $a | $b;
				} else if ($op == 'XOR') {
					$result = $a ^ $b;
				}
			} else {
				die('Invalid gate or wire: ' . $gateid . "\n");
			}

			$__WIRECACHE[$gateid] = $result;
		}

		return $__WIRECACHE[$gateid];

	}

	function getNumbers() {
		global $gates, $wires;

		$keys = array_unique(array_merge(array_keys($gates), array_keys($wires)));

		sort($keys);
		$result = ['x' => '', 'y' => '', 'z' => ''];

		foreach ($keys as $wire) {
			if (isset($result[$wire[0]])) {
				$result[$wire[0]] .= getWireValue($wire);
			}
		}
		foreach (array_keys($result) as $k) { $result[$k] = bindec(strrev($result[$k])); }

		return $result;
	}

	$numbers = getNumbers();
	$part1 = $numbers['z'];
	echo 'Part 1: ', $part1, "\n";

	function getInstructionType($g) {
		global $gates;

		$gate = $gates[$g];

		if ($gate['a'][0] == 'x' || $gate['a'][0] == 'y' || $gate['b'][0] == 'x' || $gate['b'][0] == 'y') {
			if ($gate['op'] == 'XOR') {
				return 'XYADD';
			} else if ($gate['op'] == 'AND') {
				return 'XYCARRY';
			}
		}

		if ($gate['op'] == 'AND') {
			return 'AND';
		} else if ($gate['op'] == 'OR') {
			return 'CARRY';
		} else if ($gate['op'] == 'XOR') {
			return 'ZOUT';
		}

		die("Unknown Type: {$gate['a']} {$gate['op']} {$gate['b']} -> {$g}\n");
	}

	// Making HUGE assumptions about the input.
	// Thanks to https://www.reddit.com/r/adventofcode/comments/1hl698z/comment/m3kb5ix/

	$bad = [];

	$types = [];

	foreach ($gates as $g => $gate) {
		$instType = getInstructionType($g);
		$types[$instType][] = $g;
	}

	function getGateListRegisters($gateList) {
		global $gates;

		$targets = [];

		foreach ($gateList as $g) {
			$targets[] = $gates[$g]['a'];
			$targets[] = $gates[$g]['b'];
		}

		return $targets;
	}

	// The input is a fancy list of "Full Adders" but some things are swapped
	// So now find any that are actually wrong.

	foreach ($types['XYADD'] as $g) {
		// Allow x00 ^ y00
		if ($gates[$g]['a'] == 'x00' || $gates[$g]['a'] == 'y00') { continue; }

		// This should never be a z and should always output to a ZOUT.
		if ($g[0] == 'z' || !in_array($g, getGateListRegisters($types['ZOUT']))) {
			$bad[] = $g;
		}
	}

	foreach ($types['XYCARRY'] as $g) {
		// Allow x00 & y00
		if ($gates[$g]['a'] == 'x00' || $gates[$g]['a'] == 'y00') { continue; }

		// This should never be a z and should always output to a CARRY.
		if ($g[0] == 'z' || !in_array($g, getGateListRegisters($types['CARRY']))) {
			$bad[] = $g;
		}
	}

	// If it's not a Z, it's wrong.
	foreach ($types['ZOUT'] as $g) {
		if ($g[0] != 'z') {
			$bad[] = $g;
		}
	}

	// If it's a Z other than z45, it's wrong.
	foreach ($types['CARRY'] as $g) {
		if ($g[0] == 'z' && $g != 'z45') {
			$bad[] = $g;
		}
	}

	// If it's a Z, it's wrong
	foreach ($types['AND'] as $g) {
		if ($g[0] == 'z') {
			$bad[] = $g;
		}
	}

	sort($bad);
	$part2 = implode(',', $bad);
	echo 'Part 2: ', $part2, "\n";
