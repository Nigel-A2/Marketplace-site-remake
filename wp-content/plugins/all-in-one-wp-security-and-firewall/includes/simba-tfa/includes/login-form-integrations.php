<?php

if (!defined('ABSPATH')) die('No direct access.');

/**
 * Purpose of this class: abstract out code handling integrations with login forms
 */

class Simba_TFA_Login_Form_Integrations {

	// Main class
	private $tfa;

	/**
	 * Plugin constructor
	 *
	 * @param Object $tfa
	 */
	public function __construct($tfa) {
	
		$this->tfa = $tfa;
	
		$enqueue_upon_actions = array(
			// This is needed for the login form on the dedicated payment page (e.g. /checkout/order-pay/123456/?pay_for_order=true&key=wc_order_blahblahblah)
			'woocommerce_login_form_start',
			'woocommerce_before_customer_login_form',
			// The login form on the checkout doesn't call the woocommerce_before_customer_login_form action
			'woocommerce_before_checkout_form',
			'affwp_login_fields_before',
		);
		
		foreach ($enqueue_upon_actions as $action) {
			add_action($action, array($this->tfa, 'login_enqueue_scripts'));
		}
		
		if (!defined('TWO_FACTOR_DISABLE') || !TWO_FACTOR_DISABLE) {
			add_action('affwp_process_login_form', array($this, 'affwp_process_login_form'));
		}
		
		add_filter('tml_display', array($this, 'tml_display'));
	
		// We want to run first if possible, so that we're not aborted by JavaScript exceptions in other components (our code is critical to the login process for TFA users)
		// Unfortunately, though, people start enqueuing from init onwards (before that is buggy - https://core.trac.wordpress.org/ticket/11526), so, we try to detect the login page and go earlier there. 
		if (isset($GLOBALS['pagenow']) && 'wp-login.php' === $GLOBALS['pagenow']) {
			add_action('init', array($this->tfa, 'login_enqueue_scripts'), -99999999999);
		} else {
			add_action('login_enqueue_scripts', array($this->tfa, 'login_enqueue_scripts'), -99999999999);
		}
	
		add_filter('do_shortcode_tag', array($this, 'do_shortcode_tag'), 10, 2);
	
		add_filter('simba_tfa_login_enqueue_localize', array($this, 'simba_tfa_login_enqueue_localize'), 9);
	
	}
	
	/**
	 * Catch TML login widgets (other TML login forms already trigger)
	 *
	 * @param Mixed $whatever
	 *
	 * @return Mixed
	 */
	public function tml_display($whatever) {
		$this->tfa->login_enqueue_scripts();
		return $whatever;
	}
	
	/**
	 * Runs upon the WP filter simba_tfa_login_enqueue_localize.
	 *
	 * @param Array $localize
	 *
	 * @return Array
	 */
	public function simba_tfa_login_enqueue_localize($localize) {
		// WP login form is #loginform
		// Ultimate Membership Pro - April 2018
		// Theme My Login 6.x - .tml-login form[name="loginform"]
		// Theme My Login 7.x - .tml-login form[name="login"] (July 2018)
		// WP Members - March 2018
		// bbPress - June 2021
		// WooCommerce - ported over from the separate wooextend.js code, June 2021
		// Affiliates WP - ported over from the separate wooextend.js code, June 2021
		$localize['login_form_selectors'] .= '.tml-login form[name="loginform"], .tml-login form[name="login"], #loginform, #wpmem_login form, form#ihc_login_form, .bbp-login-form, .woocommerce form.login, #affwp-login-form';
		$localize['login_form_off_selectors'] .= '#ihc_login_form';
		return $localize;
	}
	
	/**
	 * Runs upon the WP action affwp_process_login_form
	 */
	public function affwp_process_login_form() {
	
		if (!function_exists('affiliate_wp')) return;
		
		$affiliate_wp = affiliate_wp();
		$login = $affiliate_wp->login;
		
		$params = array(
			'log' => stripslashes($_POST['affwp_user_login']),
			'caller'=> $_SERVER['PHP_SELF'] ? $_SERVER['PHP_SELF'] : $_SERVER['REQUEST_URI'],
			'two_factor_code' => stripslashes((string) $_POST['two_factor_code'])
		);
		$code_ok = $this->tfa->authorise_user_from_login($params, true);
		
		$code_ok = apply_filters('simbatfa_affwp_process_login_form_auth_result', $code_ok, $params);
		
		if (is_wp_error($code_ok)) {
			$login->add_error($code_ok->get_error_code, $code_ok->get_error_message());
		} elseif (!$code_ok) {
			$login->add_error('authentication_failed', __('Error:', 'all-in-one-wp-security-and-firewall').' '.apply_filters('simba_tfa_message_code_incorrect', __('The one-time password (TFA code) you entered was incorrect.', 'all-in-one-wp-security-and-firewall')));
		}
		
	}
	
	/**
	 * Ultimate Membership Pro support
	 *
	 * @param String $output
	 * @param String $tag
	 *
	 * @return String
	 */
	public function do_shortcode_tag($output, $tag) {
		if ('ihc-login-form' == $tag) $this->tfa->login_enqueue_scripts();
		return $output;
	}

}
