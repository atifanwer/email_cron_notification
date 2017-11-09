<?php

namespace Drupal\email_cron_notification;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;

/**
 * Service for notification related questions about the moderated entity.
 */
class NotificationInformation implements NotificationInformationInterface {

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;


  /**
   * Creates a new NotificationInformation instance.
   *
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
  }


  /**
   * {@inheritdoc}
   */
  public function getNotifications() {
    $notifications = [];

        // Find out if we have a config entity that contains this transition.
        $query = $this->entityTypeManager->getStorage('email_cron_notification')
          ->getQuery();

        $notification_ids = $query->execute();

        $notifications = $this->entityTypeManager
          ->getStorage('email_cron_notification')
          ->loadMultiple($notification_ids);


    return $notifications;
  }


}
