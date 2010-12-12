<?php

namespace Modula\Framework;

class Factory {

    private static $_instances;

    public static function getUser($username) {
        $user = new User($username);
        return $user;
    }

    public static function getCache() {
        if (!array_key_exists('cache', self::$_instances)) {
            self::$_instances['cache'] = Cache::getInstance(array('id' => 'default', 'type' => 'file'));
        }
        return self::$_instances['cache'];
    }

    public static function getConfig() {
        if (!array_key_exists('config', self::$_instances)) {
            self::$_instances['config'] = new Configuration(self::getDatabase(), self::getEventDispatcher(), self::getCache());
        }
        return self::$_instances['config'];
    }

    public static function getSession() {
        $database = self::getDatabase();
        $options = array('storage' => SESSION_STORAGE,
            'cookie.expire' => self::getConfig()->get('session.cookie.expire', 90000),
            'cookie.name' => self::getConfig()->get('session.cookie.name', 'chocolatechip'),
            'cookie.path' => self::getConfig()->get('session.cookie.path', '/'));
        if (!array_key_exists('session', self::$_instances)) {
            self::$_instances['session'] = new Session($options);
        }
        return self::$_instances['session'];
    }

    public static function getLoader() {
        if (!array_key_exists('modloader', self::$_instances)) {
            self::$_instances['modloader'] = new ModuleHandler();
        }
        return self::$_instances['modloader'];
    }

    public static function getDatabase() {
// @todo Link into database configuration page
        $database = Database::getInstance(array('id' => 'default',
                    'type' => 'pdo',
                    'server' => db_SERVER,
                    'user' => db_USER,
                    'pass' => db_PASS));
        return $database;
    }

    public static function getEventDispatcher() {
        if (!array_key_exists('eventdispatcher', self::$_instances)) {
            self::$_instances['eventdispatcher'] = new EventDispatcher();
        }
        return self::$_instances['eventdispatcher'];
    }

    public static function getRouter() {
        if (!array_key_exists('router', self::$_instances)) {
            self::$_instances['router'] = new Router();
        }
        return self::$_instances['router'];
    }

}

?>