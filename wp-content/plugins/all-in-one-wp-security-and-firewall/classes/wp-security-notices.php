<?php

if (!defined('AIO_WP_SECURITY_PATH')) die('No direct access allowed');

if (!class_exists('Updraft_Notices_1_2')) require_once(AIO_WP_SECURITY_PATH.'/vendor/team-updraft/common-libs/src/updraft-notices/updraft-notices.php');

class AIOWPSecurity_Notices extends Updraft_Notices_1_2 {

	private $initialized = false;

	protected $notices_content = array();
	
	// protected $self_affiliate_id = null;

	/**
	 * Returns array_merge of notices from parent and notices in $child_notice_content.
	 *
	 * @return Array
	 */
	protected function populate_notices_content() {
		global $aio_wp_security;
		$parent_notice_content = parent::populate_notices_content();

		$login_whitelist_notice_text = '<p>' .
		__('The All in One Security plugin has disabled the login whitelist setting that you have enabled in the past.', 'all-in-one-wp-security-and-firewall') .
		'</p>' .
		'<p>';
		if (AIOWPSecurity_Utility::is_apache_server()) {
			$login_whitelist_notice_text .= __('Your website is running on an Apache webserver, the login whitelisting might not be functional until the recent update of AIOS (because it relied upon Apache-specific module features).', 'all-in-one-wp-security-and-firewall');
		} else {
			$login_whitelist_notice_text .= __('Your website is running on a non-Apache webserver, so the login whitelisting was not functional until the recent update of AIOS (because it relied upon Apache-specific features).', 'all-in-one-wp-security-and-firewall');
		}
		$login_whitelist_notice_text .= ' ' . __('It began working with AIOS version 5.0.8.', 'all-in-one-wp-security-and-firewall') . '   ' . __('We have disabled it so that your login page will not be blocked unexpectedly.', 'all-in-one-wp-security-and-firewall') .
		'</p>';

		$allowed_ip_addresses = explode("\n", $aio_wp_security->configs->get_value('aiowps_allowed_ip_addresses'));
		$allowed_ip_addresses = array_map('trim', $allowed_ip_addresses);
		$login_whitelist_notice_text .= '<p>' .
			__('Whitelisted login IP address(es):') . ' ' . htmlspecialchars(implode(', ', $allowed_ip_addresses)) .
		'</p>' .
		'<p>' .
		__('Would you like to re-enable login whitelisting?', 'all-in-one-wp-security-and-firewall') .
		'</p>';

		$child_notice_content = array(
			// Upgrade AIOS backup to UDP backup in the 5.0.0 version
			'automated-database-backup' => array(
				'title'		  => htmlspecialchars(__('Removed database backup feature from the All In One WP Security & Firewall plugin', 'all-in-one-wp-security-and-firewall')),
				'text' 		  => '<p>' .
									__('Beginning with version 5.0.0, AIOS has replaced the AIOS backup method with the superior UpdraftPlus method.', 'all-in-one-wp-security-and-firewall') . '  '.
									__('It remains free and is fully supported by the UpdraftPlus team.', 'all-in-one-wp-security-and-firewall') .
								'</p>' .
								'<p>' .
									__('You are seeing this notice because you have previously set up automated database backups in AIOS.', 'all-in-one-wp-security-and-firewall') . '   ' .
									__('Would you like to set up scheduled backups with UpdraftPlus?', 'all-in-one-wp-security-and-firewall') .
								'</p>',
				'button_link' => add_query_arg(array(
					'page' => 'aiowpsec_database',
					'tab'  => 'tab2',
				), admin_url('admin.php')) . '#automated-scheduled-backups-heading',
				'button_meta' => __('Setup UpdraftPlus backup plugin', 'all-in-one-wp-security-and-firewall'),
				'dismiss_time' => 'dismiss_automated_database_backup_notice',
				'supported_positions' => array('automated-database-backup'),
				'validity_function' => 'should_show_automated_database_backup_notice',
			),
			'ip-retrieval-settings' => array(
				'title'		  => htmlspecialchars(__('Important: set up your IP address detection settings', 'all-in-one-wp-security-and-firewall')),
				'text' 		  => '<p>' .
					__("The All in One Security plugin couldn't be certain about the correct method to detect the IP address for your site visitors with your currently-configured IP address detection settings.", 'all-in-one-wp-security-and-firewall') . '  '.
					__('It is important for your security to set the IP address detection settings properly.', 'all-in-one-wp-security-and-firewall') .
					'</p>' .
					'<p>' .
					__('Please go to the settings and set them now.', 'all-in-one-wp-security-and-firewall') .
					'</p>',
				'button_link' => add_query_arg(array(
					'page' => 'aiowpsec_settings',
					'tab'  => 'advanced-settings',
				), admin_url('admin.php')) . '#automated-scheduled-backups-heading',
				'button_meta' => __('Setup IP address detection settings', 'all-in-one-wp-security-and-firewall'),
				'dismiss_time' => 'dismiss_ip_retrieval_settings_notice',
				'supported_positions' => array('ip-retrieval-settings'),
				'validity_function' => 'should_show_ip_retrieval_settings_notice',
			),
			'login-whitelist-disabled-on-upgrade' => array(
				'title'		  => htmlspecialchars(__('Important: Disabled login whitelist setting', 'all-in-one-wp-security-and-firewall')),
				'text' 		  => $login_whitelist_notice_text,
				'button_link' => add_query_arg(array(
					'page' => AIOWPSEC_BRUTE_FORCE_MENU_SLUG,
					'tab'  => 'tab4',
				), admin_url('admin.php')) . '#poststuff',
				'action_button_text' => __('Turn it back on', 'all-in-one-wp-security-and-firewall'),
				'button_meta' => __('Edit the settings', 'all-in-one-wp-security-and-firewall'),
				'dismiss_time' => 'dismiss_login_whitelist_disabled_on_upgrade_notice',
				'supported_positions' => array('login-whitelist-disabled-on-upgrade'),
				'dismiss_text' => __('Keep it off', 'all-in-one-wp-security-and-firewall'),
				'validity_function' => 'should_show_login_whitelist_disabled_on_upgrade_notice',
			),
			'rate_plugin' => array(
				'text' => sprintf(htmlspecialchars(__('Hey - We noticed All In One WP Security & Firewall has kept your site safe for a while.  If you like us, please consider leaving a positive review to spread the word.  Or if you have any issues or questions please leave us a support message %s.', 'all-in-one-wp-security-and-firewall')), '<a href="https://wordpress.org/support/plugin/all-in-one-wp-security-and-firewall/" target="_blank">'.__('here', 'all-in-one-wp-security-and-firewall').'</a>').'<br>'.__('Thank you so much!', 'all-in-one-wp-security-and-firewall').'<br><br>- <b>'.htmlspecialchars(__('Team All In One WP Security & Firewall', 'all-in-one-wp-security-and-firewall')).'</b>',
				'image' => 'plugin-logos/aiowps-logo.png',
				'button_link' => 'https://wordpress.org/support/plugin/all-in-one-wp-security-and-firewall/reviews/?rate=5#new-post',
				'button_meta' => 'review',
				'dismiss_time' => 'dismiss_review_notice',
				'supported_positions' => $this->dashboard_top,
				'validity_function' => 'show_rate_notice'
			),
			'updraftplus' => array(
				'prefix' => '',
				'title' => __('Enhance your security even more by backing up your site', 'all-in-one-wp-security-and-firewall'),
				'text' => htmlspecialchars(__('UpdraftPlus is the world\'s most trusted backup plugin from the owners of All In One WP Security & Firewall', 'all-in-one-wp-security-and-firewall')),
				'image' => 'plugin-logos/updraft_logo.png',
				'button_link' => 'https://wordpress.org/plugins/updraftplus/',
				'button_meta' => 'updraftplus',
				'dismiss_time' => 'dismiss_page_notice_until',
				'supported_positions' => $this->dashboard_top_or_report,
				'validity_function' => 'updraftplus_not_installed',
			),
			'wp-optimize' => array(
				'prefix' => '',
				'title' => 'WP-Optimize',
				'text' => __("After you've secured your site, we recommend you install our WP-Optimize plugin to streamline it for better website performance.", "all-in-one-wp-security-and-firewall"),
				'image' => 'plugin-logos/wp_optimize_logo.png',
				'button_link' => 'https://wordpress.org/plugins/wp-optimize/',
				'button_meta' => 'wp-optimize',
				'dismiss_time' => 'dismiss_notice',
				'supported_positions' => $this->anywhere,
				'validity_function' => 'wp_optimize_not_installed',
			),
		);

		return array_merge($parent_notice_content, $child_notice_content);
	}

