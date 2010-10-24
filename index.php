<?php

error_reporting(E_ALL);

define('INIT', true);
define('dir_ROOT', dirname(__FILE__));
define('dir_FRAMEWORK', dir_ROOT . '/Framework');
define('url_ROOT', url_base_dir());
define('file_SELF', 'http://' . $_SERVER['HTTP_HOST'] . ($_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_FILENAME']));

function url_base_dir() {
    $url = str_replace($_SERVER['SCRIPT_NAME'], '', str_replace('\\', '/', realpath($_SERVER['SCRIPT_FILENAME'])));
    $url = str_replace($url, '', str_replace('\\', '/', realpath(__FILE__)));
    $url = str_replace(basename($url), '', $url);
    $url = ((isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $url;
    return $url;
}

function __autoload($class) {
    $file = dir_FRAMEWORK . '/Libraries/' . str_replace('\\', '/', $class) . '.php';
    echo $file;
    return file_exists($file) && include($file) ? true : false;
}

require(dir_FRAMEWORK . '/Framework.php');

$framework = new \Modula\Framework\Framework();
?>