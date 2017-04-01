<?php

class MyObserver1 implements SplObserver
{
    public function update(SplSubject $subject)
    {
        echo __CLASS__ . ' - ' . $subject->getName();
    }
}

class MyObserver2 implements SplObserver
{
    public function update(SplSubject $subject)
    {
        echo __CLASS__ . ' - ' . $subject->getName();
    }
}

class MySubject implements SplSubject
{
    private $_name;

    private $_observers;

    /**
     * Subject constructor.
     * @param $name
     */
    public function __construct($name)
    {
        $this->_name = $name;
        $this->_observers = new SplObjectStorage();
    }
    public function attach(SplObserver $observer) {
        $this->_observers->attach($observer);
    }

    public function detach(SplObserver $observer) {
        $this->_observers->detach($observer);
    }

    public function notify() {
        foreach ($this->_observers as $observer) {
            $observer->update($this);
        }
    }

    public function getName() {
        return $this->_name;
    }
}

$observer1 = new MyObserver1();
$observer2 = new MyObserver2();

$subject = new MySubject("test");

$subject->attach($observer1);
$subject->attach($observer2);
$subject->notify();
