#!/usr/bin/php
<?php
	require_once(dirname(__FILE__) . '/../common/common.php');
	$input = getInputLines();

	$register = [];
	$program = [];
	foreach ($input as $line) {
		if (preg_match('#^Register (.*): (.*)$#ADi', $line, $m)) {
			$register[$m[1]] = $m[2];
		} else if (preg_match('#^Program: (.*)$#ADi', $line, $m)) {
			$program = explode(',', $m[1]);
		}
	}

	require_once(dirname(__FILE__) . '/Day17VM.php');

    $vm = new Day17VM($register, $program);

	// 2,4,1,5,7,5,1,6,4,3,5,5,0,3,3,0
	// 139543356615066
	function runAsPHP($A = 0, $B = 0, $C = 0) {
		$out = [];

		/* do {
			// 2,4 > bst 4
			$B = $A % 8;

			// 1,5 > bxl 5
			$B = $B ^ 5;

			// 7,5 > cdv 5
			$C = (int)($A / pow(2, $B));

			// 1,6 > bxl 6
			$B = $B ^ 6;

			// 4,3 > bxc 3
			$B = $B ^ $C;

			// 5,5 > out 5
			$out[] = $B % 8;

			// 0,3 > adv 3
			$A = (int)($A / 8);

			// 3,0 > jnz 0
		} while ($A != 0); */

		do {
			$B = ($A % 8) ^ 5;
			$out[] = (($B ^ 6) ^ (int)($A / pow(2, $B))) % 8;
			$A = (int)($A / 8);
		} while ($A != 0);

		return $out;
	}

    function vmdebug($vm, $a) {
        $out = $vm->run($a);
        echo $a, ': ', implode(',', $out), ' [', count($out),']', "\n";
    }

	$testA = $register['A'];

    $run = implode(',', $vm->run($testA));
	$runPHP = implode(',', runAsPHP($testA));

	if ($run != $runPHP) {
        echo 'PHP: ERROR', "\n";
        die();
    } else {
        echo 'PHP: OK', "\n";
    }


    // Extra digit at: 0, 8, 64, 512, 4096
	// for ($i = 0; 5000; $i++) {
		// $vm->debug($i);
	// }

    // Extra digit at: 0, 8, 64, 512, 4096
    vmdebug($vm, 0);
    echo "...", "\n";
	for ($i = 7; $i < PHP_INT_MAX; $i = ((($i + 1) * 8) - 1)) {
		vmdebug($vm, $i);
        vmdebug($vm, $i + 1);
        echo "...", "\n";
	}
