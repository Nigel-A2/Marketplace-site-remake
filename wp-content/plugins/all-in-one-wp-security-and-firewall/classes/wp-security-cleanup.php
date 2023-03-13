<?php
if (!defined('ABSPATH')) {
	exit;//Exit if accessed directly
}

/**
 * AIOWPSecurity_Cleanup class for clean up database etc.
 *
 * @access public
 */
class AIOWPSecurity_Cleanup {
	
	/**
	 * Class constructor added action
	 */
	public function __construct() {
		add_action('aiowps_perform_db_cleanup_tasks', array($this, 'aiowps_scheduled_db_cleanup_handler'));
	}
	
	/**
	 * Clean up unnecessary old data from aiowps tables.
	 *
	 * @return void
	 */
	public function aiowps_scheduled_db_cleanup_handler() {
		//Check the events table because this can grow quite large especially when 404 events are being logged
		$events_table_name = AIOWPSEC_TBL_EVENTS;
		$purge_events_records_after_days = AIOS_PURGE_EVENTS_RECORDS_AFTER_DAYS; //purge older records in the events table
		$purge_events_records_after_days = apply_filters('aios_purge_events_records_after_days', $purge_events_records_after_days);
		AIOWPSecurity_Utility::purge_table_records($events_table_name, $purge_events_records_after_days, 'event_date');

		//Check the failed logins table
		// aiowps_perform_failed_login_cleanup_task already does it.
		
		//Check the login activity table
		$login_activity_table_name = AIOWPSEC_TBL_USER_LOGIN_ACTIVITY;
		$purge_login_activity_records_after_days = AIOS_PURGE_LOGIN_ACTIVITY_RECORDS_AFTER_DAYS; //purge older records in the login activity table
		$purge_login_activity_records_after_days = apply_filters('aios_purge_login_activity_records_after_days', $purge_login_activity_records_after_days);
		AIOWPSecurity_Utility::purge_table_records($login_activity_table_name, $purge_login_activity_records_after_days, 'login_date');

		//Check the global meta table
		$global_meta_table_name = AIOWPSEC_TBL_GLOBAL_META_DATA;
		$purge_global_meta_records_after_days = AIOS_PURGE_GLOBAL_META_DATA_RECORDS_AFTER_DAYS; //purge older records in global meta table
		$purge_global_meta_records_after_days = apply_filters('aios_purge_global_meta_records_after_days', $purge_global_meta_records_after_days);
		AIOWPSecurity_Utility::purge_table_records($global_meta_table_name, $purge_global_meta_records_after_days, 'date_time');

		//Delete any expired _aiowps_captcha_string_info_xxxx option
		AIOWPSecurity_Utility::delete_expired_captcha_options();
		//Keep adding other DB cleanup tasks as they arise...
	}
}
