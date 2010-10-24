<?php

namespace Modula\Framework;

/**
 * @defgroup database
 * @ingroup application
 */
class Database {

    private static $_instances = array();

    static function getInstance($params) {
        if (is_array($params) && array_key_exists('id', $params) && array_key_exists('type', $params)) {
            if (!array_key_exists($params['id'], self::$_instances))
                self::$_instances[$params['id']] = DatabaseAdaptor::getInstance($params);
            if (is_object(self::$_instances[$params['id']]))
                return self::$_instances[$params['id']];
            else
                throw new CustomException("Failed to create an instance of database adaptor: {$params['type']}");
        } else {
            throw new CustomException('Invalid parameters passed to Database::getInstance()');
        }
    }

    private function __construct() {

        // Do nothing
    }

}

?>