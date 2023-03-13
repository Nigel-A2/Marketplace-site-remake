<?php

if (!defined('ABSPATH')) die('No direct access.');

if (!class_exists('HOTP')) require_once(__DIR__.'/hotp-php-master/hotp.php');
if (!class_exists('Base32')) require_once(__DIR__.'/Base32/Base32.php');

class Simba_TFA_Provider_totp {
	
	/**
	 * Simba 2FA object
	 *
	 * @var object instance of Simba_Two_Factor_Authentication(_version)
	 */
	private $tfa;
	
	/**
	 * OTP helper object
	 *
	 * @var object instance of HOTP
	 */
	private $otp_helper;
	
	/**
	 * Forward counter window to check number of times
	 *
	 * @var int
	 */
	private $check_forward_counter_window;
	
	/**
	 * Salt prefix
	 *
	 * @var string
	 */
	private $salt_prefix;
	
	/**
	 * Password prefix
	 *
	 * @var string
	 */
	private $pw_prefix;
	
	/**
	 * Time window size
	 *
	 * @var int
	 */
	private $time_window_size;
	
	/**
	 * Check back time window
	 *
	 * @var int
	 */
	private $check_back_time_windows;
	
	/**
	 * Check forward time windows
	 *
	 * @var int
	 */
	private $check_forward_time_windows;
	
	/**
	 * OTP length
	 *
	 * @var int
	 */
	private $otp_length = 6;
	
	/**
	 * Emergency codes length
	 *
	 * @var int
	 */
	private $emergency_codes_length = 8;
	
	/**
	 * Default HMAC type
	 *
	 * @var string
	 */
	public $default_hmac = 'totp';
	
	/**
	 * Settings saved flag
	 *
	 * @var boolean
	 */
	private $settings_saved = false;

	/**
	 * Class constructor
	 *
	 * @param Object - main Simba_Two_Factor_Authentication(_version) plugin class
	 */
	public function __construct($tfa) {
		$this->tfa = $tfa;
		
		$this->otp_helper = new HOTP();
		
		add_action('plugins_loaded', array($this, 'plugins_loaded'));
		
		add_action('admin_init', array($this, 'admin_init'));
		
		if (!is_admin()) {
			add_action('init', array($this, 'check_possible_reset'));
		}
		
		// Potentially show off-sync message for hotp
		add_action('admin_notices', array($this, 'tfa_show_hotp_off_sync_message'));
	}
	
	/**
	 * Return whether or not this class detected and saved new settings
	 *
	 * @return Boolean
	 */
	public function were_settings_saved() {
		return $this->settings_saved;
	}
	
	/**
	 * Runs upon the WP action admin_init
	 */
	public function admin_init() {
		
		$this->check_possible_reset();
		
		global $current_user;
		
		if (!empty($_REQUEST['_tfa_activate_nonce']) && !empty($_POST['tfa_enable_tfa']) && wp_verify_nonce($_REQUEST['_tfa_activate_nonce'], 'tfa_activate') && !empty($_GET['settings-updated'])) {
			$this->tfa->change_tfa_enabled_status($current_user->ID, $_POST['tfa_enable_tfa']);
			$this->settings_saved = true;
		}
		
		if (!empty($_REQUEST['_tfa_algorithm_nonce']) && !empty($_POST['tfa_algorithm_type']) && !empty($_GET['settings-updated']) && wp_verify_nonce($_REQUEST['_tfa_algorithm_nonce'], 'tfa_algorithm')) {
			
			$old_algorithm = $this->get_user_otp_algorithm($current_user->ID);
			
			if ($old_algorithm != $_POST['tfa_algorithm_type']) {
				$this->changeUserAlgorithmTo($current_user->ID, $_POST['tfa_algorithm_type']);
			}
			
			$this->settings_saved = true;
		}
		
		if (!empty($_GET['warning_button_clicked']) && !empty($_REQUEST['resyncnonce']) && wp_verify_nonce($_REQUEST['resyncnonce'], 'tfaresync')) {
			delete_user_meta($current_user->ID, 'tfa_hotp_off_sync');
		}
		
	}
	
	/**
	 * Enqueue adding of JavaScript for footer
	 */
	public function add_footer() {
		
		static $added_footer = false;
		if ($added_footer) return;
		$added_footer = true;
		
		$qr_script_file = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? 'jquery-qrcode.js' : 'jquery-qrcode.min.js';
		
		$qr_script_ver = (defined('WP_DEBUG') && WP_DEBUG) ? time() : filemtime($this->tfa->includes_dir()."/jquery-qrcode/$qr_script_file");
		
		wp_register_script('jquery-qrcode', $this->tfa->includes_url()."/jquery-qrcode/$qr_script_file", array('jquery'), $qr_script_ver);
		
		$script_ver = (defined('WP_DEBUG') && WP_DEBUG) ? time() : filemtime($this->tfa->includes_dir()."/totp.js");
		
		// Adds the necessary JavaScript for rendering and updating QR codes, and handling trusted devices removal in the admin area
		wp_enqueue_script('simba-tfa-totp', $this->tfa->includes_url()."/totp.js", array('jquery-qrcode'), $script_ver);
		
		wp_localize_script('simba-tfa-totp', 'simbatfa_totp', $this->translation_strings());

	}
	
