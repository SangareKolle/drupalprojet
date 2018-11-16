!!!This module requiring the dev version of the recaptcha module! With stable 7.x-2.2 recaptcha version you will get WSOD on form submission!

reCAPTCHA v3 returns a score for each request without user friction. The score is based on interactions with your site and enables you to take an appropriate action for your site. Register reCAPTCHA v3 keys here.
Some explains how to enable and customize reCAPTCHA v3 on your webpage:

At first you need to create at least one action: populate action name*, choose score threshold** and select action on user verification fail***.
* - reCAPTCHA v3 introduces a new concept: actions. Actions name will be displayed in detailed break-down of data for your top ten actions in the admin console
** - reCAPTCHA v3 returns a score (1.0 is very likely a good interaction, 0.0 is very likely a bot). Based on the score, you can take variable action in the context of your site.
*** - You could specify the additional validation challenge for failed recaptcha v3 validations. If you leaved empty "Default challenge type" option and "Challenge" for concrete action, then if user validation failed, recaptcha v3 will be using again. This can bring to inability to submit form for suspicious users.

I'm recommending to use this module together with recaptcha: set default captcha type to recaptcha v3 and select recaptcha v2 for the additional challenge. In this case you will have unintrusive recaptcha v3 as default challenge and standard recaptcha for failed user validations.

Dependencies:

    captcha
    recaptcha
