<?php

namespace Modula\Framework;

class Utils {

    /**
     * Redirects to given url, using headers or javascript if headers have already
     * been sent to the browser
     *
     * @todo Move into seperate utility class
     * @param string $url
     * @param int $wait
     */
    static function redirect($url = null, $wait = 0) {
        if (!$url)
            $url = Utils::getReferer() ? Utils::getReferer() : url_ROOT;
        sleep($wait);
        if (!headers_sent()) {
            //If headers not sent yet... then do php redirect
            header("location: $url");
        } else {
            //If headers are sent... do javascript redirect... if
            //javascript disabled, do html redirect.
            echo '<script type="text/javascript">';
            echo 'window.location.href="' . $url . '";';
            echo '</script>';
            echo '<noscript>';
            echo '<meta http-equiv="refresh" content="0;url=' . $url . '" />';
            echo '</noscript>';
        }
        exit;
    }

    static function getTime() {
        $time = microtime(true);
        $time = explode(' ', $time);
        return $time[0] + $time[1];
    }

    static function timeSince($time) {
        $now = time();
        $now_day = date("j", $now);
        $now_month = date("n", $now);
        $now_year = date("Y", $now);

        $time_day = date("j", $time);
        $time_month = date("n", $time);
        $time_year = date("Y", $time);
        $time_since = "";

        switch (TRUE) {
            case ($now - $time < 60):
                $seconds = $now - $time;
                // Append "s" if plural
                $time_since = $seconds > 1 ? "$seconds seconds" : "$seconds second";
                break;
            case ($now - $time < 45 * 60): // twitter considers > 45 mins as about an hour, change to 60 for general purpose
                $minutes = round(($now - $time) / 60);
                $time_since = $minutes > 1 ? "$minutes minutes" : "$minutes minute";
                break;
            case ($now - $time < 86400):
                $hours = round(($now - $time) / 3600);
                $time_since = $hours > 1 ? "about $hours hours" : "about $hours hour";
                break;
            case ($now - $time < 1209600):
                $days = round(($now - $time) / 86400);
                $time_since = "$days days";
                break;
            case (mktime(0, 0, 0, $now_month - 1, $now_day, $now_year) < mktime(0, 0, 0, $time_month, $time_day, $time_year)):
                $weeks = round(($now - $time) / 604800);
                $time_since = "$weeks weeks";
                break;
            case (mktime(0, 0, 0, $now_month, $now_day, $now_year - 1) < mktime(0, 0, 0, $time_month, $time_day, $time_year)):
                if ($now_year == $time_year) {
                    $subtract = 0;
                } else {
                    $subtract = 12;
                }
                $months = round($now_month - $time_month + $subtract);
                $time_since = "$months months";
                break;
            default:
                if ($now_month < $time_month) {
                    $subtract = 1;
                } elseif ($now_month == $time_month) {
                    if ($now_day < $time_day) {
                        $subtract = 1;
                    } else {
                        $subtract = 0;
                    }
                } else {
                    $subtract = 0;
                }
                $years = $now_year - $time_year - $subtract;
                $time_since = "$years years";
                break;
            default: $time_since = - 1;
        }
        if ($time_since == "0 years ago") {
            $time_since = "";
        }
        return $time_since;
    }

    static function getRemoteAddr() {
        return self::get_real_ip();
    }

    static function getRequested() {
        return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '';
    }

    static function getReferer() {
        return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
    }

    static function getAgent() {
        return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '';
    }

    static function get_real_ip() {
        $ip = false;
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
            if ($ip) {
                array_unshift($ips, $ip);
                $ip = false;
            }
            for ($i = 0; $i < count($ips); $i++) {
                if (!preg_match("/^(10|172\.16|192\.168)\./i", $ips[$i])) {
                    if (version_compare(phpversion(), "5.0.0", ">=")) {
                        if (ip2long($ips[$i]) != false) {
                            $ip = $ips[$i];
                            break;
                        }
                    } else {
                        if (ip2long($ips[$i]) != - 1) {
                            $ip = $ips[$i];
                            break;
                        }
                    }
                }
            }
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }

    public static function isGuid($guid) {
        return preg_match('/^({)?([0-9a-fA-F]){8}(-([0-9a-fA-F]){4}){3}-([0-9a-fA-F]){12}(?(1)})$/', $guid) ? true : false;
    }

    /**
     * Multi dimentional array implode
     *
     * @param string $seperator
     * @param array $array
     * @return string
     */
    public static function multiArrayImplode($seperator, $array) {
        $return = array();
        if (is_array($array)) {
            foreach ($array as $element) {
                $return[] = self::arrayToString($seperator, $element);
            }
        } else {
            $return[] = $array;
        }

        return implode($seperator, $return);
    }

}

?>