<?php

namespace Modula\Framework\Routing;

/**
 * @defgroup router
 * @ingroup application
 */

/**
 * Router object
 *
 * Allows the mapping of modules/actions to be mapped to custom URL's via
 * regular expressions.
 */
class Dispatcher {

    private $controller;
    private $action;
    private $routes = array();

    public function getController() {
        return $this->controller;
    }

    public function getAction() {
        return $this->action;
    }

    /**
     * Takes a Route object and finds the appropiate module/action
     *
     * @param object $route
     */
    function route($route) {
        $this->controller = array_key_exists('controller', $route->params) ? $route->params['controller'] : null;
        $this->action = array_key_exists('action', $route->params) ? $route->params['action'] : null;
        if (empty($this->controller))
            $this->controller = 'news';
        if (empty($this->action))
            $this->action = 'index';
        $_GET = array_merge($_GET, $route->params);

//        /**
//         * Create path and check if the file exists
//         *
//         * @todo Need to make this more dynamic, will store module/action
//         * against the routing pattern in the database.
//         */
//        $this->file = dir_MODULES . DS . $this->module . DS . $this->module . '.php';
//
//        if (file_exists($this->file)) {
//            try {
//                // Sanitise class name then try and instantiate
//                $class = ucfirst(strtolower($this->module)) . 'Module';
//                $this->module = new $class($this->module, $this->action);
//            } catch (Exception $e) {
//                throw new CustomException('Problem performing action (' . $e->getMessage() . ')');
//            }
//        } else {
//            throw new CustomException('Module not found: ' . $this->module);
//        }
//
//        if (is_callable(array($this->module, $this->action))) {
//            call_user_func(array($this->module, $this->action));
//        } else {
//            throw new CustomException('Problem performing action: ' . $this->action);
//        }
    }

    /**
     * Maps a new routing pattern to a given module/action
     *
     * @param string $pattern
     * @param array $target
     * @param array $conditions
     */
    function map($pattern, $target = array(), $conditions = array()) {
        $this->routes[$pattern] = new Route($pattern, $target, $conditions);
    }

    /**
     * Executes the routing procedure
     */
    function execute() {
        foreach ($this->routes as $route) {
            if ($route->match) {
                $this->route($route);
                return true;
            }
        }
        return false;
    }

}

?>