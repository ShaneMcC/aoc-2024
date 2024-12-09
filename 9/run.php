#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	function displaydisk($diskMap) {
		foreach ($diskMap['blocks'] as $b) {
			echo $b === null ? '.' : $b;
		}
		echo "\n";
	}

	function generateDiskMap($input) {
		$map = [];
		$map['blocks'] = [];
		$map['files'] = [];
		$map['free'] = [];

		$isFile = True;
		$fileID = 0;
		$pos = 0;
		for ($i = 0; $i < strlen($input); $i++) {
			$s = (int)($input[$i]);
			for ($j = 0; $j < $s; $j++) {
				$map['blocks'][] = ($isFile) ? $fileID : NULL;
			}
			$detail = ['start' => $pos, 'length' => $s];
			$pos += $s;

			if ($isFile) {
				$map['files'][$fileID] = $detail;
				$fileID++;
			} else if ($s > 0) {
				$map['free'][] = $detail;
			}
			$isFile = !$isFile;
		}

		return $map;
	}

	function nextFree($diskMap, $size = 1) {
		foreach ($diskMap['free'] as $freeID => $freeDetail) {
			if ($freeDetail['length'] >= $size) {
				return $freeID;
			}
		}

		return null;
	}

	function nextData($diskMap, $pos) {
		// Find last non-free space
		for ($i = $pos; $i >= 0; $i--) {
			if ($diskMap['blocks'][$i] !== NULL) {
				return $i;
			}
		}
		return null;
	}

	function sortMap($diskMap) {
		$freeID = nextFree($diskMap, 1);
		$freeBlockID = $diskMap['free'][$freeID]['start'];
		$dataID = nextData($diskMap, count($diskMap['blocks']) - 1);

		// Begin sorting
		while ($freeBlockID < $dataID) {
			$diskMap['free'][$freeID]['start']++;
			$diskMap['free'][$freeID]['length']--;
			if ($diskMap['free'][$freeID]['length'] == 0) { unset($diskMap['free'][$freeID]); }

			$diskMap['blocks'][$freeBlockID] = $diskMap['blocks'][$dataID];
			$diskMap['blocks'][$dataID] = NULL;

			$freeID = nextFree($diskMap);
			$freeBlockID = $diskMap['free'][$freeID]['start'];
			$dataID = nextData($diskMap, $dataID);
		}

		return $diskMap['blocks'];
	}

	function cleverSortMap($diskMap) {
		// displaydisk($diskMap);
		// echo "\n";

		$largest = max(array_values($diskMap['blocks']));

		for ($fileid = $largest; $fileid >= 1; $fileid--) {
			$fileStart = $diskMap['files'][$fileid]['start'];
			$len = $diskMap['files'][$fileid]['length'];

			$freeID = nextFree($diskMap, $len);
			if ($freeID !== null) {
				$freeSpace = $diskMap['free'][$freeID]['start'];
				$diskMap['free'][$freeID]['start'] += $len;
				$diskMap['free'][$freeID]['length'] -= $len;
				if ($diskMap['free'][$freeID]['length'] == 0) { unset($diskMap['free'][$freeID]); }
			} else {
				$freeSpace = null;
			}

			// echo "{$fileid} => Length {$len}, Moving from {$fileStart} to {$freeSpace}", "\n";

			if ($freeSpace !== null && $freeSpace < $fileStart) {
				for ($i = 0; $i < $len; $i++) {
					$diskMap['blocks'][$freeSpace + $i] = $fileid;
					$diskMap['blocks'][$fileStart + $i] = NULL;
				}
			}

			// displaydisk($diskMap);
			// echo "\n";

		}

		return $diskMap['blocks'];
	}

	function checksum($diskMapBlocks) {
		$checksum = 0;
		for ($i = 0; $i < count($diskMapBlocks); $i++) {
			if ($diskMapBlocks[$i] !== NULL) {
				$checksum += $diskMapBlocks[$i] * $i;
			}
		}

		return $checksum;
	}

	$diskMap = generateDiskMap($input);
	$part1 = checksum(sortMap($diskMap));
	echo 'Part 1: ', $part1, "\n";

	$part2 = checksum(cleverSortMap($diskMap));
	echo 'Part 2: ', $part2, "\n";
