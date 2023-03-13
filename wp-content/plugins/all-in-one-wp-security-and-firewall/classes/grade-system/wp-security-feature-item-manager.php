<?php

class AIOWPSecurity_Feature_Item_Manager {

	public $feature_items;

	public $total_points = 0;

	public $total_achievable_points = 0;

	public $feature_point_1 = "5";

	public $feature_point_2 = "10";

	public $feature_point_3 = "15";

	public $feature_point_4 = "20";

	public $sec_level_basic = "1";

	public $sec_level_inter = "2";

	public $sec_level_advanced = "3";

	public $feature_active = "active";

	public $feature_inactive = "inactive";

	public $feature_partial = "partial";

	public function __construct() {

	}

	public function initialize_features() {
		$this->feature_items = array();
		// Settings menu features
		// WP version info
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('wp-generator-meta-tag', __('Remove WP generator meta tag', 'all-in-one-wp-security-and-firewall'), $this->feature_point_1, $this->sec_level_basic);

		// User Accounts menu features
		// WP username
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('user-accounts-change-admin-user', __('Change admin username', 'all-in-one-wp-security-and-firewall'), $this->feature_point_3, $this->sec_level_basic);
		// Display name
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('user-accounts-display-name', __('Change display name', 'all-in-one-wp-security-and-firewall'), $this->feature_point_1, $this->sec_level_basic);

		// User Login menu features
		// Login lockdown
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('user-login-login-lockdown', __('Login lockdown', 'all-in-one-wp-security-and-firewall'), $this->feature_point_4, $this->sec_level_basic);
		// Force logout
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('user-login-force-logout', __('Force logout', 'all-in-one-wp-security-and-firewall'), $this->feature_point_1, $this->sec_level_basic);
		// Additional settings
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('disable-application-password', __('Disable application password', 'all-in-one-wp-security-and-firewall'), $this->feature_point_2, $this->sec_level_inter);

		// User Registration menu features
		// Manual approval
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('manually-approve-registrations', __('Registration approval', 'all-in-one-wp-security-and-firewall'), $this->feature_point_4, $this->sec_level_basic);
		// Registration CAPTCHA
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('user-registration-captcha', __('Registration CAPTCHA', 'all-in-one-wp-security-and-firewall'), $this->feature_point_4, $this->sec_level_basic);
		// Registration honeypot
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('registration-honeypot', __('Enable registration honeypot', 'all-in-one-wp-security-and-firewall'), $this->feature_point_2, $this->sec_level_inter);

		// Database Security menu features
		// Database prefix
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('db-security-db-prefix', __('Database prefix', 'all-in-one-wp-security-and-firewall'), $this->feature_point_2, $this->sec_level_inter);

		// Filesystem Security menu features
		// File permissions
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('filesystem-file-permissions', __('File permissions', 'all-in-one-wp-security-and-firewall'), $this->feature_point_4, $this->sec_level_basic);
		// PHP file editing
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('filesystem-file-editing', __('File editing', 'all-in-one-wp-security-and-firewall'), $this->feature_point_2, $this->sec_level_basic);
		// WP file access
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('block-wp-files-access', __('WordPress files access', 'all-in-one-wp-security-and-firewall'), $this->feature_point_2, $this->sec_level_basic);

		// Blacklist Manager menu features
		// Ban users
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('blacklist-manager-ip-user-agent-blacklisting', __('IP and user agent blacklisting', 'all-in-one-wp-security-and-firewall'), $this->feature_point_3, $this->sec_level_advanced);

		// Firewall menu features
		// Basic firewall rules
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('firewall-basic-rules', __('Enable basic firewall', 'all-in-one-wp-security-and-firewall'), $this->feature_point_3, $this->sec_level_basic);
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('firewall-pingback-rules', __('Enable pingback vulnerability protection', 'all-in-one-wp-security-and-firewall'), $this->feature_point_3, $this->sec_level_basic);
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('firewall-block-debug-file-access', __('Block accesss to debug log file', 'all-in-one-wp-security-and-firewall'), $this->feature_point_2, $this->sec_level_inter);
		// Additional firewall rules
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('firewall-disable-index-views', __('Disable index views', 'all-in-one-wp-security-and-firewall'), $this->feature_point_1, $this->sec_level_inter);
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('firewall-disable-trace-track', __('Disable trace and track', 'all-in-one-wp-security-and-firewall'), $this->feature_point_2, $this->sec_level_advanced);
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('firewall-forbid-proxy-comments', __('Forbid proxy comments', 'all-in-one-wp-security-and-firewall'), $this->feature_point_2, $this->sec_level_advanced);
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('firewall-deny-bad-queries', __('Deny bad queries', 'all-in-one-wp-security-and-firewall'), $this->feature_point_3, $this->sec_level_advanced);
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('firewall-advanced-character-string-filter', __('Advanced character string filter', 'all-in-one-wp-security-and-firewall'), $this->feature_point_3, $this->sec_level_advanced);
		// 6G blacklist firewall rules
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('firewall-enable-5g-6g-blacklist', __('5G/6G blacklist', 'all-in-one-wp-security-and-firewall'), $this->feature_point_4, $this->sec_level_advanced);
		// Internet bots
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('firewall-block-fake-googlebots', __('Block fake Googlebots', 'all-in-one-wp-security-and-firewall'), $this->feature_point_1, $this->sec_level_advanced);
		// Prevent hotlinks
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('prevent-hotlinking', __('Prevent image hotlinking', 'all-in-one-wp-security-and-firewall'), $this->feature_point_2, $this->sec_level_basic);
		// 404 detection
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('firewall-enable-404-blocking', __('Enable IP blocking for 404 detection', 'all-in-one-wp-security-and-firewall'), $this->feature_point_1, $this->sec_level_inter);

		// Brute Force menu features
		// Rename login page
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('bf-rename-login-page', __('Enable rename login page', 'all-in-one-wp-security-and-firewall'), $this->feature_point_2, $this->sec_level_inter);
		// Cookie based brute force prevention
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('firewall-enable-brute-force-attack-prevention', __('Enable brute force attack prevention', 'all-in-one-wp-security-and-firewall'), $this->feature_point_4, $this->sec_level_advanced);
		// Login CAPTCHA
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('user-login-captcha', __('Login CAPTCHA', 'all-in-one-wp-security-and-firewall'), $this->feature_point_4, $this->sec_level_basic);
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('lost-password-captcha', __('Lost password CAPTCHA', 'all-in-one-wp-security-and-firewall'), $this->feature_point_2, $this->sec_level_basic);
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('custom-login-captcha', __('Custom login CAPTCHA', 'all-in-one-wp-security-and-firewall'), $this->feature_point_4, $this->sec_level_basic);
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('woo-login-captcha', __('Woo login CAPTCHA', 'all-in-one-wp-security-and-firewall'), $this->feature_point_2, $this->sec_level_basic);
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('woo-lostpassword-captcha', __('Woo lost password CAPTCHA', 'all-in-one-wp-security-and-firewall'), $this->feature_point_2, $this->sec_level_basic);
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('woo-register-captcha', __('Woo register CAPTCHA', 'all-in-one-wp-security-and-firewall'), $this->feature_point_2, $this->sec_level_basic);
		// Login whitelist
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('whitelist-manager-ip-login-whitelisting', __('Login IP whitelisting', 'all-in-one-wp-security-and-firewall'), $this->feature_point_3, $this->sec_level_inter);
		// Honeypot
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('login-honeypot', __('Enable login honeypot', 'all-in-one-wp-security-and-firewall'), $this->feature_point_2, $this->sec_level_inter);

		// Spam Prevention menu features
		// Comment spam
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('comment-form-captcha', __('Comment CAPTCHA', 'all-in-one-wp-security-and-firewall'), $this->feature_point_4, $this->sec_level_basic);
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('block-spambots', __('Block spambots', 'all-in-one-wp-security-and-firewall'), $this->feature_point_2, $this->sec_level_basic);
		// BuddyPress
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('bp-register-captcha', __('BuddyPress registration CAPTCHA', 'all-in-one-wp-security-and-firewall'), $this->feature_point_1, $this->sec_level_basic);
		// bbPress
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('bbp-new-topic-captcha', __('bbPress new topic CAPTCHA', 'all-in-one-wp-security-and-firewall'), $this->feature_point_1, $this->sec_level_basic);

		// Scanner menu features
		// File change detection
		$this->feature_items[] = new AIOWPSecurity_Feature_Item('scan-file-change-detection', __('File change detection', 'all-in-one-wp-security-and-firewall'), $this->feature_point_4, $this->sec_level_inter);
	}

