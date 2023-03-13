<?php
if (!defined('ABSPATH')) {
	exit;//Exit if accessed directly
}

class AIOWPSecurity_General_Init_Tasks {
	public function __construct() {
		// Do init time tasks
		global $aio_wp_security;

		if ($aio_wp_security->configs->get_value('aiowps_disable_xmlrpc_pingback_methods') == '1') {
			add_filter('xmlrpc_methods', array($this, 'aiowps_disable_xmlrpc_pingback_methods'));
			add_filter('wp_headers', array($this, 'aiowps_remove_x_pingback_header'));
		}

		// Check permanent block list and block if applicable (ie, do PHP blocking)
		AIOWPSecurity_Blocking::check_visitor_ip_and_perform_blocking();

		if ($aio_wp_security->configs->get_value('aiowps_enable_autoblock_spam_ip') == '1') {
			add_action('comment_post', array($this, 'spam_detect_process_comment_post'), 10, 2); //this hook gets fired just after comment is saved to DB
			add_action('transition_comment_status', array($this, 'process_transition_comment_status'), 10, 3); //this hook gets fired when a comment's status changes
		}

		if ($aio_wp_security->configs->get_value('aiowps_enable_rename_login_page') == '1') {
			add_action('widgets_init', array($this, 'remove_standard_wp_meta_widget'));
			add_filter('retrieve_password_message', array($this, 'decode_reset_pw_msg'), 10, 4); //Fix for non decoded html entities in password reset link
		}

		if (current_user_can(AIOWPSEC_MANAGEMENT_PERMISSION) && is_admin()) {
			if ($aio_wp_security->configs->get_value('aios_is_google_recaptcha_wrong_site_key')) {
				add_action('all_admin_notices', array($this, 'google_recaptcha_notice'));
			}

			add_action('all_admin_notices', array($this, 'do_firewall_notice'));
			add_action('admin_post_aiowps_firewall_setup', array(AIOWPSecurity_Firewall_Setup_Notice::get_instance(), 'handle_setup_form'));
			add_action('admin_post_aiowps_firewall_setup_dismiss', array(AIOWPSecurity_Firewall_Setup_Notice::get_instance(), 'handle_dismiss_form'));

			$this->reapply_htaccess_rules();
			add_action('admin_notices', array($this,'reapply_htaccess_rules_notice'));
		}

		/**
		 * Send X-Frame-Options: SAMEORIGIN in HTTP header
		 */
		if ($aio_wp_security->configs->get_value('aiowps_prevent_site_display_inside_frame') == '1') {
			add_action('template_redirect', 'send_frame_options_header');
		}

		if ($aio_wp_security->configs->get_value('aiowps_remove_wp_generator_meta_info') == '1') {
			add_filter('the_generator', array($this,'remove_wp_generator_meta_info'));
			add_filter('style_loader_src', array($this,'remove_wp_css_js_meta_info'));
			add_filter('script_loader_src', array($this,'remove_wp_css_js_meta_info'));
		}

		// For the cookie based brute force prevention feature
		// Already logged in user should not redirected to brute_force_redirect_url in any case so added condition !is_user_logged_in()
		if ($aio_wp_security->should_cookie_based_brute_force_prvent() && !is_user_logged_in()) {
			$bfcf_secret_word = $aio_wp_security->configs->get_value('aiowps_brute_force_secret_word');
			$login_page_slug = $aio_wp_security->configs->get_value('aiowps_login_page_slug');
			if (isset($_GET[$bfcf_secret_word])) {
				AIOWPSecurity_Utility_IP::check_login_whitelist_and_forbid();

				// If URL contains secret word in query param then set cookie and then redirect to the login page
				AIOWPSecurity_Utility::set_cookie_value(AIOWPSecurity_Utility::get_brute_force_secret_cookie_name(), wp_hash($bfcf_secret_word));
				if ('1' == $aio_wp_security->configs->get_value('aiowps_enable_rename_login_page') && !is_user_logged_in()) {
					$login_url = home_url((get_option('permalink_structure') ? '' : '?')  . $aio_wp_security->configs->get_value('aiowps_login_page_slug'));
					AIOWPSecurity_Utility::redirect_to_url($login_url);
				} else {
					AIOWPSecurity_Utility::redirect_to_url(AIOWPSEC_WP_URL.'/wp-admin');
				}
			} else {
				$secret_word_cookie_val = AIOWPSecurity_Utility::get_cookie_value(AIOWPSecurity_Utility::get_brute_force_secret_cookie_name());
				$pw_protected_exception = $aio_wp_security->configs->get_value('aiowps_brute_force_attack_prevention_pw_protected_exception');
				$prevent_ajax_exception = $aio_wp_security->configs->get_value('aiowps_brute_force_attack_prevention_ajax_exception');

				if ('' != $_SERVER['REQUEST_URI'] && !hash_equals($secret_word_cookie_val, wp_hash($bfcf_secret_word))) {
					// admin section or login page or login custom slug called
					$is_admin_or_login = (false != strpos($_SERVER['REQUEST_URI'], 'wp-admin') || false != strpos($_SERVER['REQUEST_URI'], 'wp-login') || ('' != $login_page_slug && false != strpos($_SERVER['REQUEST_URI'], $login_page_slug))) ? 1 : 0;
					
					// admin side ajax called
					$is_admin_ajax_request = ('1' == $prevent_ajax_exception && false != strpos($_SERVER['REQUEST_URI'], 'wp-admin/admin-ajax.php')) ? 1 : 0;
					
					// password protected page called
					$is_password_protected_access = ('1' == $pw_protected_exception && isset($_GET['action']) && 'postpass' == $_GET['action']) ? 1 : 0;
					// cookie based brute force on and accessing admin without ajax and password protected then redirect
					if ($is_admin_or_login && !$is_admin_ajax_request && !$is_password_protected_access) {
						$redirect_url = $aio_wp_security->configs->get_value('aiowps_cookie_based_brute_force_redirect_url');
						AIOWPSecurity_Utility::redirect_to_url($redirect_url);
					}
				}
			}
		}
		// Stop users enumeration feature
		if ($aio_wp_security->configs->get_value('aiowps_prevent_users_enumeration') == 1) {
			include_once(AIO_WP_SECURITY_PATH.'/other-includes/wp-security-stop-users-enumeration.php');
		}

		// REST API security
		if ($aio_wp_security->configs->get_value('aiowps_disallow_unauthorized_rest_requests') == 1) {
			add_action('rest_api_init', array($this, 'check_rest_api_requests'), 10, 1);
		}

		// For user unlock request feature
		if (isset($_POST['aiowps_unlock_request']) || isset($_POST['aiowps_wp_submit_unlock_request'])) {
			nocache_headers();
			remove_action('wp_head', 'head_addons', 7);
			include_once(AIO_WP_SECURITY_PATH.'/other-includes/wp-security-unlock-request.php');
			exit();
		}

		if (isset($_GET['aiowps_auth_key'])) {
			//If URL contains unlock key in query param then process the request
			$unlock_key = sanitize_text_field($_GET['aiowps_auth_key']);
			AIOWPSecurity_User_Login::process_unlock_request($unlock_key);
		}

		// For honeypot feature
		if (isset($_POST['aio_special_field'])) {
			$special_field_value = sanitize_text_field($_POST['aio_special_field']);
			if (!empty($special_field_value)) {
				//This means a robot has submitted the login form!
				//Redirect back to its localhost
				AIOWPSecurity_Utility::redirect_to_url('http://127.0.0.1');
			}
		}

		// For 404 IP lockout feature
		if ($aio_wp_security->configs->get_value('aiowps_enable_404_IP_lockout') == '1') {
			if (!is_user_logged_in() || !current_user_can('administrator')) {
				$this->do_404_lockout_tasks();
			}
		}


		// For login CAPTCHA feature
		if ($aio_wp_security->configs->get_value('aiowps_enable_login_captcha') == '1') {
			if (!is_user_logged_in()) {
				add_action('login_form', array($this, 'insert_captcha_question_form'));
			}
		}

		// For woo form CAPTCHA features
		if ($aio_wp_security->configs->get_value('aiowps_enable_woo_login_captcha') == '1') {
			if (!is_user_logged_in()) {
				add_action('woocommerce_login_form', array($this, 'insert_captcha_question_form'));
			}
			if (isset($_POST['woocommerce-login-nonce'])) {
				add_filter('woocommerce_process_login_errors', array($this, 'aiowps_validate_woo_login_or_reg_captcha'), 10, 3);
			}
		}

		if ($aio_wp_security->configs->get_value('aiowps_enable_woo_register_captcha') == '1') {
			if (!is_user_logged_in()) {
				add_action('woocommerce_register_form', array($this, 'insert_captcha_question_form'));
			}

			if (isset($_POST['woocommerce-register-nonce'])) {
				add_filter('woocommerce_process_registration_errors', array($this, 'aiowps_validate_woo_login_or_reg_captcha'), 10, 3);
			}
		}

		if ($aio_wp_security->configs->get_value('aiowps_enable_woo_lostpassword_captcha') == '1') {
			if (!is_user_logged_in()) {
				add_action('woocommerce_lostpassword_form', array($this, 'insert_captcha_question_form'));
			}
			if (isset($_POST['woocommerce-lost-password-nonce'])) {
				add_action('lostpassword_post', array($this, 'process_woo_lost_password_form_post'));
			}
		}

		// For bbPress new topic form CAPTCHA
		if ($aio_wp_security->configs->get_value('aiowps_enable_bbp_new_topic_captcha') == '1') {
			if (!is_user_logged_in()) {
				add_action('bbp_theme_before_topic_form_submit_wrapper', array($this, 'insert_captcha_question_form'));
			}
		}

		// For custom login form CAPTCHA feature, ie, when wp_login_form() function is used to generate login form
		if ($aio_wp_security->configs->get_value('aiowps_enable_custom_login_captcha') == '1') {
			if (!is_user_logged_in()) {
				add_filter('login_form_middle', array($this, 'insert_captcha_custom_login'), 10, 2); //For cases where the WP wp_login_form() function is used
			}
		}

		// For honeypot feature
		if ($aio_wp_security->configs->get_value('aiowps_enable_login_honeypot') == '1') {
			if (!is_user_logged_in()) {
				add_action('login_form', array($this, 'insert_honeypot_hidden_field'));
			}
		}
 
		// For registration honeypot feature
		if ($aio_wp_security->configs->get_value('aiowps_enable_registration_honeypot') == '1') {
			if (!is_user_logged_in()) {
				add_action('register_form', array($this, 'insert_honeypot_hidden_field'));
			}
		}

		// For disable application password feature hide generate password
		if ('1' == $aio_wp_security->configs->get_value('aiowps_disable_application_password')) {
			add_filter('wp_is_application_passwords_available', '__return_false');
			add_action('edit_user_profile', array($this, 'show_disabled_application_password_message'));
			add_action('show_user_profile', array($this, 'show_disabled_application_password_message'));

			// Override the wp_die handler for app passwords were disabled.
			if (!empty($_SERVER['SCRIPT_FILENAME']) && ABSPATH . 'wp-admin/authorize-application.php' == $_SERVER['SCRIPT_FILENAME']) {
				add_filter('wp_die_handler', function () {
					return function ($message, $title, $args) {
						if ('Application passwords are not available.' == $message) {
							$message = htmlspecialchars(__('Application passwords have been disabled by All In One WP Security & Firewall plugin.', 'all-in-one-wp-security-and-firewall'));
						}
						_default_wp_die_handler($message, $title, $args);
					};
				}, 10, 1);
			}
		}

		// For lost password CAPTCHA feature
		if ($aio_wp_security->configs->get_value('aiowps_enable_lost_password_captcha') == '1') {
			if (!is_user_logged_in()) {
				add_action('lostpassword_form', array($this, 'insert_captcha_question_form'));
				add_action('lostpassword_post', array($this, 'process_lost_password_form_post'));
			}
		}

		// For registration manual approval feature
		if ($aio_wp_security->configs->get_value('aiowps_enable_manual_registration_approval') == '1') {
			add_filter('wp_login_errors', array($this, 'modify_registration_page_messages'), 10, 2);
		}

		// For registration page CAPTCHA feature
		if (is_multisite()) {
			$blog_id = get_current_blog_id();
			switch_to_blog($blog_id);
			if ($aio_wp_security->configs->get_value('aiowps_enable_registration_page_captcha') == '1') {
				if (!is_user_logged_in()) {
					add_action('signup_extra_fields', array($this, 'insert_captcha_question_form_multi'));
					//add_action('preprocess_signup_form', array($this, 'process_signup_form_multi'));
					add_filter('wpmu_validate_user_signup', array($this, 'process_signup_form_multi'));
				}
			}
			restore_current_blog();
		} else {
			if ($aio_wp_security->configs->get_value('aiowps_enable_registration_page_captcha') == '1') {
				if (!is_user_logged_in()) {
					add_action('register_form', array($this, 'insert_captcha_question_form'));
				}
			}
		}

		// For comment CAPTCHA feature or custom login form CAPTCHA
		if (is_multisite()) {
			$blog_id = get_current_blog_id();
			switch_to_blog($blog_id);
			if ($aio_wp_security->configs->get_value('aiowps_enable_comment_captcha') == '1') {
				if (!is_user_logged_in()) {
					if ($aio_wp_security->configs->get_value('aiowps_default_recaptcha')) {
						add_action('wp_head', array($this, 'add_recaptcha_script'));
					}
					add_action('comment_form_after_fields', array($this, 'insert_captcha_question_form'), 1);
					add_action('comment_form_logged_in_after', array($this, 'insert_captcha_question_form'), 1);
					add_filter('preprocess_comment', array($this, 'process_comment_post'));
				}
			}
			restore_current_blog();
		} else {
			if ($aio_wp_security->configs->get_value('aiowps_enable_comment_captcha') == '1') {
				if (!is_user_logged_in()) {
					if ($aio_wp_security->configs->get_value('aiowps_default_recaptcha')) {
						add_action('wp_head', array($this, 'add_recaptcha_script'));
					}
					add_action('comment_form_after_fields', array($this, 'insert_captcha_question_form'), 1);
					add_action('comment_form_logged_in_after', array($this, 'insert_captcha_question_form'), 1);
					add_filter('preprocess_comment', array($this, 'process_comment_post'));
				}
			}
		}

		// For BuddyPress registration CAPTCHA feature
		if ($aio_wp_security->configs->get_value('aiowps_enable_bp_register_captcha') == '1') {
			add_action('bp_account_details_fields', array($this, 'insert_captcha_question_form'));
			add_action('bp_signup_validate', array($this, 'buddy_press_signup_validate_captcha'));
		}


		// For feature which displays logged in users
		$aio_wp_security->user_login_obj->update_users_online_transient();

		// For block fake Googlebots feature
		if ($aio_wp_security->configs->get_value('aiowps_block_fake_googlebots') == '1') {
			include_once(AIO_WP_SECURITY_PATH.'/classes/wp-security-bot-protection.php');
			AIOWPSecurity_Fake_Bot_Protection::block_fake_googlebots();
		}

		// For 404 event logging
		if ($aio_wp_security->configs->get_value('aiowps_enable_404_logging') == '1') {
			add_action('wp_head', array($this, 'check_404_event'));
		}
		// Add more tasks that need to be executed at init time

	} // end _construct()

