<?php

class Coin implements Countable
{
    private $coin = 0;

    public function increment()
    {
        ++$this->coin;
    }

    public function count()
    {
        return $this->coin;
    }
}

$coin = new Coin();

echo $coin->count() . PHP_EOL;

$coin->increment();

echo $coin->count() . PHP_EOL;

$coin->increment();

echo $coin->count() . PHP_EOL;

$coin->increment();

echo $coin->count() . PHP_EOL;
