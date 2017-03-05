<?php

ini_set('memory_limit', '2024M');

for($size = 5; $size < 20000; $size *= 2) {
    echo '<br>' . "Testing size: $size" . '<br>';

    unset($container);
    $start_memory = memory_get_usage();
    $s = microtime(true);
    for($container = Array(), $i = 0; $i < $size; $i++) {
        $container[$i] = $i;
    }
    echo "Array():memory:    " . (memory_get_usage() - $start_memory) . '<br>';
    echo "Array():write: " . (microtime(true) - $s) . '<br>';

    $s = microtime(true);
    foreach ($container as $value);
    echo "Array():read: " . (microtime(true) - $s) . '<br>';


    unset($container);
    $start_memory = memory_get_usage();
    $s = microtime(true);
    for($container = new SplFixedArray($size), $i = 0; $i < $size; $i++) {
        $container[$i] = $i;
    }
    echo "SplArray():memory:    " . (memory_get_usage() - $start_memory) . '<br>';
    echo "SplArray():write: " . (microtime(true) - $s) . '<br>';

    $s = microtime(true);
    foreach ($container as $value);
    echo "SplArray():read: " . (microtime(true) - $s) . '<br>';
}

