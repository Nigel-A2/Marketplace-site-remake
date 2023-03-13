<?php

/**
 * Helper class for the Ontraport API.
 *
 * @since 2.1
 */
final class FLBuilderServiceOntraport extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 2.1
	 * @var string $id
	 */
	public $id = 'ontraport';

	/**
	 * @since 2.1
	 * @var object $api_instance
	 * @access private
	 */
	private $api_instance = null;

	/**
	 * Get an instance of the API.
	 *
	 * @since 2.1
	 * @param string $app_id A valid APP ID.
	 * @param string $api_key A valid API key.
	 * @return object The API instance.
	 */
	public function get_api( $app_id, $api_key ) {

		if ( $this->api_instance ) {
			return $this->api_instance;
		}

		if ( ! class_exists( '\\OntraportAPI\\Ontraport' ) ) {
			require_once FL_BUILDER_DIR . 'includes/vendor/ontraport/Ontraport.php';
		}

		$this->api_instance = new \OntraportAPI\Ontraport( $app_id, $api_key );

		return $this->api_instance;
	}

	/**
	 * Test the API connection.
	 *
	 * @since 2.1
	 * @param array $fields {
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

		// Make sure we have an API token.
		if ( ! isset( $fields['app_id'] ) || empty( $fields['app_id'] ) ) {
			$response['error'] = __( 'Error: You must provide an APP ID.', 'fl-builder' );
		} elseif ( ! isset( $fields['api_key'] ) || empty( $fields['api_key'] ) ) { // Make sure we have an Account ID.
			$response['error'] = __( 'Error: You must provide an API key.', 'fl-builder' );
		} else { // Try to connect and store the connection data.

			$api = $this->get_api( $fields['app_id'], $fields['api_key'] );

			// Try to request something to authenticate the validity of APP ID and API Key
			$search = json_decode( $api->contact()->retrieveMultiple( array(
				'range' => 1,
			)));

			$status_code = $api->getLastStatusCode();

			if ( 200 === $status_code ) {
				$response['data'] = array(
					'api_key' => $fields['api_key'],
					'app_id'  => $fields['app_id'],
				);
			} else {
				$response['error'] = sprintf(
					/* translators: %s: error code */
					__( 'Error: Please check your API token. Code: %s', 'fl-builder' ),
					$status_code
				);
			}
		}

		return $response;
	}

	/**
	 * Renders the markup for the connection settings.
	 *
	 * @since 2.1
	 * @return string The connection settings markup.
	 */
	public function render_connect_settings() {
		ob_start();

		FLBuilder::render_settings_field( 'app_id', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'APP ID', 'fl-builder' ),
			'help'      => __( 'Your APP ID can be found in your Ontraport account.', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		));

		FLBuilder::render_settings_field( 'api_key', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'API Key', 'fl-builder' ),
			'help'      => __( 'Your API key can be found in your Ontraport account.', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		));

		return ob_get_clean();
	}

	/**
	 * Render the markup for service specific fields.
	 *
	 * @since 2.1
	 * @param string $account The name of the saved account.
	 * @param object $settings Saved module settings.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 *      @type string $html The field markup.
	 * }
	 */
	public function render_fields( $account, $settings ) {
		$account_data   = $this->get_account_data( $account );
		$api            = $this->get_api( $account_data['app_id'], $account_data['api_key'] );
		$campaigns      = json_decode( $api->campaignbuilder()->retrieveMultiplePaginated( array(
			'listFields' => 'id, name',
			'start'      => 0,
			'range'      => 50,
		)));
		$campaigns_list = array();

		if ( $campaigns ) {
			foreach ( $campaigns as $obj ) {
				if ( isset( $obj->data ) && count( $obj->data ) > 0 ) {
					$campaigns_list = array_merge( $campaigns_list, $obj->data );
				}
			}
		}

		$response = array(
			'error' => false,
			'html'  => $this->render_campaigns_field( $campaigns_list, $settings ),
		);

		return $response;
	}

	/**
	 * Render markup for the campaign field.
	 *
	 * @since 2.1
	 * @param array $campaigns Campaigns data from the API.
	 * @param object $settings Saved module settings.
	 * @return string The markup for the campaign field.
	 * @access private
	 */
	private function render_campaigns_field( $campaigns, $settings ) {
		ob_start();

		$options = array(
			'0' => __( 'Choose...', 'fl-builder' ),
		);

		if ( $campaigns > 0 ) {
			foreach ( $campaigns as $campaign ) {
				$options[ $campaign->id ] = esc_attr( $campaign->name );
			}
		}

		FLBuilder::render_settings_field( 'campaign_id', array(
			'row_class' => 'fl-builder-service-field-row',
			'class'     => 'fl-builder-service-list-select',
			'type'      => 'select',
			'label'     => _x( 'Campaign', 'An email campaign from your Ontraport account.', 'fl-builder' ),
			'options'   => $options,
			'default'   => 0,
			'preview'   => array(
				'type' => 'none',
			),
		), $settings);

		return ob_get_clean();
	}

	/**
	 * Subscribe an email address to Ontraport.
	 *
	 * @since 2.1
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
			$response['error'] = __( 'There was an error subscribing to Ontraport. The account is no longer connected.', 'fl-builder' );
		} else {

			$api  = $this->get_api( $account_data['app_id'], $account_data['api_key'] );
			$args = array(
				'email'          => $email,
				'updateCampaign' => $settings->campaign_id,
			);

			// Add the name to the data array if we have one.
			if ( $name ) {
				$names = explode( ' ', $name );

				if ( isset( $names[0] ) ) {
					$args['firstname'] = $names[0];
				}
				if ( isset( $names[1] ) ) {
					$args['lastname'] = $names[1];
				}
			}

			// Save or update subscriber.
			$result = $api->contact()->saveOrUpdate( $args );

			if ( 200 !== $api->getLastStatusCode() ) {
				$response['error'] = sprintf(
					/* translators: %s: error code */
					__( 'There was an error subscribing to Ontraport. Code: %s', 'fl-builder' ),
					$api->getLastStatusCode()
				);
			}
		}

		return $response;
	}
}
