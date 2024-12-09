#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	function generateDiskMap($input) {
		$real = [];
		$isFile = True;
		$fileID = 0;
		foreach (str_split($input) as $s) {
			if ($isFile) {
				$real = array_merge($real, array_fill(0, intval($s), $fileID));
				$fileID++;
			} else {
				$real = array_merge($real, array_fill(0, intval($s), '.'));
			}
			$isFile = !$isFile;
		}

		return $real;
	}

	function nextFree($diskMap, $pos) {
		// Find first free space
		for ($i = $pos; $i < count($diskMap); $i++) {
			if ($diskMap[$i] == '.') {
				return $i;
			}
		}

		return null;
	}

	function nextData($diskMap, $pos) {
		// Find last non-free space
		for ($i = $pos; $i >= 0; $i--) {
			if ($diskMap[$i] != '.') {
				return $i;
			}
		}
		return null;
	}

	function sortMap($diskMap) {
		$freeID = nextFree($diskMap, 0);
		$dataID = nextData($diskMap, count($diskMap) - 1);

		// Begin sorting
		while ($freeID < $dataID) {
			$diskMap[$freeID] = $diskMap[$dataID];
			$diskMap[$dataID] = '.';

			$freeID = nextFree($diskMap, $freeID);
			$dataID = nextData($diskMap, $dataID);
		}

		return $diskMap;
	}

	function checksum($diskMap) {
		$checksum = 0;
		for ($i = 0; $i < count($diskMap); $i++) {
			if ($diskMap[$i] != '.') {
				$checksum += $diskMap[$i] * $i;
			}
		}

		return $checksum;
	}

	$diskMap = generateDiskMap($input);
	$sorted = sortMap($diskMap);
	$part1 = checksum($sorted);
	echo 'Part 1: ', $part1, "\n";

	// $part2 = 0;
	// echo 'Part 2: ', $part2, "\n";
