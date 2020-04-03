<?php if (!defined('ABSPATH')) {exit;}

// register theme menus
add_action('init', 'bt_register_theme_menus');
function bt_register_theme_menus () {
	$args = [
		'main-site-menu'        => __('Header menu', 'bt'),
		'main-site-footer-menu' => __('Footer menu', 'bt')
	];

	register_nav_menus($args);
}

/* // quick menu display code
 * 
 * reference: https://developer.wordpress.org/reference/functions/wp_nav_menu/
$args = [
	'container' 	 => false,
	'theme_location' => 'main-site-menu',
	'menu_class' 	 => 'main-site-navigation'
];

wp_nav_menu($args);
*/
