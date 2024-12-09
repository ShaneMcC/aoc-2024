#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLine();

	function generateDiskMap($input) {
		$diskMap = ['.' => []];
		$isFile = True;
		$fileID = 0;
		$id = 0;
		foreach (str_split($input) as $s) {
			if ($isFile) { $diskMap[$fileID] = []; }

			$f = ($isFile) ? $fileID : '.';
			for ($i = 0; $i < intval($s); $i++) {
				$diskMap[$f][$id++] = True;
			}

			if ($isFile) { $fileID++; }
			$isFile = !$isFile;
		}

		return $diskMap;
	}

	function nextFree($diskMap, $size = 1) {
		$blocks = array_keys($diskMap['.']);
		if ($size == 1) {
			return min($blocks);
		} else {
			sort($blocks);
			for ($i = 0; $i < count($blocks); $i++) {
				$isValid = true;

				for ($j = $i + 1; $j < $i + $size; $j++) {
					if (!isset($blocks[$j]) || $blocks[$j] - ($blocks[$j - 1] ?? 0) > 1) {
						$isValid = false;
						$i = $j - 1;
						break;
					}
				}

				if ($isValid) { return $blocks[$i]; }
			}

			return null;
		}
	}

	function nextData($diskMap) {
		$max = 0;
		$maxFileID = null;
		foreach ($diskMap as $fileid => $data) {
			if ($fileid == '.') { continue; }
			$maxDataPos = max(array_keys($data));
			if ($maxDataPos > $max) {
				$max = $maxDataPos;
				$maxFileID = $fileid;
			}
		}

		return [$max, $maxFileID];
	}

	function sortMap($diskMap) {
		$freeID = nextFree($diskMap);
		[$dataID, $fileID] = nextData($diskMap);

		// Begin sorting
		while ($freeID < $dataID) {
			unset($diskMap[$fileID][$dataID]);
			unset($diskMap['.'][$freeID]);

			$diskMap[$fileID][$freeID] = True;
			$diskMap['.'][$dataID] = True;

			$freeID = nextFree($diskMap);
			[$dataID, $fileID] = nextData($diskMap);
		}

		return $diskMap;
	}

	function cleverSortMap($diskMap) {
		$largest = max(array_keys($diskMap));

		for ($fileid = $largest; $fileid >= 1; $fileid--) {
			$fileStart = min(array_keys($diskMap[$fileid]));
			$len = count($diskMap[$fileid]);

			$freeSpace = nextFree($diskMap, $len);

			// echo "{$fileid} => Length {$len}, Moving from {$fileStart} to {$freeSpace}", "\n";

			if ($freeSpace !== null && $freeSpace < $fileStart) {
				$diskMap[$fileid] = [];
				for ($i = 0; $i < $len; $i++) {
					unset($diskMap['.'][$freeSpace + $i]);

					$diskMap[$fileid][$freeSpace + $i] = True;
					$diskMap['.'][$fileStart + $i] = True;
				}
			}

			// echo implode('', $diskMap), "\n";
			// echo "\n";

		}

		return $diskMap;
	}

	function checksum($diskMap) {
		$checksum = 0;
		foreach ($diskMap as $fileid => $data) {
			if ($fileid == '.') { continue; }

			$checksum += array_sum(array_keys($data)) * $fileid;
		}

		return $checksum;
	}

	$diskMap = generateDiskMap($input);
 	$sorted = sortMap($diskMap);
	$part1 = checksum($sorted);
	echo 'Part 1: ', $part1, "\n";

	$sorted = cleverSortMap($diskMap);
	$part2 = checksum($sorted);
	echo 'Part 2: ', $part2, "\n";