	/**
	 * Decides whether to show the automated database backup notice.
	 *
	 * @return Boolean True if the automated database notice should be shown, otherwise false.
	 */
	protected function should_show_automated_database_backup_notice() {
		if ($this->is_database_backup_admin_page_tab()) {
			return false;
		}

		if (defined('AIOS_FORCE_AUTOMATED_DATABASE_BACKUP_NOTICE') && AIOS_FORCE_AUTOMATED_DATABASE_BACKUP_NOTICE) {
			return true;
		}

		if ($this->is_updraftplus_plugin_active() && $this->is_schedule_database_backup_set_in_updraftplus()) {
			return false;
		}

		global $aio_wp_security;
		if ('1' == $aio_wp_security->configs->get_value('aiowps_enable_automated_backups')) {
			return true;
		}

		return false;
	}

	/**
	 * Whether the current page is the AIOS database backup admin page
	 *
	 * @return Boolean True if the current page is the AIOS database backup admin page, otherwise false.
	 */
	private function is_database_backup_admin_page_tab() {
		return $this->is_database_security_admin_page() && $this->is_database_backup_tab();
	}

	/**
	 * Whether the current page is the database security admin page.
	 *
	 * @return Boolean True if the current page is the database security admin page, otherwise false.
	 */
	private function is_database_security_admin_page() {
		return ('admin.php' == $GLOBALS['pagenow'] && isset($_GET['page']) && 'aiowpsec_database' == $_GET['page']);
	}

