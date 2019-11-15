<?php

namespace Drupal\recaptcha_v3\Form;

use Drupal\Core\Entity\EntityConfirmFormBase;
use Drupal\Core\Form\FormStateInterface;
use Drupal\Core\Url;

/**
 * Builds the form to delete reCAPTCHA v3 action entities.
 */
class ReCaptchaV3ActionDeleteForm extends EntityConfirmFormBase {

  /**
   * {@inheritdoc}
   */
  public function getQuestion() {
    return $this->t('Are you sure you want to delete action %label?', ['%label' => $this->entity->label()]);
  }

  /**
   * {@inheritdoc}
   */
  public function getConfirmText() {
    return $this->t('Delete');
  }

  /**
   * {@inheritdoc}
   */
  public function submitForm(array &$form, FormStateInterface $form_state) {
    if ($this->entity->delete()) {
      $this->messenger()->addStatus($this->t('Action %label with %id deleted.', [
        '%id' => $this->entity->id(),
        '%label' => $this->entity->label(),
      ]));
      $this->getLogger('recaptcha_v3')->info('Action %label with %id deleted.', [
        '%id' => $this->entity->id(),
        '%label' => $this->entity->label(),
      ]);
    }

    $form_state->setRedirectUrl($this->getCancelUrl());
  }

  /**
   * {@inheritdoc}
   */
  public function getCancelUrl() {
    return new Url('entity.recaptcha_v3_action.collection');
  }

}
