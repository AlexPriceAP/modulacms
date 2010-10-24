<?php

namespace Modula\Framework\Routing;

/**
 * Route object
 */
class Route {

    public $pattern;
    public $params = array();
    public $conditions = array();
    public $match = false;

    function __construct($pattern, $target, $conditions) {
        $this->pattern = $pattern;
        $this->params = array();
        $this->conditions = $conditions;

        // Find all replacement patterns in the request URI
        preg_match_all('@:([\w]+)@', $pattern, $param_names, PREG_PATTERN_ORDER);
        $param_names = $param_names[0];

        // Replace all replacement patterns with their appropiate values
        $url_regex = preg_replace_callback('@:[\w]+@', array($this, 'regex_url'), $pattern);
        $url_regex .= '/?';

        $uri = new URI();
        // Checks the route against the request URI to find a match
        if (preg_match('@^' . $url_regex . '$@', $uri->requesturi, $param_values)) {
            array_shift($param_values);
            foreach ($param_names as $index => $value) {
                $this->params[substr($value, 1)] = urldecode($param_values[$index]);
            }
            foreach ($target as $key => $value) {
                $this->params[$key] = $value;
            }
            $this->match = true;
        }
    }

    /**
     * Regex callback method to replace :key's their values
     *
     * @param array $matches
     * @return string
     */
    function regex_url($matches) {
        $key = str_replace(':', '', $matches[0]);
        if (array_key_exists($key, $this->conditions)) {
            return '(' . $this->conditions[$key] . ')';
        } else {
            return '([a-zA-Z0-9_\+\-%]+)';
        }
    }


}

?>