<?php if (!defined('ABSPATH')) {exit;}

// add menus support
add_theme_support('menus');

// add thumbnails support
add_theme_support('post-thumbnails');

// make script and styles tags in html5 format
add_action('after_setup_theme', 'bt_html5_script_and_styles_format');
function bt_html5_script_and_styles_format () {
	add_theme_support('html5', ['script', 'style']);
}