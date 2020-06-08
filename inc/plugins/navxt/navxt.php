<?php if (!defined('ABSPATH')) {exit;}

// edit every title in the breadcrumb structure
add_filter('bcn_breadcrumb_title', 'bt_manipulate_breadcrumb_titles', 10, 2);
function bt_manipulate_breadcrumb_titles ($title, $type) {
  // do some magic
}
