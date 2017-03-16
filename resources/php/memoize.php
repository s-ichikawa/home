<?php
function getVal()
{
    return random_bytes(10000);
}

function memoize()
{
    static $val;
    return $val ?? $val = getVal();
}

/*
 * non memoize
 */

$start = memory_get_usage();

$val = getVal();

echo (memory_get_usage() - $start) . PHP_EOL;

unset($val);

echo (memory_get_usage() - $start) . PHP_EOL;


/*
 * memoize
 */

$start = memory_get_usage();

$val = memoize();

echo (memory_get_usage() - $start) . PHP_EOL;

unset($val);

echo (memory_get_usage() - $start) . PHP_EOL;


