<?php

function validate_url($url)
{
    $result = preg_match("/^(?:ftp|https?):\/\/(?:(?:[\w\.\-\+%!$&'\(\)*\+,;=]+:)*[\w\.\-\+%!$&'\(\)*\+,;=]+@)?(?:[a-z0-9\-\.%]+)(?::[0-9]+)?(?:[\/|\?][\w#!:\.\?\+=&%@!$'~*,;\/\(\)\[\]\-]*)?$/xi", $url);
    return($result != 0 || $result != false) ? true : false;
}

function validate_email($email)
{
    $result = preg_match("/^[0-9a-z~!#$%&_-]([.]?[0-9a-z~!#$%&_-])*@[0-9a-z~!#$%&_-]([.]?[0-9a-z~!#$%&_-])*$/xi", $email);
    return($result != 0 || $result != false) ? true : false;
}

?>