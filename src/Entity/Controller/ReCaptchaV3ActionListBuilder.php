<?php

namespace Drupal\recaptcha_v3\Entity\Controller;

use Drupal\captcha\Service\CaptchaService;
use Drupal\Core\Config\Entity\ConfigEntityListBuilder;
use Drupal\Core\Entity\EntityInterface;
use Drupal\Core\Entity\EntityStorageInterface;
use Drupal\Core\Entity\EntityTypeInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Provides a listing of reCAPTCHA v3 action entities.
 */
class ReCaptchaV3ActionListBuilder extends ConfigEntityListBuilder {

  /**
   * The CAPTCHA helper service.
   *
   * @var \Drupal\captcha\Service\CaptchaService
   */
  protected $captchaService;

  /**
   * @var challengeTypes
   */
  protected $challengeTypes;

  /**
   * {@inheritdoc}
   */
  public static function createInstance(ContainerInterface $container, EntityTypeInterface $entity_type) {
    return new static(
      $entity_type,
      $container->get('entity.manager')->getStorage($entity_type->id()),
      $container->get('captcha.helper')
    );
  }

  /**
   * ReCaptchaV3ActionListBuilder constructor.
   *
   * @param \Drupal\Core\Entity\EntityTypeInterface $entity_type
   * @param \Drupal\Core\Entity\EntityStorageInterface $storage
   * @param \Drupal\captcha\Service\CaptchaService $captcha_service
   */
  public function __construct(EntityTypeInterface $entity_type, EntityStorageInterface $storage, CaptchaService $captcha_service) {
    parent::__construct($entity_type, $storage);
    $this->captchaService = $captcha_service;
  }


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
      $this->challengeTypes = $this->captchaService->getAvailableChallengeTypes(FALSE);
      $default = \Drupal::config('recaptcha_v3.settings')->get('default_challenge');
      $this->challengeTypes['default'] = $this->challengeTypes[$default] ?? $this->t('Default');
    }
    return $this->challengeTypes;
  }

}
