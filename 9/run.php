#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	function generateDiskMap($input) {
		$real = [];
		$isFile = True;
		$fileID = 0;
		foreach (str_split($input) as $s) {
			for ($i = 0; $i < $s; $i++) {
				$real[] = ($isFile) ? $fileID : NULL;
			}
			if ($isFile) {
				$fileID++;
			}
			$isFile = !$isFile;
		}

		return $real;
	}

	function nextFree($diskMap, $pos, $size = 1) {
		for ($i = $pos; $i < count($diskMap); $i++) {
			if ($diskMap[$i] === NULL) {
				$isValid = true;
				for ($j = $i; $j < $i + $size; $j++) {
					if (!array_key_exists($j, $diskMap) || $diskMap[$j] !== NULL) {
						$isValid = false;
						$i = $j - 1;
						break;
					}
				}

				if ($isValid) { return $i; }
			}
		}

		return null;
	}

	function nextData($diskMap, $pos) {
		// Find last non-free space
		for ($i = $pos; $i >= 0; $i--) {
			if ($diskMap[$i] !== NULL) {
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
			$diskMap[$dataID] = NULL;

			$freeID = nextFree($diskMap, $freeID);
			$dataID = nextData($diskMap, $dataID);
		}

		return $diskMap;
	}

	function findEndOfFile($diskMap, $file, $start = 0) {
		for ($i = $start; $i < count($diskMap); $i++) {
			if ($diskMap[$i] != $file) { break; }
		}

		return $i - 1;
	}

	function cleverSortMap($diskMap) {
		$largest = max(array_values($diskMap));

		for ($fileid = $largest; $fileid >= 1; $fileid--) {
			$fileStart = array_search($fileid, $diskMap);
			$fileEnd = findEndOfFile($diskMap, $fileid, $fileStart);
			$len = ($fileEnd + 1) - $fileStart;

			$freeSpace = nextFree($diskMap, 0, $len);

			// echo "{$fileid} => Length {$len}, Moving to: {$freeSpace}", "\n";

			if ($freeSpace !== null && $freeSpace < $fileStart) {
				for ($i = 0; $i < $len; $i++) {
					$diskMap[$freeSpace + $i] = $fileid;
					$diskMap[$fileStart + $i] = NULL;
				}
			}

			// echo implode('', $diskMap), "\n";
			// echo "\n";

		}

		return $diskMap;
	}

	function checksum($diskMap) {
		$checksum = 0;
		for ($i = 0; $i < count($diskMap); $i++) {
			if ($diskMap[$i] !== NULL) {
				$checksum += $diskMap[$i] * $i;
			}
		}

		return $checksum;
	}

	$diskMap = generateDiskMap($input);

 	$sorted = sortMap($diskMap);
	$part1 = checksum($sorted);
	echo 'Part 1: ', $part1, "\n";

	$sorted = cleverSortMap($diskMap);

	// echo implode('', $sorted), "\n";

	$part2 = checksum($sorted);
	echo 'Part 2: ', $part2, "\n";
