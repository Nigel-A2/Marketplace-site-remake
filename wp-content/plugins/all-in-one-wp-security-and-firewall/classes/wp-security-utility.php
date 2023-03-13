<?php
if (!defined('ABSPATH')) {
	exit;//Exit if accessed directly
}

class AIOWPSecurity_Utility {

	/**
	 * Returned when we can't detect the user's server software
	 *
	 * @var int
	 */
	const UNSUPPORTED_SERVER_TYPE = -1;

	/**
	 * Class constructor
	 */
	public function __construct() {
		//NOP
	}

	/**
	 * Check whether the current logged in user has the capability to manage the AIOWPS plugin
	 *
	 * @return Boolean True if the logged in user has capability to manage the AIOWPS plugin, otherwise false
	 */
	public static function has_manage_cap() {
		// This filter will useful when the administrator would like to give permission to access AIOWPS to Security Analyst.
		$cap = apply_filters('aiowps_management_capability', AIOWPSEC_MANAGEMENT_PERMISSION);
		return current_user_can($cap);
	}

	/**
	 * Explode $string with $delimiter, trim all lines and filter out empty ones.
	 *
	 * @param string $string
	 * @param string $delimiter
	 * @return array
	 */
	public static function explode_trim_filter_empty($string, $delimiter = PHP_EOL) {
		return array_filter(array_map('trim', explode($delimiter, $string)), 'strlen');
	}

	/**
	 * Returns the current URL
	 *
	 * @return string
	 */
	public static function get_current_page_url() {
		$pageURL = 'http';
		if (isset($_SERVER["HTTPS"]) && "on" == $_SERVER["HTTPS"]) {
			$pageURL .= "s";
		}
		$pageURL .= "://";
		if ("80" != $_SERVER["SERVER_PORT"]) {
			$pageURL .= $_SERVER["SERVER_NAME"] . ":" . $_SERVER["SERVER_PORT"] . $_SERVER["REQUEST_URI"];
		} else {
			$pageURL .= $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
		}
		return $pageURL;
	}

	/**
	 * Redirects to specified URL
	 *
	 * @param type $url
	 * @param type $delay
	 * @param type $exit
	 */
	public static function redirect_to_url($url, $delay = '0', $exit = '1') {
		if (empty($url)) {
			echo "<br /><strong>Error! The URL value is empty. Please specify a correct URL value to redirect to!</strong>";
			exit;
		}
		if (!headers_sent()) {
			header('Location: ' . $url);
		} else {
			echo '<meta http-equiv="refresh" content="' . $delay . ';url=' . $url . '" />';
		}
		if ('1' == $exit) {
			exit;
		}
	}

	/**
	 * Returns logout URL with "after logout URL" query params
	 *
	 * @param type $after_logout_url
	 * @return type
	 */
	public static function get_logout_url_with_after_logout_url_value($after_logout_url) {
		return AIOWPSEC_WP_URL . '?aiowpsec_do_log_out=1&after_logout=' . $after_logout_url;
	}

	/**
	 * Checks if a particular username exists in the WP Users table
	 *
	 * @global type $wpdb
	 * @param type $username
	 * @return boolean
	 */
	public static function check_user_exists($username) {
		global $wpdb;

		//if username is empty just return false
		if ('' == $username) {
			return false;
		}

		//If multisite
		if (is_multisite()) {
			$blog_id = get_current_blog_id();
			$admin_users = get_users('blog_id=' . $blog_id . '&orderby=login&role=administrator');
			foreach ($admin_users as $user) {
				if ($user->user_login == $username) {
					return true;
				}
			}
			return false;
		}

		//check users table
		$sanitized_username = sanitize_text_field($username);
		$sql_1 = $wpdb->prepare("SELECT user_login FROM $wpdb->users WHERE user_login=%s", $sanitized_username);
		$user_login = $wpdb->get_var($sql_1);
		if ($user_login == $sanitized_username) {
			return true;
		} else {
			//make sure that the sanitized username is an integer before comparing it to the users table's ID column
			$sanitized_username_is_an_integer = (1 === preg_match('/^\d+$/', $sanitized_username));
			if ($sanitized_username_is_an_integer) {
				$sql_2 = $wpdb->prepare("SELECT ID FROM $wpdb->users WHERE ID=%d", intval($sanitized_username));
				$userid = $wpdb->get_var($sql_2);
				return ($userid == $sanitized_username);
			} else {
				return false;
			}
		}
	}