	public function aiowps_disable_xmlrpc_pingback_methods($methods) {
	   unset($methods['pingback.ping']);
	   unset($methods['pingback.extensions.getPingbacks']);
	   return $methods;
	}

	public function aiowps_remove_x_pingback_header($headers) {
	   unset($headers['X-Pingback']);
	   return $headers;
	}

	public function spam_detect_process_comment_post($comment_id, $comment_approved) {
		if ("spam" === $comment_approved) {
			$this->block_comment_ip($comment_id);
		}

	}

	public function process_transition_comment_status($new_status, $old_status, $comment) {
		if ('spam' == $new_status) {
			$this->block_comment_ip($comment->comment_ID);
		}

	}

	/**
	 * Will check auto-spam blocking settings and will add IP to blocked table accordingly
	 *
	 * @param int $comment_id
	 */
	public function block_comment_ip($comment_id) {
		global $aio_wp_security, $wpdb;
		$comment_obj = get_comment($comment_id);
		$comment_ip = $comment_obj->comment_author_IP;
		//Get number of spam comments from this IP
		$sql = $wpdb->prepare("SELECT * FROM $wpdb->comments
				WHERE comment_approved = 'spam'
				AND comment_author_IP = %s
				", $comment_ip);
		$comment_data = $wpdb->get_results($sql, ARRAY_A);
		$spam_count = count($comment_data);
		$min_comment_before_block = $aio_wp_security->configs->get_value('aiowps_spam_ip_min_comments_block');
		if (!empty($min_comment_before_block) && $spam_count >= ($min_comment_before_block - 1)) {
			AIOWPSecurity_Blocking::add_ip_to_block_list($comment_ip, 'spam');
		}
	}

	public function remove_standard_wp_meta_widget() {
		unregister_widget('WP_Widget_Meta');
	}

	public function remove_wp_generator_meta_info() {
		return '';
	}

	public function remove_wp_css_js_meta_info($src) {
		global $wp_version;
		static $wp_version_hash = null; // Cache hash value for all function calls

		// Replace only version number of assets with WP version
		if (strpos($src, 'ver=' . $wp_version) !== false) {
			if (!$wp_version_hash) {
				$wp_version_hash = wp_hash($wp_version);
			}
			// Replace version number with computed hash
			$src = add_query_arg('ver', $wp_version_hash, $src);
		}
		return $src;
	}

	public function do_404_lockout_tasks() {
		global $aio_wp_security;
		$redirect_url = $aio_wp_security->configs->get_value('aiowps_404_lock_redirect_url'); //This is the redirect URL for blocked users

		$visitor_ip = AIOWPSecurity_Utility_IP::get_user_ip_address();

		$is_locked = AIOWPSecurity_Utility::check_locked_ip($visitor_ip);

		if ($is_locked) {
			//redirect blocked user to configured URL
			AIOWPSecurity_Utility::redirect_to_url($redirect_url);
		} else {
			//allow through
		}
	}

	/**
	 * Renders CAPTCHA on form produced by the wp_login_form() function, ie, custom wp login form
	 *
	 * @global type $aio_wp_security
	 * @param type $cust_html_code
	 * @return string
	 */
	public function insert_captcha_custom_login($cust_html_code) {
		global $aio_wp_security;
		if ($aio_wp_security->is_login_lockdown_by_const()) {
			return '';
		}

		if ($aio_wp_security->configs->get_value('aiowps_default_recaptcha')) {
			$site_key = esc_html($aio_wp_security->configs->get_value('aiowps_recaptcha_site_key'));
			$cap_form = '<div class="g-recaptcha-wrap" style="padding:10px 0 10px 0"><div class="g-recaptcha" data-sitekey="'.$site_key.'"></div></div>';
			$cust_html_code .= $cap_form;
			return $cust_html_code;
		} else {
			$cap_form = '<p class="aiowps-captcha"><label>'.__('Please enter an answer in digits:', 'all-in-one-wp-security-and-firewall').'</label>';
			$cap_form .= '<div class="aiowps-captcha-equation"><strong>';
			$maths_question_output = $aio_wp_security->captcha_obj->generate_maths_question();
			$cap_form .= $maths_question_output . '</strong></div></p>';

			$cust_html_code .= $cap_form;
			return $cust_html_code;
		}
	}

	public function insert_captcha_question_form_multi() {
		global $aio_wp_security;
		$aio_wp_security->captcha_obj->display_captcha_form();
	}

	public function process_signup_form_multi($result) {
		global $aio_wp_security;
		// Check if CAPTCHA enabled
		$verify_captcha = $aio_wp_security->captcha_obj->verify_captcha_submit();
		if (false === $verify_captcha) {
			// wrong answer was entered
			$result['errors']->add('generic', __('<strong>ERROR</strong>: Your answer was incorrect - please try again.', 'all-in-one-wp-security-and-firewall'));
		}
		return $result;
	}

	public function insert_captcha_question_form() {
		global $aio_wp_security;

		if ($aio_wp_security->configs->get_value('aiowps_default_recaptcha')) {

			// WooCommerce "my account" page needs special consideration, ie,
			// need to display two Google reCAPTCHA forms on same page (for login and register forms)
			// For this case we use the "explicit" reCAPTCHA display
			$calling_hook = current_filter();
			$site_key = esc_html($aio_wp_security->configs->get_value('aiowps_recaptcha_site_key'));
			if ('woocommerce_login_form' == $calling_hook || 'woocommerce_lostpassword_form' == $calling_hook) {
				echo '<div class="g-recaptcha-wrap" style="padding:10px 0 10px 0"><div id="woo_recaptcha_1" class="g-recaptcha" data-sitekey="'.$site_key.'"></div></div>';
				return;
			}

			if ('woocommerce_register_form' == $calling_hook) {
				echo '<div class="g-recaptcha-wrap" style="padding:10px 0 10px 0"><div id="woo_recaptcha_2" class="g-recaptcha" data-sitekey="'.$site_key.'"></div></div>';
				return;
			}

			// For all other forms simply display Google reCAPTCHA as per normal
			$aio_wp_security->captcha_obj->display_recaptcha_form();
		} else {
			// Display plain maths CAPTCHA form
			$aio_wp_security->captcha_obj->display_captcha_form();
		}

	}

	public function insert_honeypot_hidden_field() {
		$honey_input = '<p style="display: none;"><label>'.__('Enter something special:', 'all-in-one-wp-security-and-firewall').'</label>';
		$honey_input .= '<input name="aio_special_field" type="text" id="aio_special_field" class="aio_special_field" value="" /></p>';
		echo $honey_input;
	}

	/**
	 * Shows application password disabled message on user edit profile page.
	 * If logged user is admin showing the Change Setting option.
	 *
	 * @return void
	 */
	public function show_disabled_application_password_message() {
		if (is_user_logged_in() && is_admin()) {
			$disabled_message =	'<h2>'.__('Application passwords', 'all-in-one-wp-security-and-firewall').'</h2>';
			$disabled_message .= '<table class="form-table" role="presentation">';
			$disabled_message .= '<tbody>';
			$disabled_message .= '<tr id="disable-password">';
			$disabled_message .= '<th>'.__('Disabled').'</th>';
			$disabled_message .= '<td>'.htmlspecialchars(__('Application passwords have been disabled by All In One WP Security & Firewall plugin.', 'all-in-one-wp-security-and-firewall'));
			if (current_user_can(AIOWPSEC_MANAGEMENT_PERMISSION)) {
				$aiowps_addtional_setting_url = 'admin.php?page=aiowpsec_userlogin&tab=additional';
				$change_setting_url = is_multisite() ? network_admin_url($aiowps_addtional_setting_url) : admin_url($aiowps_addtional_setting_url);
				$disabled_message .= '<p><a href="'.$change_setting_url.'"  class="button">'.__('Change setting', 'all-in-one-wp-security-and-firewall').'</a></p>';
			} else {
				$disabled_message .= ' '.__('Site admin can only change this setting.', 'all-in-one-wp-security-and-firewall');
			}
			$disabled_message .= '</td>';
			$disabled_message .= '</tr>';
			$disabled_message .= '<tbody>';
			$disabled_message .= '</table>';
			echo $disabled_message;
		}
	}

	public function process_comment_post($comment) {
		global $aio_wp_security;
		if (is_user_logged_in()) {
				return $comment;
		}

		// Don't process CAPTCHA for comment replies inside admin menu
		if (isset($_REQUEST['action']) && 'replyto-comment' == $_REQUEST['action'] && (check_ajax_referer('replyto-comment', '_ajax_nonce', false) || check_ajax_referer('replyto-comment', '_ajax_nonce-replyto-comment', false))) {
			return $comment;
		}

		// Don't do CAPTCHA for pingback/trackback
		if ('' != $comment['comment_type'] && 'comment' != $comment['comment_type'] && 'review' != $comment['comment_type']) {
			return $comment;
		}

		$verify_captcha = $aio_wp_security->captcha_obj->verify_captcha_submit();
		if (false === $verify_captcha) {
			//Wrong answer
			wp_die(__('Error: You entered an incorrect CAPTCHA answer. Please go back and try again.', 'all-in-one-wp-security-and-firewall'));
		} else {
			return($comment);
		}
	}

	/**
	 * Process the main Wordpress account lost password login form post
	 * Called by wp hook "lostpassword_post"
	 */
	public function process_lost_password_form_post() {
		global $aio_wp_security;

		// Workaround - the WooCommerce lost password form also uses the same "lostpassword_post" hook.
		// We don't want to process woo forms here so ignore if this is a woo lost password $_POST
		if (!array_key_exists('woocommerce-lost-password-nonce', $_POST)) {
			$verify_captcha = $aio_wp_security->captcha_obj->verify_captcha_submit();
			if (false === $verify_captcha) {
				add_filter('allow_password_reset', array($this, 'add_lostpassword_captcha_error_msg'));
			}
		}
	}

	public function add_lostpassword_captcha_error_msg() {
		//Insert an error just before the password reset process kicks in
		return new WP_Error('aiowps_captcha_error', __('<strong>ERROR</strong>: Your answer was incorrect - please try again.', 'all-in-one-wp-security-and-firewall'));
	}

	public function check_404_event() {
		if (is_404()) {
			//This means a 404 event has occurred - let's log it!
			AIOWPSecurity_Utility::event_logger('404');
		}

	}

	public function buddy_press_signup_validate_captcha() {
		global $bp, $aio_wp_security;
		// Check CAPTCHA if required
		$verify_captcha = $aio_wp_security->captcha_obj->verify_captcha_submit();
		if (false === $verify_captcha) {
			// wrong answer was entered
			$bp->signup->errors['aiowps-captcha-answer'] = __('Your CAPTCHA answer was incorrect - please try again.', 'all-in-one-wp-security-and-firewall');
		}
		return;
	}

	public function aiowps_validate_woo_login_or_reg_captcha($errors) {
		global $aio_wp_security;
		$locked = $aio_wp_security->user_login_obj->check_locked_user();
		if (!empty($locked)) {
			$errors->add('authentication_failed', __('<strong>ERROR</strong>: Your IP address is currently locked please contact the administrator!', 'all-in-one-wp-security-and-firewall'));
			return $errors;
		}

		$verify_captcha = $aio_wp_security->captcha_obj->verify_captcha_submit();
		if (false === $verify_captcha) {
			// wrong answer was entered
			$errors->add('authentication_failed', __('<strong>ERROR</strong>: Your answer was incorrect - please try again.', 'all-in-one-wp-security-and-firewall'));
		}
		return $errors;

	}

	/**
	 * Process the WooCommerce lost password login form post
	 * Called by wp hook "lostpassword_post"
	 */
	public function process_woo_lost_password_form_post() {
		global $aio_wp_security;

		if (isset($_POST['woocommerce-lost-password-nonce'])) {
			$verify_captcha = $aio_wp_security->captcha_obj->verify_captcha_submit();
			if (false === $verify_captcha) {
				add_filter('allow_password_reset', array($this, 'add_lostpassword_captcha_error_msg'));
			}
		}
	}

	/**
	 * Reapply htaccess rule or dismiss the related notice.
	 *
	 * @return void
	 */
	public function reapply_htaccess_rules() {
		if (isset($_REQUEST['aiowps_reapply_htaccess'])) {
			global $aio_wp_security;

			if (strip_tags($_REQUEST['aiowps_reapply_htaccess']) == 1) {
				if (!wp_verify_nonce($_GET['_wpnonce'], 'aiowps-reapply-htaccess-yes')) {
					$aio_wp_security->debug_logger->log_debug("Nonce check failed on reapply .htaccess rule!", 4);
					// Temp
					die('nonce issue');
					return;
				}
				include_once('wp-security-installer.php');
				if (AIOWPSecurity_Installer::reactivation_tasks()) {
					$aio_wp_security->debug_logger->log_debug('The AIOS .htaccess rules were successfully re-inserted.');
					$_SESSION['reapply_htaccess_rules_action_result'] = '1';//Success indicator.
					// Can't echo to the screen here. It will create an header already sent error.
				} else {
					$aio_wp_security->debug_logger->log_debug('AIOS encountered an error when trying to write to your .htaccess file. Please check the logs.', 5);
					$_SESSION['reapply_htaccess_rules_action_result'] = '2';//fail indicator.
					// Can't echo to the screen here. It will create an header already sent error.
				}
			} elseif (strip_tags($_REQUEST['aiowps_reapply_htaccess']) == 2) {
				if (!wp_verify_nonce($_GET['_wpnonce'], 'aiowps-reapply-htaccess-no')) {
					$aio_wp_security->debug_logger->log_debug("Nonce check failed on dismissing reapply .htaccess rule notice!", 4);
					return;
				}
				// Don't re-write the rules and just delete the temp config item
				delete_option('aiowps_temp_configs');
			}
		}
	}

	/**
	 * Displays a notice message if the entered recatcha site key is wrong.
	 */
	public function google_recaptcha_notice() {
		global $aio_wp_security;

		if (($aio_wp_security->is_admin_dashboard_page() || $aio_wp_security->is_plugin_admin_page() || $aio_wp_security->is_aiowps_admin_page()) && !$aio_wp_security->is_aiowps_google_recaptcha_tab_page()) {
			$recaptcha_tab_url = 'admin.php?page='.AIOWPSEC_BRUTE_FORCE_MENU_SLUG.'&tab=tab3';
			echo '<div class="notice notice-warning"><p>';
			/* translators: %s: Admin Dashboard > WP Security > Brute Force > Login CAPTCHA Tab Link */
			printf(__('Your Google reCAPTCHA site key is wrong. Please fill the correct reCAPTCHA keys %s to use the Google reCAPTCHA feature.', 'all-in-one-wp-security-and-firewall'), '<a href="'.esc_url($recaptcha_tab_url).'">'.__('here', 'all-in-one-wp-security-and-firewall').'</a>');
			echo '</p></div>';
		}
	}

	/**
	 * Displays a notice message if the plugin was reactivated after being initially deactivated.
	 * Gives users option of re-applying the AIOS rules which were deleted from the .htaccess after deactivation.
	 */
	public function reapply_htaccess_rules_notice() {
		if (get_option('aiowps_temp_configs') !== false) {
			$reapply_htaccess_yes_url = wp_nonce_url('admin.php?page='.AIOWPSEC_MENU_SLUG_PREFIX.'&aiowps_reapply_htaccess=1', 'aiowps-reapply-htaccess-yes');
			$reapply_htaccess_no_url  = wp_nonce_url('admin.php?page='.AIOWPSEC_MENU_SLUG_PREFIX.'&aiowps_reapply_htaccess=2', 'aiowps-reapply-htaccess-no');
			echo '<div class="updated"><p>'.htmlspecialchars(__('Would you like All In One WP Security & Firewall to re-insert the security rules in your .htaccess file which were cleared when you deactivated the plugin?', 'all-in-one-wp-security-and-firewall')).'&nbsp;&nbsp;<a href="'.esc_url($reapply_htaccess_yes_url).'" class="button-primary">'.__('Yes', 'all-in-one-wp-security-and-firewall').'</a>&nbsp;&nbsp;<a href="'.esc_url($reapply_htaccess_no_url).'" class="button-primary">'.__('No', 'all-in-one-wp-security-and-firewall').'</a></p></div>';
		}
	}

	/**
	 * This is a fix for cases when the password reset URL in the email was not decoding all html entities properly
	 *
	 * @param string $message
	 * @return string
	 */
	public function decode_reset_pw_msg($message) {
		$message = html_entity_decode($message);
		return $message;
	}

	public function modify_registration_page_messages($errors) {
		if (isset($_GET['checkemail']) && 'registered' == $_GET['checkemail']) {
			if (is_wp_error($errors)) {
				$errors->remove('registered');
				$pending_approval_msg = __('Your registration is pending approval.', 'all-in-one-wp-security-and-firewall');
				$pending_approval_msg = apply_filters('aiowps_pending_registration_message', $pending_approval_msg);
				$errors->add('registered', $pending_approval_msg, array('registered' => 'message'));
			}
		}
		return $errors;
	}

	/**
	 * Re-wrote code which checks for REST API requests
	 * Below uses the "rest_api_init" action hook to check for REST requests.
	 * The code will block "unauthorized" requests whilst allowing genuine requests.
	 * (P. Petreski June 2018)
	 *
	 * @return void
	 */
	public function check_rest_api_requests() {
		$rest_user = wp_get_current_user();
		if (empty($rest_user->ID)) {
			$error_message = apply_filters('aiowps_rest_api_error_message', __('You are not authorized to perform this action.', 'disable-wp-rest-api'));
			wp_die($error_message);
		}
	}

	/**
	 * Enqueues the Google reCAPTCHA API URL in the wp_head for general pages
	 * Caters for scenarios when reCAPTCHA used on wp comments or custom wp login form
	 */
	public function add_recaptcha_script() {
		// Enqueue the reCAPTCHA API url

		// Do NOT enqueue if this is the main WooCommerce account login page because for WooCommerce page we "explicitly" render the reCAPTCHA widget
		$is_woo = false;

		// We don't want to load for woo account page because we have a special function for this
		if (function_exists('is_account_page')) {
			// Check if this a WooCommerce account page
			$is_woo = is_account_page();
		}

		if (empty($is_woo)) {
			// Only enqueue when not a WooCommerce page
			wp_enqueue_script('google-recaptcha', 'https://www.google.com/recaptcha/api.js?hl=' . AIOWPSecurity_Captcha::get_google_recaptcha_compatible_site_locale(), array(), AIO_WP_SECURITY_VERSION);
		}
	}

	/**
	 * Shows the firewall notice
	 *
	 * @return void
	 */
	public function do_firewall_notice() {
		
		$firewall_setup = AIOWPSecurity_Firewall_Setup_Notice::get_instance();
		$firewall_setup->start_firewall_setup();

	}
}
