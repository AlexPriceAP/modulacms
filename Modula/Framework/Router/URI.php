<?php

namespace Modula\Framework;

/**
 * @ingroup application
 * @version $Id: uri.php -1   $
 */
class URI {

    public $requesturi;

    public function __construct() {
        $request = $_SERVER['REQUEST_URI'];
        $pos = strpos($request, '?');
        if ($pos)
            $request = substr($request, 0, $pos);
        $this->requesturi = $request;
    }

}

?>