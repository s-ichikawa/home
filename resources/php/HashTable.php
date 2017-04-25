<?php

class HashTable
{
    private $data = [];

    /**
     * HashTable constructor.
     */
    public function __construct()
    {
        $this->data = new SplFixedArray(5);
    }


    public function add($str)
    {
        $this->data[md5($str)] = $str;
    }

    public function all()
    {
        return $this->data;
    }

}

$hashTable = new HashTable();
$list = ['hi', 'abc', 'aa', 'qs', 'pl'];

foreach ($list as $str) {
    $hashTable->add($str);
}

var_dump($hashTable->all());