	/**
	 * Whether the current tab is the database backup tab.
	 *
	 * @return Boolean True if the current tab is the database backup tab, otherwise false.
	 */
	private function is_database_backup_tab() {
		return (isset($_GET['tab']) && 'tab2' == $_GET['tab']);
	}

	/**
	 * Decides whether to show the IP address detection settings notice.
	 *
	 * @return Boolean True if the IP address detection settings notice should be shown, otherwise false.
	 */
	protected function should_show_ip_retrieval_settings_notice() {
		if (!is_main_site()) {
			return false;
		}

		if ($this->is_ip_settings_admin_page_tab()) {
			return false;
		}

		if (defined('AIOS_FORCE_IP_RETRIEVAL_SETTINGS_NOTICE') && AIOS_FORCE_IP_RETRIEVAL_SETTINGS_NOTICE) {
			return true;
		}

		global $aio_wp_security;

		// Is notice dismissed.
		if ('1' == $aio_wp_security->configs->get_value('dismiss_ip_retrieval_settings_notice')) {
			return false;
		}

		$configured_ip_method_id = $aio_wp_security->configs->get_value('aiowps_ip_retrieve_method');

		if (AIOWPSecurity_Utility_IP::is_server_suitable_ip_methods_give_same_ip_address()) {
			if ('' === $configured_ip_method_id) {
				$server_suitable_ip_methods = AIOWPSecurity_Utility_IP::get_server_suitable_ip_methods();
				$most_suitable_ip_method = reset($server_suitable_ip_methods);
				if (!empty($most_suitable_ip_method)) {
					$most_suitable_ip_method_id = array_search($most_suitable_ip_method, AIOS_Abstracted_Ids::get_ip_retrieve_methods());
					$aio_wp_security->configs->set_value('aiowps_ip_retrieve_method', $most_suitable_ip_method_id);
					$aio_wp_security->configs->save_config();//save the value
				}
			}

			return false;
		}

		// If the IP retrieval method is not set.
		$configured_ip_method_id = $aio_wp_security->configs->get_value('aiowps_ip_retrieve_method');
		if ('' === $configured_ip_method_id) {
			return true;
		}

		$server_user_ip_address = AIOWPSecurity_Utility_IP::get_server_detected_user_ip_address();
		return empty($server_user_ip_address);
	}

	/**
	 * Whether the current page is the AIOS IP retrieval admin page
	 *
	 * @return Boolean True if the current page is the AIOS database backup admin page, otherwise false.
	 */
	private function is_ip_settings_admin_page_tab() {
		return $this->is_settings_admin_page() && $this->is_advanced_settings_tab();
	}

