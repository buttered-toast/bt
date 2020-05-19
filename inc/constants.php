<?php if (!defined('ABSPATH')) {exit;}

define('BPATH', get_site_url());
define('FPATH', filter_var(BPATH . $_SERVER['REQUEST_URI'] , FILTER_SANITIZE_URL));
define('CPATH', explode('?',FPATH )[0]);
define('TINYGIF', BPATH . '/assets/images/tinygif.gif');
define('IS_RTL', is_rtl());
define('BASE_LANG', 'he');
