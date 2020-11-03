<?php

/**
 * @file
 * Provides the Google No CAPTCHA administration settings.
 */

/**
 * Form callback; administrative settings for Google No CAPTCHA.
 */
function recaptcha_v3_admin_settings($form, &$form_state) {
  module_load_include('inc', 'captcha', 'captcha.admin');
  $form['#variable_edit_form'] = TRUE;
  $form['general_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('General settings'),
    '#collapsible' => TRUE,
    '#collapsed' => TRUE,
  );
  $form['general_settings']['recaptcha_v3_site_key'] = array(
    '#type' => 'textfield',
    '#title' => t('Site key'),
    '#default_value' => variable_get('recaptcha_v3_site_key', ''),
    '#maxlength' => 40,
    '#description' => t('The site key given to you when you <a href="@url">register for reCAPTCHA</a>.', array('@url' => 'https://www.google.com/recaptcha/admin')),
    '#required' => TRUE,
  );
  $form['general_settings']['recaptcha_v3_secret_key'] = array(
    '#type' => 'textfield',
    '#title' => t('Secret key'),
    '#default_value' => variable_get('recaptcha_v3_secret_key', ''),
    '#maxlength' => 40,
    '#description' => t('The secret key given to you when you <a href="@url">register for reCAPTCHA</a>.', array('@url' => 'https://www.google.com/recaptcha/admin')),
    '#required' => TRUE,
  );
  $form['general_settings']['recaptcha_v3_verify_hostname'] = array(
    '#type' => 'checkbox',
    '#title' => t('Local domain name validation'),
    '#default_value' => variable_get('recaptcha_v3_verify_hostname', FALSE),
    '#description' => t('Checks the hostname on your server when verifying a solution. Enable this validation only, if <em>Verify the origin of reCAPTCHA solutions</em> is unchecked for your key pair. Provides crucial security by verifying requests come from one of your listed domains.'),
  );
  $form['actions_settings'] = array(
    '#type' => 'fieldset',
    '#title' => t('Widget settings'),
    '#collapsible' => TRUE,
    '#collapsed' => FALSE,
    '#tree' => TRUE,
  );
  $challenges = _captcha_available_challenge_types(FALSE);
  $challenges = array_merge(array('' => t('None')), $challenges);
  $form['actions_settings']['recaptcha_v3_captcha_default_challenge'] = array(
    '#type' => 'select',
    '#title' => t('Default fallback challenge type'),
    '#description' => t('Select the default fallback challenge type on verification fail.'),
    '#options' => $challenges,
    '#default_value' => variable_get('recaptcha_v3_captcha_default_challenge', 'recaptcha/recaptcha'),
  );
  $form['actions_settings']['overview'] = array(
    '#theme' => 'recaptcha_v3_admin_settings_actions_overview',
  );
  $actions = _recaptcha_v3_get_all_actions();
  foreach ($actions as $id => $action) {
    $form['actions_settings']['overview'][$id]['id'] = array(
      '#type' => 'value',
      '#value' => $id,
    );
    $form['actions_settings']['overview'][$id]['action'] = array(
      '#type' => 'textfield',
      '#title' => t('Action'),
      '#title_display' => 'invisible',
      '#size' => 30,
      '#max_length' => 60,
      '#default_value' => $action['action'],
      '#required' => TRUE,
    );
    $form['actions_settings']['overview'][$id]['score'] = array(
      '#type' => 'select',
      '#title' => t('Score'),
      '#title_display' => 'invisible',
      '#options' => array(0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1),
      '#default_value' => $action['score'],
      '#required' => TRUE,
    );
    $form['actions_settings']['overview'][$id]['challenge'] = array(
      '#type' => 'select',
      '#title' => t('Fallback challenge'),
      '#title_display' => 'invisible',
      '#options' => $challenges,
      '#default_value' => $action['challenge'],
    );
  }
  $form['actions_settings']['new'] = array(
    '#type' => 'fieldset',
    '#collapsible' => FALSE,
    'collapsed' => FALSE,
    '#attributes' => array(
      'class' => array('container-inline',),
    ),
    '#title' => t('Create new action'),
  );
  $form['actions_settings']['new']['action'] = array(
    '#type' => 'textfield',
    '#title' => t('Action'),
    '#size' => 30,
    '#max_length' => 60,
  );
  $form['actions_settings']['new']['id'] = array(
    '#type' => 'machine_name',
    '#title' => t('Machine name'),
    '#size' => 20,
    '#machine_name' => array(
      'exists' => '_recaptcha_v3_id_exists',
      'source' => array('actions_settings', 'new', 'action'),
    ),
    '#required' => FALSE,
  );
  $form['actions_settings']['new']['score'] = array(
    '#type' => 'select',
    '#title' => t('Score'),
    '#options' => array(0, 0.1, 0.2, 0.3, 0.4, 0.5, 0.6, 0.7, 0.8, 0.9, 1),
    '#default_value' => 5,
  );
  $form['actions_settings']['new']['challenge'] = array(
    '#type' => 'select',
    '#title' => t('Fallback challenge'),
    '#options' => $challenges,
  );
  $form['actions_settings']['new']['description'] = array(
    '#theme' => 'item_list',
    '#items' => array(
      t('<strong>Action</strong> may only contain alphanumeric characters, underscores and forward slashes.'),
      t('Additional verification <strong>Challenge</strong> will be executed, if google verification response scores will be lower then in <strong>Scores</strong> select box.'),
      t('If <strong>Challenge</strong> value is <strong>None</strong>, then instead additional challenge recaptcha v3 will be used again. This may cause unability to submit form.'),
    ),
  );
  $form['recaptcha_v3_error_message'] = array(
    '#type' => 'textfield',
    '#size' => 128,
    '#title' => t('Error message'),
    '#description' => t('This message will be displayed to user in case of failed recaptcha v3 verification.'),
    '#default_value' => variable_get('recaptcha_v3_error_message', t('Recaptcha verification failed.')),
  );
  $form['actions'] = array('#type' => 'actions');
  $form['actions']['submit'] = array(
    '#type' => 'submit',
    '#value' => t('Save configuration'),
  );
  return $form;
}

