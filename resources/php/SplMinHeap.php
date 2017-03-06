<?php

ini_set('memory_limit', '2024M');

for ($size = 5; $size < 20000; $size *= 2) {
    echo '<br>' . "Testing size: $size" . '<br>';


    $start_time = microtime(true);
    $start_memory = memory_get_usage();
    $heap = new SplMinHeap();
    for ($i = 0; $i < $size; $i++) {
        $heap->insert($i);
    }
    echo 'SplMaxHeap:time:' . (microtime(true) - $start_time) . 's<br>';
    echo 'SplMaxHeap:memory:' . (memory_get_usage() - $start_memory) . 'B<br>';

    $start_time = microtime(true);
    $start_memory = memory_get_usage();
    $array = [];
    for ($i = 0; $i < $size; $i++) {
        $array[] = $i;
    }
    rsort($array);
    echo 'array and sort:time:' . (microtime(true) - $start_time) . 's<br>';
    echo 'array and sort:memory:' . (memory_get_usage() - $start_memory) . 'B<br>';

    unset($heap);
    unset($array);
}
