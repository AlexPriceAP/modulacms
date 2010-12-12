<?php

namespace Modula\Framework\Html;

class Form extends Element {

    public function render() {
        $tokenfield = new Input(array('name' => 'securetoken', 'type' => 'hidden', 'value' => md5('test')));
        return sprintf('<form %s>%s%s</form>', $this->renderAttributes(), $this->renderElements(), $tokenfield->render());
    }

}

?>
