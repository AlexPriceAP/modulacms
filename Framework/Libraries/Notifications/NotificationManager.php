<?php

namespace Modula\Framework\Notifications;

/**
 * @ingroup application
 */
class NotificationManager extends \Modula\Framework\Object {

  public static function addNotification(Notification $notification){
    NotificationRepository::create($notification);
  }

  public static function deleteNotification(Notification $notification){
    NotificationRepository::delete($notification);
  }

  public static function getNotifications($bindAttributes){

  }

}

?>
