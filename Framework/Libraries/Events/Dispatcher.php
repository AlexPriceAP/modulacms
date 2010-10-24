<?php

/**
 * @defgroup events
 * @ingroup application
 */
final class EventDispatcher extends Observable {

    /**
     * registerEventCallback
     * Registers a new callback function for a specified
     * event id
     * @param string $eventid
     * @param pointer $handler
     */
    public function registerEventCallback($handler) {
        if (($handler instanceof Module)) {
            $this->attach($handler);
        } else {
            throw new Exception("Event handler " . (string) $handler . " not recognized.");
        }
    }

    /**
     * triggerEvent
     * Triggers all callback functions for a specified
     * event id, takes an array of arguments that can be passed
     * onto the callback function.
     * @param string $eventid
     * @param array $args
     * @return mixed
     */
    public function triggerEvent($eventid, $stack = array(), $pipeline = false) {
        $result = array();
        foreach ($this->_observers as $observer) {
            if (is_object($observer)) {
                // Check that the handler is callable
                if (method_exists($observer, $eventid)) {
                    if ($pipeline) {
                        $result = $stack = $observer->$eventid($stack);
                    } else {
                        $result[] = $observer->$eventid($stack);
                    }
                }else {
                    continue;
                }
            }
        }
        return $result;


    }

}


?>