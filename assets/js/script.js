/*** INDEX ***\

= WHOLE SITE SCRIPTS

\*** END INDEX ***/

//======================================================================
// WHOLE SITE SCRIPTS
//======================================================================

// on page scroll ...
// jQuery(window).scroll(function () {
//   var ce = jQuery(this);

//   if (ce.scrollTop() > 0) {
//     // scrolled action
//   } else {
//     // at top of page action
//   }
// });

// custom lazy load for images and background images
function bt_lazy_load_img(scroll_position) {
  jQuery('.bt-lazy-load').each(function () {
    var ce = jQuery(this);
    if (scroll_position >= ce.offset().top) {
      ce.attr('src', ce.data('image-src')).removeClass('bt-lazy-load');
    }
  });
  jQuery('.bt-lazy-load-bg').each(function () {
    var ce = jQuery(this),
      ce_style = ce.attr('style'),
      style = 'background-image: url(\'' + ce.data('image-src') + '\');';

    if (scroll_position >= ce.offset().top) {
      if (ce_style.indexOf('background-image') >= 0) {
        var style_arr = ce_style.split(';');
        jQuery.each(style_arr, function (key, value) {
          if (value !== undefined) {
            if (value.indexOf('background-image') >= 0) {
              style_arr.splice((key + 1), 1);
              style_arr[key] = 'background-image: url( "' + ce.data('image-src') + '" )';
              style = style_arr.join(';');
            }
          }
        });
      }
      ce.attr('style', style).removeClass('bt-lazy-load-bg');
    }
  });
}

jQuery(function () {
  bt_lazy_load_img(jQuery(window).scrollTop() + screen.height);
});

jQuery(window).scroll(function () {
  var ce = jQuery(this);

  bt_lazy_load_img(ce.scrollTop() + screen.height);
});

//======================================================================
// WHOLE SITE SCRIPTS
//======================================================================
