<?php if (!defined('ABSPATH')) {exit;}

/* example *\

add_action('wp_ajax_YOUR_ACTION', 'your_callback_function');
add_action('wp_ajax_nopriv_YOUR_ACTION', 'your_callback_function');
function your_callback_function () {
  check_ajax_referer('bt_site_ajax_nonce', 'security');
  
  // do something amazing!
  
  die;
}

// js
jQuery.ajax({
  url: system_globals.ajaxurl,
  method: 'POST',
  data: {
    action: 'YOUR_ACTION',
    security: system_globals.ajax_nonce,
  },
  success: function (response) {
    
  }
});

\* example - end */
