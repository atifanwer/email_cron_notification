<?php

namespace Drupal\email_cron_notification;

use Drupal\Core\Entity\ContentEntityInterface;
use Drupal\Core\Entity\EntityInterface;

/**
 * Interface for notification_information service.
 */
interface NotificationInformationInterface {

  public function getNotifications();


}
