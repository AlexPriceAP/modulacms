<?php

namespace Modula\Framework;

/**
 * @defgroup download
 * @ingroup application
 */
class Download {

    function download($file) {
        Factory::getEventDispatcher()->triggerEvent('onLog', array('msg' => Utils::getRemoteAddr() . " has downloaded a file ($file)"));
        // Check that the file starts with our url or that it's not a url (specified as relative path)
        if (preg_match('/\b' . str_replace('/', '\/', url_ROOT) . '.*/i', $file) || !$this->validate_url($file)) {
            $parsed_url = parse_url($file);
            $file = dir_ROOT . DS . $parsed_url['path'];
            $file = preg_replace('/\/\//', '/', $file);
            // Check if file exists, if not, fail
            if (!is_file($file) or connection_status() != 0) {
                return false;
            } else {
                while (@ob_end_clean()
                    ); //turn off output buffering to decrease cpu usage
                    // required for IE, otherwise Content-Disposition may be ignored
 if (ini_get('zlib.output_compression'))
                    ini_set('zlib.output_compression', 'Off');

                header('Content-Type: ' . FileHandler::getMime($file));
                header('Content-Disposition: attachment; filename="' . basename($file) . '"');
                header("Content-Transfer-Encoding: binary");
                header('Accept-Ranges: bytes');
                header("Cache-control: private");
                header('Pragma: private');
                header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

                $size = filesize($file);

                // multipart-download and download resuming support
                if (isset($_SERVER['HTTP_RANGE'])) {
                    list($a, $range) = explode("=", $_SERVER['HTTP_RANGE'], 2);
                    list($range) = explode(",", $range, 2);
                    list($range, $range_end) = explode("-", $range);
                    $range = intval($range);
                    if (!$range_end) {
                        $range_end = $size - 1;
                    } else {
                        $range_end = intval($range_end);
                    }
                    $new_length = $range_end - $range + 1;
                    header("HTTP/1.1 206 Partial Content");
                    header("Content-Length: $new_length");
                    header("Content-Range: bytes $range-$range_end/$size");
                } else {
                    $new_length = $size;
                    header("Content-Length: " . $size);
                }

                $chunkSize = 1 * (1024 * 1024);
                $bytesSent = 0;
                $handle = fopen($file, 'r');

                if ($handle) {
                    if (isset($_SERVER['HTTP_RANGE']))
                        fseek($handle, $range);

                    while (!feof($handle)) {
                        @set_time_limit(0);
                        $buffer = fread($handle, $chunkSize);
                        $bytesSent += strlen($buffer);
                        echo $buffer;
                        flush();
                        ob_flush();
                        sleep(1);
                        Factory::getEventDispatcher()->triggerEvent('onLog', array('msg' => "Sent $bytesSent bytes of $size (" . round($bytesSent / $size * 100) . "%)..."));
                    }
                    fclose($handle);
                    if ($bytesSent == filesize($file)) {
                        Factory::getEventDispatcher()->triggerEvent('onLog', array('msg' => "Finished downloading file $file!"));
                    }
                    return((connection_status() == 0) && !connection_aborted());
                } else
                    throw new CustomException("Could not download file: $file");
            }
        } else {
            Utils::redirect($file, 0);
        }
    }

    function validate_url($url) {
        $result = preg_match("/^(?:ftp|https?):\/\/(?:(?:[\w\.\-\+%!$&'\(\)*\+,;=]+:)*[\w\.\-\+%!$&'\(\)*\+,;=]+@)?(?:[a-z0-9\-\.%]+)(?::[0-9]+)?(?:[\/|\?][\w#!:\.\?\+=&%@!$'~*,;\/\(\)\[\]\-]*)?$/xi", $url);
        return($result != 0 || $result != false) ? true : false;
    }

}

?>