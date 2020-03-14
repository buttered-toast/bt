<?php if (!defined('ABSPATH')) {exit;}

add_action('send_headers', 'bt_add_headers');
function bt_add_headers () {
	header('x-Powered-By: bt');
	header('server: bt');
	//header('X-XSS-Protection: 1; mode=block');
	//header('X-Frame-Options: DENY');
	//header('X-Content-Type-Options: nosniff');
	//header('Strict-Transport-Security: max-age=31536000');
}
