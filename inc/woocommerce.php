<?php if (!defined('ABSPATH')) {exit;}

// add woocommerce theme support
add_action('after_setup_theme', 'bt_add_woocommerce_support');
function bt_add_woocommerce_support () {
	add_theme_support('woocommerce');
}

// all woocommerce actions
require THEME_DIR . 'inc/woocommerce/actions.php';

// all woocommerce filters
require THEME_DIR . 'inc/woocommerce/filters.php';

// custom woocommerce template exstansions
require THEME_DIR . 'inc/woocommerce/template-ext.php';