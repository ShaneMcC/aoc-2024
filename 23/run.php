#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$computers = [];
	foreach ($input as $line) {
		preg_match('#(.*)-(.*)#ADi', $line, $m);
		[$all, $a, $b] = $m;
		if (!isset($computers[$a])) { $computers[$a] = []; }
		if (!isset($computers[$b])) { $computers[$b] = []; }

		$computers[$a][] = $b;
		$computers[$b][] = $a;
	}

	function findSetsOfThree($computers) {
		$setsOfThree = [];

		foreach ($computers as $a => $adata) {
			foreach ($adata as $b) {
				foreach ($computers[$b] as $c) {
					foreach ($computers[$c] as $d) {
						if ($d == $a) {
							$set = [$a, $b, $c];
							sort($set);
							$set = implode(',', $set);

							$startsWithT = ($a[0] == 't' || $b[0] == 't' || $c[0] == 't');

							$setsOfThree[$set] = $startsWithT;
						}
					}
				}
			}
		}

		return $setsOfThree;
	}

	// Bron–Kerbosch algorithm.
	//
	// Based on https://stackoverflow.com/a/24415469
	function findMaximalCliques($computers, $possibleClique=[], $skip=[]) {
		$cliques = [];

		if (empty($computers) && empty($skip)) {
			sort($possibleClique);
			$name = implode(',', $possibleClique);
			$cliques[] = $name;
		} else {
			foreach ($computers as $c => $neigh) {
				$newPossibleClique = array_merge($possibleClique, [$c]);
				$newComputers = array_filter($computers, fn($x) => in_array($x, $neigh), ARRAY_FILTER_USE_KEY);
				$newSkip = array_filter($skip, fn($x) => in_array($x, $neigh));

				$cliques = array_merge($cliques, findMaximalCliques($newComputers, $newPossibleClique, $newSkip));

				unset($computers[$c]);
				$skip[] = $c;
			}
		}

		return $cliques;
	}

	$setsOfThree = findSetsOfThree($computers);
	$part1 = array_sum($setsOfThree);
	echo 'Part 1: ', $part1, "\n";

	$part2 = '';
	$cliques = findMaximalCliques($computers);
	foreach ($cliques as $clique) {
		if (strlen($clique) > strlen($part2)) {
			$part2 = $clique;
		}
	}
	echo 'Part 2: ', $part2, "\n";
