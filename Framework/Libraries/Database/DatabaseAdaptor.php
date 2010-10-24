<?php

namespace Modula\Framework;

class DatabaseAdaptor {

    // Stores a list of database adaptors
    static private $_instances = array();

    /**
     * Tries to return an adaptor object for the given parameters, first we try
     * and include the file, then we try instantiating the newly included
     * adaptor class.
     *
     * @param string $name
     * @return object
     */
    static function getInstance($params) {
        if (is_array($params) && array_key_exists('type', $params)) {
            $file = dirname(__FILE__) . '/adaptors/' . $params['type'] . '.php';
            $class = ucfirst(strtolower($params['type'])) . 'DatabaseAdaptor';
            if (empty(self::$_instances[$params['type']])) {
                if (file_exists($file)) {
                    require_once($file);
                    if (class_exists($class)) {
                        self::$_instances[$params['type']] = new $class($params);
                    } else {
                        throw new CustomException('Unable to load specified database adaptor');
                    }
                } else {
                    throw new CustomException("Unable to load database adaptor from: {$file}");
                }
            }
            return self::$_instances[$params['type']];
        } else {
            throw new CustomException("Invalid parameters passed to DatabaseAdaptor class");
        }
    }

    private function __construct() {

        // Do nothing
    }

}

?>
