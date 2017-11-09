<?php

namespace Drupal\email_cron_notification\Entity;

use Drupal\email_cron_notification\EmailCronNotificationInterface;
use Drupal\Core\Config\Entity\ConfigEntityBase;
use Drupal\Core\Entity\EntityStorageInterface;

/**
 * Defines the email_cron_notification entity.
 *
 * @see http://previousnext.com.au/blog/understanding-drupal-8s-config-entities
 * @see annotation
 * @see Drupal\Core\Annotation\Translation
 *
 * @ingroup email_cron_notification
 *
 * @ConfigEntityType(
 *   id = "email_cron_notification",
 *   label = @Translation("Notification"),
 *   admin_permission = "administer email cron notifications",
 *   handlers = {
 *     "access" = "Drupal\email_cron_notification\EmailCronNotificationAccessController",
 *     "list_builder" = "Drupal\email_cron_notification\Controller\EmailCronNotificationListBuilder",
 *     "form" = {
 *       "add" = "Drupal\email_cron_notification\Form\EmailCronNotificationAddForm",
 *       "edit" = "Drupal\email_cron_notification\Form\EmailCronNotificationEditForm",
 *       "delete" = "Drupal\email_cron_notification\Form\EmailCronNotificationDeleteForm",
 *       "disable" = "Drupal\email_cron_notification\Form\DisableForm",
 *       "enable" = "Drupal\email_cron_notification\Form\DisableForm"
 *     }
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label"
 *   },
 *   links = {
 *     "add-form" = "/admin/config/system/notification/add",
 *     "edit-form" = "/admin/config/system/notification/manage/{email_cron_notification}",
 *     "delete-form" = "/admin/config/system/notification/manage/{email_cron_notification}/delete",
 *     "enable-form" = "/admin/config/system/notification/manage/{email_cron_notification}/enable",
 *     "disable-form" = "/admin/config/system/notification/manage/{email_cron_notification}/disable",
 *     "collection" = "/admin/config/system/notification"
 *   },
 *   config_export = {
 *     "id",
 *     "roles",
 *     "emails",
 *     "subject",
 *     "body",
 *     "label",
 *   }
 * )
 */
class EmailCronNotification extends ConfigEntityBase implements EmailCronNotificationInterface {


  /**
   * The notification body value and format.
   *
   * @var array
   */
  public $body = [
    'value' => '',
    'format' => '',
  ];

  /**
   * Additional recipient emails.
   *
   * @var string
   */
  public $emails = '';

  /**
   * The role IDs to send notifications to.
   *
   * @var string[]
   */
  public $roles = [];

  /**
   * The message subject.
   *
   * @var string
   */
  public $subject;


  /**
   * {@inheritdoc}
   */
  public function getRoleIds() {
    return $this->get('roles');
  }

  /**
   * {@inheritdoc}
   */
  public function preSave(EntityStorageInterface $storage) {
    $this->set('roles', array_filter($this->get('roles')));
    parent::preSave($storage);
  }

  /**
   * {@inheritdoc}
   */
  public function getSubject() {
    return $this->get('subject');
  }

  /**
   * {@inheritdoc}
   */
  public function getMessage() {
    return $this->get('body')['value'];
  }

  /**
   * {@inheritdoc}
   */
  public function getMessageFormat() {
    return $this->get('body')['format'];
  }

  /**
   * {@inheritdoc}
   */
  public function getEmails() {
    return $this->get('emails');
  }


}