	/**
	 * Whether the current page is the AIOS settings admin page
	 *
	 * @return Boolean True if the current page is the AIOS settings admin page, otherwise false.
	 */
	private function is_settings_admin_page() {
		return ('admin.php' == $GLOBALS['pagenow'] && isset($_GET['page']) && 'aiowpsec_settings' == $_GET['page']);
	}

	/**
	 * Whether the current tab is the advanced settings tab.
	 *
	 * @return Boolean True if the current tab is the advanced settings tab, otherwise false.
	 */
	private function is_advanced_settings_tab() {
		return (isset($_GET['tab']) && 'advanced-settings' == $_GET['tab']);
	}

	/**
	 * Check whether the UpdraftPlus plugin is active or not.
	 *
	 * @return bool True if the UpdraftPlus plugin is active, otherwise false.
	 */
	private function is_updraftplus_plugin_active() {
		return class_exists('UpdraftPlus');
	}

	/**
	 * Check whether the database backup scheduled in the UpdraftPlus plugin.
	 *
	 * @return bool
	 */
	private function is_schedule_database_backup_set_in_updraftplus() {
		$updraft_interval_database_option_val = get_option('updraft_interval_database', '');
		if (empty($updraft_interval_database_option_val) || 'manual' == $updraft_interval_database_option_val) {
			return false;
		}

		return true;
	}

	/**
	 * Decides whether to show the IP address detection settings notice.
	 *
	 * @return Boolean True if the IP address detection settings notice should be shown, otherwise false.
	 */
	protected function should_show_login_whitelist_disabled_on_upgrade_notice() {
		if (!is_main_site()) {
			return false;
		}

		if ($this->is_login_whitelist_admin_page_tab()) {
			return false;
		}

		if (defined('AIOS_FORCE_LOGIN_WHITELIST_DISABLED_ON_UPGRADE_NOTICE') && AIOS_FORCE_LOGIN_WHITELIST_DISABLED_ON_UPGRADE_NOTICE) {
			return true;
		}

		global $aio_wp_security;

		if ('1' == $aio_wp_security->configs->get_value('aiowps_is_login_whitelist_disabled_on_upgrade') && '1' != $aio_wp_security->configs->get_value('aiowps_enable_whitelisting')) {
			return true;
		}

		return false;
	}

	/**
	 * Whether the current page is the AIOS IP retrieval admin page
	 *
	 * @return Boolean True if the current page is the AIOS database backup admin page, otherwise false.
	 */
	private function is_login_whitelist_admin_page_tab() {
		return $this->is_brute_force_admin_page() && $this->is_login_whitelist_tab();
	}

	/**
	 * Whether the current page is the AIOS settings admin page
	 *
	 * @return Boolean True if the current page is the AIOS settings admin page, otherwise false.
	 */
	private function is_brute_force_admin_page() {
		return ('admin.php' == $GLOBALS['pagenow'] && isset($_GET['page']) && AIOWPSEC_BRUTE_FORCE_MENU_SLUG == $_GET['page']);
	}

	/**
	 * Whether the current tab is the advanced settings tab.
	 *
	 * @return Boolean True if the current tab is the advanced settings tab, otherwise false.
	 */
	private function is_login_whitelist_tab() {
		return (isset($_GET['tab']) && 'tab4' == $_GET['tab']);
	}

	/**
	 * Call this method to setup the notices
	 */
	public function notices_init() {
		if ($this->initialized) return;
		$this->initialized = true;
		$this->notices_content = $this->populate_notices_content();

		$enqueue_version = (defined('WP_DEBUG') && WP_DEBUG) ? AIO_WP_SECURITY_VERSION.'.'.time() : AIO_WP_SECURITY_VERSION;
		wp_enqueue_style('aiowpsec-admin-notices-css',  AIO_WP_SECURITY_URL.'/css/wp-security-notices.css', array(), $enqueue_version);
	}