	public function get_feature_item_by_id($feature_id) {
		foreach ($this->feature_items as $item) {
			if ($item->feature_id == $feature_id) {
				return $item;
			}
		}
		return "";
	}

	public function output_feature_details_badge($feature_id) {
		$cau_feature_item = $this->get_feature_item_by_id($feature_id);
		$cau_security_level = $cau_feature_item->security_level;
		$cau_security_points = $cau_feature_item->item_points;
		$cau_your_points = 0;
		if ($cau_feature_item->feature_status == $this->feature_active) {
			$cau_your_points = $cau_security_points;
		}
		$level_str = $cau_feature_item->get_security_level_string($cau_security_level);
		?>
		<div class="aiowps_feature_details_badge">
				<div class="aiowps_feature_details_badge_difficulty" title="Feature Difficulty">
					<span class="aiowps_feature_details_badge_difficulty_text"><?php _e($level_str, 'all-in-one-wp-security-and-firewall'); ?></span>
				</div>
				<div class="aiowps_feature_details_badge_points" title="Security Points">
					<span class="aiowps_feature_details_badge_points_text"><?php echo $cau_your_points .'/'. $cau_security_points; ?></span>
				</div>
		</div>
		<?php
	}

	public function check_feature_status_and_recalculate_points() {
		$this->check_and_set_feature_status();
		$this->calculate_total_points();
	}

