<?php

namespace Drupal\email_cron_notification\Form;

use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Entity\EntityTypeManagerInterface;
use Drupal\Core\Entity\Query\QueryFactory;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class EmailCronNotificationFormBase.
 *
 * Typically, we need to build the same form for both adding a new entity,
 * and editing an existing entity. Instead of duplicating our form code,
 * we create a base class. Drupal never routes to this class directly,
 * but instead through the child classes of EmailCronNotificationAddForm
 * and EmailCronNotificationEditForm.
 *
 * @package Drupal\email_cron_notification\Form
 *
 * @ingroup email_cron_notification
 */
class EmailCronNotificationFormBase extends EntityForm {

  /**
   * Entity Query factory.
   *
   * @var \Drupal\Core\Entity\Query\QueryFactory
   */
  protected $entityQueryFactory;

  /**
   * Entity Type Manager service.
   *
   * @var \Drupal\Core\Entity\EntityTypeManagerInterface
   */
  protected $entityTypeManager;

  /**
   * Construct the EmailCronNotificationFormBase.
   *
   * For simple entity forms, there's no need for a constructor. Our form
   * base, however, requires an entity query factory to be injected into it
   * from the container. We later use this query factory to build an entity
   * query for the exists() method.
   *
   * @param \Drupal\Core\Entity\Query\QueryFactory $query_factory
   *   An entity query factory for the entity type.
   * @param \Drupal\Core\Entity\EntityTypeManagerInterface $entity_type_manager
   *   An entity type manager for the entity type.
   */
  public function __construct(QueryFactory $query_factory, EntityTypeManagerInterface $entity_type_manager) {
    $this->entityTypeManager = $entity_type_manager;
    $this->entityQueryFactory = $query_factory;
  }

  /**
   * Factory method for EmailCronNotificationFormBase.
   *
   * When Drupal builds this class it does not call the constructor directly.
   * Instead, it relies on this method to build the new object. Why? The class
   * constructor may take multiple arguments that are unknown to Drupal. The
   * create() method always takes one parameter -- the container. The purpose
   * of the create() method is twofold: It provides a standard way for Drupal
   * to construct the object, meanwhile it provides you a place to get needed
   * constructor parameters from the container.
   *
   * In this case, we ask the container for an entity query factory. We then
   * pass the factory to our class as a constructor parameter.
   */
  public static function create(ContainerInterface $container) {
    return new static($container->get('entity.query'), $container->get('entity_type.manager'));
  }


  /**
   * Overrides Drupal\Core\Entity\EntityFormController::form().
   *
   * Builds the entity add/edit form.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   An associative array containing the current state of the form.
   *
   * @return array
   *   An associative array containing the email_cron_notification
   *   add/edit form.
   */
  public function buildForm(array $form, FormStateInterface $form_state) {


    // Get anything we need from the base class.
    $form = parent::buildForm($form, $form_state);

    // Drupal provides the entity to us as a class variable. If this is an
    // existing entity, it will be populated with existing values as class
    // variables. If this is a new entity, it will be a new object with the
    // class of our entity. Drupal knows which class to call from the
    // annotation on our EmailCronNotification class.
    /** @var \Drupal\email_cron_notification\EmailCronNotificationInterface $email_cron_notification */
    $email_cron_notification = $this->entity;


    $form['label'] = [
      '#title' => $this->t('Label'),
      '#type' => 'textfield',
      '#default_value' => $email_cron_notification->label(),
      '#description' => $this->t('The label for this notification.'),
      '#required' => TRUE,
      '#size' => 30,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#title' => $this->t('Machine name'),
      '#default_value' => $email_cron_notification->id(),
      '#machine_name' => [
        'exists' => [$this, 'exists'],
        'source' => ['label'],
      ],
      '#disabled' => !$email_cron_notification->isNew(),
    ];


    // Role selection.
    $roles_options = user_role_names(TRUE);

    $form['roles'] = [
      '#type' => 'checkboxes',
      '#title' => $this->t('Roles'),
      '#options' => $roles_options,
      '#default_value' => $email_cron_notification->getRoleIds(),
      '#description' => $this->t('Send notifications to all users with these roles.'),
    ];


    $form['emails'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Adhoc email addresses'),
      '#default_value' => $email_cron_notification->getEmails(),
      '#description' => $this->t('Send notifications to these email addresses, emails should be entered as a comma separated list.'),
    ];

    // Email subject line.
    $form['subject'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Email Subject'),
      '#default_value' => $email_cron_notification->getSubject(),
    ];

    // Email body content.
    $form['body'] = [
      '#type' => 'text_format',
      '#format' => $email_cron_notification->getMessageFormat() ?: filter_default_format(),
      '#title' => $this->t('Email Body'),
      '#default_value' => $email_cron_notification->getMessage(),
    ];

    // Return the form.
    return $form;
  }

  /**
   * Checks for an existing email_cron_notification.
   *
   * @param string|int $entity_id
   *   The entity ID.
   * @param array $element
   *   The form element.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   The form state.
   *
   * @return bool
   *   TRUE if this format already exists, FALSE otherwise.
   */
  public function exists($entity_id, array $element, FormStateInterface $form_state) {
    // Use the query factory to build a new entity query.
    $query = $this->entityQueryFactory->get('email_cron_notification');

    // Query the entity ID to see if its in use.
    $result = $query->condition('id', $element['#field_prefix'] . $entity_id)
      ->execute();

    // We don't need to return the ID, only if it exists or not.
    return (bool) $result;
  }

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::actions().
   *
   * To set the submit button text, we need to override actions().
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   An associative array containing the current state of the form.
   *
   * @return array
   *   An array of supported actions for the current entity form.
   */
  protected function actions(array $form, FormStateInterface $form_state) {
    // Get the basic actions from the base class.
    $actions = parent::actions($form, $form_state);

    // Change the submit button text.
    $actions['submit']['#value'] = $this->t('Save');

    // Return the result.
    return $actions;
  }

  /**
   * Overrides Drupal\Core\Entity\EntityFormController::save().
   *
   * Saves the entity. This is called after submit() has built the entity from
   * the form values. Do not override submit() as save() is the preferred
   * method for entity form controllers.
   *
   * @param array $form
   *   An associative array containing the structure of the form.
   * @param \Drupal\Core\Form\FormStateInterface $form_state
   *   An associative array containing the current state of the form.
   */
  public function save(array $form, FormStateInterface $form_state) {
    // EntityForm provides us with the entity we're working on.
    $email_cron_notification = $this->getEntity();

    // Drupal already populated the form values in the entity object. Each
    // form field was saved as a public variable in the entity class. PHP
    // allows Drupal to do this even if the method is not defined ahead of
    // time.
    $status = $email_cron_notification->save();

    if ($status == SAVED_UPDATED) {
      // If we edited an existing entity...
      drupal_set_message($this->t('Notification <a href=":url">%label</a> has been updated.', ['%label' => $email_cron_notification->label(), ':url' => $email_cron_notification->toUrl('edit-form')->toString()]));
      $this->logger('email_cron_notification')->notice('Notification has been updated.', ['%label' => $email_cron_notification->label()]);
    }
    else {
      // If we created a new entity...
      drupal_set_message($this->t('Notification <a href=":url">%label</a> has been added.', ['%label' => $email_cron_notification->label(), ':url' => $email_cron_notification->toUrl('edit-form')->toString()]));
      $this->logger('email_cron_notification')->notice('Notification has been added.', ['%label' => $email_cron_notification->label()]);
    }

    // Redirect the user back to the listing route after the save operation.
    $form_state->setRedirect('entity.email_cron_notification.collection');
  }

}
