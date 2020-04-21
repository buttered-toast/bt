<?php if (!defined('ABSPATH')) {exit;}

define('TEMPLATE_DIRECTORY_URI', get_template_directory_uri());

if (current_user_can('editor') || current_user_can('administrator')) {
	define('FILES_VERSION', '?v=' . time());
} else {
	define('FILES_VERSION', '?v=000000');
}

// combine minify and inline the css (experimental - don't use this)
//add_action( 'wp_head', 'load_critical_css_inline' );
function load_critical_css_inline () {
	Helper::minify_css([BDIR . '/assets/css/critical/style.css'], $css_files_content);
	echo '<style>' . $css_files_content . '</style>';
}

// loading all CRITICAL css files
add_action('wp_enqueue_scripts', 'bt_enqueue_critical_styles');
function bt_enqueue_critical_styles () {
	wp_enqueue_style('bt-critical-main', TEMPLATE_DIRECTORY_URI . '/assets/css/critical/style.css' . FILES_VERSION);
	
	if (is_front_page()) {
		wp_enqueue_style('bt-critical-front-page', TEMPLATE_DIRECTORY_URI . '/assets/css/critical/pages/front-page.css' . FILES_VERSION);
	}
}

// loading all NONE CRITICAL CSS FILES 
add_action('get_footer', 'bt_enqueue_none_critical_styles');
function bt_enqueue_none_critical_styles () {
    wp_enqueue_style('bt-none-critical-main', TEMPLATE_DIRECTORY_URI . '/assets/css/none-critical/style.css' . FILES_VERSION);
	
	if (is_front_page()) {
		wp_enqueue_style('bt-none-critical-front-page', TEMPLATE_DIRECTORY_URI . '/assets/css/none-critical/pages/front-page.css' . FILES_VERSION);
	}
}

// load all scripts
add_action('wp_enqueue_scripts', 'bt_enqueue_scripts');
function bt_enqueue_scripts () {
	wp_enqueue_script('bt-main', TEMPLATE_DIRECTORY_URI . '/assets/js/script.js' . FILES_VERSION, ['jquery'], '', true);

	$args = [
		'ajax_nonce' => wp_create_nonce('bt_site_ajax_nonce'),
		'ajaxurl'    => BPATH . '/wp-admin/admin-ajax.php'
		'BPATH'   	 => BPATH,
		'FPATH'   	 => FPATH,
		'CPATH'   	 => CPATH,
		'TINYGIF' 	 => TINYGIF
	];
  
    wp_localize_script('bt-main', 'system_globals', $args);
	
	if (is_front_page()) {
		wp_enqueue_script('bt-front-page', TEMPLATE_DIRECTORY_URI . '/assets/js/pages/front-page.js' . FILES_VERSION, ['jquery'], '', true);
	}
}
