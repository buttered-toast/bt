<?php if (!defined('ABSPATH')) {exit;}

//======================================================================
// INDEX
//======================================================================
/*

= GENERAL
 - Remove
  |_ // add_filter('woocommerce_enqueue_styles', 'bt_dequeue_woocommerce_styles');
  
*/
//======================================================================
// INDEX - END
//======================================================================

//======================================================================
// GENERAL
//======================================================================

//-----------------------------------------------------
// Remove
//-----------------------------------------------------

// unset all default styles
// add_filter('woocommerce_enqueue_styles', 'bt_dequeue_woocommerce_styles');
function bt_dequeue_woocommerce_styles ($enqueue_styles) {
  if (WOOCOMMERCE_CONDITIONAL_FUNCTIONS_HERE) {
    $style_handles = [
      'woocommerce-general',
      'woocommerce-layout',
      'woocommerce-smallscreen'
    ];
    
    foreach ($style_handles as $style_handle) {
      unset($enqueue_styles[$style_handle]);
    }
  }

  return $enqueue_styles;
}

//-----------------------------------------------------
// Remove - end
//-----------------------------------------------------

//======================================================================
// GENERAL - END
//======================================================================
