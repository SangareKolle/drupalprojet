<?php

namespace Drupal\recaptcha_v3\Form;

use Drupal\Core\Entity\EntityDeleteForm;
use Drupal\Core\Form\FormStateInterface;

/**
 * Builds the form to delete reCAPTCHA v3 action entities.
 */
class ReCaptchaV3ActionDeleteForm extends EntityDeleteForm {

  /**
   * {@inheritdoc}
   */
  public function buildForm(array $form, FormStateInterface $form_state) {

    // @todo need to find count of captcha points using deleted action and show appropriate title
    // @see \Drupal\node\Form\NodeTypeDeleteConfirm::buildForm()
    return parent::buildForm($form, $form_state);
  }

}
