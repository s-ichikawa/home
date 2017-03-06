<?php

$stack = new SplStack();

$stack->push(1);
$stack->push(3);
$stack->push(5);
$stack->push(7);
$stack->push(9);

var_dump($stack);
foreach ($stack as $value) {
    echo $value . ',';
}


$stack->add(0, 2);

var_dump($stack);
foreach ($stack as $value) {
    echo $value . ',';
}