	/**
	 * This function will return a list of user accounts which have login and nick names which are identical
	 *
	 * @global type $wpdb
	 * @return type
	 */
	public static function check_identical_login_and_nick_names() {
		global $wpdb;
		$accounts_found = $wpdb->get_results("SELECT ID,user_login FROM `" . $wpdb->users . "` WHERE user_login<=>display_name;", ARRAY_A);
		return $accounts_found;
	}


	public static function add_query_data_to_url($url, $name, $value) {
		if (strpos($url, '?') === false) {
			$url .= '?';
		} else {
			$url .= '&';
		}
		$url .= $name . '=' . urlencode($value);
		return $url;
	}


	/**
	 * Generates a random alpha-numeric number
	 *
	 * @param type $string_length
	 * @return string
	 */
	public static function generate_alpha_numeric_random_string($string_length) {
		//Charecters present in table prefix
		$allowed_chars = 'abcdefghijklmnopqrstuvwxyz0123456789';
		$string = '';
		//Generate random string
		for ($i = 0; $i < $string_length; $i++) {
			$string .= $allowed_chars[rand(0, strlen($allowed_chars) - 1)];
		}
		return $string;
	}


	/**
	 * Generates a random string using a-z characters
	 *
	 * @param type $string_length
	 * @return string
	 */
	public static function generate_alpha_random_string($string_length) {
		//Charecters present in table prefix
		$allowed_chars = 'abcdefghijklmnopqrstuvwxyz';
		$string = '';
		//Generate random string
		for ($i = 0; $i < $string_length; $i++) {
			$string .= $allowed_chars[rand(0, strlen($allowed_chars) - 1)];
		}
		return $string;
	}

	/**
	 * Sets cookie
	 *
	 * @param type   $cookie_name
	 * @param type   $cookie_value
	 * @param type   $expiry_seconds
	 * @param type   $path
	 * @param string $cookie_domain
	 */
	public static function set_cookie_value($cookie_name, $cookie_value, $expiry_seconds = 86400, $path = '/', $cookie_domain = '') {
		$expiry_time = time() + intval($expiry_seconds);
		if (empty($cookie_domain)) {
			$cookie_domain = COOKIE_DOMAIN;
		}
		setcookie($cookie_name, $cookie_value, $expiry_time, $path, $cookie_domain, is_ssl(), true);
	}

	/**
	 * Get brute force secret cookie name.
	 *
	 * @return String Brute force secret cookie name.
	 */
	public static function get_brute_force_secret_cookie_name() {
		return 'aios_brute_force_secret_' . COOKIEHASH;
	}
	
	/**
	 * Gets cookie
	 *
	 * @param type $cookie_name
	 * @return string
	 */
	public static function get_cookie_value($cookie_name) {
		if (isset($_COOKIE[$cookie_name])) {
			return $_COOKIE[$cookie_name];
		}
		return "";
	}

	/**
	 * Checks if installation is multisite or not.
	 *
	 * @return Boolean True if the site is network multisite, false otherwise.
	 */
	public static function is_multisite_install() {
		return function_exists('is_multisite') && is_multisite();
	}

	/**
	 * This is a general yellow box message for when we want to suppress a feature's config items because site is subsite of multi-site
	 */
	public static function display_multisite_message() {
		echo '<div class="aio_yellow_box">';
		echo '<p>' . __('The plugin has detected that you are using a Multi-Site WordPress installation.', 'all-in-one-wp-security-and-firewall') . '</p>
			  <p>' . __('This feature can only be configured by the "superadmin" on the main site.', 'all-in-one-wp-security-and-firewall') . '</p>';
		echo '</div>';
	}

