<?php

/**
 * Helper class for the ConvertKit API.
 *
 * @since 1.5.4
 */
final class FLBuilderServiceConvertKit extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.5.4
	 * @var string $id
	 */
	public $id = 'convertkit';

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
	 * @param string $api_key A valid API key.
	 * @return object The API instance.
	 */
	public function get_api( $api_key ) {
		if ( $this->api_instance ) {
			return $this->api_instance;
		}
		if ( ! class_exists( 'ConvertKit' ) ) {
			require_once FL_BUILDER_DIR . 'includes/vendor/convertkit/ConvertKit.php';
		}

		$this->api_instance = new ConvertKit( $api_key );

		return $this->api_instance;
	}

	/**
	 * Test the API connection.
	 *
	 * @since 1.5.4
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

		// Make sure we have an API key.
		if ( ! isset( $fields['api_key'] ) || empty( $fields['api_key'] ) ) {
			$response['error'] = __( 'Error: You must provide an API key.', 'fl-builder' );
		} else {

			$api = $this->get_api( $fields['api_key'] );

			if ( $api->is_authenticated() ) {
				$response['data'] = array(
					'api_key' => $fields['api_key'],
				);
			} else {
				/* translators: %s: error */
				$response['error'] = sprintf( __( 'Error: Please check your API key. %s', 'fl-builder' ), $api::$response['error_message'] );
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

		FLBuilder::render_settings_field( 'api_key', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'API Key', 'fl-builder' ),
			'help'      => __( 'Your API key can be found in your ConvertKit account under Account > Account Settings > API Key.', 'fl-builder' ),
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
		$api          = $this->get_api( $account_data['api_key'] );
		$forms        = $api->get_resources( 'forms' );
		$response     = array(
			'error' => false,
			'html'  => '',
		);

		if ( ! $forms ) {
			$response['error'] = __( 'Error: Please check your API key.', 'fl-builder' );
		} else {
			$response['html'] = $this->render_list_field( $forms, $settings );
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
	private function render_list_field( $forms, $settings ) {
		ob_start();

		$options = array(
			'' => __( 'Choose...', 'fl-builder' ),
		);

		if ( isset( $forms['forms'] ) ) {
			foreach ( $forms['forms'] as $form ) {
				$options[ $form['id'] ] = esc_attr( $form['name'] );
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
	 * Subscribe an email address to ConvertKit.
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
			$response['error'] = __( 'There was an error subscribing to ConvertKit. The account is no longer connected.', 'fl-builder' );
		} else {

			$api  = $this->get_api( $account_data['api_key'] );
			$data = array(
				'email' => $email,
			);

			if ( ! empty( $name ) ) {
				$data['fname'] = $name;
			}

			$result = $api->form_subscribe( $settings->list_id, $data );

			if ( isset( $result->error ) ) {
				$message           = isset( $result->message ) ? $result->message : '';
				$response['error'] = sprintf(
					/* translators: %s: error */
					__( 'There was an error subscribing to ConvertKit. Error: %s', 'fl-builder' ),
					$message
				);
			}
		}

		return $response;
	}
}
