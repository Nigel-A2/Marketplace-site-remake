<?php

/**
 * Helper class for the Mailrelay API.
 *
 * @since 1.5.4
 */
final class FLBuilderServiceMailrelay extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.5.4
	 * @var string $id
	 */
	public $id = 'mailrelay';

	/**
	 * The API url suffix for this service.
	 *
	 * @since 1.5.8
	 * @access private
	 * @var string $api_url
	 */
	private $api_url = '/ccm/admin/api/version/2/&type=json';

	/**
	 * Request data from the thir party API.
	 *
	 * @since 1.5.4
	 * @param string $base_url Base URL where API is available
	 * @param string $method Method to request available from this service.
	 * @param array $params Data to be passed to API
	 * @return array|object The API response.
	 */
	private function get_api_response( $base_url, $params ) {
		// Exclude http:// for the specific service
		$base_url = preg_replace( '#^https?://#', '', $base_url );
		$response = wp_remote_post( 'https://' . $base_url . $this->api_url, array(
			'timeout' => 60,
			'body'    => $params,
		) );

		if ( is_wp_error( $response ) || ( isset( $response->status ) && 0 == $response->status ) ) {
			if ( isset( $response->status ) ) {
				$data = json_decode( $response, true );
			} else {
				$data['error'] = $response->get_error_message();
			}
		} else {
			$data = json_decode( wp_remote_retrieve_body( $response ), true );
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

			$result = $this->get_api_response( $fields['api_host'], array(
				'function' => 'getGroups',
				'apiKey'   => $fields['api_key'],
				'offset'   => 0,
				'count'    => 1,
			) );

			if ( ! isset( $result['error'] ) ) {
				$response['data'] = array(
					'api_host' => $fields['api_host'],
					'api_key'  => $fields['api_key'],
				);
			} else {
				/* translators: %s: error */
				$response['error'] = sprintf( __( 'Error: Could not connect to Mailrelay. %s', 'fl-builder' ), $result['error'] );
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
			'help'      => __( 'The host you chose when you signed up for your account. Check your welcome email if you forgot it. Please enter it without the initial http:// (e.g. demo.ip-zone.com).', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		));

		FLBuilder::render_settings_field( 'api_key', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'API Key', 'fl-builder' ),
			'help'      => __( 'Your API key can be found in your Mailrelay account under Menu > Settings > API access.', 'fl-builder' ),
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
		$result       = $this->get_api_response( $account_data['api_host'], array(
			'function' => 'getGroups',
			'apiKey'   => $account_data['api_key'],
		) );

		$response = array(
			'error' => false,
			'html'  => '',
		);

		if ( isset( $result['error'] ) ) {
			/* translators: %s: error */
			$response['error'] = sprintf( __( 'Error: Please check your API key. %s', 'fl-builder' ), $result['error'] );
		} else {
			$response['html'] = $this->render_list_field( $result['data'], $settings );
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
	private function render_list_field( $groups, $settings ) {
		ob_start();

		$options = array(
			'' => __( 'Choose...', 'fl-builder' ),
		);

		foreach ( $groups as $group ) {
			$options[ $group['id'] ] = esc_attr( $group['name'] );
		}

		FLBuilder::render_settings_field( 'list_id', array(
			'row_class'    => 'fl-builder-service-field-row',
			'class'        => 'fl-builder-service-list-select',
			'type'         => 'select',
			'multi-select' => true,
			'label'        => _x( 'Group', 'A list of subscribers group from a Mailrelay account.', 'fl-builder' ),
			'options'      => $options,
			'preview'      => array(
				'type' => 'none',
			),
		), $settings);

		return ob_get_clean();
	}

	/**
	 * Subscribe an email address to Mailrelay.
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

		if ( ! $account_data ) {
			$response['error'] = __( 'There was an error subscribing to Mailrelay. The account is no longer connected.', 'fl-builder' );
		} else {

			$result = $this->get_api_response( $account_data['api_host'], array(
				'function' => 'addSubscriber',
				'apiKey'   => $account_data['api_key'],
				'email'    => $email,
				'name'     => $name,
				'groups'   => $settings->list_id,
			) );

			if ( isset( $result['error'] ) ) {
				/* translators: %s: error */
				$response['error'] = sprintf( __( 'There was an error subscribing to Mailrelay. %s', 'fl-builder' ), $result['error'] );
			}
		}

		return $response;
	}
}
