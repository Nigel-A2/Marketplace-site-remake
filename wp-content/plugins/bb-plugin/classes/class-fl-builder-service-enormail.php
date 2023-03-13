<?php

/**
 * Helper class for the Enormail API.
 *
 * @since 1.9.5
 */
final class FLBuilderServiceEnormail extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.9.5
	 * @var string $id
	 */
	public $id = 'enormail';

	/**
	 * @since 1.9.5
	 * @var object $api_instance
	 * @access private
	 */
	private $api_instance = null;

	/**
	 * Get an instance of the API.
	 *
	 * @since 1.9.5
	 * @param array $api_key A valid API key to authenticate.
	 * @return object The API instance.
	 */
	public function get_api( $api_key ) {
		if ( $this->api_instance ) {
			return $this->api_instance;
		}

		if ( ! class_exists( '\\Enormail\\ApiClient' ) ) {
			require_once FL_BUILDER_DIR . 'includes/vendor/enormail/autoload.php';
		}

		$this->api_instance = new \Enormail\ApiClient( $api_key );

		return $this->api_instance;
	}

	/**
	 * Test the API connection.
	 *
	 * @since 1.9.5
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

		// Make sure we have an API key.
		if ( ! isset( $fields['api_key'] ) || empty( $fields['api_key'] ) ) {
			$response['error'] = __( 'Error: You must provide an API key.', 'fl-builder' );
		} else {

			$api = $this->get_api( $fields['api_key'] );

			// Fetch account info
			$api_response = json_decode( $api->test() );

			if ( isset( $api_response->ping ) && 'hello' === $api_response->ping ) {
				$response['data'] = array(
					'api_key' => $fields['api_key'],
				);
			} else {
				/* translators: %s: error */
				$response['error'] = sprintf(__( 'Error: Could not connect to Enormail. %s', 'fl-builder' ),
					'(' . $api_response->error->http_code . ': ' . $api_response->error->message . ')'
				);
			}
		}

		return $response;
	}

	/**
	 * Renders the markup for the connection settings.
	 *
	 * @since 1.9.5
	 * @return string The connection settings markup.
	 */
	public function render_connect_settings() {
		ob_start();

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

		return ob_get_clean();
	}

	/**
	 * Render the markup for service specific fields.
	 *
	 * @since 1.9.5
	 * @param string $account The name of the saved account.
	 * @param object $settings Saved module settings.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 *      @type string $html The field markup.
	 * }
	 */
	public function render_fields( $account, $settings ) {
		$account_data = $this->get_account_data( $account );
		$api          = $this->get_api( $account_data['api_key'] );
		$lists        = json_decode( $api->lists->get() );
		$response     = array(
			'error' => false,
			'html'  => '',
		);

		if ( ! $lists ) {
			$response['error'] = __( 'Error: Please check your API key.', 'fl-builder' );
		} else {
			$response['html'] = $this->render_list_field( $lists, $settings );
		}

		return $response;
	}

	/**
	 * Render markup for the list field.
	 *
	 * @since 1.9.5
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
			if ( isset( $list->listid ) ) {
				$options[ $list->listid ] = esc_attr( $list->title );
			}
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
	 * Subscribe an email address to Sendy.
	 *
	 * @since 1.9.5
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
			$response['error'] = __( 'There was an error subscribing to Enormail. The account is no longer connected.', 'fl-builder' );
		} else {

			$api = $this->get_api( $account_data['api_key'] );

			// Search user if already exists
			$contact = json_decode( $api->contacts->details( $settings->list_id, $email ) );

			// Name is required
			if ( empty( $name ) ) {
				$name = explode( '@', $email )[0];
			}

			// Add if not exists
			if ( -1 == $contact->code ) {
				$result = $api->contacts->add( $settings->list_id, $name, $email );
			} else {
				$result = $api->contacts->update( $settings->list_id, $name, $email );
			}

			$get_results = json_decode( $result );

			if ( isset( $get_results->status ) && 'error' === $get_results->status ) {
				/* translators: %s: error */
				$response['error'] = sprintf(__( 'There was an error subscribing to Enormail. %s', 'fl-builder' ),
					'(' . $get_results->code . ': ' . $get_results->message . ')'
				);
			}
		}

		return $response;
	}
}
