<?php

namespace Modula\Framework\Html;

class Image extends Element {

    public function render() {
        return sprintf('<img %s />', $this->renderAttributes());
    }

}

?>