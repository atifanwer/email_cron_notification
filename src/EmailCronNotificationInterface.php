<?php

namespace Drupal\email_cron_notification;

use Drupal\Core\Config\Entity\ConfigEntityInterface;

/**
 * Defines a content moderation notification interface.
 */
interface EmailCronNotificationInterface extends ConfigEntityInterface {

  /**
   * Get the email addresses.
   *
   * @return string
   *   The email addresses (comma-separated) for which to send the notification.
   */
  public function getEmails();


  /**
   * Gets the relevant roles for this notification.
   *
   * @return string[]
   *   The role IDs that should receive notification.
   */
  public function getRoleIds();


  /**
   * Gets the notification subject.
   *
   * @return string
   *   The message subject.
   */
  public function getSubject();

  /**
   * Gets the message value.
   *
   * @return string
   *   The message body text.
   */
  public function getMessage();

  /**
   * Gets the message format.
   *
   * @return string
   *   The format to be used for the message body.
   */
  public function getMessageFormat();

}
