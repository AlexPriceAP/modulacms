<?php

namespace Modula\Framework\Sessions\Storage;

/**
 * @ingroup sessionstorage
 */
class DefaultSessionStorage extends SessionStorage {

    function __construct() {

        // Stop parent from setting custom save handlers
    }

}

?>