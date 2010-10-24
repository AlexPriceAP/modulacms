<?php

namespace Modula\Framework;

class Debug {

    /**
     * Buffer to hold output until script terminates
     *
     * @var array
     */
    private $output = array();

    /**
     * Echo output when script terminates
     */
    public function __destruct() {
        echo 'blaaaahh';
        $lineformat = "<pre>%s</pre>\n";
        foreach ($this->output as $line) {
            printf($lineformat, $line);
        }
    }

    /**
     * Handles any PHP error
     *
     * @param int $errno
     * @param string $errstr
     * @param string $errfile
     * @param int $errline
     * @return void
     */
    public function errorHandler($errno, $errstr, $errfile, $errline) {
        $errno = $errno & error_reporting();

        if ($errno == 0)
            return;

        $this->output[] = sprintf("\n<b>%s:</b> <i>%s</i> in <b>%s</b> on line <b>%s</b>", $this->getErrorType($errno), $errstr, $errfile, $errline);

        if (function_exists('debug_backtrace')) {
            $trace = debug_backtrace();
            array_shift($trace);
            $this->outputTrace($trace);
        }
    }

    /**
     * Handles any uncaught exception
     *
     * @param Exception $exception
     */
    public function exceptionHandler($exception) {
        $this->output[] = sprintf("\n<b>%s:</b><i>%s</i> in <b>%s</b> on line <b>%s</b>", get_class($exception), $exception->getMessage(), $exception->getFile(), $exception->getLine());

        $trace = $exception->getTrace();
        $this->outputTrace($trace);
    }

    /**
     * Takes a PHP error integer and returns the literal string
     * 
     * @param int $error_number
     * @return string
     */
    private function getErrorType($error_number) {

        if (!defined('E_STRICT'))
            define('E_STRICT', 2048);

        if (!defined('E_RECOVERABLE_ERROR'))
            define('E_RECOVERABLE_ERROR', 4096);

        switch ($error_number) {
            case E_ERROR:
                return "Error";
            case E_WARNING:
                return "Warning";
            case E_PARSE:
                return "Parse Error";
            case E_NOTICE:
                return "Notice";
            case E_CORE_ERROR:
                return "Core Error";
            case E_CORE_WARNING:
                return "Core Warning";
            case E_COMPILE_ERROR:
                return "Compile Error";
            case E_COMPILE_WARNING:
                return "Compile Warning";
            case E_USER_ERROR:
                return "User Error";
            case E_USER_WARNING:
                return "User Warning";
            case E_USER_NOTICE:
                return "User Notice";
            case E_STRICT:
                return "Strict Notice";
            case E_RECOVERABLE_ERROR:
                return "Recoverable Error";
            default:
                return "Unknown error ($error_number)";
        }
    }

    /**
     * Formats a backtrace array and sends to buffer to be output
     *
     * @param array $trace
     */
    private function outputTrace($trace) {
        foreach ($trace as $key => $stack) {
            $this->output[] = sprintf("[%s] <b>%s%s%s(</b>%s<b>)</b> in file <b>%s</b> on line <b>%s</b>", $key, array_key_exists('class', $stack) ? $stack['class'] : '', array_key_exists('type', $stack) ? $stack['type'] : '', $stack['function'], array_key_exists('args', $stack) ? Utils::multiArrayImplode(', ', $stack['args']) : '', $stack['file'], $stack['line']);
        }
    }

}

?>