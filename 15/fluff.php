<?php

    function initFluff() {
        $__stty = exec('/bin/stty -g');
        system('/bin/stty min 0 time 0');
        system('/bin/stty -echo');
        system("/bin/stty -icanon");
        echo "\033[?25l"; // Hide Cursor
        echo "\033[?7l"; // Wrap Off

        $exitFunc = function() use ($__stty) {
            global $__EXIT;
            $__EXIT = True;

            system("/bin/stty icanon");
            system('/bin/stty echo');
            system('/bin/stty ' . escapeshellarg($__stty));

            echo "\033[?25h"; // Show Cursor
            echo "\033[?7h"; // Wrap On
        };

        pcntl_signal(SIGINT, $exitFunc);
        pcntl_signal(SIGTERM, $exitFunc);
        register_shutdown_function($exitFunc);
        pcntl_async_signals(true);

        echo 'Interactive mode enabled.', "\n";
        echo "\n";
        echo 'Move with: W, A, S, D', "\n";
        echo 'Finish with: X', "\n";
        echo "\n";

        function getInstructions($instructions) {
            global $__EXIT;

            stream_set_blocking(STDIN, False);

            while (!$__EXIT) {
                [$r, $w, $e] = [[STDIN], NULL, NULL];
                try {
                    $res = @stream_select($r, $w, $e, 0);
                } catch (Exception $e) { $res = False; }

                if ($res === FALSE || $res === 0) {
                    usleep(100);
                    continue;
                }
                $char = stream_get_line(STDIN, 1);

                $char = strtolower($char);
                if (in_array($char, ['w', 'a', 's', 'd'])) {
                    yield str_replace(['w', 'a', 's', 'd'], ['^', '<', 'v', '>'], $char);
                } else if ($char == 'x') {
                    break;
                }
            }
        }
    }

    function fluffMap($map, $title = '', $redraw = False) {
        foreach (cells($map) as [$x, $y, $cell]) {
            if ($cell == '#') {
                $map[$y][$x] = "\033[91m" . $cell . "\033[0m";
            }

            if ($cell == '.') {
                $map[$y][$x] = ' ';
            }

            if ($cell == 'O' || $cell == '[' || $cell == ']') {
                $map[$y][$x] = "\033[92m" . $cell . "\033[0m";
            }

            if ($cell == '@') {
                $map[$y][$x] = "\033[93m" . $cell . "\033[0m";
            }
        }

        if ($redraw) {
            echo "\033[" . (count($map) + 7) . "A";
        }
        drawMap($map, true, empty($title) ? 'Map' : $title);
    }
