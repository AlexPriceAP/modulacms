<?php

namespace Modula\Framework;

/**
 * @ingroup application
 */
abstract class Object {

    protected $vars = array();

    /**
     * Sets the given variable name to the value passed
     *
     * @param string $index
     * @param mixed $value
     */
    function __set($index, $value) {
        $this->vars[$index] = $value;
    }

    /**
     * Returns the value of a previously set variable
     *
     * @param string $index
     * @return mixed
     */
    function __get($index) {
        if (!isset($this->vars[$index])) {
            return null;
        }
        return $this->vars[$index];
    }

    /**
     * Converts an object name into a string
     *
     * @return string
     */
    public function toString() {
        return get_class($this);
    }

}

?>