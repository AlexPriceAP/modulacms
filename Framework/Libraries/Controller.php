<?php

namespace Modula\Framework;

class Controller {

    private $_module;

    public function __construct($module, $action) {
        $this->_module = ModuleHandler::getModule($module);

        if (is_callable(array($this->_module, $action))) {
            call_user_func(array($this->_module, $action));
        } else {
            throw new CustomException('Problem performing action: ' . $this->action);
        }
    }

}

?>
