<?php

/**
 * Helper class for the Sendy API.
 *
 * @since 1.5.4
 */
final class FLBuilderServiceSendy extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.5.4
	 * @var string $id
	 */
	public $id = 'sendy';

	/**
	 * @since 1.5.4
	 * @var object $api_instance
	 * @access private
	 */
	private $api_instance = null;

	/**
	 * Get an instance of the API.
	 *
	 * @since 1.5.4
	 * @param array $args A valid API authentication data.
	 * @return object The API instance.
	 */
	public function get_api( array $args ) {
		if ( $this->api_instance ) {
			return $this->api_instance;
		}
		if ( ! class_exists( '\\SendyPHP\\SendyPHP' ) ) {
			require_once FL_BUILDER_DIR . 'includes/vendor/sendy/SendyPHP.php';
		}

		$this->api_instance = new \SendyPHP\SendyPHP( $args );

		return $this->api_instance;
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
			$response['error'] = __( 'Error: You must provide your Sendy installation URL.', 'fl-builder' );
		}
		// Make sure we have an API key.
		if ( ! isset( $fields['api_key'] ) || empty( $fields['api_key'] ) ) {
			$response['error'] = __( 'Error: You must provide an API key.', 'fl-builder' );
		}
		// Make sure we have the list ID.
		if ( ! isset( $fields['list_id'] ) || empty( $fields['list_id'] ) ) {
			$response['error'] = __( 'Error: You must provide a list ID.', 'fl-builder' );
		} else {

			$api = $this->get_api( array(
				'installation_url' => $fields['api_host'],
				'api_key'          => $fields['api_key'],
				'list_id'          => $fields['list_id'],
			) );

			// Send request for list ID validation
			$get_api_response = $api->subcount();

			if ( true === $get_api_response['status'] ) {
				$response['data'] = array(
					'api_host' => $fields['api_host'],
					'api_key'  => $fields['api_key'],
					'list_id'  => $fields['list_id'],
				);
			} else {
				/* translators: %s: error */
				$response['error'] = sprintf( __( 'Error: Could not connect to Sendy. %s', 'fl-builder' ), $get_api_response['message'] );
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
			'label'     => __( 'Installation URL', 'fl-builder' ),
			'help'      => __( 'The URL where your Sendy application is installed (e.g. http://mywebsite.com/sendy).', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		));

		FLBuilder::render_settings_field( 'api_key', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'API Key', 'fl-builder' ),
			'help'      => __( 'Found in your Sendy application under Settings.', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		));

		FLBuilder::render_settings_field( 'list_id', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'List ID', 'fl-builder' ),
			'help'      => __( 'The ID of the list you would like users to subscribe to. The ID of a list can be found under "View all lists" in the section named ID.', 'fl-builder' ),
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

		$response = array(
			'error' => false,
			'html'  => '',
		);

		return $response;
	}

	/**
	 * Subscribe an email address to Sendy.
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
			$response['error'] = __( 'There was an error subscribing to Sendy. The account is no longer connected.', 'fl-builder' );
		} else {

			$api = $this->get_api( array(
				'installation_url' => $account_data['api_host'],
				'api_key'          => $account_data['api_key'],
				'list_id'          => $account_data['list_id'],
			) );

			$args = array(
				'name'    => $name,
				'email'   => $email,
				'api_key' => $account_data['api_key'],
			);

			if ( isset( $settings->terms_checkbox ) && 'show' === $settings->terms_checkbox ) {
				$args['gdpr'] = 'true';
			}

			// Send request for list ID validation
			$get_api_response = $api->subscribe( $args );

			if ( false === $get_api_response['status'] ) {
				/* translators: %s: error */
				$response['error'] = sprintf( __( 'There was an error subscribing to Sendy. %s', 'fl-builder' ), $get_api_response['message'] );
			}
		}

		return $response;
	}
}
