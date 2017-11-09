<?php

namespace Drupal\email_cron_notification;

use Drupal\Core\Entity\EntityInterface;

/**
 * Interface for notification service.
 */
interface NotificationInterface {

  /**
   * Processes a given entity in transition.
   *
   * @param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity of the current cron job (todo).
   */
  public function processEntity();

  /**
   * Send notifications for a given entity and set of notifications.
   *
   * @todo param \Drupal\Core\Entity\EntityInterface $entity
   *   The entity we may be moderating. The entity of the current cron job (todo).
   * @param \Drupal\email_cron_notification\EmailCronNotificationInterface[] $notifications
   *   List of email cron notification entities.
   *
   *
   */
  public function sendNotification( array $notifications);

}
