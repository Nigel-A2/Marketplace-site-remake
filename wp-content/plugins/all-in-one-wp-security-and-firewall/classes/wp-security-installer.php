<?php
if (!defined('ABSPATH')) {
	exit;//Exit if accessed directly
}

class AIOWPSecurity_Installer {

	/**
	 * Run installer function.
	 *
	 * @param boolean $networkwide
	 * @return void
	 */
	public static function run_installer($networkwide = '') {
		global $wpdb;
		if (function_exists('is_multisite') && is_multisite() && $networkwide) {
			// check if it is a network activation - if so, run the activation function for each blog id
				$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					AIOWPSecurity_Installer::create_db_tables();
					AIOWPSecurity_Configure_Settings::add_option_values();
					restore_current_blog();
				}
				AIOWPSecurity_Installer::create_db_backup_dir(); //Create a backup dir in the WP uploads directory
		} else {
			AIOWPSecurity_Installer::create_db_tables();
			AIOWPSecurity_Configure_Settings::add_option_values();
			AIOWPSecurity_Installer::create_db_backup_dir(); //Create a backup dir in the WP uploads directory
		}
	}

	public static function create_db_tables() {
		global $wpdb;

		if (!function_exists('dbDelta')) {
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		}

		if (function_exists('is_multisite') && is_multisite()) {
			/*
			 * FIX for multisite table creation case:
			 * Although each table name is defined in a constant inside the wp-security-core.php,
			 * we need to do this step for multisite case because we need to refresh the $wpdb->prefix value
			 * otherwise it will contain the original blog id and not the current id we need.
			 *
			 */
			$lockdown_tbl_name = $wpdb->prefix.'aiowps_login_lockdown';
			$failed_login_tbl_name = $wpdb->prefix.'aiowps_failed_logins';
			$user_login_activity_tbl_name = $wpdb->prefix.'aiowps_login_activity';
			$aiowps_global_meta_tbl_name = $wpdb->prefix.'aiowps_global_meta';
			$aiowps_event_tbl_name = $wpdb->prefix.'aiowps_events';
			$perm_block_tbl_name = $wpdb->prefix.'aiowps_permanent_block';
			
		} else {
			$lockdown_tbl_name = AIOWPSEC_TBL_LOGIN_LOCKDOWN;
			$failed_login_tbl_name = AIOWPSEC_TBL_FAILED_LOGINS;
			$user_login_activity_tbl_name = AIOWPSEC_TBL_USER_LOGIN_ACTIVITY;
			$aiowps_global_meta_tbl_name = AIOWPSEC_TBL_GLOBAL_META_DATA;
			$aiowps_event_tbl_name = AIOWPSEC_TBL_EVENTS;
			$perm_block_tbl_name = AIOWPSEC_TBL_PERM_BLOCK;
		}

		$debug_log_tbl_name = AIOWPSEC_TBL_DEBUG_LOG;

		$charset_collate = '';
		if (!empty($wpdb->charset)) {
			$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";
		} else {
			$charset_collate = "DEFAULT CHARSET=utf8";
		}
		if (!empty($wpdb->collate)) {
			$charset_collate .= " COLLATE $wpdb->collate";
		}

		$ld_tbl_sql = "CREATE TABLE " . $lockdown_tbl_name . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		user_id bigint(20) NOT NULL,
		user_login VARCHAR(150) NOT NULL,
		lockdown_date datetime NOT NULL DEFAULT '1000-10-10 10:00:00',
		release_date datetime NOT NULL DEFAULT '1000-10-10 10:00:00',
		failed_login_ip varchar(100) NOT NULL DEFAULT '',
		lock_reason varchar(128) NOT NULL DEFAULT '',
		unlock_key varchar(128) NOT NULL DEFAULT '',
		is_lockout_email_sent tinyint(1) NOT NULL DEFAULT '1',
		backtrace_log text NOT NULL DEFAULT '',
		PRIMARY KEY  (id),
		  KEY failed_login_ip (failed_login_ip),
		  KEY is_lockout_email_sent (is_lockout_email_sent),
		  KEY unlock_key (unlock_key)
		)" . $charset_collate . ";";
		dbDelta($ld_tbl_sql);

		$fl_tbl_sql = "CREATE TABLE " . $failed_login_tbl_name . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		user_id bigint(20) NOT NULL,
		user_login VARCHAR(150) NOT NULL,
		failed_login_date datetime NOT NULL DEFAULT '1000-10-10 10:00:00',
		login_attempt_ip varchar(100) NOT NULL DEFAULT '',
		PRIMARY KEY  (id),
		  KEY failed_login_date (failed_login_date),
		  KEY login_attempt_ip (login_attempt_ip),
		  KEY failed_login_date_and_login_attempt_ip (failed_login_date, login_attempt_ip)
		)" . $charset_collate . ";";
		dbDelta($fl_tbl_sql);

		$ula_tbl_sql = "CREATE TABLE " . $user_login_activity_tbl_name . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		user_id bigint(20) NOT NULL,
		user_login VARCHAR(150) NOT NULL,
		login_date datetime NOT NULL DEFAULT '1000-10-10 10:00:00',
		logout_date datetime NOT NULL DEFAULT '1000-10-10 10:00:00',
		login_ip varchar(100) NOT NULL DEFAULT '',
		login_country varchar(150) NOT NULL DEFAULT '',
		browser_type varchar(150) NOT NULL DEFAULT '',
		PRIMARY KEY  (id)
		)" . $charset_collate . ";";
		dbDelta($ula_tbl_sql);

		$gm_tbl_sql = "CREATE TABLE " . $aiowps_global_meta_tbl_name . " (
		meta_id bigint(20) NOT NULL auto_increment,
		date_time datetime NOT NULL default '1000-10-10 10:00:00',
		meta_key1 varchar(255) NOT NULL,
		meta_key2 varchar(255) NOT NULL,
		meta_key3 varchar(255) NOT NULL,
		meta_key4 varchar(255) NOT NULL,
		meta_key5 varchar(255) NOT NULL,
		meta_value1 varchar(255) NOT NULL,
		meta_value2 text NOT NULL,
		meta_value3 text NOT NULL,
		meta_value4 longtext NOT NULL,
		meta_value5 longtext NOT NULL,
		PRIMARY KEY  (meta_id)
		)" . $charset_collate . ";";
		dbDelta($gm_tbl_sql);

		$evt_tbl_sql = "CREATE TABLE " . $aiowps_event_tbl_name . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		event_type VARCHAR(150) NOT NULL DEFAULT '',
		username VARCHAR(150),
		user_id bigint(20),
		event_date datetime NOT NULL DEFAULT '1000-10-10 10:00:00',
		ip_or_host varchar(100),
		referer_info varchar(255),
		url varchar(255),
		country_code varchar(50),
		event_data longtext,
		PRIMARY KEY  (id)
		)" . $charset_collate . ";";
		dbDelta($evt_tbl_sql);

		$pb_tbl_sql = "CREATE TABLE " . $perm_block_tbl_name . " (
		id bigint(20) NOT NULL AUTO_INCREMENT,
		blocked_ip varchar(100) NOT NULL DEFAULT '',
		block_reason varchar(128) NOT NULL DEFAULT '',
		country_origin varchar(50) NOT NULL DEFAULT '',
		blocked_date datetime NOT NULL DEFAULT '1000-10-10 10:00:00',
		unblock tinyint(1) NOT NULL DEFAULT '0',
		PRIMARY KEY  (id),
		KEY blocked_ip (blocked_ip)
		)" . $charset_collate . ";";
		dbDelta($pb_tbl_sql);

		$debug_log_tbl_sql = "CREATE TABLE " . $debug_log_tbl_name . " (
			id bigint(20) NOT NULL AUTO_INCREMENT,
			level varchar(25) NOT NULL DEFAULT '',
			message text NOT NULL DEFAULT '',
			type varchar(25) NOT NULL DEFAULT '',
			created datetime NOT NULL DEFAULT '1000-10-10 10:00:00',
			PRIMARY KEY  (id)
			)" . $charset_collate . ";";
		dbDelta($debug_log_tbl_sql);
	}

	public static function create_db_backup_dir() {
		global $aio_wp_security;
		//Create our folder in the "wp-content" directory
		$aiowps_dir = WP_CONTENT_DIR . '/' . AIO_WP_SECURITY_BACKUPS_DIR_NAME;
		if (!is_dir($aiowps_dir) && is_writable(WP_CONTENT_DIR)) {
			mkdir($aiowps_dir, 0755, true);
			//Let's also create an empty index.html file in this folder
			$index_file = $aiowps_dir . '/index.html';
			$handle = fopen($index_file, 'w'); //or die('Cannot open file:  '.$index_file);
			fclose($handle);
		}
		$server_type = AIOWPSecurity_Utility::get_server_type();
		//Only create .htaccess if server is the right type
		if ('apache' == $server_type || 'litespeed' == $server_type) {
			$file = $aiowps_dir . '/.htaccess';
			if (!file_exists($file)) {
				//Create an .htacces file
				//Write some rules which will only allow people originating from wp admin page to download the DB backup
				$rules = '';
				$rules .= 'order deny,allow' . PHP_EOL;
				$rules .= 'deny from all' . PHP_EOL;
				$write_result = file_put_contents($file, $rules);
				if (false === $write_result) {
					$aio_wp_security->debug_logger->log_debug("Creation of .htaccess file in " . AIO_WP_SECURITY_BACKUPS_DIR_NAME . " directory failed!", 4);
				}
			}
		}
	}

	public static function reactivation_tasks() {
		global $aio_wp_security;
		$temp_cfgs = get_option('aiowps_temp_configs');
		if (false !== $temp_cfgs) {
			//Case where previously installed plugin was reactivated
			//Let's copy the original configs back to the options table
			$updated = update_option('aio_wp_security_configs', $temp_cfgs);
			if (!$updated) {
				$aio_wp_security->debug_logger->log_debug("AIOWPSecurity_Installer::reactivation_tasks() - Update of option settings failed upon plugin activation!", 4);
			}
			$aio_wp_security->configs->configs = $temp_cfgs; //copy the original configs to memory
			//Now let's write any rules to the .htaccess file if necessary
			$res = AIOWPSecurity_Utility_Htaccess::write_to_htaccess();

			if (!$res) {
				$aio_wp_security->debug_logger->log_debug("AIOWPSecurity_Installer::reactivation_tasks() - Could not write to the .htaccess file. Please check the file permissions.", 4);
				return false;
			}
			delete_option('aiowps_temp_configs');
			return true;
		} else {
			$aio_wp_security->debug_logger->log_debug("AIOWPSecurity_Deactivation::run_deactivation_tasks() - Original config settings not found!", 4);
			return false;
		}
	}
	
	/**
	 * Setup AIOS cron tasks.
	 * Handles both single and multi-site (NW activation) cases.
	 *
	 * @global type $wpdb
	 * @param Boolean $networkwide Whether set cronjob networkwide or normal site.
	 * @return Void
	 */
	public static function set_cron_tasks_upon_activation($networkwide = false) {
		require_once(__DIR__.'/wp-security-cronjob-handler.php');
		// It is required because we are going to schedule a 15-minute cron event upon activation.
		add_filter('cron_schedules', array('AIOWPSecurity_Cronjob_Handler', 'cron_schedules'));
		if (is_multisite() && $networkwide) {
			global $wpdb;
			// check if it is a network activation
			$blogids = $wpdb->get_col("SELECT blog_id FROM $wpdb->blogs");
			foreach ($blogids as $blog_id) {
				switch_to_blog($blog_id);
				AIOWPSecurity_Installer::schedule_cron_events();
				do_action('aiowps_activation_complete');
				restore_current_blog();
			}
		} else {
			AIOWPSecurity_Installer::schedule_cron_events();
			do_action('aiowps_activation_complete');
		}
	}

	/**
	 * Helper function for scheduling AIOS cron events.
	 *
	 * @return Void
	 */
	public static function schedule_cron_events() {
		if (!wp_next_scheduled('aios_15_minutes_cron_event')) {
			wp_schedule_event(time(), 'aios-every-15-minutes', 'aios_15_minutes_cron_event'); //schedule a 15 minutes cron event
		}
		if (!wp_next_scheduled('aiowps_hourly_cron_event')) {
			wp_schedule_event(time(), 'hourly', 'aiowps_hourly_cron_event'); //schedule an hourly cron event
		}
		if (!wp_next_scheduled('aiowps_daily_cron_event')) {
			wp_schedule_event(time(), 'daily', 'aiowps_daily_cron_event'); //schedule an daily cron event
		}
	}
}
