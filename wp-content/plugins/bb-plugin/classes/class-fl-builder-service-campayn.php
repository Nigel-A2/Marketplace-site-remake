<?php

/**
 * Helper class for the Campayn API.
 *
 * @since 1.5.4
 */
final class FLBuilderServiceCampayn extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.5.4
	 * @var string $id
	 */
	public $id = 'campayn';

	/**
	 * The HTTP protocol
	 *
	 * @since 1.5.8
	 * @access private
	 * @var string $api_protocol
	 */
	private $api_protocol = 'http';

	/**
	 * The API version
	 *
	 * @since 1.5.8
	 * @access private
	 * @var string $api_version
	 */
	private $api_version = 1;

	/**
	 * Request data from the thir party API.
	 *
	 * @since 1.5.4
	 * @param string $base_url  Base URL where API is available
	 * @param string $api_key   API Key provided by this service
	 * @param string $endpoint  Method to request available from this service.
	 * @param array $params     Data to be passed to API
	 * @return array|object     The API response.
	 */
	private function get_api_response( $base_url, $api_key, $endpoint, $params = array() ) {
		// Exclude http:// from the user's input
		$request_uri = $this->api_protocol . '://' . preg_replace( '#^https?://#', '', $base_url ) . '/api/v' . $this->api_version . $endpoint;

		$params['timeout'] = 60;
		$params['body']    = isset( $params['data'] ) && $params['data'] ? json_encode( $params['data'] ) : '';
		$params['headers'] = array(
			'Authorization' => 'TRUEREST apikey=' . $api_key,
		);
		$response          = wp_remote_get( $request_uri, $params );
		$response_code     = wp_remote_retrieve_response_code( $response );
		$response_message  = wp_remote_retrieve_response_message( $response );
		$get_response      = json_decode( wp_remote_retrieve_body( $response ), true );

		if ( is_wp_error( $response ) || ( 200 != $response_code ) ) {

			if ( is_wp_error( $response ) ) {
				$data['error'] = $response->get_error_message();
			} else {
				$data['error'] = isset( $get_response['msg'] ) ? $get_response['msg'] : $response_code . ' - ' . $response_message;
			}
		} else {
			if ( $get_response ) {
				$data = $get_response;
			} else {
				$data = $response;
			}
		}
		return $data;
	}

	/**
	 * Test the API connection.
	 *
	 * @since 1.5.4
	 * @param array $fields {
	 *      @type string $api_host A valid Host.
	 *      @type string $api_key A valid API key.
	 * }
	 * @return array{
	 *      @type bool|string $error The error message or false if no error.
	 *      @type array $data An array of data used to make the connection.
	 * }
	 */
	public function connect( $fields = array() ) {
		$response = array(
			'error' => false,
			'data'  => array(),
		);

		// Make sure we have the Host.
		if ( ! isset( $fields['api_host'] ) || empty( $fields['api_host'] ) ) {
			$response['error'] = __( 'Error: You must provide a Host.', 'fl-builder' );
		} elseif ( ! isset( $fields['api_key'] ) || empty( $fields['api_key'] ) ) {
			$response['error'] = __( 'Error: You must provide an API key.', 'fl-builder' );
		} else { // Try to connect and store the connection data.

			$result = $this->get_api_response( $fields['api_host'], $fields['api_key'], '/lists.json' );

			if ( ! isset( $result['error'] ) ) {
				$response['data'] = array(
					'api_host' => $fields['api_host'],
					'api_key'  => $fields['api_key'],
				);
			} else {
				/* translators: %s: error */
				$response['error'] = sprintf( __( 'Error: Could not connect to Campayn. %s', 'fl-builder' ), $result['error'] );
			}
		}

		return $response;
	}

	/**
	 * Renders the markup for the connection settings.
	 *
	 * @since 1.5.4
	 * @return string The connection settings markup.
	 */
	public function render_connect_settings() {
		ob_start();

		FLBuilder::render_settings_field( 'api_host', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'Host', 'fl-builder' ),
			'help'      => __( 'The host you chose when you signed up for your account. Check your welcome email if you forgot it. Please enter it without the initial http:// (for example: demo.campayn.com).', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		));

		FLBuilder::render_settings_field( 'api_key', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'API Key', 'fl-builder' ),
			'help'      => __( 'Your API key can be found in your Campayn account under Settings > API Key.', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		));

		return ob_get_clean();
	}

	/**
	 * Render the markup for service specific fields.
	 *
	 * @since 1.5.4
	 * @param string $account The name of the saved account.
	 * @param object $settings Saved module settings.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 *      @type string $html The field markup.
	 * }
	 */
	public function render_fields( $account, $settings ) {
		$account_data = $this->get_account_data( $account );
		$results      = $this->get_api_response( $account_data['api_host'], $account_data['api_key'], '/lists.json' );

		$response = array(
			'error' => false,
			'html'  => '',
		);

		if ( isset( $results['error'] ) ) {
			/* translators: %s: error */
			$response['error'] = sprintf( __( 'Error: Please check your API key. %s', 'fl-builder' ), $results['error'] );
		} else {
			$response['html'] = $this->render_list_field( $results, $settings );
		}

		return $response;
	}

	/**
	 * Render markup for the list field.
	 *
	 * @since 1.5.4
	 * @param array $lists List data from the API.
	 * @param object $settings Saved module settings.
	 * @return string The markup for the list field.
	 * @access private
	 */
	private function render_list_field( $lists, $settings ) {
		ob_start();

		$options = array(
			'' => __( 'Choose...', 'fl-builder' ),
		);

		foreach ( $lists as $list ) {
			$options[ $list['id'] ] = esc_attr( $list['list_name'] );
		}

		FLBuilder::render_settings_field( 'list_id', array(
			'row_class' => 'fl-builder-service-field-row',
			'class'     => 'fl-builder-service-list-select',
			'type'      => 'select',
			'label'     => _x( 'List', 'An email list from third party provider.', 'fl-builder' ),
			'options'   => $options,
			'preview'   => array(
				'type' => 'none',
			),
		), $settings);

		return ob_get_clean();
	}

	/**
	 * Subscribe an email address to Campayn.
	 *
	 * @since 1.5.4
	 * @param object $settings A module settings object.
	 * @param string $email The email to subscribe.
	 * @param string $name Optional. The full name of the person subscribing.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 * }
	 */
	public function subscribe( $settings, $email, $name = '' ) {
		$account_data = $this->get_account_data( $settings->service_account );
		$response     = array(
			'error' => false,
		);
		$contact_id   = null;

		if ( ! $account_data ) {
			$response['error'] = __( 'There was an error subscribing to Campayn. The account is no longer connected.', 'fl-builder' );
		} else {

			// Build data array
			$data = array(
				'email' => $email,
			);

			// Add the name to the data array if we have one.
			if ( $name ) {

				$names = explode( ' ', $name );

				if ( isset( $names[0] ) ) {
					$data['first_name'] = $names[0];
				}
				if ( isset( $names[1] ) ) {
					$data['last_name'] = $names[1];
				}
			}

			// Check if email already exists
			$result = $this->get_api_response( $account_data['api_host'], $account_data['api_key'],
				"/lists/{$settings->list_id}/contacts.json?filter[contact]=" . $email
			);

			// Already exists
			if ( ! isset( $result['error'] ) && ( is_array( $result ) && isset( $result[0]['id'] ) ) ) {
				$contact_id = $result[0]['id'];
			}

			// Add the contact if it doesn't exist.
			if ( ! $contact_id ) {
				$endpoint = "/lists/{$settings->list_id}/contacts.json";
				$method   = 'POST';
			} else {
				$endpoint   = "/contacts/{$contact_id}.json";
				$method     = 'PUT';
				$data['id'] = $contact_id;
			}

			$result = $this->get_api_response( $account_data['api_host'], $account_data['api_key'], $endpoint, array(
				'data'   => $data,
				'method' => $method,
			) );

			if ( isset( $result['error'] ) ) {
				/* translators: %s: error */
				$response['error'] = sprintf( __( 'There was an error subscribing to Campayn. %s', 'fl-builder' ), $result['error'] );
			}
		}

		return $response;
	}
}
