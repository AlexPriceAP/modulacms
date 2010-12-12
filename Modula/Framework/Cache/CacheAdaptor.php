<?php

namespace Modula\Framework;

/**
 * @ingroup cache
 */
abstract class CacheAdaptor extends Object {

    static private $_instances = array();

    /**
     * @param string $name
     * @return object
     */
    static function getInstance($params) {
        if (is_array($params) && array_key_exists('type', $params)) {
            $file = dirname(__FILE__) . '/adaptors/' . $params['type'] . 'cacheadaptor.php';
            $class = ucfirst(strtolower($params['type'])) . 'CacheAdaptor';
            if (empty(self::$_instances[$params['type']])) {
                if (file_exists($file)) {
                    require($file);
                    if (class_exists($class)) {
                        self::$_instances[$params['type']] = new $class($params);
                    } else {
                        throw new CustomException('Unable to load specified cache adaptor');
                    }
                } else {
                    throw new CustomException("Unable to load cache adaptor from: {$file}");
                }
            }
            return self::$_instances[$params['type']];
        } else {
            throw new CustomException("Invalid parameters passed to CacheAdaptor class");
        }
    }

    private function __construct() {
        // Do nothing
    }

    public function get($group, $id) {
        die('Get method not implemented in ' . $this->toString());
    }

    public function set($group, $id, $data) {
        die('Set method not implemented in ' . $this->toString());
    }

    public function remove($group, $id) {
        die('Remove method not implemented in ' . $this->toString());
    }

    public function clear($group) {
        die('Clear method not implemented in ' . $this->toString());
    }

    public function gc() {
        die('GC method not implemented in ' . $this->toString());
    }

}

?>