	/**
	 * Modifies the wp-config.php file to disable PHP file editing from the admin panel
	 * This function will add the following code:
	 * define('DISALLOW_FILE_EDIT', false);
	 *
	 * NOTE: This function will firstly check if the above code already exists
	 * and it will modify the bool value, otherwise it will insert the code mentioned above
	 *
	 * @global type $aio_wp_security
	 * @return boolean
	 */
	public static function disable_file_edits() {
		global $aio_wp_security;
		$edit_file_config_entry_exists = false;

		//Config file path
		$config_file = AIOWPSecurity_Utility_File::get_wp_config_file_path();

		//Get wp-config.php file contents so we can check if the "DISALLOW_FILE_EDIT" variable already exists
		$config_contents = file($config_file);

		foreach ($config_contents as $line_num => $line) {
			if (strpos($line, "'DISALLOW_FILE_EDIT', false")) {
				$config_contents[$line_num] = str_replace('false', 'true', $line);
				$edit_file_config_entry_exists = true;
				//$this->show_msg_updated(__('Settings Saved - The ability to edit PHP files via the admin the panel has been DISABLED.', 'all-in-one-wp-security-and-firewall'));
			} elseif (strpos($line, "'DISALLOW_FILE_EDIT', true")) {
				$edit_file_config_entry_exists = true;
				//$this->show_msg_updated(__('Your system config file is already configured to disallow PHP file editing.', 'all-in-one-wp-security-and-firewall'));
				return true;

			}

			//For wp-config.php files originating from early WP versions we will remove the closing php tag
			if (strpos($line, "?>") !== false) {
				$config_contents[$line_num] = str_replace("?>", "", $line);
			}
		}

		if (!$edit_file_config_entry_exists) {
			//Construct the config code which we will insert into wp-config.php
			$new_snippet = '//Disable File Edits' . PHP_EOL;
			$new_snippet .= 'define(\'DISALLOW_FILE_EDIT\', true);';
			$config_contents[] = $new_snippet; //Append the new snippet to the end of the array
		}

		//Make a backup of the config file
		if (!AIOWPSecurity_Utility_File::backup_and_rename_wp_config($config_file)) {
			AIOWPSecurity_Admin_Menu::show_msg_error_st(__('Failed to make a backup of the wp-config.php file. This operation will not go ahead.', 'all-in-one-wp-security-and-firewall'));
			//$aio_wp_security->debug_logger->log_debug("Disable PHP File Edit - Failed to make a backup of the wp-config.php file.",4);
			return false;
		} else {
			//$this->show_msg_updated(__('A backup copy of your wp-config.php file was created successfully....', 'all-in-one-wp-security-and-firewall'));
		}

		//Now let's modify the wp-config.php file
		if (AIOWPSecurity_Utility_File::write_content_to_file($config_file, $config_contents)) {
			//$this->show_msg_updated(__('Settings Saved - Your system is now configured to not allow PHP file editing.', 'all-in-one-wp-security-and-firewall'));
			return true;
		} else {
			//$this->show_msg_error(__('Operation failed! Unable to modify wp-config.php file!', 'all-in-one-wp-security-and-firewall'));
			$aio_wp_security->debug_logger->log_debug("Disable PHP File Edit - Unable to modify wp-config.php", 4);
			return false;
		}
	}

	/**
	 * Modifies the wp-config.php file to allow PHP file editing from the admin panel
	 * This func will modify the following code by replacing "true" with "false":
	 * define('DISALLOW_FILE_EDIT', true);
	 *
	 * @global type $aio_wp_security
	 * @return boolean
	 */
	public static function enable_file_edits() {
		$edit_file_config_entry_exists = false;

		//Config file path
		$config_file = AIOWPSecurity_Utility_File::get_wp_config_file_path();

		//Get wp-config.php file contents
		$config_contents = file($config_file);
		foreach ($config_contents as $line_num => $line) {
			if (strpos($line, "'DISALLOW_FILE_EDIT', true")) {
				$config_contents[$line_num] = str_replace('true', 'false', $line);
				$edit_file_config_entry_exists = true;
			} elseif (strpos($line, "'DISALLOW_FILE_EDIT', false")) {
				$edit_file_config_entry_exists = true;
				//$this->show_msg_updated(__('Your system config file is already configured to allow PHP file editing.', 'all-in-one-wp-security-and-firewall'));
				return true;
			}
		}

		if (!$edit_file_config_entry_exists) {
			//if the DISALLOW_FILE_EDIT settings don't exist in wp-config.php then we don't need to do anything
			//$this->show_msg_updated(__('Your system config file is already configured to allow PHP file editing.', 'all-in-one-wp-security-and-firewall'));
			return true;
		} else {
			//Now let's modify the wp-config.php file
			if (AIOWPSecurity_Utility_File::write_content_to_file($config_file, $config_contents)) {
				//$this->show_msg_updated(__('Settings Saved - Your system is now configured to allow PHP file editing.', 'all-in-one-wp-security-and-firewall'));
				return true;
			} else {
				//$this->show_msg_error(__('Operation failed! Unable to modify wp-config.php file!', 'all-in-one-wp-security-and-firewall'));
				//$aio_wp_security->debug_logger->log_debug("Disable PHP File Edit - Unable to modify wp-config.php",4);
				return false;
			}
		}
	}


