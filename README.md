reCaptcha V3
------------
   
## Introduction
 
The reCaptcha V3 module provides integration with Google reCaptcha V3 and
[CAPTCHA module](https://www.drupal.org/project/captcha).

The documentation for Google reCaptcha V3 may be found [here](https://developers.google.com/recaptcha/docs/v3), with
information regarding keys registration. 

We no more rely on the reCAPTCHA module for the use of the `recaptcha-php` library which is included in this module, and
make use of Composer instead of keeping a  
duplicating code.

__Caution__: This module is not compatible with [reCAPTCHA](https://www.drupal.org/project/recaptcha) (implementation for
Google reCaptcha V2).  

## Requirements

* [CAPTCHA](htps://www.drupal.org/project/captcha)
* [google/recaptacha](https://github.com/google/recaptcha)

## Recommended modules

## Installation

Install the module as usual.
If not using Composer, install the [google/recaptacha](https://github.com/google/recaptcha) library.

## Configuration

You need to specify you reCaptcha key.

## Troubleshooting

## FAQ

## Maintainers
