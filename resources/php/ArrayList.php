<?php

class ArrayList
{
    /**
     * @var SplFixedArray
     */
    private $data;

    static private $index = 0;

    /**
     * ArrayList constructor.
     * @param int $size
     */
    public function __construct($size = 1)
    {
        $this->initData($size);
    }

    private function initData($size, $data = [])
    {
        $this->data = new SplFixedArray($size);
        for ($i = 0; $i < count($data); $i++) {
            $this->data->offsetSet($i, $data[$i]);
        }
    }

    public function add($value)
    {
        if ($this->data->getSize() == self::$index) {
            $this->initData($this->data->getSize() + 1, $this->data);
        }
        $this->data->offsetSet(self::$index++, $value);
    }

    public function all()
    {
        return $this->data->toArray();
    }
}

$list = new ArrayList();

$i = 0;
$list->add('val: ' . ++$i);
$list->add('val: ' . ++$i);
$list->add('val: ' . ++$i);
$list->add('val: ' . ++$i);
$list->add('val: ' . ++$i);
$list->add('val: ' . ++$i);


var_dump($list->all());