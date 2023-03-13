<?php
if (!defined('ABSPATH')) {
	exit;//Exit if accessed directly
}

require_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-base-tasks.php');

class AIOWPSecurity_Uninstallation_Tasks extends AIOWPSecurity_Base_Tasks {
	/**
	 * Runs various uninstallation tasks
	 * Handles single and multi-site (NW activation) cases
	 *
	 * @global type $wpdb
	 * @global type $aio_wp_security
	 */
	public static function run() {
		if (is_multisite()) {
			delete_site_transient('users_online');
		} else {
			delete_transient('users_online');
		}
		parent::run();
	}

	/**
	 * Run uninstallation task for a single site.
	 *
	 * @return void
	 */
	protected static function run_for_a_site() {
		self::clear_cron_events();
		// Drop db tables and configs
		self::drop_database_tables_and_configs();
	}
	
	/**
	 * Function to drop database tables and remove configuration settings
	 *
	 * @return void
	 */
	public static function drop_database_tables_and_configs() {

		global $wpdb, $aio_wp_security;

		$database_tables = array(
			$wpdb->prefix.'aiowps_login_lockdown',
			$wpdb->prefix.'aiowps_failed_logins',
			$wpdb->prefix.'aiowps_login_activity',
			$wpdb->prefix.'aiowps_global_meta',
			$wpdb->prefix.'aiowps_events',
			$wpdb->prefix.'aiowps_permanent_block',
			$wpdb->prefix.'aiowps_debug_log',
		);

		// check and drop database tables
		if ('1' == $aio_wp_security->configs->get_value('aiowps_on_uninstall_delete_db_tables')) {
			foreach ($database_tables as $table_name) {
				$wpdb->query("DROP TABLE IF EXISTS `$table_name`");
			}
		}

		// check and delete configurations
		if ('1' == $aio_wp_security->configs->get_value('aiowps_on_uninstall_delete_configs')) {

			delete_option('aio_wp_security_configs');
			delete_option('aiowps_temp_configs');
			delete_option('aiowpsec_db_version');

			if (is_main_site()) {
				// Remove all settings from .htaccess file that were added by this plugin
				AIOWPSecurity_Utility_Htaccess::write_to_htaccess();
			}
		}
	}
	
	/**
	 * Helper function which clears aiowps cron events
	 */
	private static function clear_cron_events() {
		wp_clear_scheduled_hook('aiowps_hourly_cron_event');
		wp_clear_scheduled_hook('aiowps_daily_cron_event');
	}
}
