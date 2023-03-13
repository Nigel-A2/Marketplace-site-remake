<?php
if (!defined('ABSPATH')) {
	exit;//Exit if accessed directly
}

class AIOWPSecurity_Captcha {

	private $google_verify_recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';

	public function __construct() {
		//NOP
	}

	/**
	 * Displays Google reCAPTCHA form v2
	 *
	 * @global type $aio_wp_security
	 */
	public function display_recaptcha_form() {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_bp_register_captcha') == '1' && defined('BP_VERSION')) {
			//if buddy press feature active add action hook so buddy press can display our errors properly on bp registration form
			do_action('bp_aiowps-captcha-answer_errors');
		}
		$site_key = $aio_wp_security->configs->get_value('aiowps_recaptcha_site_key');
		if (false === $aio_wp_security->google_recaptcha_sitekey_verification($site_key)) {
			$aio_wp_security->configs->set_value('aios_is_google_recaptcha_wrong_site_key', 1);
			$aio_wp_security->configs->save_config();
			return;
		}

		$cap_form = '<div class="g-recaptcha-wrap" style="padding:10px 0 10px 0"><div class="g-recaptcha" data-sitekey="'.esc_attr($site_key).'"></div></div>';
		echo $cap_form;
	}

	/**
	 * Displays simple maths CAPTCHA form
	 *
	 * @global type $aio_wp_security
	 */
	public function display_captcha_form() {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_enable_bp_register_captcha') == '1' && defined('BP_VERSION')) {
			//if buddy press feature active add action hook so buddy press can display our errors properly on bp registration form
			do_action('bp_aiowps-captcha-answer_errors');
		}
		$cap_form = '<p class="aiowps-captcha hide-when-displaying-tfa-input"><label for="aiowps-captcha-answer">'.__('Please enter an answer in digits:', 'all-in-one-wp-security-and-firewall').'</label>';
		$cap_form .= '<div class="aiowps-captcha-equation hide-when-displaying-tfa-input"><strong>';
		$maths_question_output = $this->generate_maths_question();
		$cap_form .= $maths_question_output . '</strong></div></p>';
		echo $cap_form;
	}

	public function generate_maths_question() {
		global $aio_wp_security;
		//For now we will only do plus, minus, multiplication
		$equation_string = '';
		$operator_type = array('&#43;', '&#8722;', '&#215;');

		$operand_display = array('word', 'number');

		//let's now generate an equation
		$operator = $operator_type[rand(0, 2)];

		if ('&#215;' === $operator) {
			//Don't make the question too hard if multiplication
			$first_digit = rand(1, 5);
			$second_digit = rand(1, 5);
		} else {
			$first_digit = rand(1, 20);
			$second_digit = rand(1, 20);
		}

		if ('word' == $operand_display[rand(0, 1)]) {
			$first_operand = $this->number_word_mapping($first_digit);
		} else {
			$first_operand = $first_digit;
		}

		if ('word' == $operand_display[rand(0, 1)]) {
			$second_operand = $this->number_word_mapping($second_digit);
		} else {
			$second_operand = $second_digit;
		}

		//Let's caluclate the result and construct the equation string
		if ('&#43;' === $operator) {
			//Addition
			$result = $first_digit+$second_digit;
			$equation_string .= $first_operand . ' ' . $operator . ' ' . $second_operand . ' = ';
		} elseif ('&#8722;' === $operator) {
			//Subtraction
			//If we are going to be negative let's swap operands around
			if ($first_digit < $second_digit) {
				$equation_string .= $second_operand . ' ' . $operator . ' ' . $first_operand . ' = ';
				$result = $second_digit-$first_digit;
			} else {
				$equation_string .= $first_operand . ' ' . $operator . ' ' . $second_operand . ' = ';
				$result = $first_digit-$second_digit;
			}
		} elseif ('&#215;' === $operator) {
			//Multiplication
			$equation_string .= $first_operand . ' ' . $operator . ' ' . $second_operand . ' = ';
			$result = $first_digit*$second_digit;
		}

		//Let's encode correct answer
		$captcha_secret_string = $aio_wp_security->configs->get_value('aiowps_captcha_secret_key');
		$current_time = time();
		$enc_result = base64_encode($current_time.$captcha_secret_string.$result);
		$random_str = AIOWPSecurity_Utility::generate_alpha_numeric_random_string(10);
		if (is_multisite()) {
			update_site_option('aiowps_captcha_string_info_'.$random_str, $enc_result);
			update_site_option('aiowps_captcha_string_info_time_'.$random_str, $current_time);
		} else {
			update_option('aiowps_captcha_string_info_'.$random_str, $enc_result, false);
			update_option('aiowps_captcha_string_info_time_'.$random_str, $current_time, false);
		}
		$equation_string .= '<input type="hidden" name="aiowps-captcha-string-info" id="aiowps-captcha-string-info" value="'.$random_str.'" />';
		$equation_string .= '<input type="hidden" name="aiowps-captcha-temp-string" id="aiowps-captcha-temp-string" value="'.$current_time.'" />';
		$equation_string .= '<input type="text" size="2" id="aiowps-captcha-answer" name="aiowps-captcha-answer" value="" autocomplete="off" />';
		return $equation_string;
	}

	public function number_word_mapping($num) {
		$number_map = array(
			1 => __('one', 'all-in-one-wp-security-and-firewall'),
			2 => __('two', 'all-in-one-wp-security-and-firewall'),
			3 => __('three', 'all-in-one-wp-security-and-firewall'),
			4 => __('four', 'all-in-one-wp-security-and-firewall'),
			5 => __('five', 'all-in-one-wp-security-and-firewall'),
			6 => __('six', 'all-in-one-wp-security-and-firewall'),
			7 => __('seven', 'all-in-one-wp-security-and-firewall'),
			8 => __('eight', 'all-in-one-wp-security-and-firewall'),
			9 => __('nine', 'all-in-one-wp-security-and-firewall'),
			10 => __('ten', 'all-in-one-wp-security-and-firewall'),
			11 => __('eleven', 'all-in-one-wp-security-and-firewall'),
			12 => __('twelve', 'all-in-one-wp-security-and-firewall'),
			13 => __('thirteen', 'all-in-one-wp-security-and-firewall'),
			14 => __('fourteen', 'all-in-one-wp-security-and-firewall'),
			15 => __('fifteen', 'all-in-one-wp-security-and-firewall'),
			16 => __('sixteen', 'all-in-one-wp-security-and-firewall'),
			17 => __('seventeen', 'all-in-one-wp-security-and-firewall'),
			18 => __('eighteen', 'all-in-one-wp-security-and-firewall'),
			19 => __('nineteen', 'all-in-one-wp-security-and-firewall'),
			20 => __('twenty', 'all-in-one-wp-security-and-firewall'),
		);
		return $number_map[$num];
	}


	/**
	 * Verifies the math or Google reCAPTCHA v2 forms
	 * Returns TRUE if correct answer.
	 * Returns FALSE on wrong CAPTCHA result or missing data.
	 *
	 * @global type $aio_wp_security
	 * @return boolean
	 */
	public function verify_captcha_submit() {
		global $aio_wp_security;
		if ($aio_wp_security->configs->get_value('aiowps_default_recaptcha')) {
			// Google reCAPTCHA enabled
			if (1 == $aio_wp_security->configs->get_value('aios_is_google_recaptcha_wrong_site_key')) {
				return true;
			}
			$site_key = esc_html($aio_wp_security->configs->get_value('aiowps_recaptcha_site_key'));// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
			if (array_key_exists('g-recaptcha-response', $_POST)) {
				$g_recaptcha_response = isset($_POST['g-recaptcha-response']) ? sanitize_text_field($_POST['g-recaptcha-response']) : '';
				$verify_captcha = $this->verify_google_recaptcha($g_recaptcha_response);
				return $verify_captcha;
			} else {
				// Expected CAPTCHA field in $_POST but got none!
				return false;
			}
		} else {
			// Math CAPTCHA is enabled
			if (array_key_exists('aiowps-captcha-answer', $_POST)) {
				$captcha_answer = isset($_POST['aiowps-captcha-answer']) ? sanitize_text_field($_POST['aiowps-captcha-answer']) : '';

				$verify_captcha = $this->verify_math_captcha_answer($captcha_answer);
				return $verify_captcha;
			} else {
				// Expected CAPTCHA field in $_POST but got none!
				return false;
			}
		}
	}

	/**
	 * Verifies the math CAPTCHA answer entered by the user
	 *
	 * @param type $captcha_answer
	 * @return boolean
	 */
	public function verify_math_captcha_answer($captcha_answer = '') {
		global $aio_wp_security;
		$captcha_secret_string = $aio_wp_security->configs->get_value('aiowps_captcha_secret_key');
		$captcha_temp_string = sanitize_text_field($_POST['aiowps-captcha-temp-string']);
		$submitted_encoded_string = base64_encode($captcha_temp_string.$captcha_secret_string.$captcha_answer);
		$trans_handle = sanitize_text_field($_POST['aiowps-captcha-string-info']);
		if (is_multisite()) {
			$captcha_string_info_option = get_site_option('aiowps_captcha_string_info_'.$trans_handle);
			delete_site_option('aiowps_captcha_string_info_'.$trans_handle);
			delete_site_option('aiowps_captcha_string_info_time_'.$trans_handle);
		} else {
			$captcha_string_info_option = get_option('aiowps_captcha_string_info_'.$trans_handle);
			delete_option('aiowps_captcha_string_info_'.$trans_handle);
			delete_option('aiowps_captcha_string_info_time_'.$trans_handle);
		}
		if ($submitted_encoded_string === $captcha_string_info_option) {
			return true;
		} else {
			return false; // wrong answer was entered
		}
	}

	/**
	 * Send a query to Google API to verify reCAPTCHA submission
	 *
	 * @global type $aio_wp_security
	 * @param type $resp_token
	 * @return boolean
	 */
	public function verify_google_recaptcha($resp_token = '') {
		global $aio_wp_security;
		$is_humanoid = false;

		if (empty($resp_token)) {
			return $is_humanoid;
		}

		$url = $this->google_verify_recaptcha_url;

		$sitekey = $aio_wp_security->configs->get_value('aiowps_recaptcha_site_key');// phpcs:ignore VariableAnalysis.CodeAnalysis.VariableAnalysis.UnusedVariable
		$secret = $aio_wp_security->configs->get_value('aiowps_recaptcha_secret_key');
		$ip_address = AIOWPSecurity_Utility_IP::get_user_ip_address();
		$response = wp_safe_remote_post($url, array(
			'body' => array(
				'secret' => $secret,
				'response' => $resp_token,
				'remoteip' => $ip_address,
			),
		));

		if (wp_remote_retrieve_response_code($response) != 200) {
			return $is_humanoid;
		}
		$response = wp_remote_retrieve_body($response);
		$response = json_decode($response, true);
		if (isset($response['success']) && true == $response['success']) {
			$is_humanoid = true;
		}
		return $is_humanoid;
	}

	/**
	 *  Get site locale code for Google reCaptcha.
	 *
	 * @return string The site locale code.
	 */
	public static function get_google_recaptcha_compatible_site_locale() {
		$google_recaptcha_locale_codes = AIOS_Abstracted_Ids::get_google_recaptcha_locale_codes();
		$locale = str_replace('_', '-', determine_locale());

		if (in_array($locale, $google_recaptcha_locale_codes, true)) {
			return $locale;
		}

		// Return 2 letter locale code.
		$locale = explode('-', $locale);
		return $locale[0];
	}

}
