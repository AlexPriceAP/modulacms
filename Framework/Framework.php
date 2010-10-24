<?php

namespace Modula\Framework;

class Framework {

    public function __construct() {
        if (version_compare(phpversion(), '5.3.2', '<')) {
            die('I\'m sorry but your current PHP version is unsupported, please upgrade PHP to at least 5.3.2');
        }
        $user = new User();
    }

}
?>