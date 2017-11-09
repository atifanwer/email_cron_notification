<?php

namespace Drupal\email_cron_notification;

use Drupal\Core\Entity\EntityAccessControlHandler;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Session\AccountInterface;

/**
 * Defines an access controller for the email_cron_notification entity.
 *
 * We set this class to be the access controller in
 * EmailCronNotification's entity annotation.
 *
 * @see \Drupal\email_cron_notification\Entity\EmailCronNotification
 *
 * @ingroup email_cron_notification
 */
class EmailCronNotificationAccessController extends EntityAccessControlHandler {

  /**
   * {@inheritdoc}
   */
  public function checkAccess(EntityInterface $entity, $operation, AccountInterface $account) {
    // No special access handling. Defer to the entity system which will only
    // allow admin access by default.
    return parent::checkAccess($entity, $operation, $account);
  }

}
