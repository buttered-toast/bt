<?php if (!defined('ABSPATH')) {exit;}

// Clears the site php cache
if (current_user_can('manage_options') && current_user_can('administrator')) {
	opcache_reset();
}