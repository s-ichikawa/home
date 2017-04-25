<?php

class StringBuilder
{
    private $buffer = [];

    /**
     * StringBuilder constructor.
     * @param $str
     */
    public function __construct($str = null)
    {
        $this->append($str);
    }

    public function append($str)
    {
        $this->buffer[] = $str;
        return $this;
    }

    public function toString()
    {
        return $this->__toString();
    }

    function __toString()
    {
        return implode('', $this->buffer);
    }
}

$builder = new StringBuilder();
$builder->append('abc')->append('def')->append('ghij');

echo $builder;