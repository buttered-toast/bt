<?php if (!defined('ABSPATH')) {exit;}

define('BDIR', __DIR__);
define('THEME_DIR', WP_CONTENT_DIR . '/themes/bt/');

// clear cache for admins (can remove when site is live)
require THEME_DIR . 'inc/cache.php';

// setting php headers
require THEME_DIR . 'inc/headers.php';

// General theme helper functions
require THEME_DIR . 'inc/Helper.class.php';

// Theme constants
require THEME_DIR . 'inc/constants.php';

// Loading theme styles and scripts
require THEME_DIR . 'inc/enqueue.php';

// Loading ACF related content
require THEME_DIR . 'inc/acf.php';

// stopping theme/plugins updates
require THEME_DIR . 'inc/updates.php';

// general theme/plugin supports
require THEME_DIR . 'inc/support.php';

// registering content
require THEME_DIR . 'inc/register.php';

// remove wordpress core features
require THEME_DIR . 'inc/remove-core-features.php';

// add woocommerce support, custom actions, filters and template exstansions
//require THEME_DIR . 'inc/woocommerce.php';