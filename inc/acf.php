<?php if (!defined('ABSPATH')) {exit;}

/** add an options page from the ACF plugin **/
if (function_exists('acf_add_options_page')) {
	$titles = [
		'parent'  => __('הגדרות כלליות של התבנית', 'bt'),
		'header'  => __('הגדרות HEADER של התבנית', 'bt'),
		'footer'  => __('הגדרות FOOTER של התבנית', 'bt'),
		'contact' => __('הגדרות יצירת קשר של התבנית', 'bt'),
		'social'  => __('הגדרות מדיה חברתית של התבנית', 'bt')
	];
	
	$parent = acf_add_options_page([
		'page_title' 	=> $titles['parent'],
		'menu_title'	=> $titles['parent'],
		'menu_slug' 	=> 'general',
		'capability'	=> 'edit_posts',
		'icon_url' 		=> 'dashicons-admin-settings',
		'redirect'		=> false
	]);

	$args = [
		[
			'page_title' 	=> $titles['header'],
			'menu_title'	=> $titles['header'],
			'menu_slug' 	=> 'header',
			'parent_slug' 	=> $parent['menu_slug']
		],
		[
			'page_title' 	=> $titles['footer'],
			'menu_title'	=> $titles['footer'],
			'menu_slug' 	=> 'footer',
			'parent_slug' 	=> $parent['menu_slug']
		],
		[
			'page_title' 	=> $titles['contact'],
			'menu_title'	=> $titles['contact'],
			'menu_slug' 	=> 'contact',
			'parent_slug' 	=> $parent['menu_slug']
		],
		[
			'page_title' 	=> $titles['social'],
			'menu_title'	=> $titles['social'],
			'menu_slug' 	=> 'social',
			'parent_slug' 	=> $parent['menu_slug']
		]
	];

	foreach ($args as $arg) {
		acf_add_options_sub_page($arg);
	}
}