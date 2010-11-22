<?php

namespace Modula\Framework\Security;

class HashGenerator extends \Modula\Framework\Object {

    public static function randomHash($type) {
        switch ($type) {
            case 'sha1':
                return hash('sha1', mt_rand());
                break;
            case 'sha512':
                return hash('sha512', mt_rand());
                break;
            case 'md5':
                return hash('md5', mt_rand());
                break;
        }
        throw new \Exception('No hashing algorithm specified');
    }

    public static function passwordHash($password, $salt = null) {
        if (!$salt) {
            $randSalt = self::generateSalt();
            return $salt . hash('sha512', $str . $randSalt);
        } else {
            return $salt . hash('sha512', $str . $salt);
        }
    }

    private static function generateSalt($length = 12) {
        $characters = 'abcdef1234567890';
        $salt = '';
        if ($length > 0) {
            $totalChars = strlen($characters) - 1;
            for ($i = 0; $i < $length; ++$i) {
                $salt .= $characters[mt_rand(0, $totalChars)];
            }
        }
        return $salt;
    }

}

?>