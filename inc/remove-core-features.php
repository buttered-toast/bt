<?php if (!defined('ABSPATH')) {exit;}

// remove generator (wordpress version tag)
remove_action('wp_head', 'wp_generator');

add_filter('the_generator', 'bt_remove_wp_generator');
function bt_remove_wp_generator () {
	return '';
}

// disable access to RSS feed
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'feed_links', 2);
add_action('do_feed', 'bt_disable_rss_feed', 1);
add_action('do_feed_rdf', 'bt_disable_rss_feed', 1);
add_action('do_feed_rss', 'bt_disable_rss_feed', 1);
add_action('do_feed_rss2', 'bt_disable_rss_feed', 1);
add_action('do_feed_atom', 'bt_disable_rss_feed', 1);
add_action('do_feed_rss2_comments', 'bt_disable_rss_feed', 1);
add_action('do_feed_atom_comments', 'bt_disable_rss_feed', 1);
function bt_disable_rss_feed () {
	wp_redirect('/');
	die;
}

// remove dns prefetch
add_filter('emoji_svg_url', '__return_false');

// remove emojis
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('admin_print_scripts', 'print_emoji_detection_script');
remove_action('wp_print_styles', 'print_emoji_styles');
remove_action('admin_print_styles', 'print_emoji_styles');

// disable wp-embed
add_action('wp_footer', 'bt_remove_wp_embed');
function bt_remove_wp_embed (){
	wp_dequeue_script('wp-embed');
}

// Disable REST API link tag
remove_action('wp_head', 'rest_output_link_wp_head', 10);

// Disable oEmbed Discovery Links
remove_action('wp_head', 'wp_oembed_add_discovery_links', 10);

// Disable REST API link in HTTP headers
remove_action('template_redirect', 'rest_output_link_header', 11, 0);

//Remove Gutenberg Block Library CSS from loading on the frontend
add_action('wp_enqueue_scripts', 'bt_remove_wp_block_library_css');
function bt_remove_wp_block_library_css(){
	wp_dequeue_style('wp-block-library');
	wp_dequeue_style('wp-block-library-theme');
}

// remove rsd xml link
remove_action ('wp_head', 'rsd_link');

// remove wlwmanifest link
remove_action('wp_head', 'wlwmanifest_link');

// remove shorlink
add_filter('after_setup_theme', 'bt_remove_shortlink');
function bt_remove_shortlink () {
	remove_action('wp_head', 'wp_shortlink_wp_head', 10);
	remove_action('template_redirect', 'wp_shortlink_header', 11);
}

// remove the version from css and js files
add_filter('style_loader_src', 'remove_css_and_js_versions', 9999);
add_filter('script_loader_src', 'remove_css_and_js_versions', 9999);
function remove_css_and_js_versions ($src) {
	if (strpos($src, 'ver=')) {
		return remove_query_arg('ver', $src);
	}
}

// remove jquery migrate
add_action('wp_default_scripts', 'bt_remove_jquery_migrate');
function bt_remove_jquery_migrate ($scripts) {
	if (!is_admin() && isset($scripts->registered['jquery'])) {
		$script = $scripts->registered['jquery'];

		if ($script->deps) {
			$script->deps = array_diff($script->deps, ['jquery-migrate']);
		}
	}
}

// remove dashicons
//add_action('wp_print_styles', 'bt_remove_dashicons', 100);
function bt_remove_dashicons () {
	wp_deregister_style('amethyst-dashicons-style'); 
	wp_deregister_style('dashicons'); 
}

// remove add new content link from top toolbar
add_action('admin_bar_menu', 'bt_remove_wp_nodes', 999);
function bt_remove_wp_nodes () {
	global $wp_admin_bar;
	
	$wp_admin_bar->remove_node('new-content');
}

// remove default jquery and jquery migrate and load jquery 1.12.4
add_action('init', 'jquery_cdn');
function jquery_cdn() {
  if (!is_admin()) {
    wp_deregister_script('jquery');
    wp_register_script('jquery', 'https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js', false, '1.12.4');
    wp_enqueue_script('jquery');
  }
}

// remove WPML Generator
global $sitepress;
remove_action( 'wp_head', array( $sitepress, 'meta_generator_tag' ) );
