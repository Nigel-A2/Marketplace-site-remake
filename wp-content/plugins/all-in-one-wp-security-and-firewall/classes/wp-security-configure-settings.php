<?php
if (!defined('ABSPATH')) {
	exit;//Exit if accessed directly
}

class AIOWPSecurity_Configure_Settings {

	/**
	 * Set default settings.
	 *
	 * @return boolean True if the settings options was updated, false otherwise.
	 */
	public static function set_default_settings() {

		global $aio_wp_security;

		$blog_email_address = get_bloginfo('admin_email'); // Get the blog admin email address - we will use as the default value

		//Debug
		$aio_wp_security->configs->set_value('aiowps_enable_debug', '');//Checkbox

		//PHP backtrace
		$aio_wp_security->configs->set_value('aiowps_enable_php_backtrace_in_email', '');//Checkbox

		//WP Generator Meta Tag feature
		$aio_wp_security->configs->set_value('aiowps_remove_wp_generator_meta_info', '');//Checkbox

		//Prevent Image Hotlinks
		$aio_wp_security->configs->set_value('aiowps_prevent_hotlinking', '');//Checkbox
		//General Settings Page

		//User password feature

		//Lockdown feature
		$aio_wp_security->configs->set_value('aiowps_enable_login_lockdown', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_allow_unlock_requests', '1'); // Checkbox
		$aio_wp_security->configs->set_value('aiowps_max_login_attempts', '3');
		$aio_wp_security->configs->set_value('aiowps_retry_time_period', '5');
		$aio_wp_security->configs->set_value('aiowps_lockout_time_length', '5');
		$aio_wp_security->configs->set_value('aiowps_max_lockout_time_length', '60');
		$aio_wp_security->configs->set_value('aiowps_set_generic_login_msg', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_email_notify', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_email_address', $blog_email_address);//text field
		$aio_wp_security->configs->set_value('aiowps_enable_forced_logout', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_logout_time_period', '60');
		$aio_wp_security->configs->set_value('aiowps_enable_invalid_username_lockdown', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_instantly_lockout_specific_usernames', array()); // Textarea (list of strings)
		$aio_wp_security->configs->set_value('aiowps_unlock_request_secret_key', AIOWPSecurity_Utility::generate_alpha_numeric_random_string(20));//Hidden secret value which will be used to do some unlock request processing. This will be assigned a random string generated when lockdown settings saved
		$aio_wp_security->configs->set_value('aiowps_lockdown_enable_whitelisting', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_lockdown_allowed_ip_addresses', '');

		// CAPTCHA feature
		$aio_wp_security->configs->set_value('aiowps_enable_login_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_custom_login_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_woo_login_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_woo_lostpassword_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_woo_register_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_lost_password_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_captcha_secret_key', AIOWPSecurity_Utility::generate_alpha_numeric_random_string(20)); // Hidden secret value which will be used to do some CAPTCHA processing. This will be assigned a random string generated when CAPTCHA settings saved

		//Login Whitelist feature
		$aio_wp_security->configs->set_value('aiowps_enable_whitelisting', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_allowed_ip_addresses', '');

		//User registration
		$aio_wp_security->configs->set_value('aiowps_enable_manual_registration_approval', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_registration_page_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_registration_honeypot', '');//Checkbox

		//DB Security feature
		//$aio_wp_security->configs->set_value('aiowps_new_manual_db_pefix', ''); //text field
		$aio_wp_security->configs->set_value('aiowps_enable_random_prefix', '');//Checkbox

		//Filesystem Security feature
		AIOWPSecurity_Utility::enable_file_edits();
		$aio_wp_security->configs->set_value('aiowps_disable_file_editing', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_prevent_default_wp_file_access', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_system_log_file', 'error_log');

		//Blacklist feature
		$aio_wp_security->configs->set_value('aiowps_enable_blacklisting', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_banned_ip_addresses', '');
		$aio_wp_security->configs->set_value('aiowps_banned_user_agents', '');

		//Firewall features
		$aio_wp_security->configs->set_value('aiowps_enable_basic_firewall', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_max_file_upload_size', AIOS_FIREWALL_MAX_FILE_UPLOAD_LIMIT_MB); //Default
		$aio_wp_security->configs->set_value('aiowps_enable_pingback_firewall', '');//Checkbox - blocks all access to XMLRPC
		$aio_wp_security->configs->set_value('aiowps_disable_xmlrpc_pingback_methods', '');//Checkbox - Disables only pingback methods in XMLRPC functionality
		$aio_wp_security->configs->set_value('aiowps_block_debug_log_file_access', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_disable_index_views', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_disable_trace_and_track', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_forbid_proxy_comments', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_deny_bad_query_strings', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_advanced_char_string_filter', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_5g_firewall', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_6g_firewall', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_block_fake_googlebots', ''); // Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_custom_rules', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_place_custom_rules_at_top', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_custom_rules', '');

		//404 detection
		$aio_wp_security->configs->set_value('aiowps_enable_404_logging', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_404_IP_lockout', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_404_lockout_time_length', '60');
		$aio_wp_security->configs->set_value('aiowps_404_lock_redirect_url', 'http://127.0.0.1');

		//Brute Force features
		$aio_wp_security->configs->set_value('aiowps_enable_rename_login_page', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_login_honeypot', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_disable_application_password', '');//Checkbox

		$aio_wp_security->configs->set_value('aiowps_enable_brute_force_attack_prevention', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_brute_force_secret_word', '');
		$aio_wp_security->configs->set_value('aiowps_cookie_brute_test', '');
		$aio_wp_security->configs->set_value('aiowps_cookie_based_brute_force_redirect_url', 'http://127.0.0.1');
		$aio_wp_security->configs->set_value('aiowps_brute_force_attack_prevention_pw_protected_exception', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_brute_force_attack_prevention_ajax_exception', '');//Checkbox

		//Maintenance menu - Visitor lockout feature
		$aio_wp_security->configs->set_value('aiowps_site_lockout', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_site_lockout_msg', '');//Text area/msg box

		// Spam prevention menu
		$aio_wp_security->configs->set_value('aiowps_enable_spambot_blocking', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_comment_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_autoblock_spam_ip', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_spam_ip_min_comments_block', '');
		$aio_wp_security->configs->set_value('aiowps_enable_bp_register_captcha', '');
		$aio_wp_security->configs->set_value('aiowps_enable_bbp_new_topic_captcha', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_trash_spam_comments', '');
		$aio_wp_security->configs->set_value('aiowps_trash_spam_comments_after_days', '14');

		//Filescan features
		//File change detection feature
		$aio_wp_security->configs->set_value('aiowps_enable_automated_fcd_scan', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_fcd_scan_frequency', '4');
		$aio_wp_security->configs->set_value('aiowps_fcd_scan_interval', '2'); //Dropdown box where (0,1,2) => (hours,days,weeks)
		$aio_wp_security->configs->set_value('aiowps_fcd_exclude_filetypes', '');
		$aio_wp_security->configs->set_value('aiowps_fcd_exclude_files', '');
		$aio_wp_security->configs->set_value('aiowps_send_fcd_scan_email', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_fcd_scan_email_address', $blog_email_address);
		$aio_wp_security->configs->set_value('aiowps_fcds_change_detected', false); //used to display a global alert on site when file change detected

		//Misc Options
		//Copy protection feature
		$aio_wp_security->configs->set_value('aiowps_copy_protection', '');//Checkbox
		//Prevent others from dislaying your site in iframe
		$aio_wp_security->configs->set_value('aiowps_prevent_site_display_inside_frame', '');//Checkbox
		//Prevent users enumeration
		$aio_wp_security->configs->set_value('aiowps_prevent_users_enumeration', '');//Checkbox

		//REST API Security
		$aio_wp_security->configs->set_value('aiowps_disallow_unauthorized_rest_requests', '');//Checkbox

		// IP retrieval setting
		$aio_wp_security->configs->set_value('aiowps_ip_retrieve_method', '0'); // Default is $_SERVER['REMOTE_ADDR']

		// Google reCAPTCHA
		$aio_wp_security->configs->set_value('aiowps_recaptcha_site_key', '');
		$aio_wp_security->configs->set_value('aiowps_recaptcha_secret_key', '');
		$aio_wp_security->configs->set_value('aiowps_default_recaptcha', '');//Checkbox

		// Deactivation Handler
		$aio_wp_security->configs->set_value('aiowps_on_uninstall_delete_db_tables', '1'); //Checkbox
		$aio_wp_security->configs->set_value('aiowps_on_uninstall_delete_configs', '1'); //Checkbox

		//TODO - keep adding default options for any fields that require it

		self::turn_off_all_6g_firewall_configs();

		// Save it
		return $aio_wp_security->configs->save_config();
	}

	/**
	 * Add config settings.
	 *
	 * @return Void
	 */
	public static function add_option_values() {
		global $aio_wp_security;
		$blog_email_address = get_bloginfo('admin_email'); //Get the blog admin email address - we will use as the default value

		//Debug
		$aio_wp_security->configs->add_value('aiowps_enable_debug', '');//Checkbox

		//PHP backtrace
		$aio_wp_security->configs->add_value('aiowps_enable_php_backtrace_in_email', '');//Checkbox

		//WP Generator Meta Tag feature
		$aio_wp_security->configs->add_value('aiowps_remove_wp_generator_meta_info', '');//Checkbox

		//Prevent Image Hotlinks
		$aio_wp_security->configs->add_value('aiowps_prevent_hotlinking', '');//Checkbox

		//General Settings Page

		//User password feature

		//Lockdown feature
		$aio_wp_security->configs->add_value('aiowps_enable_login_lockdown', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_allow_unlock_requests', '1'); // Checkbox
		$aio_wp_security->configs->add_value('aiowps_max_login_attempts', '3');
		$aio_wp_security->configs->add_value('aiowps_retry_time_period', '5');
		$aio_wp_security->configs->add_value('aiowps_lockout_time_length', '5');
		$aio_wp_security->configs->add_value('aiowps_max_lockout_time_length', '60');
		$aio_wp_security->configs->add_value('aiowps_set_generic_login_msg', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_email_notify', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_email_address', $blog_email_address);//text field
		$aio_wp_security->configs->add_value('aiowps_enable_forced_logout', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_logout_time_period', '60');
		$aio_wp_security->configs->add_value('aiowps_enable_invalid_username_lockdown', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_instantly_lockout_specific_usernames', array()); // Textarea (list of strings)
		$aio_wp_security->configs->add_value('aiowps_unlock_request_secret_key', AIOWPSecurity_Utility::generate_alpha_numeric_random_string(20));//Hidden secret value which will be used to do some unlock request processing. This will be assigned a random string generated when lockdown settings saved
		$aio_wp_security->configs->add_value('aiowps_lockdown_enable_whitelisting', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_lockdown_allowed_ip_addresses', '');

		//Login Whitelist feature
		$aio_wp_security->configs->add_value('aiowps_enable_whitelisting', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_allowed_ip_addresses', '');
		// CAPTCHA feature
		$aio_wp_security->configs->add_value('aiowps_enable_login_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_custom_login_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_woo_login_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_woo_register_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_woo_lostpassword_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_captcha_secret_key', AIOWPSecurity_Utility::generate_alpha_numeric_random_string(20)); // Hidden secret value which will be used to do some CAPTCHA processing. This will be assigned a random string generated when CAPTCHA settings saved

		//User registration
		$aio_wp_security->configs->add_value('aiowps_enable_manual_registration_approval', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_registration_page_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_registration_honeypot', ''); // Checkbox

		//DB Security feature
		//$aio_wp_security->configs->add_value('aiowps_new_manual_db_pefix', ''); //text field
		$aio_wp_security->configs->add_value('aiowps_enable_random_prefix', '');//Checkbox

		//Filesystem Security feature
		$aio_wp_security->configs->add_value('aiowps_disable_file_editing', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_prevent_default_wp_file_access', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_system_log_file', 'error_log');


		//Blacklist feature
		$aio_wp_security->configs->add_value('aiowps_enable_blacklisting', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_banned_ip_addresses', '');

		//Firewall features
		$aio_wp_security->configs->add_value('aiowps_enable_basic_firewall', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_max_file_upload_size', AIOS_FIREWALL_MAX_FILE_UPLOAD_LIMIT_MB);
		$aio_wp_security->configs->add_value('aiowps_enable_pingback_firewall', '');//Checkbox - blocks all access to XMLRPC
		$aio_wp_security->configs->add_value('aiowps_disable_xmlrpc_pingback_methods', '');//Checkbox - Disables only pingback methods in XMLRPC functionality
		$aio_wp_security->configs->add_value('aiowps_block_debug_log_file_access', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_disable_index_views', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_disable_trace_and_track', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_forbid_proxy_comments', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_deny_bad_query_strings', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_advanced_char_string_filter', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_5g_firewall', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_6g_firewall', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_custom_rules', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_place_custom_rules_at_top', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_custom_rules', '');

		//404 detection
		$aio_wp_security->configs->add_value('aiowps_enable_404_logging', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_404_IP_lockout', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_404_lockout_time_length', '60');
		$aio_wp_security->configs->add_value('aiowps_404_lock_redirect_url', 'http://127.0.0.1');

		//Brute Force features
		$aio_wp_security->configs->add_value('aiowps_enable_rename_login_page', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_login_honeypot', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_disable_application_password', ''); // Checkbox

		$aio_wp_security->configs->add_value('aiowps_enable_brute_force_attack_prevention', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_brute_force_secret_word', '');
		$aio_wp_security->configs->add_value('aiowps_cookie_brute_test', '');
		$aio_wp_security->configs->add_value('aiowps_cookie_based_brute_force_redirect_url', 'http://127.0.0.1');
		$aio_wp_security->configs->add_value('aiowps_brute_force_attack_prevention_pw_protected_exception', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_brute_force_attack_prevention_ajax_exception', '');//Checkbox

		//Maintenance menu - Visitor lockout feature
		$aio_wp_security->configs->add_value('aiowps_site_lockout', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_site_lockout_msg', '');//Text area/msg box

		// Spam prevention menu
		$aio_wp_security->configs->add_value('aiowps_enable_spambot_blocking', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_comment_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_autoblock_spam_ip', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_spam_ip_min_comments_block', '');
		$aio_wp_security->configs->add_value('aiowps_enable_bp_register_captcha', '');
		$aio_wp_security->configs->add_value('aiowps_enable_bbp_new_topic_captcha', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_enable_trash_spam_comments', '');
		$aio_wp_security->configs->add_value('aiowps_trash_spam_comments_after_days', '14');


		//Filescan features
		//File change detection feature
		$aio_wp_security->configs->add_value('aiowps_enable_automated_fcd_scan', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_fcd_scan_frequency', '4');
		$aio_wp_security->configs->add_value('aiowps_fcd_scan_interval', '2'); //Dropdown box where (0,1,2) => (hours,days,weeks)
		$aio_wp_security->configs->add_value('aiowps_fcd_exclude_filetypes', '');
		$aio_wp_security->configs->add_value('aiowps_fcd_exclude_files', '');
		$aio_wp_security->configs->add_value('aiowps_send_fcd_scan_email', '');//Checkbox
		$aio_wp_security->configs->add_value('aiowps_fcd_scan_email_address', $blog_email_address);
		$aio_wp_security->configs->add_value('aiowps_fcds_change_detected', false); //used to display a global alert on site when file change detected

		//Misc Options
		//Copy protection feature
		$aio_wp_security->configs->add_value('aiowps_copy_protection', '');//Checkbox
		//Prevent others from dislaying your site in iframe
		$aio_wp_security->configs->add_value('aiowps_prevent_site_display_inside_frame', '');//Checkbox
		//Prevent users enumeration
		$aio_wp_security->configs->add_value('aiowps_prevent_users_enumeration', '');//Checkbox

	   //REST API Security
		$aio_wp_security->configs->add_value('aiowps_disallow_unauthorized_rest_requests', '');//Checkbox

		// IP retrieval setting
		// Commented the below code line because the IP retrieve method will be configured when the AIOS plugin is activated for the first time.
		// $aio_wp_security->configs->add_value('aiowps_ip_retrieve_method', '0'); // Default is $_SERVER['REMOTE_ADDR']

		// Google reCAPTCHA
		$aio_wp_security->configs->add_value('aiowps_recaptcha_site_key', '');
		$aio_wp_security->configs->add_value('aiowps_recaptcha_secret_key', '');
		$aio_wp_security->configs->add_value('aiowps_default_recaptcha', '');//Checkbox

		// Deactivation Handler
		$aio_wp_security->configs->add_value('aiowps_on_uninstall_delete_db_tables', '1'); //Checkbox
		$aio_wp_security->configs->add_value('aiowps_on_uninstall_delete_configs', '1'); //Checkbox

		$aio_wp_security->configs->add_value('installed-at', current_time('timestamp', true));

		//TODO - keep adding default options for any fields that require it

		//Save it
		$aio_wp_security->configs->save_config();

		// For Cookie based brute force prevention backward compatibility
		if ($aio_wp_security->should_cookie_based_brute_force_prvent()) {
			$brute_force_secret_word = $aio_wp_security->configs->get_value('aiowps_brute_force_secret_word');
			if (empty($brute_force_secret_word)) {
				$brute_force_secret_word = AIOS_DEFAULT_BRUTE_FORCE_FEATURE_SECRET_WORD;
			}
			AIOWPSecurity_Utility::set_cookie_value(AIOWPSecurity_Utility::get_brute_force_secret_cookie_name(), wp_hash($brute_force_secret_word));
		}

		// Login whitelisting started to work on non-apache server from db_version 1.9.5
		if (is_main_site() && version_compare(get_option('aiowpsec_db_version'), '1.9.6', '<') && '1' == $aio_wp_security->configs->get_value('aiowps_enable_whitelisting') && !empty($aio_wp_security->configs->get_value('aiowps_allowed_ip_addresses'))) {
			$aio_wp_security->configs->set_value('aiowps_enable_whitelisting', '0');
			$aio_wp_security->configs->set_value('aiowps_is_login_whitelist_disabled_on_upgrade', '1');
			$aio_wp_security->configs->save_config();
		}

		update_option('aiowpsec_db_version', AIO_WP_SECURITY_DB_VERSION);
	}

	/**
	 * Turn off all security features.
	 *
	 * @return void.
	 */
	public static function turn_off_all_security_features() {
		global $aio_wp_security;
		AIOWPSecurity_Configure_Settings::set_default_settings();

		//Refresh the .htaccess file based on the new settings
		$res = AIOWPSecurity_Utility_Htaccess::write_to_htaccess();
		if (!$res) {
			$aio_wp_security->debug_logger->log_debug(__METHOD__ . " - Could not write to the .htaccess file. Please check the file permissions.", 4);
		}
	}
	
	/**
	 * Turn off 6g firewall configs.
	 *
	 * @return void.
	 */
	public static function turn_off_all_6g_firewall_configs() {
		global $aiowps_firewall_config;
		$aiowps_firewall_config->set_value('aiowps_6g_block_request_methods', array());
		$aiowps_firewall_config->set_value('aiowps_6g_block_query', false);
		$aiowps_firewall_config->set_value('aiowps_6g_block_request', false);
		$aiowps_firewall_config->set_value('aiowps_6g_block_referrers', false);
		$aiowps_firewall_config->set_value('aiowps_6g_block_agents', false);
	}
	
	/**
	 * Turn off all firewall rules.
	 *
	 * @return void.
	 */
	public static function turn_off_all_firewall_rules() {
		global $aio_wp_security;
		$aio_wp_security->configs->set_value('aiowps_enable_blacklisting', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_whitelisting', '');//Checkbox

		$aio_wp_security->configs->set_value('aiowps_enable_basic_firewall', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_pingback_firewall', '');//Checkbox - blocks all access to XMLRPC
		$aio_wp_security->configs->set_value('aiowps_disable_xmlrpc_pingback_methods', '');//Checkbox - Disables only pingback methods in XMLRPC functionality
		$aio_wp_security->configs->set_value('aiowps_block_debug_log_file_access', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_disable_index_views', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_disable_trace_and_track', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_forbid_proxy_comments', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_deny_bad_query_strings', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_advanced_char_string_filter', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_5g_firewall', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_6g_firewall', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_brute_force_attack_prevention', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_custom_rules', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_place_custom_rules_at_top', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_custom_rules', '');

		$aio_wp_security->configs->set_value('aiowps_prevent_default_wp_file_access', '');//Checkbox

		$aio_wp_security->configs->set_value('aiowps_enable_spambot_blocking', '');//Checkbox

		//404 detection
		$aio_wp_security->configs->set_value('aiowps_enable_404_logging', '');//Checkbox
		$aio_wp_security->configs->set_value('aiowps_enable_404_IP_lockout', '');//Checkbox

		//Prevent Image Hotlinks
		$aio_wp_security->configs->set_value('aiowps_prevent_hotlinking', '');//Checkbox

		$aio_wp_security->configs->save_config();

		self::turn_off_all_6g_firewall_configs();

		// Refresh the .htaccess file based on the new settings
		$res = AIOWPSecurity_Utility_Htaccess::write_to_htaccess();

		if (!$res) {
			$aio_wp_security->debug_logger->log_debug(__METHOD__ . " - Could not write to the .htaccess file. Please check the file permissions.", 4);
		}
	}

}
