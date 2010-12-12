<?php

namespace Modula\Framework\Security;

abstract class Token extends \Modula\Framework\Object {

    /**
     * Holds all attributes to bind against the token
     *
     * @example array('email', 'user@company.com', 'restrict', 'email_verify')
     * @var array
     */
    protected $bindAttributes = array();
    /**
     * Actual token string
     *
     * @var string
     */
    protected $tokenString;
    /**
     * Timestamp at which the token becomes invalid
     *
     * @var int
     */
    protected $expire;

    protected function __construct(array $bindAttributes, $expire, $hashType = 'sha1', $tokenString = null) {
        $this->bindAttributes = $bindAttributes;
        $this->tokenString = $tokenString ? $tokenString : HashGenerator::randomHash($hashType);
        $this->expire = $expire;
    }

    /**
     * Returns the token string
     *
     * @return string
     */
    protected function getTokenString() {
        return $this->tokenString;
    }

    /**
     * Returns the bindAttributes
     *
     * @return <type>
     */
    protected function getBindAttributes() {
        return $this->bindAttributes;
    }

    /**
     * Returns the expiry timestamp
     *
     * @return <type> 
     */
    protected function getExpireTime() {
        return $this->expire;
    }

}

?>