<?php

namespace Modula\Framework\Html;

class Input extends Element {

    public function render() {
        return sprintf('<input %s />', $this->renderAttributes());
    }

}

?>