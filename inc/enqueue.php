<?php if (!defined('ABSPATH')) {exit;}

define('TEMPLATE_DIRECTORY_URI', get_template_directory_uri());

if (current_user_can('editor') || current_user_can('administrator')) {
  define('FILES_VERSION', '?v=' . time());
} else {
  define('FILES_VERSION', '?v=000000');
}

// loading all CRITICAL css files
add_action('wp_enqueue_scripts', 'bt_enqueue_critical_styles');
function bt_enqueue_critical_styles () {
  wp_enqueue_style('bt-critical-main', TEMPLATE_DIRECTORY_URI . '/assets/css/critical/style.css' . FILES_VERSION);

  if (!IS_RTL) {
    wp_enqueue_style('bt-critical-main-ltr', TEMPLATE_DIRECTORY_URI . '/assets/css/ltr/critical/style.css' . FILES_VERSION);
  }

  if (is_front_page()) {
    wp_enqueue_style('bt-critical-front-page', TEMPLATE_DIRECTORY_URI . '/assets/css/critical/pages/front-page.css' . FILES_VERSION);

    if (!IS_RTL) {
      wp_enqueue_style('bt-critical-front-page-ltr', TEMPLATE_DIRECTORY_URI . '/assets/css/ltr/critical/pages/front-page.css' . FILES_VERSION);
    }
  }

  // under construction
  /*$args = [
    [
      'condition' => is_front_page(),
      'handle' 	  => 'bt-critical-front-page',
      'url'       => '/assets/css/critical/pages/front-page.css',
      'url_ltr'   => '/assets/css/ltr/critical/pages/front-page.css'
    ]
  ];

  Helper::style_enqueue_builder($args, FILES_VERSION);*/
}

// loading all NONE CRITICAL CSS FILES 
add_action('get_footer', 'bt_enqueue_none_critical_styles');
function bt_enqueue_none_critical_styles () {
  wp_enqueue_style('bt-none-critical-main', TEMPLATE_DIRECTORY_URI . '/assets/css/none-critical/style.css' . FILES_VERSION);

  if (!IS_RTL) {
    wp_enqueue_style('bt-none-critical-main-ltr', TEMPLATE_DIRECTORY_URI . '/assets/css/ltr/none-critical/style.css' . FILES_VERSION);
  }

  if (is_front_page()) {
    wp_enqueue_style('bt-none-critical-front-page', TEMPLATE_DIRECTORY_URI . '/assets/css/none-critical/pages/front-page.css' . FILES_VERSION);

    if (!IS_RTL) {
      wp_enqueue_style('bt-none-critical-front-page-ltr', TEMPLATE_DIRECTORY_URI . '/assets/css/ltr/none-critical/pages/front-page.css' . FILES_VERSION);
    }
  }

  // under construction
  /*$args = [
    [
      'condition' => is_front_page(),
      'handle' 	  => 'bt-none-critical-front-page',
      'url'       => '/assets/css/none-critical/pages/front-page.css',
      'url_ltr'   => '/assets/css/ltr/none-critical/pages/front-page.css'
    ]
  ];

  Helper::style_enqueue_builder($args, FILES_VERSION);*/
}

// load all scripts
add_action('wp_enqueue_scripts', 'bt_enqueue_scripts');
function bt_enqueue_scripts () {
  wp_enqueue_script('bt-main', TEMPLATE_DIRECTORY_URI . '/assets/js/script.js' . FILES_VERSION, ['jquery'], '', true);

  $args = [
    'ajax_nonce' => wp_create_nonce('bt_site_ajax_nonce'),
    'ajaxurl'    => BPATH . '/wp-admin/admin-ajax.php',
    'BPATH'   	 => BPATH,
    'FPATH'   	 => FPATH,
    'CPATH'   	 => CPATH,
    'TINYGIF' 	 => TINYGIF
  ];

  wp_localize_script('bt-main', 'system_globals', $args);

  if (is_front_page()) {
    wp_enqueue_script('bt-front-page-ltr', TEMPLATE_DIRECTORY_URI . '/assets/js/pages/front-page.js' . FILES_VERSION, ['jquery'], '', true);
  }

  // under construction
  /*$args = [
    [
      'condition' => is_front_page(),
      'handle' 	  => 'bt-front-page',
      'url'       => '/assets/js/pages/front-page.js'
    ]
  ];

  Helper::script_enqueue_builder($args, FILES_VERSION);*/
}