	/**
	 * Inserts event logs to the database
	 * For now we are using for 404 events but in future will expand for other events
	 * Event types: 404 (...add more as we expand this)
	 *
	 * @param string $event_type :Event type, eg, 404 (see below for list of event types)
	 * @param string $username   (optional): username
	 * @return bool
	 */
	public static function event_logger($event_type, $username = '') {
		global $wpdb, $aio_wp_security;

		//Some initialising
		$url = '';
		$referer_info = '';

		$events_table_name = AIOWPSEC_TBL_EVENTS;

		$ip_or_host = AIOWPSecurity_Utility_IP::get_user_ip_address(); //Get the IP address of user
		$username = sanitize_user($username);
		$user = get_user_by('login', $username); //Returns WP_User object if exists
		if ($user) {
			//If valid user set variables for DB storage later on
			$user_id = (absint($user->ID) > 0) ? $user->ID : 0;
		} else {
			//If the login attempt was made using a non-existent user then let's set user_id to blank and record the attempted user login name for DB storage later on
			$user_id = 0;
		}

		if ('404' == $event_type) {
			//if 404 event get some relevant data
			$url = isset($_SERVER['REQUEST_URI']) ? esc_attr($_SERVER['REQUEST_URI']) : '';
			$referer_info = isset($_SERVER['HTTP_REFERER']) ? esc_attr($_SERVER['HTTP_REFERER']) : '';
		}

		$current_time = current_time('mysql', true);
		$data = array(
			'event_type' => $event_type,
			'username' => $username,
			'user_id' => $user_id,
			'event_date' => $current_time,
			'ip_or_host' => $ip_or_host,
			'referer_info' => $referer_info,
			'url' => $url,
			'event_data' => '',
		);

		$data = apply_filters('aiowps_filter_event_logger_data', $data);
		//log to database
		$result = $wpdb->insert($events_table_name, $data);
		if (false === $result) {
			$aio_wp_security->debug_logger->log_debug("event_logger: Error inserting record into " . $events_table_name, 4);//Log the highly unlikely event of DB error
			return false;
		}
		return true;
	}

