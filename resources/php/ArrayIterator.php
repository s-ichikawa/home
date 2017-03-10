<?php

$array = new \ArrayIterator([
    'a' => 'hoge',
    0 => 'zero',
    'b' => 'foo',
    1 => 'bar',
]);

foreach (new \LimitIterator($array, 1, 2) as $key => $val) {
    var_dump($key . ':' . $val);
}
