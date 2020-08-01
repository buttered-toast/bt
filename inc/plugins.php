<?php if (!defined('ABSPATH')) {exit;}

//======================================================================
// INDEX
//======================================================================
/*

= WOOCOMMERCE

= NAVXT

*/
//======================================================================
// INDEX - END
//======================================================================

//======================================================================
// WOOCOMMERCE
//======================================================================

// add woocommerce theme support
add_action('after_setup_theme', 'bt_add_woocommerce_support');
function bt_add_woocommerce_support () {
	add_theme_support('woocommerce');
}

// all woocommerce actions
require THEME_DIR . 'inc/plugins/woocommerce/actions.php';

// all woocommerce filters
require THEME_DIR . 'inc/plugins/woocommerce/filters.php';

// custom woocommerce template exstansions
require THEME_DIR . 'inc/plugins/woocommerce/template-ext.php';

//======================================================================
// WOOCOMMERCE - END
//======================================================================

//======================================================================
// NAVXT
//======================================================================

// all the actions/filters (I don't split it into multiple files because usualy there isn't much code for this plugin)
require THEME_DIR . 'inc/plugins/navxt/navxt.php';

//======================================================================
// NAVXT - END
//======================================================================
