<?php

namespace Modula\Framework\Notifications;

/**
 * @ingroup application
 */
class Notification extends \Modula\Framework\Object {

  private $message;
  private $bindAttributes = array();
  private $expireTime;

  public function __construct($inputMsg, $inputAttribs, $inputTime){
    $this->message = $inputMsg;
    $this->bindAttributes = $inputAttribs;
    $this->expireTime = $inputTime;
  }

  public function getMessage(){
    return $this->message;
  }

  public function getExpireTime(){
    return $this->expireTime;
  }

  public function getAttribute($name){
    return $this->bindAttributes[$name];
  }

}

?>
