<?php

namespace Drupal\email_cron_notification;

use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Extension\ModuleHandlerInterface;
use Drupal\Core\Mail\MailManager;
use Drupal\Core\Session\AccountInterface;
use Drupal\token\TokenEntityMapperInterface;

/**
 * General service for moderation-related questions about Entity API.
 */
class Notification implements NotificationInterface {

  /**
   * The entity type manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * The entity type manager.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $mailManager;

  /**
   * The module handler service.
   *
   * @var \Drupal\Core\Extension\ModuleHandlerInterface
   */
  protected $moduleHandler;

  /**
   * The notification information service.
   *
   * @var \Drupal\email_cron_notification\NotificationInformationInterface
   */
  protected $notificationInformation;

  /**
   * The token entity mapper, if available.
   *
   * @var \Drupal\token\TokenEntityMapperInterface
   */
  protected $tokenEntityMapper;

  /**
   * Creates a new ModerationInformation instance.
   *
   * @param \Drupal\Core\Session\AccountInterface $current_user
   *   The current user.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   The entity type manager service.
   * @param \Drupal\Core\Mail\MailManager $mail_manager
   *   The mail manager.
   * @param \Drupal\Core\Extension\ModuleHandlerInterface $module_handler
   *   The module handler service.
   * @param \Drupal\email_cron_notification\NotificationInformationInterface $notification_information
   *   The notification information service.
   * @param \Drupal\token\TokenEntityMapperInterface $token_entity_mappper
   *   The token entity mapper service.
   */
  public function __construct(EntityTypeManagerInterface $entity_type_manager, MailManager $mail_manager, ModuleHandlerInterface $module_handler, NotificationInformationInterface $notification_information, TokenEntityMapperInterface $token_entity_mappper = NULL) {
    $this->entityTypeManager = $entity_type_manager;
    $this->mailManager = $mail_manager;
    $this->moduleHandler = $module_handler;
    $this->notificationInformation = $notification_information;
    $this->tokenEntityMapper = $token_entity_mappper;
  }

  /**
   * {@inheritdoc}
   */
  public function processEntity() {
    $notifications = $this->notificationInformation->getNotifications();
    if (!empty($notifications)) {
      $this->sendNotification($notifications);
    }
  }

  /**
   * {@inheritdoc}
   */
  public function sendNotification(array $notifications) {

    /** @var \Drupal\email_cron_notification\EmailCronNotificationInterface $notification */
    foreach ($notifications as $notification) {
      $data['notification'] = $notification;
      // Setup the email subject and body content.
      $data['params']['subject'] = $notification->getSubject();
      $data['params']['message'] = check_markup($notification->getMessage(), $notification->getMessageFormat());
/*
 * Todo : add context for token replacement in the body of the email (last cron logs ??)

      // Add the entity as context to aid in token replacement.
      if ($this->tokenEntityMapper) {
        $data['params']['context'] = [
          'entity' => $entity,
          $this->tokenEntityMapper->getTokenTypeForEntityType($entity->getEntityTypeId()) => $entity,
        ];
      }
      else {
        $data['params']['context'] = [
          'entity' => $entity,
          $entity->getEntityTypeId() => $entity,
        ];
      }
*/
      // Figure out who the email should be going to.
      $data['to'] = [];


      // Roles.
      foreach ($notification->getRoleIds() as $role) {
        /** @var \Drupal\user\UserInterface[] $role_users */
        $role_users = $this->entityTypeManager
          ->getStorage('user')
          ->loadByProperties(['roles' => $role]);
        foreach ($role_users as $role_user) {
          $data['to'][] = $role_user->getEmail();
        }
      }

      // Adhoc emails.
      $adhoc_emails = array_map('trim', explode(',', $notification->getEmails()));
      foreach ($adhoc_emails as $email) {
        $data['to'][] = $email;
      }

      // Let other modules to alter the email data.
      $this->moduleHandler->alter('email_cron_notification_mail_data', $entity, $data);

      // Remove any null values that have crept in.
      $data['to'] = array_filter($data['to']);

      // Remove any duplicates.
      $data['to'] = array_unique($data['to']);

      // Force to BCC.
      $data['params']['headers']['Bcc'] = implode(',', $data['to']);

      // Displayed 'to' address.
      // @todo Make this configurable.
      $recipient = \Drupal::config('system.site')->get('mail');

      $this->mailManager->mail('email_cron_notification', 'email_cron_notification', $recipient, NULL, $data['params'], NULL, TRUE);
    }
  }

}
