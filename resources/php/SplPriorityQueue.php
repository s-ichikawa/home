<?php

$queue = new SplPriorityQueue();

$queue->insert('a', 1);
$queue->insert('b', 3);
$queue->insert('c', 2);

foreach ($queue as $value) {
    var_dump($value);
}
