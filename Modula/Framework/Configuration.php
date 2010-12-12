<?php

namespace Modula\Framework;

/**
 * @ingroup application
 */
class Configuration extends Object {

    private $database;
    private $eventdispatcher;
    private $cache;
    private $configStore = array();

    public function __construct($database, $eventdispatcher, $cache) {
        $this->database = $database;
        $this->eventdispatcher = $eventdispatcher;
        $this->cache = $cache;
    }

    public function get($key, $default = '') {
        if (!$this->configStore[$key] = Factory::getCache()->get('configuration', $key)) {
            $this->configStore[$key] = unserialize(Factory::getDatabase()->dbSelect("SELECT c.value FROM configuration c WHERE c.key = :key", array(':key' => $key))->fetchColumn());
            $this->cache->set('configuration', $key, $this->configStore[$key]);
        }
        if ($this->configStore[$key]) {
            return $this->configStore[$key];
        } else {
            $this->set($key, $default);
            return $default;
        }
    }

    public function set($key, $value) {
        if ($this->database->dbExecute("REPLACE INTO configuration VALUES (:key, :value)", array(':key' => $key, ':value' => serialize($value)))) {
            $this->cache->remove('configuration', $key);
            return true;
        } else {
            return false;
        }
    }

}

?>