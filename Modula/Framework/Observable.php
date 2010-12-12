<?php

abstract class Observable
{
    
    protected $_observers = array();
    
    public function attach($observer)
    {
        $this->_observers[] = $observer;
    }
    
    public function notify()
    {
        foreach ($this->_observers as $observer) {
            $observer->update($this);
        }
    }

}

?>