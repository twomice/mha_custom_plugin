<?php

/*
  Plugin Name: MHA custom plugin
  Description: Contains custom functions for MHA
  Version: 1.0.5
  Author: Allen Shaw for Colorado Creative Designs
  Author URI: joineryhq.com
 */

add_action('login_form_register', 'mha_redirect_login_form_register');
add_action('after_setup_theme', 'mha_hide_admin_bar');
add_filter('body_class', 'mha_add_slug_body_class');
add_filter('login_redirect', 'mha_login_redirect');

/**
 * Redirect user after successful login.
 *
 * @param string $redirect_to URL to redirect to.
 * @param string $request URL the user is coming from.
 * @param object $user Logged user's data.
 * @return string
 */
function mha_login_redirect($redirect_to, $request = NULL, $user = NULL) {
  //is there a user to check?
  if ($user === NULL) {
    // If no user provided, use the global user variable.
    global $user;
  }
  if (isset($user->roles) && is_array($user->roles)) {
    //check for subscribers
    if (in_array('subscriber', $user->roles)) {
      // redirect them to another URL, in this case, the homepage
      $redirect_to = home_url();
    }
  }
  return $redirect_to;
}

function mha_redirect_login_form_register() {
  // This URL is assumed to exist, but be sure, and redirect only if it does.
  $page_path = '/user-registration/';
  if ($page = get_page_by_path($page_path)) {
    wp_redirect($page_path); // Redirect directly to $page_path.
                             // In earlier versions we redirected to $page=>guid,
                             // but this is problematic because somehow that 
                             // causes us to be redirected to the dev-only
                             // "MHA2018" subdomain.
    exit(); // always call `exit()` after `wp_redirect`
  }
}

//Page Slug Body Class
function mha_add_slug_body_class($classes) {
  global $post;
  $parsed = wp_parse_url(get_page_link());
  $path_slug = str_replace('/', '-', trim($parsed['path'], '/'));
  if ($path_slug) {
    $classes[] = 'page-path-' . $path_slug;
  }
  return $classes;
}

function mha_hide_admin_bar() {
  if (!current_user_can('administrator') && !is_admin()) {
    show_admin_bar(false);
  }
}