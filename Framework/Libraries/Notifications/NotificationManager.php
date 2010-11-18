<?php

namespace Modula\Framework\Notifications;

/**
 * @ingroup application
 */
class NotificationManager extends \Modula\Framework\Object {

  private $repo;

  public static function addNotification(Notification $notification){
    $repo = new NotificationRepository();
    $repo->create();
  }

  public static function deleteNotification(Notification $notification){

  }

  public static function getNotifications(){

  }

}

?>
