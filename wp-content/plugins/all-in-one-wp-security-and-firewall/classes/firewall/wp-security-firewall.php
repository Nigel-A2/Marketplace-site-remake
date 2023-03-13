<?php
/**
 * This is the file that will be loaded using auto_prepend_file directive
 */

if (!defined('AIOWPS_FIREWALL_DIR')) {
	define('AIOWPS_FIREWALL_DIR', dirname(__FILE__));
}

if (!defined('AIOWPSEC_FIREWALL_DONE')) {
	
	//Gracefully handle if the file is unable to be included. (i.e: ensure the user's site does not crash)
	if (!(@include_once AIOWPS_FIREWALL_DIR . '/wp-security-firewall-loader.php')) {
		error_log('AIOS firewall error: unable to load the firewall.');
		return;
	}

	\AIOWPS\Firewall\Loader::get_instance()->load_firewall();
	define('AIOWPSEC_FIREWALL_DONE', true);
}
