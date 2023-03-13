<?php

/**
 * Helper class for the Mautic API.
 *
 * @since 1.10.6
 */
final class FLBuilderServiceMautic extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.10.6
	 * @var string $id
	 */
	public $id = 'mautic';

	/**
	 * @since 1.10.6
	 * @var object $api_instance
	 * @access private
	 */
	private $api_instance = null;

	/**
	 * Get an instance of the API.
	 *
	 * @since 1.10.6
	 * @param array $args A valid API authentication data.
	 * @return object The API instance.
	 */
	public function get_api( array $args ) {
		if ( $this->api_instance ) {
			return $this->api_instance;
		}

		if ( ! class_exists( 'MauticApi' ) ) {
			require_once FL_BUILDER_DIR . 'includes/vendor/mautic/MauticApi.php';
		}

		$settings = array(
			'userName' => $args['api_username'], // The username - set up a new user for each external site
			'password' => $args['api_password'], // Make this a Long passPhrase e.g. (Try:  !wE4.And.*@ws4.Guess!  )
			'apiUrl'   => $args['api_host'],     // NOTE: Required for Unit tests; *must* contain a valid url
		);

		$this->api_instance = new MauticApi( $settings );

		return $this->api_instance;
	}

	/**
	 * Test the API connection.
	 *
	 * @since 1.10.6
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
			$response['error'] = __( 'Error: You must provide your Mautic installation URL.', 'fl-builder' );
		}
		// Make sure we have a username
		if ( ! isset( $fields['api_username'] ) || empty( $fields['api_username'] ) ) {
			$response['error'] = __( 'Error: You must provide your Mautic app username.', 'fl-builder' );
		}
		// Make sure we have password
		if ( ! isset( $fields['api_password'] ) || empty( $fields['api_password'] ) ) {
			$response['error'] = __( 'Error: You must provide your Mautic app user password.', 'fl-builder' );
		} else { // Try to connect and store the connection data.

			$api = $this->get_api( array(
				'api_host'     => $fields['api_host'],
				'api_username' => $fields['api_username'],
				'api_password' => $fields['api_password'],
			) );

			// Try sending request to verify credentials
			$get_response  = $api->getSegments( array(
				'limit' => 1,
			) );
			$response_info = $api->getResponseInfo();

			if ( in_array( $response_info['http_code'], array( 200, 201 ) ) ) {
				$response['data'] = array(
					'api_host'     => $fields['api_host'],
					'api_username' => $fields['api_username'],
					'api_password' => $fields['api_password'],
				);
			} else {
				$error_message = $response_info['http_code'];
				if ( isset( $get_response['errors'] ) && count( $get_response['errors'] ) > 0 ) {
					$error_message = '[' . $get_response['errors'][0]['code'] . '] ' . $get_response['errors'][0]['message'];
				}
				/* translators: %s: error */
				$response['error'] = sprintf( __( 'Error: Could not connect to Mautic. %s', 'fl-builder' ), $error_message );
			}
		}

		return $response;
	}

	/**
	 * Renders the markup for the connection settings.
	 *
	 * @since 1.10.6
	 * @return string The connection settings markup.
	 */
	public function render_connect_settings() {
		ob_start();

		FLBuilder::render_settings_field( 'api_host', array(
			'row_class'   => 'fl-builder-service-connect-row',
			'class'       => 'fl-builder-service-connect-input',
			'type'        => 'text',
			'label'       => __( 'Installation URL', 'fl-builder' ),
			'help'        => __( 'The URL where your Mautic application is installed (e.g. http://mautic.mywebsite.com).', 'fl-builder' ),
			'description' => __( 'API should be enabled in your Mautic application.
					Go to Mautic Configuration / API Settings and set `API enabled` to `Yes`, set `Enable HTTP basic auth` to `Yes` . Save changes.', 'fl-builder' ),
			'preview'     => array(
				'type' => 'none',
			),
		));

		FLBuilder::render_settings_field( 'api_username', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'Mautic Username', 'fl-builder' ),
			'help'      => __( 'Username from your Mautic application. Make sure it has `Full system access`. Best practice would be to set up a new user for each external site.', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		));

		FLBuilder::render_settings_field( 'api_password', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'Mautic Password', 'fl-builder' ),
			'help'      => __( 'Password associated with the username. Make this a Long Passphrase.', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		));

		return ob_get_clean();
	}

	/**
	 * Render the markup for service specific fields.
	 *
	 * @since 1.10.6
	 * @param string $account The name of the saved account.
	 * @param object $settings Saved module settings.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 *      @type string $html The field markup.
	 * }
	 */
	public function render_fields( $account, $settings ) {
		$account_data = $this->get_account_data( $account );
		$api          = $this->get_api( array(
			'api_host'     => $account_data['api_host'],
			'api_username' => $account_data['api_username'],
			'api_password' => $account_data['api_password'],
		) );
		$lists        = $api->getSegments();

		$response = array(
			'error' => false,
			'html'  => '',
		);

		if ( ! isset( $lists['lists'] ) ) {
			$response['error'] = __( 'Error: Please check your API credentials.', 'fl-builder' );
		} else {
			$response['html'] = $this->render_list_field( $lists['lists'], $settings );
		}

		return $response;
	}

	/**
	 * Render markup for the list field.
	 *
	 * @since 1.10.6
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
			$options[ $list['id'] ] = esc_attr( $list['name'] );
		}

		FLBuilder::render_settings_field( 'list_id', array(
			'row_class' => 'fl-builder-service-field-row',
			'class'     => 'fl-builder-service-list-select',
			'type'      => 'select',
			'label'     => _x( 'List', 'An email list from a third party provider.', 'fl-builder' ),
			'options'   => $options,
			'preview'   => array(
				'type' => 'none',
			),
		), $settings);

		return ob_get_clean();
	}

	/**
	 * Subscribe an email address to Mautic.
	 *
	 * @since 1.10.6
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
			$response['error'] = __( 'There was an error subscribing to Mautic. The account is no longer connected.', 'fl-builder' );
		} else {

			$api = $this->get_api( array(
				'api_host'     => $account_data['api_host'],
				'api_username' => $account_data['api_username'],
				'api_password' => $account_data['api_password'],
			) );

			$data = array(
				'email'     => $email,
				'ipAddress' => $_SERVER['REMOTE_ADDR'],
				'segmentId' => $settings->list_id,
			);

			if ( $name ) {
				$names = explode( ' ', $name );
			}

			if ( isset( $names[0] ) ) {
				$data['firstname'] = $names[0];
			}
			if ( isset( $names[1] ) ) {
				$data['lastname'] = $names[1];
			}

			// Add new contact
			$get_api_response = $api->subscribe( $data );
			$response_info    = $api->getResponseInfo();

			if ( isset( $get_api_response['errors'] ) && count( $get_api_response['errors'] ) > 0 ) {
				$response['error'] = sprintf(
					/* translators: %s: error */
					__( 'There was an error subscribing to Mautic. %s', 'fl-builder' ),
					'[' . $get_api_response['errors'][0]['code'] . '] ' . $get_api_response['errors'][0]['message']
				);
			}
		}

		return $response;
	}
}
