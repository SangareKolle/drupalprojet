<?php

namespace Drupal\recaptcha_v3;

use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;

/**
 * Provides a listing of reCAPTCHA v3 action entities.
 */
class ReCaptchaV3ActionListBuilder extends ConfigEntityListBuilder {

  protected $challengeTypes;

  /**
   * {@inheritdoc}
   */
  public function buildHeader() {
    $header['label'] = $this->t('Label');
    $header['id'] = $this->t('Action');
    $header['threshold'] = $this->t('Threshold');
    $header['challenge'] = $this->t('Fail challenge');
    return $header + parent::buildHeader();
  }

  /**
   * {@inheritdoc}
   */
  public function buildRow(EntityInterface $entity) {
    $row['label'] = $entity->label();
    $row['id'] = $entity->id();
    $row['threshold'] = $entity->get('threshold');
    $challenge_type = $entity->get('challenge');
    $challenge_types = $this->getCaptchaChallengeTypes();
    $row['challenge'] = $challenge_types[$challenge_type] ?? $this->t('Not defined');
    // You probably want a few more properties here...
    return $row + parent::buildRow($entity);
  }

  protected function getCaptchaChallengeTypes() {
    if ($this->challengeTypes === NULL) {
      module_load_include('inc', 'captcha', 'captcha.admin');
      $this->challengeTypes = _captcha_available_challenge_types(FALSE);
      $default = \Drupal::config('recaptcha_v3.settings')->get('default_challenge');
      $this->challengeTypes['default'] = $this->challengeTypes[$default] ?? $this->t('Default');
    }
    return $this->challengeTypes;
  }

}
