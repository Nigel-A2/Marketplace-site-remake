<?php

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (!class_exists('AIO_WP_Security')) {

	class AIO_WP_Security {

		public $version = '5.1.0';

		public $db_version = '1.9.6';

		public $plugin_url;

		public $plugin_path;

		public $configs;

		/**
		 * Notice class object.
		 *
		 * @var object instance of AIOWPSecurity_Notices
		 */
		public $notices;

		public $admin_init;

		public $debug_logger;

		public $cron_handler;

		public $user_login_obj;

		public $user_registration_obj;

		public $scan_obj;

		public $captcha_obj;
				
		public $cleanup_obj;

		/**
		 * Whether the page is admin dashboard page.
		 *
		 * @var boolean
		 */
		public $is_admin_dashboard_page;

		/**
		 * Whether the page is admin plugin page.
		 *
		 * @var boolean
		 */
		public $is_plugin_admin_page;

		/**
		 * Whether the page is admin AIOS page.
		 *
		 * @var boolean
		 */
		public $is_aiowps_admin_page;

		/**
		 * Whether the page is AIOS Login reCAPTCHA page.
		 *
		 * @var boolean
		 */
		public $is_aiowps_google_recaptcha_tab_page;

		/**
		 * Seup constans, load configs, includes required files and added actions.
		 *
		 * @return Void.
		 */
		public function __construct() {
			$this->define_constants();
			$this->load_configs();
			$this->includes();
			$this->loader_operations();

			add_action('init', array($this, 'wp_security_plugin_init'), 0);
			add_action('wp_loaded', array($this, 'aiowps_wp_loaded_handler'));

			$add_update_action_prefixes = array(
				'add_option_',
				'update_option_',
			);
			foreach ($add_update_action_prefixes as $add_update_action_prefix) {
				add_action($add_update_action_prefix . '_updraft_interval_database', array($this, 'udp_schedule_db_option_add_update_action_handler'), 10, 2);
			}

			do_action('aiowpsecurity_loaded');

		}

		/**
		 * Return the URL for the plugin directory
		 *
		 * @return String
		 */
		public function plugin_url() {
			if ($this->plugin_url) return $this->plugin_url;
			return $this->plugin_url = plugins_url('', __FILE__);
		}

		public function plugin_path() {
			if ($this->plugin_path) return $this->plugin_path;
			return $this->plugin_path = untrailingslashit(plugin_dir_path(__FILE__));
		}

		public function load_configs() {
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-config.php');
			$this->configs = AIOWPSecurity_Config::get_instance();
		}

		public function define_constants() {
			define('AIO_WP_SECURITY_VERSION', $this->version);
			define('AIO_WP_SECURITY_DB_VERSION', $this->db_version);
			define('AIOWPSEC_WP_HOME_URL', home_url());
			define('AIOWPSEC_WP_SITE_URL', site_url());
			define('AIOWPSEC_WP_URL', AIOWPSEC_WP_SITE_URL); // for backwards compatibility
			define('AIO_WP_SECURITY_URL', $this->plugin_url());
			define('AIO_WP_SECURITY_PATH', $this->plugin_path());
			define('AIO_WP_SECURITY_BACKUPS_DIR_NAME', 'aiowps_backups');
			define('AIO_WP_SECURITY_BACKUPS_PATH', AIO_WP_SECURITY_PATH.'/backups');
			define('AIO_WP_SECURITY_LIB_PATH', AIO_WP_SECURITY_PATH.'/lib');
			if (!defined('AIOWPSEC_MANAGEMENT_PERMISSION')) {//This will allow the user to define custom capability for this constant in wp-config file
				define('AIOWPSEC_MANAGEMENT_PERMISSION', 'manage_options');
			}
			define('AIOWPSEC_MENU_SLUG_PREFIX', 'aiowpsec');
			define('AIOWPSEC_MAIN_MENU_SLUG', 'aiowpsec');
			define('AIOWPSEC_SETTINGS_MENU_SLUG', 'aiowpsec_settings');
			define('AIOWPSEC_USER_ACCOUNTS_MENU_SLUG', 'aiowpsec_useracc');
			define('AIOWPSEC_USER_LOGIN_MENU_SLUG', 'aiowpsec_userlogin');
			define('AIOWPSEC_USER_REGISTRATION_MENU_SLUG', 'aiowpsec_user_registration');
			define('AIOWPSEC_DB_SEC_MENU_SLUG', 'aiowpsec_database');
			define('AIOWPSEC_FILESYSTEM_MENU_SLUG', 'aiowpsec_filesystem');
			define('AIOWPSEC_BLACKLIST_MENU_SLUG', 'aiowpsec_blacklist');
			define('AIOWPSEC_FIREWALL_MENU_SLUG', 'aiowpsec_firewall');
			define('AIOWPSEC_MAINTENANCE_MENU_SLUG', 'aiowpsec_maintenance');
			define('AIOWPSEC_SPAM_MENU_SLUG', 'aiowpsec_spam');
			define('AIOWPSEC_FILESCAN_MENU_SLUG', 'aiowpsec_filescan');
			define('AIOWPSEC_BRUTE_FORCE_MENU_SLUG', 'aiowpsec_brute_force');
			define('AIOWPSEC_MISC_MENU_SLUG', 'aiowpsec_misc');
			define('AIOWPSEC_TWO_FACTOR_AUTH_MENU_SLUG', 'aiowpsec_two_factor_auth_user');
			define('AIOWPSEC_TOOLS_MENU_SLUG', 'aiowpsec_tools');
			
			if (!defined('AIOS_TFA_PREMIUM_LATEST_INCOMPATIBLE_VERSION')) define('AIOS_TFA_PREMIUM_LATEST_INCOMPATIBLE_VERSION', '1.14.7');
			
			if (!defined('AIOWPSEC_PURGE_FAILED_LOGIN_RECORDS_AFTER_DAYS')) define('AIOWPSEC_PURGE_FAILED_LOGIN_RECORDS_AFTER_DAYS', 90);
			if (!defined('AIOS_PURGE_EVENTS_RECORDS_AFTER_DAYS')) define('AIOS_PURGE_EVENTS_RECORDS_AFTER_DAYS', 90);
			if (!defined('AIOS_PURGE_LOGIN_ACTIVITY_RECORDS_AFTER_DAYS')) define('AIOS_PURGE_LOGIN_ACTIVITY_RECORDS_AFTER_DAYS', 90);
			if (!defined('AIOS_PURGE_GLOBAL_META_DATA_RECORDS_AFTER_DAYS')) define('AIOS_PURGE_GLOBAL_META_DATA_RECORDS_AFTER_DAYS', 90);
			if (!defined('AIOS_DEFAULT_BRUTE_FORCE_FEATURE_SECRET_WORD')) define('AIOS_DEFAULT_BRUTE_FORCE_FEATURE_SECRET_WORD', 'aiossecret');
			if (!defined('AIOS_FIREWALL_MAX_FILE_UPLOAD_LIMIT_MB')) define('AIOS_FIREWALL_MAX_FILE_UPLOAD_LIMIT_MB', 100);

			global $wpdb;
			define('AIOWPSEC_TBL_LOGIN_LOCKDOWN', $wpdb->prefix . 'aiowps_login_lockdown');
			define('AIOWPSEC_TBL_FAILED_LOGINS', $wpdb->prefix . 'aiowps_failed_logins');
			define('AIOWPSEC_TBL_USER_LOGIN_ACTIVITY', $wpdb->prefix . 'aiowps_login_activity');
			define('AIOWPSEC_TBL_GLOBAL_META_DATA', $wpdb->prefix . 'aiowps_global_meta');
			define('AIOWPSEC_TBL_EVENTS', $wpdb->prefix . 'aiowps_events');
			define('AIOWPSEC_TBL_PERM_BLOCK', $wpdb->prefix . 'aiowps_permanent_block');
			define('AIOWPSEC_TBL_DEBUG_LOG', $wpdb->prefix . 'aiowps_debug_log');
		}

		public function includes() {
			//Load common files for everywhere
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-debug-logger.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-abstract-ids.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-utility.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-utility-htaccess.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-utility-ip-address.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-utility-file.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-general-init-tasks.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-wp-loaded-tasks.php');

			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-user-login.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-user-registration.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-captcha.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-cleanup.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-file-scan.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-comment.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-cronjob-handler.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/grade-system/wp-security-feature-item.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/grade-system/wp-security-feature-item-manager.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-wp-footer-content.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-blocking.php');
			include_once(AIO_WP_SECURITY_PATH .'/classes/wp-security-two-factor-login.php');


			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-utility-firewall.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-block-file.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-block-bootstrap.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-block-htaccess.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-block-litespeed.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-block-userini.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-block-wpconfig.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-block-muplugin.php');

			// At this time, sometimes is_admin() can't be populated, It gives the error PHP Fatal error:  Uncaught Error: Class 'AIOWPSecurity_Admin_Init' not found.
			// so we should not use is_admin() condition.
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-configure-settings.php');
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-notices.php');
			require_once(AIO_WP_SECURITY_PATH.'/admin/wp-security-admin-init.php');
			include_once(AIO_WP_SECURITY_PATH.'/admin/general/wp-security-list-table.php');
			include_once(AIO_WP_SECURITY_PATH.'/admin/wp-security-firewall-setup-notice.php');
		}

		public function loader_operations() {
			add_action('plugins_loaded', array($this, 'load_aio_firewall'), 0);
			add_action('plugins_loaded', array($this, 'plugins_loaded_handler'));//plugins loaded hook
			add_action('plugins_loaded', array($this, 'load_plugin_textdomain'));

			$debug_config = $this->configs->get_value('aiowps_enable_debug');
			$debug_enabled = empty($debug_config) ? false : true;
			$this->debug_logger = new AIOWPSecurity_Logger($debug_enabled);

			$this->load_ajax_handler();
		}

		/**
		 * Activation handler function.
		 *
		 * @param boolean $networkwide whether activate plugin network wide.
		 * @return void
		 */
		public static function activate_handler($networkwide) {
			global $wpdb;// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable -- Used for the include below
			//Only runs when the plugin activates
			if (version_compare(phpversion(), '5.6.0', '<')) {
				deactivate_plugins(basename(__FILE__));
				wp_die(
					sprintf(htmlspecialchars(__('This plugin requires PHP version %s.', 'all-in-one-wp-security-and-firewall')), '<strong>5.6+</strong>')
					.' '.sprintf(htmlspecialchars(__('Current site PHP version is %s.', 'all-in-one-wp-security-and-firewall')), '<strong>'.phpversion().'</strong>')
					.' '.htmlspecialchars(__('You will need to ask your web hosting company to upgrade.', 'all-in-one-wp-security-and-firewall'))
				);
			}
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-installer.php');
			AIOWPSecurity_Installer::run_installer($networkwide);
			AIOWPSecurity_Installer::set_cron_tasks_upon_activation($networkwide);
		}

		/**
		 * Handles ajax. This is hooked into the inbuilt 'wp_ajax_(action)' action through 'wp_ajax_aiowps_ajax'.
		 *
		 * @return Void
		 */
		public function aiowps_ajax_handler() {
			$nonce = empty($_POST['nonce']) ? '' : $_POST['nonce'];

			if (!wp_verify_nonce($nonce, 'wp-security-ajax-nonce') || empty($_POST['subaction'])) {
				wp_send_json(array(
					'result' => false,
					'error_code' => 'security_check',
					'error_message' => __('The security check failed; try refreshing the page.', 'all-in-one-wp-security-and-firewall')
				));
			}

			$subaction = sanitize_text_field($_POST['subaction']);

			if (!current_user_can(AIOWPSEC_MANAGEMENT_PERMISSION)) {
				wp_send_json(array(
					'result' => false,
					'error_code' => 'security_check',
					'error_message' => __('You are not allowed to run this command.', 'all-in-one-wp-security-and-firewall')
				));
			}


			// Currently the settings are only available to network admins.
			if (is_multisite() && !current_user_can('manage_network_options')) {
			/**
			 * Filters the commands allowed to the subsite admins. Other commands are only available to network admin. Only used in a multisite context.
			 */
				$allowed_commands = apply_filters('aiowps_multisite_allowed_commands', array());
				if (!in_array($subaction, $allowed_commands)) wp_send_json(array(
					'result' => false,
					'error_code' => 'update_failed',
					'error_message' => __('Options can only be saved by network admin', 'all-in-one-wp-security-and-firewall')
				));
			}

			$time_now = $this->notices->get_time_now();
			$results = array();

			// Some commands that are available via AJAX only.
			if (in_array($subaction, array('dismissdashnotice', 'dismiss_season'))) {
				$this->configs->set_value($subaction, $time_now + (366 * 86400));
			} elseif (in_array($subaction, array('dismiss_page_notice_until', 'dismiss_notice'))) {
				$this->configs->set_value($subaction, $time_now + (84 * 86400));
			} elseif ('dismiss_review_notice' == $subaction) {
				if (empty($_POST['dismiss_forever'])) {
					$this->configs->set_value($subaction, $time_now + (84 * 86400));
				} else {
					$this->configs->set_value($subaction, $time_now + (100 * 365.25 * 86400));
				}
			} elseif ('dismiss_automated_database_backup_notice' == $subaction) {
				$this->delete_automated_backup_configs();
			} elseif ('dismiss_ip_retrieval_settings_notice' == $subaction) {
				$this->configs->set_value($subaction, 1);
			} elseif ('dismiss_ip_retrieval_settings_notice' == $subaction) {
				$this->configs->set_value('aiowps_is_login_whitelist_disabled_on_upgrade', 1);
			} elseif ('dismiss_login_whitelist_disabled_on_upgrade_notice' == $subaction) {
				if (isset($_POST['turn_it_back_on']) && '1' == $_POST['turn_it_back_on']) {
					$this->configs->set_value('aiowps_enable_whitelisting', '1');
				}
				$this->configs->delete_value('aiowps_is_login_whitelist_disabled_on_upgrade');
			} else {
				// Other commands, available for any remote method.
			}

			$this->configs->save_config();

			$result = json_encode($results);

			$json_last_error = json_last_error();

			// if json_encode returned error then return error.
			if ($json_last_error) {
				$result = array(
					'result' => false,
					'error_code' => $json_last_error,
					'error_message' => 'json_encode error : '.$json_last_error,
					'error_data' => '',
				);

				$result = json_encode($result);
			}

			echo $result;

			die;
		}

		/**
		 * Delete automated backup configs
		 *
		 * @return void
		 */
		private function delete_automated_backup_configs() {
			$automated_config_keys = array(
				'aiowps_enable_automated_backups',
				'aiowps_db_backup_frequency',
				'aiowps_backup_files_stored',
				'aiowps_send_backup_email_address',
				'aiowps_backup_email_address',
			);
			foreach ($automated_config_keys as $automated_config_key) {
				$this->configs->delete_value($automated_config_key);
			}
		}

		/**
		 * UpdraftPlus schedule option add/update action handler.
		 *
		 * @param String $option Option name.
		 * @param String $value  Option value.
		 * @return Void
		 */
		public function udp_schedule_db_option_add_update_action_handler($option, $value) {
			// For extra caution
			if ('updraft_interval_database' != $option) {
				return;
			}

			if (empty($value) || 'manual' == $value) {
				return;
			}

			$this->delete_automated_backup_configs();
			$this->configs->save_config();
		}

		/**
		 * Output, or return, the results of running a template (from the 'templates' directory, unless a filter over-rides it). Templates are run with $aio_wp_security and $wpdb set.
		 *
		 * @param String  $path                   - path to the template
		 * @param Boolean $return_instead_of_echo - by default, the template is echo-ed; set this to instead return it
		 * @param Array   $extract_these          - variables to inject into the template's run context
		 *
		 * @return Void|String
		 */
		public function include_template($path, $return_instead_of_echo = false, $extract_these = array()) {
			if ($return_instead_of_echo) ob_start();

			if (!isset($template_file)) $template_file = AIO_WP_SECURITY_PATH.'/templates/'.$path;

			$template_file = apply_filters('aio_wp_security_template', $template_file, $path);

			do_action('aio_wp_security_before_template', $path, $template_file, $return_instead_of_echo, $extract_these);

			if (!file_exists($template_file)) {
				error_log("All In One WP Security: template not found: $template_file");
				echo __('Error:', 'all-in-one-wp-security-and-firewall').' '.__('template not found', 'all-in-one-wp-security-and-firewall')." ($template_file)";
			} else {
				extract($extract_these);
				global $wpdb;// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
				$aio_wp_security = $this;// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
				include $template_file;
			}

			do_action('aio_wp_security_after_template', $path, $template_file, $return_instead_of_echo, $extract_these);

			if ($return_instead_of_echo) return ob_get_clean();
		}

		/**
		 * Deactivation AIOS plugin.
		 *
		 * @return void
		 */
		public static function deactivation_handler() {
			require_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-deactivation-tasks.php');
			AIOWPSecurity_Deactivation_Tasks::run();
			do_action('aiowps_deactivation_complete');
		}

		/**
		 * Unintall AIOS plugin.
		 *
		 * @return void
		 */
		public static function uninstall_handler() {
			require_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-uninstallation-tasks.php');
			AIOWPSecurity_Uninstallation_Tasks::run();
			do_action('aiowps_uninstallation_complete');
		}

		public function db_upgrade_handler() {
			if (is_admin()) {//Check if DB needs to be upgraded
				if (get_option('aiowpsec_db_version') != AIO_WP_SECURITY_DB_VERSION) {
					require_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-installer.php');
					AIOWPSecurity_Installer::run_installer();
					AIOWPSecurity_Installer::set_cron_tasks_upon_activation();
					AIOWPSecurity_Utility_Htaccess::write_to_htaccess();

					/**
					 * Update our config file's header if needed.
					 */
					require_once(AIO_WP_SECURITY_PATH.'/classes/firewall/libs/wp-security-firewall-config.php');
					$config = new \AIOWPS\Firewall\Config(AIOWPSecurity_Utility_Firewall::get_firewall_rules_path() . 'settings.php');
					$config->update_prefix();
				}
			}
		}


		/**
		 * Loads our firewall
		 *
		 * @return void
		 */
		public function load_aio_firewall() {
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-utility-firewall.php');
			$firewall_path = AIOWPSecurity_Utility_Firewall::get_firewall_path();

			clearstatcache();
			if (file_exists($firewall_path)) {
				include_once($firewall_path);
			}
		}

		/**
		 * Runs when plugins_loaded hook is fired
		 *
		 * @return void
		 */
		public function plugins_loaded_handler() {
			//Runs when plugins_loaded action gets fired
			// Add filter for 'cron_schedules' must be run before $this->db_upgrade_handler()
			// so, AIOWPSecurity_Cronjob_Handler __construct runs this filter so the object should be initialized here.
			$this->cron_handler = new AIOWPSecurity_Cronjob_Handler();
			if (is_admin()) {
				//Do plugins_loaded operations for admin side
				$this->db_upgrade_handler();
				$this->admin_init = new AIOWPSecurity_Admin_Init();
				$this->notices = new AIOWPSecurity_Notices();
			}
		}

		/**
		 * Load plugin text domain
		 *
		 * @return void
		 */
		public function load_plugin_textdomain() {
				load_plugin_textdomain('all-in-one-wp-security-and-firewall', false, dirname(plugin_basename(__FILE__)) . '/languages/');
		}

		/**
		 * Initializes the plugin. This is hooked into the inbuilt 'init' action.
		 *
		 * @return Void
		 */
		public function wp_security_plugin_init() {
			//Actions, filters, shortcodes goes here
			$this->user_login_obj = new AIOWPSecurity_User_Login();//Do the user login operation tasks
			$this->user_registration_obj = new AIOWPSecurity_User_Registration();//Do the user login operation tasks
			$this->captcha_obj = new AIOWPSecurity_Captcha(); // Do the CAPTCHA tasks
			$this->cleanup_obj = new AIOWPSecurity_Cleanup(); // Object to handle cleanup tasks
			$this->scan_obj = new AIOWPSecurity_Scan();//Object to handle scan tasks
			add_action('login_enqueue_scripts', array($this, 'aiowps_login_enqueue'));
			add_action('wp_footer', array($this, 'aiowps_footer_content'));

			add_action('wp_ajax_aiowps_ajax', array($this, 'aiowps_ajax_handler'));

			add_action('wp_login', array('AIOWPSecurity_User_Login', 'wp_login_action_handler'), 10, 2);
			// For admin side force log out.
			add_action('admin_init', array($this, 'do_action_force_logout_check'));
			// For front side force log out.
			add_action('template_redirect', array($this, 'do_action_force_logout_check'));

			new AIOWPSecurity_General_Init_Tasks();
			new AIOWPSecurity_Comment();

			$this->redirect_user_after_force_logout();
		}

		public function aiowps_wp_loaded_handler() {
			new AIOWPSecurity_WP_Loaded_Tasks();
		}

		/**
		 * Enqueues the Google reCAPTCHA v2 API URL for the standard WP login page
		 */
		public function aiowps_login_enqueue() {
			global $aio_wp_security;
			if (!$aio_wp_security->is_login_lockdown_by_const() && $aio_wp_security->configs->get_value('aiowps_default_recaptcha')) {
				if ($aio_wp_security->configs->get_value('aiowps_enable_login_captcha') == '1' || $aio_wp_security->configs->get_value('aiowps_enable_registration_page_captcha') == '1') {
					wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js?hl=' . AIOWPSecurity_Captcha::get_google_recaptcha_compatible_site_locale(), array(), AIO_WP_SECURITY_VERSION);
					// Below is needed to provide some space for the Google reCAPTCHA form (otherwise it appears partially hidden on RHS)
					wp_add_inline_script('google-recaptcha', 'document.addEventListener("DOMContentLoaded", ()=>{document.getElementById("login").style.width = "340px";});');
				}
			}
		}

		public function aiowps_footer_content() {
			new AIOWPSecurity_WP_Footer_Content();
		}

		/**
		 * Redirect user to proper login page after forced logout
		 *
		 * @return void
		 */
		private function redirect_user_after_force_logout() {
			global $aio_wp_security;
			if (isset($_GET['aiowpsec_do_log_out'])) {
				$nonce = isset($_GET['_wpnonce']) ? $_GET['_wpnonce'] : '';
				if (!wp_verify_nonce($nonce, 'aio_logout')) {
					return;
				}
				wp_logout();
				if (isset($_GET['after_logout'])) { //Redirect to the after logout url directly
					$after_logout_url = esc_url($_GET['after_logout']);
					AIOWPSecurity_Utility::redirect_to_url($after_logout_url);
				}
				$additional_data = strip_tags($_GET['al_additional_data']);
				if (isset($additional_data)) {
					$login_url = '';

					if (AIOWPSecurity_Utility::is_woocommerce_plugin_active()) {
						$login_url = get_permalink(get_option('woocommerce_myaccount_page_id'));
					} elseif ('1' == $aio_wp_security->configs->get_value('aiowps_enable_rename_login_page')) { //Check if rename login feature enabled.
						if (get_option('permalink_structure')) {
							$home_url = trailingslashit(home_url());
						} else {
							$home_url = trailingslashit(home_url()) . '?';
						}
						$login_url = $home_url.$aio_wp_security->configs->get_value('aiowps_login_page_slug');
					} else {
						$login_url = wp_login_url();
					}

					//Inspect the payload and do redirect to login page with a msg and redirect url
					$logout_payload = (is_multisite() ? get_site_transient('aiowps_logout_payload') : get_transient('aiowps_logout_payload'));
					if (!empty($logout_payload['redirect_to'])) {
						$login_url = AIOWPSecurity_Utility::add_query_data_to_url($login_url, 'redirect_to', $logout_payload['redirect_to']);
					}
					if (!empty($logout_payload['msg'])) {
						$login_url .= '&'.$logout_payload['msg'];
					}
					if (!empty($login_url)) {
						AIOWPSecurity_Utility::redirect_to_url($login_url);
					}
				}
			}
		}

		/**
		 * Verify Google reCAPTCHA site key
		 *
		 * @param string $site_key reCAPTCHA site key.
		 * @return boolean True if site key is verified, Otherwise false.
		 */
		public function google_recaptcha_sitekey_verification($site_key) {
			$result = true;
			$arr_params = array( 'k' => $site_key, 'size' => 'checkbox' );
			$recaptcha_url = esc_url(add_query_arg($arr_params, 'https://www.google.com/recaptcha/api2/anchor'));
			$response = wp_remote_get($recaptcha_url);
			$response_body = wp_remote_retrieve_body($response);
			if (false !== strpos($response_body, 'Invalid site key')) $result = false;
			return $result;
		}

		/**
		 * Check whether current admin page is Admin Dashboard page or not.
		 *
		 * @return boolean True if Admin Dashboard page, Otherwise false.
		 */
		public function is_admin_dashboard_page() {
			if (isset($this->is_admin_dashboard_page)) {
				return $this->is_admin_dashboard_page;
			}
			global $pagenow;
			$this->is_admin_dashboard_page = 'index.php' == $pagenow;
			return $this->is_admin_dashboard_page;
		}

		/**
		 * Check whether current admin page is plugin page or not.
		 *
		 * @return boolean True if Admin Plugin page, Otherwise false.
		 */
		public function is_plugin_admin_page() {
			if (isset($this->is_plugin_admin_page)) {
				return $this->is_plugin_admin_page;
			}
			global $pagenow;
			$this->is_plugin_admin_page = 'plugins.php' == $pagenow;
			return $this->is_plugin_admin_page;
		}

		/**
		 * Check whether current admin page is All In One WP Security admin page or not.
		 *
		 * @return boolean True if All In One WP Security admin page, Otherwise false.
		 */
		public function is_aiowps_admin_page() {
			if (isset($this->is_aiowps_admin_page)) {
				return $this->is_aiowps_admin_page;
			}
			global $pagenow;
			$this->is_aiowps_admin_page = ('admin.php' == $pagenow && isset($_GET['page']) && false !== strpos($_GET['page'], AIOWPSEC_MENU_SLUG_PREFIX));
			return $this->is_aiowps_admin_page;
		}

		/**
		 * Check whether current admin page is Google reCAPTCHA tab page or not.
		 *
		 * @return boolean True if Google reCAPTCHA tab page, Otherwise false.
		 */
		public function is_aiowps_google_recaptcha_tab_page() {
			if (isset($this->is_aiowps_google_recaptcha_tab_page)) {
				return $this->is_aiowps_google_recaptcha_tab_page;
			}
			global $pagenow;
			$this->is_aiowps_google_recaptcha_tab_page = ('admin.php' == $pagenow
															&& isset($_GET['page'])
															&& 'aiowpsec_brute_force' == $_GET['page']
															&& isset($_GET['tab'])
															&& 'tab3' == $_GET['tab']
			);
			return $this->is_aiowps_google_recaptcha_tab_page;
		}

		/**
		 * Invokes all functions attached to action hook aiowps_force_logout_check
		 *
		 * @return void
		 */
		public function do_action_force_logout_check() {
			do_action('aiowps_force_logout_check');
		}

		/**
		 * Check AIOWPS_DISABLE_LOGIN_LOCKDOWN constant value
		 *
		 * @return boolean True if the AIOWPS_DISABLE_LOGIN_LOCKDOWN constant defined with true value, otherwise false.
		 */
		public function is_login_lockdown_by_const() {
			return defined('AIOWPS_DISABLE_LOGIN_LOCKDOWN') && AIOWPS_DISABLE_LOGIN_LOCKDOWN;
		}

		/**
		 * Check whether the cookie-based brute force attack is prevented or not.
		 *
		 * @return Boolean True if the cookie-based brute force attack is prevented, otherwise false.
		 */
		public function should_cookie_based_brute_force_prvent() {
			if (defined('AIOS_DISABLE_COOKIE_BRUTE_FORCE_PREVENTION') && 'AIOS_DISABLE_COOKIE_BRUTE_FORCE_PREVENTION') {
				return false;
			}

			return $this->configs->get_value('aiowps_enable_brute_force_attack_prevention');
		}

		/**
		 * Instantiate Ajax handling class
		 */
		private function load_ajax_handler() {
			include_once(AIO_WP_SECURITY_PATH.'/classes/aios-ajax.php');
			AIOS_Ajax::get_instance();
		}

	} // End of class

}//End of class not exists check

$GLOBALS['aio_wp_security'] = new AIO_WP_Security();