	/**
	 * Get textual strings used from JavaScript
	 *
	 * @return Array
	 */
	private function translation_strings() {
		
		// It's possible that FORCE_ADMIN_SSL will make that SSL, whilst the user is on the front-end having logged in over non-SSL - and as a result, their login cookies won't get sent, and they're not registered as logged in.
		$ajax_url = admin_url('admin-ajax.php');
		$also_try = '';
		if (!is_admin() && substr(strtolower($ajax_url), 0, 6) == 'https:' && !is_ssl()) {
			$also_try = 'http:'.substr($ajax_url, 6);
		}
		
		return apply_filters('simba_tfa_totp_translation_strings', array(
			'ajax_url' => $ajax_url,
			'updating' => __('Updating...', 'all-in-one-wp-security-and-firewall'),
			'tfa_shared_nonce' => wp_create_nonce('tfa_shared_nonce'),
			'also_try' => $also_try,
			'response' => __('Response:', 'all-in-one-wp-security-and-firewall'),
		));
	}
	
	/**
	 * Return a link to refresh the current OTP code
	 *
	 * @return String
	 */
	public function refresh_current_otp_link() {
		return '<a href="#" class="simbaotp_refresh">'.__('(update)', 'all-in-one-wp-security-and-firewall').'</a>';
	}
	
	/**
	 * Echo the radio buttons for changing between TOTP/HOTP
	 * 
	 * TODO: Hide this choice on new installs (TOTP only)
	 *
	 * @param Integer $user_id
	 */
	protected function print_algorithm_choice_radios($user_id) {
		if (!$user_id) return;
		
		$types = array(
			'totp' => __('TOTP (time based - most common algorithm; used by Google Authenticator)', 'all-in-one-wp-security-and-firewall'),
			'hotp' => __('HOTP (event based)', 'all-in-one-wp-security-and-firewall')
		); 
		
		$setting = $this->get_user_otp_algorithm($user_id);
		
		foreach ($types as $id => $name) {
			print '<input type="radio" id="tfa_algorithm_type_'.esc_attr($id).'" name="tfa_algorithm_type" value="'.$id.'" '.($setting == $id ? 'checked="checked"' :'').'> <label for="tfa_algorithm_type_'.esc_attr($id).'">'.$name."</label><br>\n";
		}
	}
	
	/**
	 * Print out the advanced settings box - choice of algorithm
	 *
	 * @param Boolean|Callable $submit_button_callback - if not a callback, then <form> tags will be added
	 */
	public function advanced_settings_box($submit_button_callback = false) {
		
		global $current_user;
		$algorithm_type = $this->get_user_otp_algorithm($current_user->ID);
		
		?>
		<h2 id="tfa_advanced_heading" style="clear:both;"><?php _e('Advanced settings', 'all-in-one-wp-security-and-firewall'); ?></h2>
		
		<div id="tfa_advanced_box" class="tfa_settings_form" style="margin-top: 20px;">
		
		<?php if (false === $submit_button_callback) { ?>
			<form method="post" action="<?php print esc_url(add_query_arg('settings-updated', 'true', $_SERVER['REQUEST_URI'])); ?>">
			<?php wp_nonce_field('tfa_algorithm', '_tfa_algorithm_nonce', false, true); ?>
		<?php } ?>
			
		<?php _e('Choose which algorithm for One Time Passwords you want to use.', 'all-in-one-wp-security-and-firewall'); ?>
		<p>
		<?php
		$this->print_algorithm_choice_radios($current_user->ID);
		if ('hotp' == $algorithm_type) {
			$counter = $this->getUserCounter($current_user->ID);
			print '<br>'.__('Your counter on the server is currently on', 'all-in-one-wp-security-and-firewall').': '.$counter;
		}
		?>
		
		</p>
		<?php if (false === $submit_button_callback) { submit_button(); echo '</form>'; } else { call_user_func($submit_button_callback); } ?>
		
		</div>
		<?php
	}
	
	/**
	 * Return an HTML snippet for the current OTP code
	 *
	 * @param Integer|Boolean $user_id
	 *
	 * @return String
	 */
	public function current_otp_code($user_id = false) {
		global $current_user;
		if (false == $user_id) $user_id = $current_user->ID;
		$tfa_priv_key_64 = get_user_meta($user_id, 'tfa_priv_key_64', true);
		if (!$tfa_priv_key_64) $tfa_priv_key_64 = $this->addPrivateKey($user_id);
		$time_now = time();
		$refresh_after = 30 - ($time_now % 30);
		return '<span class="simba_current_otp" data-refresh_after="'.$refresh_after.'">'.$this->generateOTP($user_id, $tfa_priv_key_64).'</span>';
	}
	
