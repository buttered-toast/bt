<?php if (!defined('ABSPATH')) {exit;}

// the template directories uri
define('TEMPLATE_DIRECTORY_URI', get_template_directory_uri());

// file versions, helps with cache busting when developing and in production
if (current_user_can('editor') || current_user_can('administrator')) {
  define('FILES_VERSION', '?v=' . time());
} else {
  define('FILES_VERSION', '?v=000000');
}

// loading all css files
add_action('wp_enqueue_scripts', 'bt_enqueue_styles');
function bt_enqueue_styles () {
  wp_enqueue_style('bt-main', TEMPLATE_DIRECTORY_URI . '/assets/css/style.css' . FILES_VERSION);

  if (!IS_RTL) {
    wp_enqueue_style('bt-main-ltr', TEMPLATE_DIRECTORY_URI . '/assets/css/ltr/style.css' . FILES_VERSION);
  }

  if (is_front_page()) {
    wp_enqueue_style('bt-front-page', TEMPLATE_DIRECTORY_URI . '/assets/css/pages/front-page.css' . FILES_VERSION);

    if (!IS_RTL) {
      wp_enqueue_style('bt-front-page-ltr', TEMPLATE_DIRECTORY_URI . '/assets/css/ltr/pages/front-page.css' . FILES_VERSION);
    }
  }
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
}
