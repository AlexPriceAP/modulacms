<?php

namespace Modula\Framework\Html;

use \Modula\Framework\Object;

abstract class Element extends Object {

    protected $_attributes = array();
    protected $_elements = array();
    protected $_errors = array();

    public function __construct($attributes = array()) {
        foreach ($attributes as $key => $value) {
            $this->_attributes[$key] = $value;
        }
    }

    public function validate() {
        foreach($this->_elements as $element){
            if(!$element->validate){
                return false;
            }
        }
        return true;
    }

    public function addElement(Element $element) {
        $this->_elements[] = $element;
    }

    public function renderAttributes() {
        $output = '';
        foreach ($this->_attributes as $key => $value) {
            $output .= $key . '="' . $value . '"';
        }
        return $output;
    }

    public function renderElements() {
        $output = '';
        foreach ($this->_elements as $element) {
            $output .= $element->render();
        }
        return $output;
    }

}

?>