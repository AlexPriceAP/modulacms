<?php

namespace Modula\Framework\Templating;

/**
 * @ingroup application
 */
class Template extends Modula\Framework\Object {

    private $path;

    public function __construct($path) {
        $this->path = $path;
    }

    /**
     * Captures a template's output and returns to the caller
     *
     * @return string
     */
    public function render() {
        if (is_readable($this->path)) {
            // 'Extract' any attached variables to use in the template
            extract($this->vars);
            // Start a buffer to capture our template output
            ob_start();
            include($this->path);
            // Fetch buffer
            $output = ob_get_contents();
            // Parse all variables from buffer output
            $output = preg_replace_callback('/\\{(.*?)=(.*?)\\}/is', array($this, 'parseVars'), $output);
            ob_end_clean();
            return $output;
        } else {
            throw new \Exception("Problem reading template: $this->path");
        }
    }

    public function parseVars($matches) {
        $var = strtoupper($matches[1]);
        $value = $matches[2];
        switch ($var) {
            case 'TEMPLATE':
                $tpl = new Template($value);
                return $tpl->render();
                break;
            default:
                return "Error, template function '$var' not implemented";
        }
    }

}

?>
