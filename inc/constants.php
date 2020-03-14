<?php if (!defined('ABSPATH')) {exit;}

define('BPATH', get_site_url());
define('FPATH', filter_var(BPATH . $_SERVER['REQUEST_URI'] , FILTER_SANITIZE_URL));
define('CPATH', explode('?',FPATH )[0]);
define('TINYGIF', 'data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=');