	/**
	 * Checks if IP address is locked
	 *
	 * @param string $ip : ip address
	 * @returns true if locked, false otherwise
	 **/
	public static function check_locked_ip($ip) {
		global $wpdb;
		$login_lockdown_table = AIOWPSEC_TBL_LOGIN_LOCKDOWN;
		$now = current_time('mysql', true);
		$locked_ip = $wpdb->get_row($wpdb->prepare("SELECT * FROM $login_lockdown_table WHERE release_date > %s AND failed_login_ip = %s", $now, $ip), ARRAY_A);
		if (null != $locked_ip) {
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Returns list of IP addresses locked out
	 *
	 * @global type $wpdb
	 * @return array of addresses found or false otherwise
	 */
	public static function get_locked_ips() {
		global $wpdb;
		$login_lockdown_table = AIOWPSEC_TBL_LOGIN_LOCKDOWN;
		$now = current_time('mysql', true);
	$locked_ips = $wpdb->get_results($wpdb->prepare("SELECT * FROM $login_lockdown_table WHERE release_date > %s", $now), ARRAY_A);
		
		if (empty($locked_ips)) {
			return false;
		} else {
			return $locked_ips;
		}
	}


	/**
	 * Locks an IP address - Adds an entry to the AIOWPSEC_TBL_LOGIN_LOCKDOWN table.
	 *
	 * @global wpdb            $wpdb
	 * @global AIO_WP_Security $aio_wp_security
	 *
	 * @param String $ip
	 * @param String $lock_reason
	 * @param String $username
	 *
	 * @return Void
	 */
	public static function lock_IP($ip, $lock_reason, $username = '') {
		global $wpdb, $aio_wp_security;
		$login_lockdown_table = AIOWPSEC_TBL_LOGIN_LOCKDOWN;

		if ('404' == $lock_reason) {
			$lock_minutes = $aio_wp_security->configs->get_value('aiowps_404_lockout_time_length');
		} else {
			$lock_minutes = $aio_wp_security->user_login_obj->get_dynamic_lockout_time_length();
		}

		$username = sanitize_user($username);
		$user = get_user_by('login', $username); //Returns WP_User object if exists

		if (false == $user) {
			// Not logged in.
			$username = '';
			$user_id = 0;
		} else {
			// Logged in.
			$username = sanitize_user($user->user_login);
			$user_id = $user->ID;
		}

		$ip = esc_sql($ip);

		$lock_time = current_time('mysql', true);
		$release_time = date('Y-m-d H:i:s', time() + ($lock_minutes * MINUTE_IN_SECONDS));

		$data = array('user_id' => $user_id, 'user_login' => $username, 'lockdown_date' => $lock_time, 'release_date' => $release_time, 'failed_login_IP' => $ip, 'lock_reason' => $lock_reason);
		$format = array('%d', '%s', '%s', '%s', '%s', '%s');
		$result = $wpdb->insert($login_lockdown_table, $data, $format);

		if ($result > 0) {
		} elseif (false === $result) {
			$aio_wp_security->debug_logger->log_debug("lock_IP: Error inserting record into " . $login_lockdown_table, 4);//Log the highly unlikely event of DB error
		}
	}

	/**
	 * Returns an array of blog_ids for a multisite install
	 *
	 * @global type $wpdb
	 * @global type $wpdb
	 * @return array or empty array if not multisite
	 */
	public static function get_blog_ids() {
		global $wpdb;
		if (is_multisite()) {
			global $wpdb;
			$blog_ids = $wpdb->get_col("SELECT blog_id FROM " . $wpdb->prefix . "blogs");
		} else {
			$blog_ids = array();
		}
		return $blog_ids;
	}

	/**
	 * Purges old records of table
	 *
	 * @global type $wpdb            WP Database object
	 * @global type $aio_wp_security AIO WP Security object
	 * @param type $table_name               Table name
	 * @param type $purge_records_after_days Records after days to be deleted
	 * @param type $date_field               Date field of table
	 * @return void
	 */
	public static function purge_table_records($table_name, $purge_records_after_days, $date_field) {
		global $wpdb, $aio_wp_security;

		$older_than_date_time = date('Y-m-d H:m:s', strtotime('-' . $purge_records_after_days . ' days', current_time('timestamp', true)));
		$sql = $wpdb->prepare('DELETE FROM ' . $table_name . ' WHERE '.$date_field.' < %s', $older_than_date_time);
		$ret_deleted = $wpdb->query($sql);
		if (false === $ret_deleted) {
			$err_db = !empty($wpdb->last_error) ? ' ('.$wpdb->last_error.' - '.$wpdb->last_query.')' : '';
			// Status level 4 indicates failure status.
			$aio_wp_security->debug_logger->log_debug_cron('Purge records error - failed to purge older records for ' . $table_name . '.' . $err_db, 4);
		} else {
			$aio_wp_security->debug_logger->log_debug_cron(sprintf('Purge records - %d records were deleted for ' . $table_name . '.', $ret_deleted));
		}
	}

	/**
	 * This function will delete the oldest rows from a table which are over the max amount of rows specified
	 *
	 * @global type $wpdb            WP Database object
	 * @global type $aio_wp_security AIO WP Security object
	 * @param type $table_name Table name
	 * @param type $max_rows   More than max to be deleted
	 * @param type $id_field   Primary field of table
	 * @return bool
	 */
	public static function cleanup_table($table_name, $max_rows = '10000', $id_field = 'id') {
		global $wpdb, $aio_wp_security;

		$num_rows = $wpdb->get_var("select count(*) from $table_name");
		$result = true;
		if ($num_rows > $max_rows) {
			//if the table has more than max entries delete oldest rows
			
			$del_sql = "DELETE FROM $table_name
						WHERE ".$id_field." <= (
						  SELECT ".$id_field."
						  FROM (
							SELECT ".$id_field." 
							FROM $table_name
							ORDER BY ".$id_field." DESC
							LIMIT 1 OFFSET $max_rows
						 ) foo_tmp
						)";

			$result = $wpdb->query($del_sql);
			if (false === $result) {
				$aio_wp_security->debug_logger->log_debug("AIOWPSecurity_Utility::cleanup_table failed for table name: " . $table_name, 4);
			}
		}
		return (false === $result) ? false : true;
	}
	
	/**
	 * Delete expired CAPTCHA info option
	 *
	 * Note: A unique instance these option is created everytime the login page is loaded with CAPTCHA enabled
	 * This function will help prune the options table of old expired entries.
	 *
	 * @global wpdb $wpdb
	 */
	public static function delete_expired_captcha_options() {
		global $wpdb;
		$current_unix_time = current_time('timestamp', true);
		$previous_hour = $current_unix_time - 3600;
		$tbl = is_multisite() ? $wpdb->sitemeta : $wpdb->prefix . 'options';
		$key_name = is_multisite() ? 'meta_key' : 'option_name';
		$key_val = is_multisite() ? 'meta_value' : 'option_value';
		$query = $wpdb->prepare("SELECT * FROM {$tbl} WHERE {$key_name} LIKE 'aiowps_captcha_string_info_time_%' AND {$key_val} < %s", $previous_hour);
		$res = $wpdb->get_results($query, ARRAY_A);
		if (!empty($res)) {
			foreach ($res as $item) {
				$option_name = $item[$key_name];
				if (is_multisite()) {
					delete_site_option($option_name);
					delete_site_option(str_replace('time_', '', $option_name));
				} else {
					delete_option($option_name);
					delete_option(str_replace('time_', '', $option_name));
				}
			}
		}
	}

	/**
	 * Get server type.
	 *
	 * @return string|integer Server type or -1 if server is not supported
	 */
	public static function get_server_type() {
		if (!isset($_SERVER['SERVER_SOFTWARE'])) {
			return -1;
		}

		// Figure out what server they're using.
		$server_software = strtolower(sanitize_text_field(wp_unslash(($_SERVER['SERVER_SOFTWARE']))));

		if (strstr($server_software, 'apache')) {
			return 'apache';
		} elseif (strstr($server_software, 'nginx')) {
			return 'nginx';
		} elseif (strstr($server_software, 'litespeed')) {
			return 'litespeed';
		} elseif (strstr($server_software, 'iis')) {
			return 'iis';
		} else { // Unsupported server
			return -1;
		}
	}

	/**
	 * Checks if the string exists in the array key value of the provided array.
	 * If it doesn't exist, it returns the first key element from the valid values.
	 *
	 * @param type $to_check
	 * @param type $valid_values
	 * @return type
	 */
	public static function sanitize_value_by_array($to_check, $valid_values) {
		$keys = array_keys($valid_values);
		$keys = array_map('strtolower', $keys);
		if (in_array(strtolower($to_check), $keys)) {
			return $to_check;
		}
		return reset($keys); //Return the first element from the valid values
	}

	/**
	 * Get textarea string from array or string.
	 *
	 * @param String|Array $vals value to render as textarea val
	 * @return String value to render in textarea.
	 */
	public static function get_textarea_str_val($vals) {
		if (empty($vals)) {
			return '';
		}

		if (is_array($vals)) {
			return implode("\n", array_filter(array_map('trim', $vals)));
		}

		return $vals;
	}

	/**
	 * Get array from textarea val.
	 *
	 * @param String|Array $vals value from textarea val
	 * @return Array value to from textarea value.
	 */
	public static function get_array_from_textarea_val($vals) {
		if (empty($vals)) {
			return array();
		}

		if (is_array($vals)) {
			return $vals;
		}

		return array_filter(array_map('trim', explode("\n", $vals)));
	}
	
	/**
	 * Partially or fully masks a string using '*' to replace original characters
	 *
	 * @param type string $str
	 * @param type int    $chars_unmasked
	 * @return type string
	 */
	public static function mask_string($str, $chars_unmasked = 0) {
	$str_length = strlen($str);
		$chars_unmasked = absint($chars_unmasked);

		if (0 == $chars_unmasked) {
			if (8 < $str_length) {
				// mask all but last 4 characters
				return preg_replace("/(.{4}$)(*SKIP)(*F)|(.)/u", "*", $str);
			} elseif (3 < $str_length) {
				// mask all but last 2 characters
				return preg_replace("/(.{2}$)(*SKIP)(*F)|(.)/u", "*", $str);
			} else {
				// return whole string masked
				return str_pad("", $str_length, "*", STR_PAD_LEFT);
			}
		}
		if ($chars_unmasked >= $str_length) return $str;
		return preg_replace("/(.{".$chars_unmasked."}$)(*SKIP)(*F)|(.)/u", "*", $str);
	}
	
	/**
	 * Create a php backtrace log file for login lockdown email
	 *
	 * @param Array $logs
	 * @global AIO_WP_Security $aio_wp_security
	 * @return string
	 */
	public static function login_lockdown_email_backtrace_log_file($logs = array()) {
		global $aio_wp_security;
		$temp_dir = get_temp_dir();
		$backtrace_filename = wp_unique_filename($temp_dir, 'log_backtrace_' . time() . '.txt');
		$backtrace_filepath = $temp_dir.$backtrace_filename;
		if (count($logs) > 0) {
			$dbg = "";
			foreach ($logs as $log) {
				$dbg.= "############ BACKTRACE STARTS  ########\n";
				$dbg.= $log['backtrace_log'];
				$dbg.= "############ BACKTRACE ENDS  ########\n\n";
			}
		} else {
			$dbg = debug_backtrace();
		}
		$is_log_file_written = file_put_contents($backtrace_filepath, print_r($dbg, true));
		if ($is_log_file_written) {
			return $backtrace_filepath;
		} else {
			$aio_wp_security->debug_logger->log_debug("Error in writing php backtrace file " . $backtrace_filepath . " to attach in email.", 4);
			return '';
		}
	}

	/**
	 * Check whether the WooCommerce plugin is active.
	 *
	 * @return Boolean True if the WooCommerce plugin is active, otherwise false.
	 */
	public static function is_woocommerce_plugin_active() {
		return class_exists('WooCommerce');
	}

	/**
	 * Check whether incompatible TFA premium plugin version active.
	 *
	 * @return boolean True if the incompatible TFA premium plugin version active, otherwise false.
	 */
	public static function is_incompatible_tfa_premium_version_active() {
		if (!function_exists('get_plugins')) {
			require_once(ABSPATH.'/wp-admin/includes/plugin.php');
		}
		foreach (get_plugins() as $plugin_slug => $plugin_info) {
			if (is_plugin_active($plugin_slug) && strpos($plugin_slug, '/') && is_dir(WP_PLUGIN_DIR.'/'.explode('/', $plugin_slug)[0].'/simba-tfa/premium') && version_compare($plugin_info['Version'], AIOS_TFA_PREMIUM_LATEST_INCOMPATIBLE_VERSION, '<=')) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Check whether TFA plugin activating.
	 *
	 * @return boolean True if the TFA plugin activating, otherwise false.
	 */
	public static function is_tfa_or_self_plugin_activating() {
		// The $GLOBALS['pagenow'] doesn't set in the network admin plugins page and it throws the warning "Notice: Undefined index: pagenow in ..." so we can't use it.
		// https://core.trac.wordpress.org/ticket/42656
		return is_admin() &&
			preg_match('#/wp-admin/plugins.php$#i', $_SERVER['PHP_SELF']) && isset($_GET['plugin']) && (preg_match("/\/two-factor-login.php/", $_GET['plugin']) || preg_match("/all-in-one-wp-security-and-firewall/", $_GET['plugin']));
	}

	/**
	 * Check whether the site is running on localhost or not.
	 *
	 * @return Boolean True if the site is on localhost, otherwise false.
	 */
	public static function is_localhost() {
		if (defined('AIOS_IS_LOCALHOST')) {
			return AIOS_IS_LOCALHOST;
		}

		if (empty($_SERVER['REMOTE_ADDR'])) {
			return false;
		}
		return in_array($_SERVER['REMOTE_ADDR'], array('127.0.0.1', '::1')) ? true : false;
	}

	/**
	 * Get server software.
	 *
	 * @return string Server software or empty.
	 */
	public static function get_server_software() {
		static $server_software;
		if (!isset($server_software)) {
			$server_software = (isset($_SERVER['SERVER_SOFTWARE']) ? $_SERVER['SERVER_SOFTWARE'] : '');
		}
		return $server_software;
	}

	/**
	 * Check whether the server is apache or not.
	 *
	 * @return Boolean True the server is apache, otherwise false.
	 */
	public static function is_apache_server() {
		return (false !== strpos(self::get_server_software(), 'Apache'));
	}
}
