<?php

namespace Drupal\email_cron_notification;

use Drupal\Core\DependencyInjection\ContainerInjectionInterface;
use Drupal\Core\Render\BubbleableMetadata;
use Drupal\Core\StringTranslation\StringTranslationTrait;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Token generation and information class.
 */
class Tokens implements ContainerInjectionInterface {

  use StringTranslationTrait;

  /**
   * The notification information service.
   *
   * @var \Drupal\email_cron_notification\NotificationInformationInterface
   */
  protected $notificationInformation;

  /**
   * Constructs the token generation object.
   *
   * @param \Drupal\email_cron_notification\NotificationInformationInterface $notification_information
   *   The notification information service.
   */
  public function __construct(NotificationInformationInterface $notification_information) {
    $this->notificationInformation = $notification_information;
  }

  /**
   * {@inheritdoc}
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('email_cron_notification.notification_information'));
  }

  /**
   * Token information.
   *
   * @see \email_cron_notification_token_info()
   */
  public static function info() {
    $type = [
      'name' => t('Cron data'),
      'description' => t('Cron data tokens'),
      'needs-data' => 'entity',
    ];

    $tokens['todo'] = [
      'name' => t('todo name'),
      'description' => t('todo description.'),
    ];


    return [
      'types' => ['email_cron_notification' => $type],
      'tokens' => [
        'email_cron_notification' => $tokens,
      ],
    ];
  }

  /**
   * Generate tokens.
   *
   * @param string $type
   *   The machine-readable name of the type (group) of token being replaced,
   *   such as 'node', 'user', or another type defined by a hook_token_info()
   *   implementation.
   * @param array $tokens
   *   An array of tokens to be replaced. The keys are the machine-readable
   *   token names, and the values are the raw [type:token] strings that
   *   appeared in the original text.
   * @param array $data
   *   An associative array of data objects to be used when generating
   *   replacement values, as supplied in the $data parameter to
   *   \Drupal\Core\Utility\Token::replace().
   * @param array $options
   *   An associative array of options for token replacement; see
   *   \Drupal\Core\Utility\Token::replace() for possible values.
   * @param \Drupal\Core\Render\BubbleableMetadata $bubbleable_metadata
   *   Bubbleable metadata.
   *
   * @return array
   *   An associative array of replacement values, keyed by the raw [type:token]
   *   strings from the original text. The returned values must be either plain
   *   text strings, or an object implementing MarkupInterface if they are
   *   HTML-formatted.
   *
   * @see \email_cron_notification_tokens()
   */
  public function getTokens($type, array $tokens, array $data, array $options, BubbleableMetadata $bubbleable_metadata) {
    $replacements = [];

    return $replacements;
  }

}
