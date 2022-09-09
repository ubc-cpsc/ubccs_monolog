<?php

namespace Drupal\ubccs_monolog\Form;

use Drupal\Core\Form\ConfigFormBase;
use Drupal\Core\Form\FormStateInterface;

/**
 * Configure UBC CS Monolog settings for this site.
 */
class SettingsForm extends ConfigFormBase {

  /**
   * {@inheritdoc}
   */
  public function getFormId() {
    return 'ubccs_monolog_settings';
  }

  /**
   * {@inheritdoc}
   */
  protected function getEditableConfigNames() {
    return ['ubccs_monolog.settings'];
  }

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {
    $form['notification_email'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Log Notification Email'),
      '#default_value' => $this->config('ubccs_monolog.settings')->get('notification_email'),
    ];
    return parent::buildForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function validateForm(array &$form, FormStateInterface $form_state) {
    if (!\Drupal::service('email.validator')->isValid($form_state->getValue('notification_email'))) {
      $form_state->setErrorByName('notification_email', $this->t('Please enter a valid email.'));
    }
    parent::validateForm($form, $form_state);
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    $this->config('ubccs_monolog.settings')
      ->set('notification_email', $form_state->getValue('notification_email'))
      ->save();
    parent::submitForm($form, $form_state);
  }

}
