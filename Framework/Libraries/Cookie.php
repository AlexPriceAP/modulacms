<?php

namespace Modula\Framework;

/**
 * @ingroup sessions
 */
class Cookie {

    function get($id) {
        return isset($_COOKIE['username']) ? $_COOKIE['username'] : false;
    }

    function set($id, $value, $expire = 30758400, $path = '/') {
        return setcookie($id, $value, time() + $expire, $path, '', 0);
    }

// Destroy the cookies by setting them to expire in the past
    function remove($id) {
        return setcookie($id, '', time('12,0,0,1, 1, 1990'), '/', '', 0);
    }

}

?>