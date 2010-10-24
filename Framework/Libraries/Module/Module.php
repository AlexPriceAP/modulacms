<?php

namespace Modula\Framework;

abstract class Module extends Object {

    public function renderTemplate($file) {
        $template = new Template($file);
        return $template->render();
    }

}

?>