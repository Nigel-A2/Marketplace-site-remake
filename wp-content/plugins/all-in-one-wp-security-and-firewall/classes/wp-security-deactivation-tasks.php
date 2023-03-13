<?php
if (!defined('ABSPATH')) {
	exit;//Exit if accessed directly
}

require_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-base-tasks.php');

class AIOWPSecurity_Deactivation_Tasks extends AIOWPSecurity_Base_Tasks {

	/**
	 * Run deactivation task for a single site.
	 *
	 * @return void
	 */
	protected static function run_for_a_site() {
		global $aio_wp_security;
		//Let's first save the current aio_wp_security_configs options in a temp option
		update_option('aiowps_temp_configs', $aio_wp_security->configs->configs);

		delete_option('aio_wp_security_configs');

		if (is_main_site()) {
			// Remove all firewall and other .htaccess rules and remove all settings from .htaccess file that were added by this plugin
			AIOWPSecurity_Configure_Settings::turn_off_all_firewall_rules();

			//Deactivates PHP-based firewall
			AIOWPSecurity_Utility_Firewall::remove_firewall();
		}
	}

}
