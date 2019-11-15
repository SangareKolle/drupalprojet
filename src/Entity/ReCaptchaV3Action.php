<?php

namespace Drupal\recaptcha_v3\Entity;

use Drupal\Core\Config\Entity\ConfigEntityBase;
use ReCaptcha\ReCaptcha;

/**
 * Defines the reCAPTCHA v3 action entity.
 *
 * @ConfigEntityType(
 *   id = "recaptcha_v3_action",
 *   label = @Translation("reCAPTCHA v3 action"),
 *   label_collection = @Translation("reCAPTCHA v3 actions"),
 *   label_singular = @Translation("reCAPTCHA v3 action"),
 *   label_plural = @Translation("reCAPTCHA v3 actions"),
 *   label_count = @PluralTranslation(
 *     singular = "@count reCAPTCHA v3 action",
 *     plural = "@count reCAPTCHA v3 actions",
 *   ),
 *   handlers = {
 *     "view_builder" = "Drupal\Core\Entity\EntityViewBuilder",
 *     "list_builder" = "Drupal\recaptcha_v3\ReCaptchaV3ActionListBuilder",
 *     "form" = {
 *       "add" = "Drupal\recaptcha_v3\Form\ReCaptchaV3ActionForm",
 *       "edit" = "Drupal\recaptcha_v3\Form\ReCaptchaV3ActionForm",
 *       "delete" = "Drupal\recaptcha_v3\Form\ReCaptchaV3ActionDeleteForm"
 *     },
 *     "local_task_provider" = {
 *       "default" = "Drupal\entity\Menu\DefaultEntityLocalTaskProvider",
 *     },
 *     "route_provider" = {
 *       "default" = "Drupal\entity\Routing\DefaultHtmlRouteProvider",
 *     },
 *   },
 *   config_prefix = "recaptcha_v3_action",
 *   admin_permission = "administer CAPTCHA settings",
 *   list_cache_tags = {
 *    "rendered"
 *   },
 *   entity_keys = {
 *     "id" = "id",
 *     "label" = "label",
 *     "uuid" = "uuid",
 *   },
 *   config_export = {
 *     "id",
 *     "label",
 *     "threshold",
 *     "challenge",
 *   },
 *   links = {
 *     "add-form" = "/admin/config/people/captcha/recaptcha-v3-actions/add",
 *     "edit-form" = "/admin/config/people/captcha/recaptcha-v3-actions/{recaptcha_v3_action}",
 *     "delete-form" = "/admin/config/people/captcha/recaptcha-v3-actions/{recaptcha_v3_action}/delete",
 *     "collection" = "/admin/config/people/captcha/recaptcha-v3-actions"
 *   }
 * )
 */
class ReCaptchaV3Action extends ConfigEntityBase implements ReCaptchaV3ActionInterface {

  /**
   * The reCAPTCHA v3 action ID.
   *
   * @var string
   */
  protected $id;

  /**
   * The reCAPTCHA v3 action label.
   *
   * @var string
   */
  protected $label;

  /**
   * The reCAPTCHA v3 action threshold.
   *
   * @var float
   */
  protected $threshold = 1;

  /**
   * The reCAPTCHA v3 action fallback challenge.
   *
   * @var string
   */
  protected $challenge = 'default';

  /**
   * Create empty action entity;
   *
   * @return \Drupal\recaptcha_v3\Entity\ReCaptchaV3Action
   */
  public static function createEmptyAction() {
    return static::create([
      'id' => '',
      'label' => '',
      'threshold' => 1,
      'challenge' => 'default',
    ]);
  }

  public function verifyToken($token) {
    $request = \Drupal::request();
    $config = \Drupal::config('recaptcha_v3.settings');
    $secret_key = $config->get('secret_key');
    $recaptcha = new ReCaptcha($secret_key);

    if ($config->get('verify_hostname')) {
      $recaptcha->setExpectedHostname($request->getHost());
    }

    return $recaptcha->setExpectedAction($this->id)
      ->setScoreThreshold($this->threshold)
      ->verify($token, $request->getClientIp())
      ->toArray();
  }

  /**
   * @return string
   */
  public function getId() {
    return $this->id;
  }

  /**
   * @return string
   */
  public function getLabel() {
    return $this->label;
  }

  /**
   * @return float
   */
  public function getThreshold() {
    return $this->threshold;
  }

  /**
   * @return string
   */
  public function getChallenge() {
    return $this->challenge;
  }

}