function theme_recaptcha_v3_admin_settings_actions_overview($variables) {
  $table = $variables['form'];
  $header = array(t('Action'), t('Score'), t('Challenge type'), t('Delete'));
  $rows = array();
  // Existing CAPTCHA points.
  foreach (element_children($table) as $key) {
    $row = array();
    $row[] = drupal_render($table[$key]['action']);
    $row[] = drupal_render($table[$key]['score']);
    $row[] = drupal_render($table[$key]['challenge']);
    $row[] = l(t('delete'), "admin/config/people/captcha/recaptcha-v3/{$key}/delete");
    $rows[] = $row;
  }
  return theme('table', array('header' => $header, 'rows' => $rows));
}

/**
 * Validation function for recaptcha_v3_admin_settings().
 *
 * @see recaptcha_v3_admin_settings()
 */
function recaptcha_v3_admin_settings_validate($form, &$form_state) {
  $new_action = $form_state['values']['actions_settings']['new'];
  if (!empty($new_action['action'])) {
    if (!preg_match('/^([0-9a-zA-Z\/_]+)$/', $new_action['action'])) {
      form_set_error('actions_settings][new][action', t('Action may only contain alphanumeric characters, underscores and forward slashes.'));
    }
    if (empty($new_action['id'])) {
      form_set_error('actions_settings][new][id', t('Action machine name is required.'));
    }
    elseif ($new_action['id'] === 'default') {
      form_set_error('actions_settings][new][id', t("You can't use string \"default\" as action id. This id reserved by captcha module."));
    }
  }
  if (!empty($form_state['values']['actions_settings']['overview'])) {
    $form_actions = $form_state['values']['actions_settings']['overview'];
    foreach ($form_actions as $id => $form_action) {
      if (!preg_match('/^([0-9a-zA-Z\/_]+)$/', $form_action['action'])) {
        form_set_error('actions_settings][overview][' . $id . '][action', t('Action may only contain alphanumeric characters, underscores and forward slashes.'));
      }
    }
  }
}

/**
 * Submit function for recaptcha_v3_admin_settings().
 *
 * @see recaptcha_v3_admin_settings()
 */
function recaptcha_v3_admin_settings_submit($form, &$form_state) {
  variable_set('recaptcha_v3_site_key', $form_state['values']['recaptcha_v3_site_key']);
  variable_set('recaptcha_v3_secret_key', $form_state['values']['recaptcha_v3_secret_key']);
  variable_set('recaptcha_v3_verify_hostname', $form_state['values']['recaptcha_v3_verify_hostname']);
  variable_set('recaptcha_v3_captcha_default_challenge', $form_state['values']['actions_settings']['recaptcha_v3_captcha_default_challenge']);
  variable_set('recaptcha_v3_error_message', $form_state['values']['recaptcha_v3_error_message']);

  $actions = _recaptcha_v3_get_all_actions();
  if (!empty($form_state['values']['actions_settings']['overview'])) {
    $count = 0;
    $form_actions = $form_state['values']['actions_settings']['overview'];
    foreach ($form_actions as $id => $form_action) {
      if (isset($actions[$id]) && $actions[$id] != $form_action) {
        $count += db_update('recaptcha_v3_actions')
          ->fields($form_action)
          ->condition('id', $id)
          ->execute();
      }
    }
    drupal_set_message(format_plural($count, '1 action has been updated.', '@count actions has been updated.'));
  }
  $new_action = $form_state['values']['actions_settings']['new'];
  if (!empty($new_action['action'])) {
    $res = drupal_write_record('recaptcha_v3_actions', $new_action);
    if ($res) {
      drupal_set_message(t('New action has been created.'));
    }
    else {
      drupal_set_message(t('An error happened during creation of thr new action.'));
    }
  }
}

function recaptcha_v3_admin_settings_delete_action($form, &$form_state, $id) {
  if (_recaptcha_v3_id_exists($id)) {
    $form['id'] = array(
      '#type' => 'value',
      '#value' => $id,
    );

    return confirm_form(
      $form,
      t('Are you sure you want to delete this action?'),
      'admin/config/people/captcha/recaptcha-v3',
      t('This action cannot be undone.'),
      t('Delete'),
      t('Cancel')
    );
  }
  drupal_set_message(t('An action with %id not exist!', array('%id' => $id)), 'error');
  drupal_goto('admin/config/people/captcha/recaptcha-v3');
}

function recaptcha_v3_admin_settings_delete_action_submit($form, &$form_state) {
  db_delete('recaptcha_v3_actions')
    ->condition('id', $form_state['values']['id'])
    ->execute();
  drupal_set_message(t('The action %id has been deleted.', array('%id' => $form_state['values']['id'])));
  $form_state['redirect'] = 'admin/config/people/captcha/recaptcha-v3';
}