	/**
	 * Runs upon the WP 'init' action - check for possible private key reset request from the user
	 */
	public function check_possible_reset() {
		if (!empty($_GET['simbatfa_priv_key_reset']) && !empty($_REQUEST['nonce']) && wp_verify_nonce($_REQUEST['nonce'], 'simbatfa_reset_private_key')) {
			$this->reset_private_key_and_emergency_codes();
			exit;
		}
	}
	
	/**
	 * Remove private key and emergency codes for the specified (or logged-in) user
	 *
	 * @param Boolean|Integer $user_id	- WP user ID, or false for the currently logged-in user
	 * @param Boolean		  $redirect - if this is not false, then a redirection will occur - where to depends upon the value of $_REQUEST['noredirect']
	 */
	public function reset_private_key_and_emergency_codes($user_id = false, $redirect = true) {
		
		if (!$user_id) {
			global $current_user;
			$user_id = $current_user->ID;
		}
		
		delete_user_meta($user_id, 'tfa_priv_key_64');
		delete_user_meta($user_id, 'simba_tfa_emergency_codes_64');
		
		if (!$redirect) return;
		
		if (empty($_REQUEST['noredirect'])) {
			// TODO: Re-factoring
			wp_safe_redirect(admin_url('admin.php').'?page='. $this->tfa->get_user_settings_page_slug() .'&settings-updated=1');
		} else {
			$url = (is_ssl() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . remove_query_arg(array('simbatfa_priv_key_reset', 'noredirect', 'nonce'));
			wp_redirect(esc_url_raw($url));
		}
	}
	
	/**
	 * Return HTML for a link to reset the current user's private key
	 *
	 * @return String
	 */
	public function reset_link() {
		
		// TODO: Refactoring
		$url_base = is_admin() ? admin_url('admin.php').'?page='. $this->tfa->get_user_settings_page_slug() .'&settings-updated=1' : (( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST']);
		
		$add_query_args = array('simbatfa_priv_key_reset' => 1);
		
		if (!is_admin()) $add_query_args['noredirect'] = 1;
		
		$url = $url_base.add_query_arg($add_query_args);
		
		$url = wp_nonce_url($url, 'simbatfa_reset_private_key', 'nonce');
		
		return '<a href="javascript:if(confirm(\''.__('Warning: if you reset this key you will have to update your apps with the new one. Are you sure you want this?', 'all-in-one-wp-security-and-firewall').'\')) { window.location = \''.esc_js($url).'\'; }">'.__('Reset private key', 'all-in-one-wp-security-and-firewall').'</a>';
		
	}
	
	/**
	 * Output the current codes box
	 *
	 * @param Boolean|Integer $user_id
	 */
	public function current_codes_box($user_id = false) {
		
		global $current_user;
		if (false == $user_id) $user_id = $current_user->ID;
		
		$admin = is_admin();
		
		$this->add_footer();
		
		$url = preg_replace('/^https?:\/\//i', '', site_url());
		
		// TODO Replace this with an appropriate method
		$tfa_priv_key_64 = get_user_meta($user_id, 'tfa_priv_key_64', true);
		if (!$tfa_priv_key_64) $tfa_priv_key_64 = $this->addPrivateKey($user_id);
		
		$tfa_priv_key = trim($this->getPrivateKeyPlain($tfa_priv_key_64, $user_id), "\x00..\x1F");
		
		$tfa_priv_key_32 = Base32::encode($tfa_priv_key);
		
		$algorithm_type = $this->get_user_otp_algorithm($user_id);
		
		if ($admin && $current_user->ID != $user_id) {
			$user = get_user_by('id', $user_id);
			$user_descrip = htmlspecialchars($user->user_nicename.' - '.$user->user_email);
			echo '<h2>'.sprintf(__('Current codes (login: %s)', 'all-in-one-wp-security-and-firewall'), $user_descrip).'</h2>';
		}
		
		?>
		<div class="postbox" style="clear:both;">
		
		<?php if ($admin) { ?>
			<h3 style="padding: 10px 6px 0px; margin:4px 0 0; cursor: default;">
			<span style="cursor: default;"><?php echo __('Current one-time password', 'all-in-one-wp-security-and-firewall').' ';
			if ($current_user->ID == $user_id) { echo $this->refresh_current_otp_link(); } ?>
				</span>
				<div class="inside">
					<p><strong style="font-size: 3em;"><?php echo $this->current_otp_code($user_id); ?></strong></p>
				</div>
				</h3>
		<?php } else { ?>
			
			<div class="inside">
			<p class="simbatfa-frontend-current-otp" style="font-size: 1.5em; margin-top:6px;">
				<strong><?php echo __('Current one-time password', 'all-in-one-wp-security-and-firewall').' '.$this->refresh_current_otp_link(); ?></strong> :
				
				<?php
				// TODO: Compare this with what's in current_otp_code() - why are we not using that?
				$time_now = time();
				$refresh_after = 30 - ($time_now % 30);
				?><span class="simba_current_otp" data-refresh_after="<?php echo $refresh_after; ?>"><?php print $this->generateOTP($user_id, $tfa_priv_key_64); ?></span>
			
			</p>
			</div>
			
			<?php } ?>
			
			<?php if ($admin) { ?>
				<h3 style="padding-left: 10px; cursor: default;">
				<span style="cursor: default;"><?php _e('Setting up - either scan the code, or type in the private key', 'all-in-one-wp-security-and-firewall'); ?></span>
				</h3>
			<?php } else { ?>
				<h2><?php _e('Setting up', 'all-in-one-wp-security-and-firewall'); ?></h2>
			<?php } ?>
			
			<div class="inside">
			<p>
			<?php
			_e('For OTP apps that support using a camera to scan a setup code (below), that is the quickest way to set the app up (e.g. with Duo Mobile, Google Authenticator).', 'all-in-one-wp-security-and-firewall');
			echo ' ';
			_e('Otherwise, you can type the textual private key (shown below) into your app. Always keep private keys secret.', 'all-in-one-wp-security-and-firewall');
			?>
			
			<?php printf(__('You are currently using %s, %s', 'all-in-one-wp-security-and-firewall'), strtoupper($algorithm_type), ($algorithm_type == 'totp') ? __('a time based algorithm', 'all-in-one-wp-security-and-firewall') : __('an event based algorithm', 'all-in-one-wp-security-and-firewall')); ?>.
			</p>
			
			<?php $qr_url = $this->tfa_qr_code_url($algorithm_type, $url, $tfa_priv_key, $user_id); ?>
			<div style="float: left; padding-right: 20px;" class="simbaotp_qr_container" data-qrcode="<?php echo esc_attr($qr_url); ?>"></div>
			
			<p>
			<?php
			$this->print_private_keys('full', $user_id);
			if ($current_user->ID == $user_id) {
				echo $this->reset_link($admin);
			} else {
				echo '<a id="tfa-reset-privkey-for-user" data-user_id="'.$user_id.'" href="#">'.__('Reset private key', 'all-in-one-wp-security-and-firewall').'</a>';
			}
			?>
			</p>
			
			<?php
			if ($admin || false !== apply_filters('simba_tfa_emergency_codes_user_settings', false, $user_id)) {
				?>
				
				<div style="min-height: 100px;">
				<h3 class="normal" style="cursor: default"><?php _e('Emergency codes', 'all-in-one-wp-security-and-firewall'); ?></h3>
				<?php
				$default_text = '<a href="'.esc_url($this->tfa->get_premium_version_url()).'">'.__('One-time emergency codes are a feature of the Premium version of this plugin.', 'all-in-one-wp-security-and-firewall').'</a>';
				echo apply_filters('simba_tfa_emergency_codes_user_settings', $default_text, $user_id);
				?>
				</div>
				
			<?php } ?>
			</div>
		
		</div>
		<?php
	}
	
	/**
	 * Print out HTML showing the specified user's private key
	 *
	 * @param String $type
	 * @param Boolean|Integer $user_id
	 */
	public function print_private_keys($type = 'full', $user_id = false) {
		
		global $current_user;
		if ($user_id == false) $user_id = $current_user->ID;
		
		$tfa_priv_key_64 = get_user_meta($user_id, 'tfa_priv_key_64', true);
		if (!$tfa_priv_key_64) $tfa_priv_key_64 = $this->addPrivateKey($user_id);
		
		$tfa_priv_key = trim($this->getPrivateKeyPlain($tfa_priv_key_64, $user_id), "\x00..\x1F");
		
		$tfa_priv_key_32 = Base32::encode($tfa_priv_key);
		
		// The first (base32) private key used to have the description "base 32 - used by Google Authenticator and Authy", and the base64 version was just described as "private key". But basically the former is what everything uses.
		//<strong>Private key:</strong> htmlspecialchars($tfa_priv_key)
		if ('full' == $type) {
			?>
			<strong><?php _e('Private key:', 'two-factor-authentication');?></strong>
			<?php echo htmlspecialchars($tfa_priv_key_32); ?><br>
			<?php
		} elseif ('plain' == $type) {
			echo htmlspecialchars($tfa_priv_key);
		} elseif ('base32' == $type) {
			echo htmlspecialchars($tfa_priv_key_32);
		} elseif ('base64' == $type) {
			echo htmlspecialchars($tfa_priv_key_64);
		}
	}
	
	/**
	 * Return the URL for a QR code image
	 *
	 * @param String $algorithm_type - 'totp' or 'hotp'
	 * @param String $url
	 * @param String $tfa_priv_key
	 * @param Boolean|Integer $user_id
	 *
	 * @return String
	 */
	public function tfa_qr_code_url($algorithm_type, $url, $tfa_priv_key, $user_id = false) {
		global $current_user;
		
		$user = (false == $user_id) ? $current_user : get_user_by('id', $user_id);
		
		$encode = 'otpauth://'.$algorithm_type.'/'.$url.':'.rawurlencode($user->user_login).'?secret='.Base32::encode($tfa_priv_key).'&issuer='.$url.'&counter='.$this->getUserCounter($user->ID);
		
		return $encode;
	}
	
	/**
	 * See if HOTP is off sync, and if show, print out a message
	 */
	public function tfa_show_hotp_off_sync_message() {
		
		global $current_user;
		$is_off_sync = get_user_meta($current_user->ID, 'tfa_hotp_off_sync', true);
		if (!$is_off_sync) return;
		
		?>
		<div class="error">
			<h3><?php _e('Two Factor Authentication re-sync needed', 'all-in-one-wp-security-and-firewall');?></h3>
			<p>
				<?php _e('You need to resync your device for Two Factor Authentication since the OTP you last used is many steps ahead of the server.', 'all-in-one-wp-security-and-firewall'); ?>
				<br>
				<?php _e('Please re-sync or you might not be able to log in if you generate more OTPs without logging in.', 'all-in-one-wp-security-and-firewall');?>
				<br><br>
				<a href="<?php echo esc_url(wp_nonce_url('admin.php?page='. $this->tfa->get_user_settings_page_slug() .'&warning_button_clicked=1', 'tfaresync', 'resyncnonce')); ?>" class="button"><?php _e('Click here and re-scan the QR-Code', 'all-in-one-wp-security-and-firewall');?></a>
			</p>
		</div>
		
		<?php
		
	}
	
	/**
	 * Runs upon the WP action plugins_loaded
	 */
	public function plugins_loaded() {
		$this->time_window_size = apply_filters('simbatfa_time_window_size', 30);
		$this->check_back_time_windows = apply_filters('simbatfa_check_back_time_windows', 2);
		$this->check_forward_time_windows = apply_filters('simbatfa_check_forward_time_windows', 1);
		$this->check_forward_counter_window = apply_filters('simbatfa_check_forward_counter_window', 20);
		
		$this->salt_prefix = defined('AUTH_SALT') ? AUTH_SALT : wp_salt('auth');
		$this->pw_prefix = defined('AUTH_KEY') ? AUTH_KEY : get_site_option('auth_key');
	}
	
	/**
	 * Generate the current code for a specified user
	 *
	 * @param $user_id Integer - WordPress user ID
	 *
	 * @return String|Boolean - false if not set up
	 */
	public function get_current_code($user_id) {
	
		$tfa_priv_key_64 = get_user_meta($user_id, 'tfa_priv_key_64', true);
		
		if (!$tfa_priv_key_64) return false;
		
		return $this->generateOTP($user_id, $tfa_priv_key_64);
	
	}

	public function print_default_hmac_radios() {
		
		$setting = $this->tfa->get_option('tfa_default_hmac');
		if (!$setting) $setting = $this->default_hmac;
		
		$types = array('totp' => __('TOTP (time based - most common algorithm; used by Google Authenticator)', 'all-in-one-wp-security-and-firewall'), 'hotp' => __('HOTP (event based)', 'all-in-one-wp-security-and-firewall'));
		
		foreach ($types as $id => $name) {
			print '<input type="radio" id="tfa_default_hmac_'.esc_attr($id).'" name="tfa_default_hmac" value="'.$id.'" '.($setting == $id ? 'checked="checked"' :'').'> '.'<label for="tfa_default_hmac_'.esc_attr($id).'">'."$name</label><br>\n";
		}
	}
	
	public function generateOTP($user_ID, $key_b64, $length = 6, $counter = false) {
		
		$length = $length ? (int)$length : 6;
		
		$key = $this->decryptString($key_b64, $user_ID);
		$alg = $this->get_user_otp_algorithm($user_ID);
		
		if ('hotp' == $alg) {
			$db_counter = $this->getUserCounter($user_ID);
			
			$counter = $counter ? $counter : $db_counter;
			$otp_res = $this->otp_helper->generateByCounter($key, $counter);
		} else {
			//time() is supposed to be UTC
			$time = $counter ? $counter : time();
			$otp_res = $this->otp_helper->generateByTime($key, $this->time_window_size, $time);
		}
		$code = $otp_res->toHotp($length);
		
		return $code;
	}
	
	/**
	 * Generate a list of OTP codes based on the user, key and time window
	 *
	 * @param Integer $user_ID - user ID
	 * @param String  $key_b64 - the user's private key, in base64 format
	 *
	 * @return Array
	 */
	private function generate_otps_for_login_check($user_ID, $key_b64) {
		$key = trim($this->decryptString($key_b64, $user_ID));
		$alg = $this->get_user_otp_algorithm($user_ID);
		
		if ('totp' == $alg) {
			$otp_res = $this->otp_helper->generateByTimeWindow($key, $this->time_window_size, -1*$this->check_back_time_windows, $this->check_forward_time_windows);
		} elseif ('hotp' == $alg) {
			
			$counter = $this->getUserCounter($user_ID);
			
			$otp_res = array();
			
			for ($i = 0; $i < $this->check_forward_counter_window; $i++) {
				$otp_res[] = $this->otp_helper->generateByCounter($key, $counter+$i);
			}
		}
		return $otp_res;
	}
	
	
	/**
	 * Generate a private key for the user.
	 *
	 * @param Integer $user_id - WordPress user ID
	 * @param Boolean|String $key
	 *
	 * @return String
	 */
	public function addPrivateKey($user_id, $key = false) {
		
		// To work with Google Authenticator it has to be 10 bytes = 16 chars in base32
		$code = $key ? $key : strtoupper($this->randString(10));
		
		// Encrypt the key
		$code = $this->encryptString($code, $user_id);
		
		// Add private key to usermeta
		update_user_meta($user_id, 'tfa_priv_key_64', $code);
		
		$alg = $this->get_user_otp_algorithm($user_id);
		
		// This hook is used for generation of emergency codes to accompany the key
		do_action('simba_tfa_adding_private_key', $alg, $user_id, $code, $this);
		
		$this->changeUserAlgorithmTo($user_id, $alg);
		
		return $code;
	}
	
	/**
	 * Port over keys that were encrypted with mcrypt and its non-compliant padding scheme, so that if the site is ever migrated to a server without mcrypt, they can still be decrypted
	 */
	public function potentially_port_private_keys() {
		
		$simba_tfa_priv_key_format = get_site_option('simba_tfa_priv_key_format', false);
		
		if ($simba_tfa_priv_key_format >= 1 || !function_exists('openssl_encrypt')) return;
		
		$attempts = 0;
		$successes = 0;
		
		error_log("TFA: Beginning attempt to port private key encryption over to openssl");
		
		global $wpdb;
		
		$sql = "SELECT user_id, meta_value FROM ".$wpdb->usermeta." WHERE meta_key = 'tfa_priv_key_64'";
		
		$user_results = $wpdb->get_results($sql);
		
		foreach ($user_results as $u) {
			$dec_openssl = $this->decryptString($u->meta_value, $u->user_id, true);
			
			$ported = false;
			if ('' == $dec_openssl) {
				
				$attempts++;
				
				$dec_default = $this->decryptString($u->meta_value, $u->user_id);
				
				if ('' != $dec_default) {
					
					$enc = $this->encryptString($dec_default, $u->user_id);
					
					if ($enc) {
						
						$ported = true;
						$successes++;
						update_user_meta($u->user_id, 'tfa_priv_key_64', $enc);
					}
				}
				
			}
			
			if ($ported) {
				error_log("TFA: Successfully ported the key for user with ID ".$u->user_id." over to openssl");
			} else {
				error_log("TFA: Failed to port the key for user with ID ".$u->user_id." over to openssl");
			}
		}
		
		if ($attempts == 0 || $successes > 0) update_site_option('simba_tfa_priv_key_format', 1);
		
	}
	
	public function getPrivateKeyPlain($enc, $user_ID) {
		$dec = $this->decryptString($enc, $user_ID);
		$this->potentially_port_private_keys();
		return $dec;
	}
	
	/**
	 * @param Integer $user_id - WP user ID
	 * @param Boolean $generate_if_empty - generate some new codes if the list is empty
	 *
	 * @return String - human-usable codes, separated by ', ' (or a human-readable message, if there were none)
	 */
	public function get_emergency_codes_as_string($user_id, $generate_if_empty = false) {
		
		$codes = get_user_meta($user_id, 'simba_tfa_emergency_codes_64', true);
		if (!is_array($codes)) $codes = array();

		if ($generate_if_empty && empty($codes)) {
			$tfa_priv_key = get_user_meta($user_id, 'tfa_priv_key_64', true);
			$algorithm = get_user_meta($user_id, 'tfa_algorithm_type', true);
			do_action('simba_tfa_emergency_codes_empty', $algorithm, $user_id, $tfa_priv_key, $this);
			$codes = get_user_meta($user_id, 'simba_tfa_emergency_codes_64', true);
			if (!is_array($codes)) $codes = array();
		}
		
		$emergency_str = '';
		
		foreach ($codes as $p_code) {
			$emergency_str .= $this->decryptString($p_code, $user_id).', ';
		}
		
		$emergency_str = rtrim($emergency_str, ', ');
		
		$emergency_str = $emergency_str ? $emergency_str : '<em>'.__('There are no emergency codes left. You will need to reset your private key.', 'all-in-one-wp-security-and-firewall').'</em>';
		
		return $emergency_str;
	}
	
	/**
	 * Check a code for a user (checks the code only - does not check activation status etc.)
	 *
	 * @param Integer $user_id	 			- WP user ID
	 * @param String  $user_code 			- the code to check
	 * @param Boolean $allow_emergency_code - whether to check against emergency codes
	 *
	 * @return Boolean
	 */
	public function check_code_for_user($user_id, $user_code, $allow_emergency_code = true) {
		
		$tfa_priv_key = get_user_meta($user_id, 'tfa_priv_key_64', true);
		// 		$tfa_last_login = get_user_meta($user_id, 'tfa_last_login', true); // Unused
		$tfa_last_pws_arr = get_user_meta($user_id, 'tfa_last_pws', true);
		$tfa_last_pws = @$tfa_last_pws_arr ? $tfa_last_pws_arr : array();
		$alg = $this->get_user_otp_algorithm($user_id);
		
		$current_time_window = intval(time()/30);
		
		//Give the user 1,5 minutes time span to enter/retrieve the code
		//Or check $this->check_forward_counter_window number of events if hotp
		$codes = $this->generate_otps_for_login_check($user_id, $tfa_priv_key);
		
		//A recently used code was entered; that's not OK.
		if (in_array($this->hash($user_code, $user_id), $tfa_last_pws)) return false;
		
		$match = false;
		foreach ($codes as $index => $code) {
			if (trim($code->toHotp(6)) == trim($user_code)) {
				$match = true;
				$found_index = $index;
				break;
			}
		}
		
		// Check emergency codes
		if (!$match) {
			$emergency_codes = $allow_emergency_code ? get_user_meta($user_id, 'simba_tfa_emergency_codes_64', true) : array();
			
			if (!$emergency_codes) return $match;
			
			$dec = array();
			foreach ($emergency_codes as $emergency_code)
				$dec[] = trim($this->decryptString(trim($emergency_code), $user_id));
			
			$in_array = array_search($user_code, $dec);
			$match = $in_array !== false;
			
			//Remove emergency code
			if ($match) {
				array_splice($emergency_codes, $in_array, 1);
				update_user_meta($user_id, 'simba_tfa_emergency_codes_64', $emergency_codes);
				do_action('simba_tfa_emergency_code_used', $user_id, $emergency_codes);
			}
			
		} else {
			//Add the used code as well so it cant be used again
			//Keep the two last codes
			$tfa_last_pws[] = $this->hash($user_code, $user_id);
			$nr_of_old_to_save = $alg == 'hotp' ? $this->check_forward_counter_window : $this->check_back_time_windows;
			
			if (count($tfa_last_pws) > $nr_of_old_to_save) array_splice($tfa_last_pws, 0, 1);

            update_user_meta($user_id, 'tfa_last_pws', $tfa_last_pws);
		}
		
		if ($match) {
			//Save the time window when the last successful login took place
			update_user_meta($user_id, 'tfa_last_login', $current_time_window);
			
			//Update the counter if HOTP was used
			if ($alg == 'hotp') {
				$counter = $this->getUserCounter($user_id);
				
				$enc_new_counter = $this->encryptString($counter+1, $user_id);
				update_user_meta($user_id, 'tfa_hotp_counter', $enc_new_counter);
				
				if ($found_index > 10) update_user_meta($user_id, 'tfa_hotp_off_sync', 1);
			}
		}
		
		return $match;
		
	}
	
	public function getUserCounter($user_ID) {
		$enc_counter = get_user_meta($user_ID, 'tfa_hotp_counter', true);
		return $enc_counter ? trim($this->decryptString(trim($enc_counter), $user_ID)) : '';
	}
	
	public function changeUserAlgorithmTo($user_id, $new_algorithm) {
		update_user_meta($user_id, 'tfa_algorithm_type', $new_algorithm);
		delete_user_meta($user_id, 'tfa_hotp_off_sync');
		
		$counter_start = rand(13, 999999999);
		$enc_counter_start = $this->encryptString($counter_start, $user_id);
		
		if ('hotp' == $new_algorithm) {
			update_user_meta($user_id, 'tfa_hotp_counter', $enc_counter_start);
		} else {
			delete_user_meta($user_id, 'tfa_hotp_counter');
		}
	}
	
	/**
	 * Whether HOTP or TOTP is being used
	 *
	 * @param Integer|Boolean $user_id - WordPress user ID, or false for the site-wide default
	 *
	 * @return String - 'hotp' or 'totp'
	 */
	public function get_user_otp_algorithm($user_id = false) {

		$setting = $user_id ? get_user_meta($user_id, 'tfa_algorithm_type', true) : false;
		
		$default_hmac = $this->tfa->get_option('tfa_default_hmac');
		if (!$default_hmac) $default_hmac = $this->default_hmac;
		
		return $setting ? $setting : $default_hmac;
	}
	
	private function get_iv_size() {
		// mcrypt first, for backwards compatibility
		if (function_exists('mcrypt_get_iv_size')) {
			return $GLOBALS['simba_two_factor_authentication']->is_mcrypt_deprecated() ? @mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC) : mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
		} elseif (function_exists('openssl_cipher_iv_length')) {
			return openssl_cipher_iv_length('AES-128-CBC');
		}
		throw new Exception('One of the mcrypt or openssl PHP modules needs to be installed');
	}
	
	private function encrypt($key, $string, $iv) {
		// Prefer OpenSSL, because it uses correct padding, and its output can be decrypted by mcrypt - whereas, the converse is not true
		if (function_exists('openssl_encrypt')) {
			return openssl_encrypt($string, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
		} elseif (function_exists('mcrypt_encrypt')) {
			return $GLOBALS['simba_two_factor_authentication']->is_mcrypt_deprecated() ? @mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $string, MCRYPT_MODE_CBC, $iv) : mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $string, MCRYPT_MODE_CBC, $iv);
		}
		throw new Exception('One of the mcrypt or openssl PHP modules needs to be installed');
	}
	
	private function decrypt($key, $enc, $iv, $force_openssl = false) {
		// Prefer mcrypt, because it can decrypt the output of both mcrypt_encrypt() and openssl_decrypt(), whereas (because of mcrypt_encrypt() using bad padding), the converse is not true
		if (function_exists('mcrypt_decrypt') && !$force_openssl) {
			return $GLOBALS['simba_two_factor_authentication']->is_mcrypt_deprecated() ? @mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $enc, MCRYPT_MODE_CBC, $iv) : mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $enc, MCRYPT_MODE_CBC, $iv);
		} elseif (function_exists('openssl_decrypt')) {
			$decrypted = openssl_decrypt($enc, 'AES-128-CBC', $key, OPENSSL_RAW_DATA, $iv);
			if (false === $decrypted && !$force_openssl) {
				$extra = function_exists('wp_debug_backtrace_summary') ? " backtrace: ".wp_debug_backtrace_summary() : '';
				error_log("TFA decryption failure: was your site migrated to a server without mcrypt? You may need to install mcrypt, or disable TFA, in order to successfully decrypt data that was previously encrypted with mcrypt.$extra");
			}
			return $decrypted;
		}
		if ($force_openssl) return false;
		throw new Exception('One of the mcrypt or openssl PHP modules needs to be installed');
	}
	
	public function encryptString($string, $salt_suffix) {
		$key = $this->hashAndBin($this->pw_prefix.$salt_suffix, $this->salt_prefix.$salt_suffix);
		
		$iv_size = $this->get_iv_size();
		$iv = $GLOBALS['simba_two_factor_authentication']->random_bytes($iv_size);
		
		$enc = $this->encrypt($key, $string, $iv);
		
		if (false === $enc) return false;
		
		$enc = $iv.$enc;
		$enc_b64 = base64_encode($enc);
		return $enc_b64;
	}
	
	private function decryptString($enc_b64, $salt_suffix, $force_openssl = false) {
		$key = $this->hashAndBin($this->pw_prefix.$salt_suffix, $this->salt_prefix.$salt_suffix);
		
		$iv_size = $this->get_iv_size();
		$enc_conc = bin2hex(base64_decode($enc_b64));
		
		$iv = hex2bin(substr($enc_conc, 0, $iv_size*2));
		$enc = hex2bin(substr($enc_conc, $iv_size*2));
		
		$string = $this->decrypt($key, $enc, $iv, $force_openssl);
		
		// Remove padding bytes
		return rtrim($string, "\x00..\x1F");
	}
	
	private function hashAndBin($pw, $salt) {
		$key = $this->hash($pw, $salt);
		$key = pack('H*', $key);
		// Yes: it's a null encryption key. See: https://wordpress.org/support/topic/warning-mcrypt_decrypt-key-of-size-0-not-supported-by-this-algorithm-only-k?replies=5#post-6806922
		// Basically: the original plugin had a bug here, which caused a null encryption key. This fails on PHP 5.6+. But, fixing it would break backwards compatibility for existing installs - and note that the only unknown once you have access to the encrypted data is the AUTH_SALT and AUTH_KEY constants... which means that actually the intended encryption was non-portable, + problematic if you lose your wp-config.php or try to migrate data to another site, or changes these values. (Normally changing these values only causes a compulsory re-log-in - but with the intended encryption in the original author's plugin, it'd actually cause a permanent lock-out until you disabled his plugin). If someone has read-access to the database, then it'd be reasonable to assume they have read-access to wp-config.php too: or at least, the number of attackers who can do one and not the other would be small. The "encryption's" not worth it.
		// In summary: this isn't encryption, and is not intended to be.
		return str_repeat(chr(0), 16);
	}
	
	private function hash($pw, $salt) {
		//$hash = hash_pbkdf2('sha256', $pw, $salt, 10);
		//$hash = crypt($pw, '$5$'.$salt.'$');
		$hash = md5($salt.$pw);
		return $hash;
	}
	
	private function randString($len = 10) {
		$chars = '23456789QWERTYUPASDFGHJKLZXCVBNM';
		$chars = str_split($chars);
		shuffle($chars);
		if (function_exists('random_int')) {
			$code = '';
			for ($i = 1; $i <= $len; $i++) {
				$code .= $chars[random_int(0, count($chars)-1)];
			}
		} else {
			$code = implode('', array_splice($chars, 0, $len));
		}
		return $code;
	}
	
	public function setUserHMACTypes() {
		trigger_error("Deprecated: setUserHMACTypes() does nothing: remove any calls to it");
	}

}
