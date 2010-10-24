<?php

namespace Modula\Framework\Sessions;

/**
 * @defgroup sessionstorage Session Storage Adaptors
 * @ingroup sessions
 */
class SessionStorage {

    /**
     * Stores a list of session storage adaptors
     */
    static private $instances = array();

    /**
     * Returns an instance of a session storage adaptor, if no adaptor is
     * specified, the default adaptor is used.
     *
     * @param string $name
     * @return object
     */
    static function getInstance($adaptor = 'default') {
        $file = dirname(__FILE__) . '/adaptors/' . $adaptor . 'sessionstorage.php';
        $class = ucfirst(strtolower($adaptor)) . 'SessionStorage';
        if (empty(self::$instances[$adaptor])) {
            if (file_exists($file)) {
                require_once($file);
                if (class_exists($class)) {
                    self::$instances[$adaptor] = new $class();
                } else {
                    throw new CustomException("Unable to load specified session storage adaptor: {$adaptor}");
                }
            }
        }
        return self::$instances[$name];
    }

    function __construct() {
        /**
         * Register our adaptor functions as session save handlers, if function
         * isn't defined, our default ones below are used.
         */
        session_set_save_handler(
                array($this, 'open'),
                array($this, 'close'),
                array($this, 'read'),
                array($this, 'write'),
                array($this, 'destroy'),
                array($this, 'gc'));
    }

    /**
     * Default open handler
     *
     * @param string $save_path
     * @param string $session_name
     * @return bool
     */
    function open($save_path, $session_name) {
        return true;
    }

    /**
     * Default close handler
     *
     * @return bool
     */
    function close() {
        return true;
    }

    /**
     * Default read handler
     *
     * @param string $id
     * @return void
     */
    function read($id) {
        return;
    }

    /**
     * Default write handler
     *
     * @param string $id
     * @param string $sess_data
     * @return bool
     */
    function write($id, $sess_data) {
        return true;
    }

    /**
     * Default destroy handler
     *
     * @param string $id
     * @return bool
     */
    function destroy($id) {
        return true;
    }

    /**
     * Default garbage collector
     *
     * @param int $maxlifetime
     * @return bool
     */
    function gc($maxlifetime) {
        return true;
    }

}

?>