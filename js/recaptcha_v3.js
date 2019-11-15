(function ($, Drupal) {

  /**
   * recaptcha v3 js.
   */
  Drupal.behaviors.reCaptchaV3 = {
    attach: function (context, settings) {
      $('.recaptcha-v3-token', context).once('recaptcha-v3-token').each(function () {
        var $token_element = $(this);
        grecaptcha.ready(function () {
          grecaptcha.execute(
              $token_element.data('recaptchaV3SiteKey'),
              {
                action: $token_element.data('recaptchaV3Action')
              }
          ).then(function (token) {
            $token_element.val(token);
            $token_element.trigger('change');
          });
        });
      });
    }
  };

})(jQuery, Drupal);