	/**
	 * Get AIOS Plugin installation timestamp.
	 *
	 * @return integer AIOS Plugin installation timestamp.
	 */
	public function get_aiowps_plugin_installed_timestamp() {
		$installed_at = @filemtime(AIO_WP_SECURITY_PATH.'/index.html'); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged
		if (false === $installed_at) {
			global $aio_wp_security;
			$installed_at = (int) $aio_wp_security->configs->get_value('installed-at');
		}
		$installed_at = apply_filters('aiowps_plugin_installed_timestamp', $installed_at);
		return $installed_at;
	}

	/**
	 * This function will check if we should display the rate notice or not
	 *
	 * @return boolean - to indicate if we should show the notice or not
	 */
	protected function show_rate_notice() {
		$installed_at = $this->get_aiowps_plugin_installed_timestamp();
		$time_now = $this->get_time_now();
		$installed_for = $time_now - $installed_at;
		
		if ($installed_at && $installed_for > 28*86400) {
			return true;
		}

		return false;
	}

	/**
	 * Checks if UpdraftPlus is installed(returns false) or not(returns true).
	 *
	 * @return Boolean
	 */
	protected function updraftplus_not_installed() {
		if (!function_exists('get_plugins')) include_once(ABSPATH.'wp-admin/includes/plugin.php');
		$plugins = get_plugins();

		foreach ($plugins as $value) {
			if ('updraftplus' == $value['TextDomain']) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Checks if WP-Optimize is installed(returns false) or not(returns true).
	 *
	 * @return Boolean
	 */
	protected function wp_optimize_not_installed() {
		if (!function_exists('get_plugins')) include_once(ABSPATH.'wp-admin/includes/plugin.php');
		$plugins = get_plugins();

		foreach ($plugins as $value) {
			if ('wp-optimize' == $value['TextDomain']) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Determines whether to prepare a seasonal notice(returns true) or not(returns false).
	 *
	 * @param Array $notice_data - all data for the notice
	 *
	 * @return Boolean
	 */
	protected function skip_seasonal_notices($notice_data) {
		$time_now = $this->get_time_now();
		$valid_from = strtotime($notice_data['valid_from']);
		$valid_to = strtotime($notice_data['valid_to']);
		$dismiss = $this->check_notice_dismissed($notice_data['dismiss_time']);
		if (($time_now >= $valid_from && $time_now <= $valid_to) && !$dismiss) {
			// return true so that we return this notice to be displayed
			return true;
		}
		
		return false;
	}

	/**
	 * Get timestamp that is considered as current timestamp for notice.
	 *
	 * @return integer timestamp that should be consider as a current time.
	 */
	public function get_time_now() {
		$time_now = defined('AIOWPSECURITY_NOTICES_FORCE_TIME') ? AIOWPSECURITY_NOTICES_FORCE_TIME : time();
		return $time_now;
	}

	/**
	 * Checks whether a notice is dismissed(returns true) or not(returns false).
	 *
	 * @param String $dismiss_time - dismiss time id for the notice
	 *
	 * @return boolean
	 */
	protected function check_notice_dismissed($dismiss_time) {
		$time_now = $this->get_time_now();

		global $aio_wp_security;
		$dismiss = ($time_now < (int) $aio_wp_security->configs->get_value($dismiss_time));

		return $dismiss;
	}

	/**
	 * Renders or returns a notice.
	 *
	 * @param Boolean|String $advert_information     - all data for the notice
	 * @param Boolean        $return_instead_of_echo - whether to return the notice(true) or render it to the page(false)
	 * @param String         $position               - notice position
	 *
	 * @return Void|String
	 */
	protected function render_specified_notice($advert_information, $return_instead_of_echo = false, $position = 'top') {

		if ('bottom' == $position) {
			$template_file = 'bottom-notice.php';
		} elseif ('report' == $position) {
			$template_file = 'report.php';
		} elseif ('report-plain' == $position) {
			$template_file = 'report-plain.php';
		} elseif (in_array($position, AIOS_Abstracted_Ids::custom_admin_notice_ids())) {
			$template_file = 'custom-notice.php';
		} elseif (in_array($position, AIOS_Abstracted_Ids::htaccess_to_php_feature_notice_ids())) {
			$template_file = 'htaccess-to-php-feature-notice.php';
		} else {
			$template_file = 'horizontal-notice.php';
		}

		global $aio_wp_security;
		return $aio_wp_security->include_template('notices/'.$template_file, $return_instead_of_echo, $advert_information);
	}
}
