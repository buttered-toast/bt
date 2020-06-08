<?php if (!defined('ABSPATH')) {exit;}

define('BDIR', __DIR__);
define('THEME_DIR', WP_CONTENT_DIR . '/themes/bt/');

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

// everything pluign hooks
require THEME_DIR . 'inc/plugins.php';

// Everything ajax
require THEME_DIR . 'inc/ajax.php';

// Theme actions
require THEME_DIR . 'inc/actions.php';

// Theme filters
require THEME_DIR . 'inc/filters.php';
