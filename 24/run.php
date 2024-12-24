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
		foreach (array_keys($result) as $k) { $result[$k] = strrev($result[$k]); }

		return $result;
	}

	$numbers = getNumbers();
	$part1 = $numbers['z'];
	echo 'Part 1: ', bindec($part1), "\n";
