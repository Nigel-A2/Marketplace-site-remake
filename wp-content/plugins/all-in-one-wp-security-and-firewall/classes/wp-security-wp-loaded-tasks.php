<?php
/**
 * This class handles tasks that need to be executed at wp-loaded time
 */
if (!defined('ABSPATH')) {
	exit;//Exit if accessed directly
}

class AIOWPSecurity_WP_Loaded_Tasks {

	public function __construct() {
		//Add tasks that need to be executed at wp-loaded time

		global $aio_wp_security;
		do_action('aiowps_wp_loaded_tasks_start', $this);

		//Handle the rename login page feature
		if ($aio_wp_security->configs->get_value('aiowps_enable_rename_login_page') == '1') {
			include_once(AIO_WP_SECURITY_PATH . '/classes/wp-security-process-renamed-login-page.php');
			new AIOWPSecurity_Process_Renamed_Login_Page();
			AIOWPSecurity_Process_Renamed_Login_Page::renamed_login_init_tasks();
		} else {
			add_action('login_init', array($this, 'aiowps_login_init'));
		}

		$this->do_lockout_tasks();

		do_action('aiowps_wp_loaded_tasks_end', $this);

	}

	/**
	 * Perform lockout task if it is applicable.
	 *
	 * @return void
	 */
	private function do_lockout_tasks() {
		global $aio_wp_security;

		if (1 != $aio_wp_security->configs->get_value('aiowps_site_lockout')) {
			return;
		}

		if ('admin-ajax.php' == $GLOBALS['pagenow']) {
			return;
		}
		
		// Show login screen to all non-logged in users.
		if ('wp-login.php' == $GLOBALS['pagenow']) {
			return;
		}

		// The lockout message should not be displayed to an administrator user.
		if (is_user_logged_in() && current_user_can('manage_options')) {
			return;
		}
		
		// Non administrator users to lockout accessing admin area.
		if (is_user_logged_in() && !current_user_can('manage_options') && is_admin()) {
			wp_redirect(home_url());
		}
		
		// Non-logged in users try access admin area, redirect to login page.
		if (is_admin()) {
			return;
		}
		
		self::site_lockout_tasks();
	}

	/**
	 * Render lockout output.
	 *
	 * @return void
	 */
	private static function site_lockout_tasks() {
		$lockout_output = apply_filters('aiowps_site_lockout_output', '');
		if (empty($lockout_output)) {
			nocache_headers();
			header("HTTP/1.0 503 Service Unavailable");
			remove_action('wp_head', 'head_addons', 7);
			$template = apply_filters('aiowps_site_lockout_template_include', AIO_WP_SECURITY_PATH . '/other-includes/wp-security-visitor-lockout-page.php');
			include_once($template);
		} else {
			echo $lockout_output;
		}

		exit();
	}
	
	public static function aiowps_login_init() {
		//if user is logged in and tries to access login page - redirect them to wp-admin
		//this will prevent issues such as the following:
		//https://wordpress.org/support/topic/already-logged-in-no-captcha
		if (is_user_logged_in()) {
			wp_redirect(admin_url());
		} else {
			AIOWPSecurity_Utility_IP::check_login_whitelist_and_forbid();
		}
	}

}
