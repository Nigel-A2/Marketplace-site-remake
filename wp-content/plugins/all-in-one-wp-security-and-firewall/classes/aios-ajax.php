<?php

if (!defined('ABSPATH')) die('Access denied.');

if (!class_exists('AIOS_Ajax')) :

	class AIOS_Ajax {

		private $nonce;

		private $subaction;

		private $data;

		private $results;

		/**
		 * Constructor
		 */
		private function __construct() {
			add_action('wp_ajax_aios_ajax', array($this, 'handle_ajax_requests'));
		}

		/**
		 * Return singleton instance
		 *
		 * @return AIOS_Ajax Returns AIOS_Ajax object
		 */
		public static function get_instance() {
			static $instance = null;
			if (null === $instance) {
				$instance = new self();
			}
			return $instance;
		}

		/**
		 * Handles ajax requests
		 *
		 * @return void
		 */
		public function handle_ajax_requests() {
			$this->set_nonce();
			$this->set_subaction();
			$this->set_data();

			if ($this->is_invalid_request()) {
				$this->send_security_check_failed_error_response();
			}

			if (!$this->is_user_capable()) {
				$this->send_user_capability_error_response();
			}

			if (is_multisite() && !current_user_can('manage_network_options')) {
				if (!$this->is_valid_multisite_command()) {
					$this->send_invalid_multisite_command_error_response();
				}
			}

			$this->execute_command();
			$this->set_error_response_on_wp_error();
			$this->maybe_set_results_as_null();

			$this->json_encode_results();

			$json_last_error = json_last_error();
			if ($json_last_error) {
				$this->set_error_response_on_json_encode_error($json_last_error);
			}

			echo $this->results;
			die;
		}

		/**
		 * Get IP address of given method.
		 *
		 * @return array
		 */
		public function get_ip_address_of_given_method() {
			$ip_method_id = $this->data['ip_retrieve_method'];
			$ip_retrieve_methods = AIOS_Abstracted_Ids::get_ip_retrieve_methods();
			if (isset($ip_retrieve_methods[$ip_method_id])) {
				return array(
					'ip_address' => isset($_SERVER[$ip_retrieve_methods[$ip_method_id]]) ? $_SERVER[$ip_retrieve_methods[$ip_method_id]] : '',
				);
			} else {
				return new WP_Error('aios-invalid-ip-retrieve-method', __('Invalid IP retrieve method.', 'all-in-one-wp-security-and-firewall'));
			}
			die;
		}

		/**
		 * Sets nonce property value
		 */
		private function set_nonce() {
			$this->nonce = empty($_POST['nonce']) ? '' : $_POST['nonce'];
		}

		/**
		 * Sets subaction property value
		 */
		private function set_subaction() {
			$this->subaction = empty($_POST['subaction']) ? '' : sanitize_text_field(wp_unslash($_POST['subaction']));
		}

		/**
		 * Sets data property value
		 */
		private function set_data() {
			$this->data = isset($_POST['data']) ? wp_unslash($_POST['data']) : null;
		}

		/**
		 * Checks whether the request is valid or not
		 *
		 * @return bool
		 */
		private function is_invalid_request() {
			return !wp_verify_nonce($this->nonce, 'aios-ajax-nonce') || empty($this->subaction);
		}

		/**
		 * Send security check failed error response to browser
		 */
		private function send_security_check_failed_error_response() {
			wp_send_json(array(
				'result' => false,
				'error_code' => 'security_check',
				'error_message' => __('The security check failed; try refreshing the page.', 'all-in-one-wp-security-and-firewall'),
			));
		}


		/**
		 * Checks whether current user capable of doing this action or not
		 *
		 * @return bool
		 */
		private function is_user_capable() {
			return current_user_can(AIOWPSEC_MANAGEMENT_PERMISSION);
		}

		/**
		 * Send user capability check failed error response to browser.
		 */
		private function send_user_capability_error_response() {
			wp_send_json(array(
				'result' => false,
				'error_code' => 'security_check',
				'error_message' => __('You are not allowed to run this command.', 'all-in-one-wp-security-and-firewall'),
			));
		}

		/**
		 * Checks whether it is multisite setup and command is valid multisite command
		 *
		 * @return bool
		 */
		private function is_valid_multisite_command() {
			/**
			 * Filters the commands allowed to the sub site admins. Other commands are only available to network admin. Only used in a multisite context.
			 */
			$allowed_commands = apply_filters('aios_multisite_allowed_commands', array('get_ip_address_of_given_method'));
			return !in_array($this->subaction, $allowed_commands);
		}

		private function send_invalid_multisite_command_error_response() {
			wp_send_json(array(
				'result' => false,
				'error_code' => 'update_failed',
				'error_message' => __('Options can only be saved by network admin', 'all-in-one-wp-security-and-firewall')
			));
		}

		/**
		 * Checks if applied ajax command is an invalid command or not
		 *
		 * @return bool Returns true if ajax command is an invalid command, false otherwise
		 */
		private function is_invalid_command() {
			return !is_callable(array($this, $this->subaction));
		}

		/**
		 * Log an error message for invalid ajax command
		 */
		private function add_invalid_command_error_log_entry() {
			error_log("AIOS: ajax_handler: no such command (" . $this->subaction . ")");
		}

		/**
		 * Set `results` property with error response array for invalid ajax command
		 *
		 * @return void
		 */
		private function set_invalid_command_error_response() {
			$this->results = array(
				'result' => false,
				'error_code' => 'command_not_found',
				'error_message' => sprintf(__('The command "%s" was not found', 'all-in-one-wp-security-and-firewall'), $this->subaction)
			);
		}

		/**
		 * Execute the ajax command
		 */
		private function execute_command() {
			$this->results = call_user_func(array($this, $this->subaction));
		}

		/**
		 * Set `results` property with error message
		 */
		private function set_error_response_on_wp_error() {
			if (is_wp_error($this->results)) {
				$this->results = array(
					'result' => false,
					'error_code' => $this->results->get_error_code(),
					'error_message' => $this->results->get_error_message(),
					'error_data' => $this->results->get_error_data(),
				);
			}
		}

		/**
		 * Set `results` property to null, if it is not yet set
		 */
		private function maybe_set_results_as_null() {
			// if nothing was returned for some reason, set as result null.
			if (empty($this->results)) {
				$this->results = array(
					'result' => null
				);
			}
		}

		/**
		 * Sets `results` property with json encode error
		 *
		 * @param int $json_last_error
		 *
		 * @return void
		 */
		private function set_error_response_on_json_encode_error($json_last_error) {
			$this->results = array(
				'result' => false,
				'error_code' => $json_last_error,
				'error_message' => 'json_encode error : ' . $json_last_error,
				'error_data' => '',
			);

			$this->results = json_encode($this->results);
		}

		/**
		 * Json encode the `results` property value
		 */
		private function json_encode_results() {
			$this->results = json_encode($this->results);
		}
	}

endif;