	public function check_and_set_feature_status() {
		foreach ($this->feature_items as $item) {
			if ("wp-generator-meta-tag" == $item->feature_id) {
				$this->check_remove_wp_generator_meta_feature($item);
			}

			if ("prevent-hotlinking" == $item->feature_id) {
				$this->check_prevent_hotlinking_feature($item);
			}

			if ("user-accounts-change-admin-user" == $item->feature_id) {
				$this->check_user_accounts_change_admin_user_feature($item);
			}

			if ("user-accounts-display-name" == $item->feature_id) {
				$this->check_user_accounts_display_name_feature($item);
			}

			if ("db-security-db-prefix" == $item->feature_id) {
				$this->check_db_security_db_prefix_feature($item);
			}

			if ("db-security-db-backup" == $item->feature_id) {
				$this->check_db_security_db_backup_feature($item);
			}

			if ("user-login-login-lockdown" == $item->feature_id) {
				$this->check_login_lockdown_feature($item);
			}

			if ("user-login-captcha" == $item->feature_id) {
				$this->check_login_captcha_feature($item);
			}

			if ("custom-login-captcha" == $item->feature_id) {
				$this->check_custom_login_captcha_feature($item);
			}

			if ("woo-login-captcha" == $item->feature_id) {
				$this->check_woo_login_captcha_feature($item);
			}

			if ("woo-lostpassword-captcha" == $item->feature_id) {
				$this->check_woo_lostpassword_captcha_feature($item);
			}

			if ("woo-register-captcha" == $item->feature_id) {
				$this->check_woo_register_captcha_feature($item);
			}

			if ("lost-password-captcha" == $item->feature_id) {
				$this->check_lost_password_captcha_feature($item);
			}

			if ("comment-form-captcha" == $item->feature_id) {
				$this->check_comment_captcha_feature($item);
			}

			if ("bp-register-captcha" == $item->feature_id) {
				$this->check_bp_register_captcha_feature($item);
			}

			if ("bbp-new-topic-captcha" == $item->feature_id) {
				$this->check_bbp_new_topic_captcha_feature($item);
			}

			if ("whitelist-manager-ip-login-whitelisting" == $item->feature_id) {
				$this->check_login_whitelist_feature($item);
			}

			if ("user-login-force-logout" == $item->feature_id) {
				$this->check_force_logout_feature($item);
			}

			if ("manually-approve-registrations" == $item->feature_id) {
				$this->check_registration_approval_feature($item);
			}

			if ("user-registration-captcha" == $item->feature_id) {
				$this->check_registration_captcha_feature($item);
			}

			if ("registration-honeypot" == $item->feature_id) {
				$this->check_enable_registration_honeypot_feature($item);
			}

			if ("filesystem-file-permissions" == $item->feature_id) {
				$this->check_filesystem_permissions_feature($item);
			}

			if ("filesystem-file-editing" == $item->feature_id) {
				$this->check_filesystem_file_editing_feature($item);
			}

			if ("block-wp-files-access" == $item->feature_id) {
				$this->check_block_wp_files_access_feature($item);
			}

			if ("blacklist-manager-ip-user-agent-blacklisting" == $item->feature_id) {
				$this->check_enable_ip_useragent_blacklist_feature($item);
			}

			if ("firewall-basic-rules" == $item->feature_id) {
				$this->check_enable_basic_firewall_feature($item);
			}

			if ("firewall-pingback-rules" == $item->feature_id) {
				$this->check_enable_pingback_firewall_feature($item);
			}

			if ("firewall-block-debug-file-access" == $item->feature_id) {
				$this->check_debug_file_access_block_firewall_feature($item);
			}

			if ("firewall-enable-404-blocking" == $item->feature_id) {
				$this->check_enable_404_blocking_feature($item);
			}

			if ("firewall-enable-brute-force-attack-prevention" == $item->feature_id) {
				$this->check_enable_bfap_firewall_feature($item);
			}

			if ("firewall-disable-index-views" == $item->feature_id) {
				$this->check_disable_index_views_firewall_feature($item);
			}

			if ("firewall-disable-trace-track" == $item->feature_id) {
				$this->check_disable_trace_track_firewall_feature($item);
			}

			if ("firewall-forbid-proxy-comments" == $item->feature_id) {
				$this->check_forbid_proxy_comments_firewall_feature($item);
			}

			if ("firewall-deny-bad-queries" == $item->feature_id) {
				$this->check_deny_bad_queries_firewall_feature($item);
			}

			if ("firewall-advanced-character-string-filter" == $item->feature_id) {
				$this->check_advanced_char_string_filter_firewall_feature($item);
			}

			if ("firewall-enable-5g-6g-blacklist" == $item->feature_id) {
				$this->check_enable_5G_6G_blacklist_firewall_feature($item);
			}

			if ("firewall-block-fake-googlebots" == $item->feature_id) {
				$this->check_block_fake_googlebots_firewall_feature($item);
			}

			if ("bf-rename-login-page" == $item->feature_id) {
				$this->check_enable_rename_login_page_feature($item);
			}

			if ("login-honeypot" == $item->feature_id) {
				$this->check_enable_login_honeypot_feature($item);
			}

			if ("disable-application-password" == $item->feature_id) {
				$this->check_disable_application_password_feature($item);
			}

			if ("block-spambots" == $item->feature_id) {
				$this->check_enable_block_spambots_feature($item);
			}

			if ("scan-file-change-detection" == $item->feature_id) {
				$this->check_enable_fcd_scan_feature($item);
			}
		}
	}

