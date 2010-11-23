<?php

namespace Modula\Framework\Security;

class TokenManager extends \Modula\Framework\Object {

    /**
     * Adds a new token to the system
     *
     * @param Token $token
     * @return bool
     */
    public static function addToken(Token $token) {
        return TokenRepository::create($token);
    }

    /**
     * Deletes a specific token
     * 
     * @param Token $token
     * @return bool
     */
    public static function deleteToken(Token $token) {
        return TokenRepository::delete($token);
    }

    /**
     * Verifies a token exists and optionaly takes an array of 'bind'
     * parameters to check against the fetched token.  If all key pairs
     * exist, returns true, else false.
     *
     * @param Token $token
     * @param array $bindAttributes
     * @return bool
     */
    public static function verifyToken(Token $token, array $bindAttributes = array()) {
        $valid = true;
        if (TokenRepository::read($token)) {
            if ($bindAttributes) {
                foreach ($bindAttribute as $attribute => $value) {
                    if (array_key_exists($token->bindAttributes, $attribute) && $token->bindAttributes[$attribute] == $value) {
                        continue;
                    } else {
                        $valid = false;
                    }
                }
            } else {
                $valid = true;
            }
        } else {
            $valid = false;
        }
        return $valid;
    }

    /**
     * Clears all expired or otherwise invalid tokens
     *
     * @return bool
     */
    public static function clear() {
        return TokenRepository::clear($token);
    }

}
?>