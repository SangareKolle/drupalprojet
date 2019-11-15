<?php

namespace Drupal\recaptcha_v3\Form;

use function array_filter;
use Drupal\Core\Entity\EntityForm;
use Drupal\Core\Form\FormStateInterface;
use function strpos;

/**
 * Class ReCaptchaV3ActionForm.
 */
class ReCaptchaV3ActionForm extends EntityForm {

  /**
   * {@inheritdoc}
   */
  public function form(array $form, FormStateInterface $form_state) {
    module_load_include('inc', 'captcha', 'captcha.admin');

    $form = parent::form($form, $form_state);
    /** @var \Drupal\recaptcha_v3\Entity\ReCaptchaV3Action $recaptcha_v3_action */
    $recaptcha_v3_action = $this->entity;
    $form['label'] = [
      '#type' => 'textfield',
      '#title' => $this->t('Label'),
      '#maxlength' => 255,
      '#default_value' => $recaptcha_v3_action->label(),
      '#description' => $this->t('Label for the reCAPTCHA v3 action.'),
      '#required' => TRUE,
    ];

    $form['id'] = [
      '#type' => 'machine_name',
      '#default_value' => $recaptcha_v3_action->id(),
      '#machine_name' => [
        'exists' => '\Drupal\recaptcha_v3\Entity\ReCaptchaV3Action::load',
      ],
      '#disabled' => !$recaptcha_v3_action->isNew(),
    ];

    $form['threshold'] = [
      '#type' => 'number',
      '#title' => $this->t('Threshold'),
      '#min' => 0,
      '#max' => 1,
      '#step' => 0.1,
      '#default_value' => $recaptcha_v3_action->get('threshold'),
    ];

    $challenges = _captcha_available_challenge_types(FALSE);
    foreach ($challenges as $captcha_type => $challenge) {
      if (strpos($captcha_type, 'recaptcha_v3') === 0) {
        unset($challenges[$captcha_type]);
      }
    }
    $form['challenge'] = [
      '#type' => 'select',
      '#title' => $this->t('Fallback challenge'),
      '#description' => $this->t('Select the fallback challenge on reCAPTCHA v3 user validation fail.'),
      '#options' => $challenges,
      '#default_value' => $recaptcha_v3_action->get('challenge'),
      '#empty_option' => $this->t('Default fallback challenge'),
      '#empty_value' => 'default',
    ];

    return $form;
  }

  /**
   * {@inheritdoc}
   */
  public function save(array $form, FormStateInterface $form_state) {
    /** @var \Drupal\recaptcha_v3\Entity\ReCaptchaV3Action $recaptcha_v3_action */
    $recaptcha_v3_action = $this->entity;
    $status = $recaptcha_v3_action->save();

    switch ($status) {
      case SAVED_NEW:
        $this->messenger()->addStatus($this->t('Created the %label reCAPTCHA v3 action.', [
          '%label' => $recaptcha_v3_action->label(),
        ]));
        $this->getLogger('recaptcha_v3')
          ->info('Created the %label reCAPTCHA v3 action.', [
            '%label' => $recaptcha_v3_action->label(),
          ]);
        break;

      default:
        $this->messenger()
          ->addStatus($this->t('Saved the %label reCAPTCHA v3 action.', [
            '%label' => $recaptcha_v3_action->label(),
          ]));
        $this->getLogger('recaptcha_v3')->info('Saved the %label reCAPTCHA v3 action.', [
          '%label' => $recaptcha_v3_action->label(),
        ]);
    }
    $form_state->setRedirectUrl($recaptcha_v3_action->toUrl('collection'));
  }

}