	public function calculate_total_points() {
		foreach ($this->feature_items as $item) {
			if ("active" == $item->feature_status) {
				$this->total_points = $this->total_points + intval($item->item_points);
			}
		}
	}

	public function get_total_site_points() {
		return $this->total_points;
	}

	public function get_total_achievable_points() {
		foreach ($this->feature_items as $item) {
			$this->total_achievable_points = $this->total_achievable_points + intval($item->item_points);
		}
		return $this->total_achievable_points;
	}

	public function check_remove_wp_generator_meta_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_remove_wp_generator_meta_info') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_prevent_hotlinking_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_prevent_hotlinking') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_user_accounts_change_admin_user_feature($item) {
		if (AIOWPSecurity_Utility::check_user_exists('admin')) {
			 $item->set_feature_status($this->feature_inactive);
		} else {
			$item->set_feature_status($this->feature_active);
		}
	}

	public function check_user_accounts_display_name_feature($item) {
		if (AIOWPSecurity_Utility::check_identical_login_and_nick_names()) {
			 $item->set_feature_status($this->feature_inactive);
		} else {
			$item->set_feature_status($this->feature_active);
		}
	}

	public function check_login_lockdown_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_login_lockdown') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_login_captcha_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_login_captcha') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_custom_login_captcha_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_custom_login_captcha') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_woo_login_captcha_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_woo_login_captcha') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_woo_lostpassword_captcha_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_woo_lostpassword_captcha') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_woo_register_captcha_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_woo_register_captcha') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_lost_password_captcha_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_lost_password_captcha') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_comment_captcha_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_comment_captcha') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_bp_register_captcha_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_bp_register_captcha') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_bbp_new_topic_captcha_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_bbp_new_topic_captcha') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_login_whitelist_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_whitelisting') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_force_logout_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_forced_logout') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_registration_approval_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_manual_registration_approval') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_registration_captcha_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_registration_page_captcha') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_enable_registration_honeypot_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_registration_honeypot') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_db_security_db_prefix_feature($item) {
		global $wpdb;
		if ('wp_' == $wpdb->prefix) {
			 $item->set_feature_status($this->feature_inactive);
		} else {
			$item->set_feature_status($this->feature_active);
		}
	}

	public function check_filesystem_permissions_feature($item) {
		//TODO
		$is_secure = 1;
		$util = new AIOWPSecurity_Utility_File;
		$files_dirs_to_check = $util->files_and_dirs_to_check;

		foreach ($files_dirs_to_check as $file_or_dir) {
			$actual_perm = AIOWPSecurity_Utility_File::get_file_permission($file_or_dir['path']);
			$is_secure = $is_secure*AIOWPSecurity_Utility_File::is_file_permission_secure($file_or_dir['permissions'], $actual_perm);
		}

		//Only if all of the files' permissions are deemed secure give this a thumbs up
		if (1 == $is_secure) {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_filesystem_file_editing_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_disable_file_editing') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_block_wp_files_access_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_prevent_default_wp_file_access') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_enable_ip_useragent_blacklist_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_blacklisting') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_enable_basic_firewall_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_basic_firewall') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_enable_pingback_firewall_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_pingback_firewall') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_debug_file_access_block_firewall_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_block_debug_log_file_access') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_disable_trace_track_firewall_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_disable_trace_and_track') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_disable_index_views_firewall_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_disable_index_views') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_enable_bfap_firewall_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_brute_force_attack_prevention') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_forbid_proxy_comments_firewall_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_forbid_proxy_comments') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_deny_bad_queries_firewall_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_deny_bad_query_strings') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_advanced_char_string_filter_firewall_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_advanced_char_string_filter') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_enable_5G_6G_blacklist_firewall_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_5g_firewall') == '1') {
			$item->set_feature_status($this->feature_active);
		} elseif ($aio_wp_security->configs->get_value('aiowps_enable_6g_firewall') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_block_fake_googlebots_firewall_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_block_fake_googlebots') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_enable_404_blocking_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_404_IP_lockout') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_enable_rename_login_page_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_rename_login_page') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_enable_login_honeypot_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_login_honeypot') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	/**
	 * Featurs list updated based on the disabled appliction password on or off
	 *
	 * @param object $item
	 * @global AIO_WP_Security $aio_wp_security
	 * @return void
	 */
	public function check_disable_application_password_feature($item) {
		global $aio_wp_security;
		if ('1' == $aio_wp_security->configs->get_value('aiowps_disable_application_password')) {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_enable_block_spambots_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_spambot_blocking') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

	public function check_enable_fcd_scan_feature($item) {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_automated_fcd_scan') == '1') {
			$item->set_feature_status($this->feature_active);
		} else {
			$item->set_feature_status($this->feature_inactive);
		}
	}

}
