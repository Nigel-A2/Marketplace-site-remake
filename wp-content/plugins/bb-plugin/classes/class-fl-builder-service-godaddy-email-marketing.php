<?php

/**
 * Helper class for the GoDaddy Email Marketing.
 *
 * @since 1.10.5
 */
final class FLBuilderServiceGoDaddyEmailMarketing extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.10.5
	 * @var string $id
	 */
	public $id = 'godaddy-email-marketing';

	/**
	 * @since 1.10.5
	 * @var object $api_instance
	 * @access private
	 */
	private $api_instance = null;

	/**
	 * Get an instance of the API.
	 *
	 * @since 1.10.5
	 * @param string $api_username The email address associated with the API key.
	 * @param string $api_key A valid API key.
	 * @return object The API instance.
	 */
	public function get_api( $api_username, $api_key ) {
		if ( $this->api_instance ) {
			return $this->api_instance;
		}
		if ( ! class_exists( 'GoDaddyEM' ) ) {
			require_once FL_BUILDER_DIR . 'includes/vendor/godaddy-email-marketing/class-godaddy-em.php';
		}

		$this->api_instance = new GoDaddyEM( $api_username, $api_key );

		return $this->api_instance;
	}

	/**
	 * Test the API connection.
	 *
	 * @since 1.10.5
	 * @param array $fields {
	 *      @type string $api_username A valid API username
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

		// Make sure we have an email address.
		if ( ! isset( $fields['api_username'] ) || empty( $fields['api_username'] ) ) {
			$response['error'] = __( 'Error: You must provide an API username.', 'fl-builder' );
		} elseif ( ! isset( $fields['api_key'] ) || empty( $fields['api_key'] ) ) {
			$response['error'] = __( 'Error: You must provide an API key.', 'fl-builder' );
		} else { // Try to connect and store the connection data.

			$api = $this->get_api( $fields['api_username'], $fields['api_key'] );

			if ( ! $api::is_account_ok() ) {
				$response['error'] = __( 'Unable to connect to GoDaddy Email Marketing. Please check your credentials.', 'fl-builder' );
			} else {
				$response['data'] = array(
					'api_username' => $fields['api_username'],
					'api_key'      => $fields['api_key'],
				);
			}
		}

		return $response;
	}

	/**
	 * Renders the markup for the connection settings.
	 *
	 * @since 1.10.5
	 * @return string The connection settings markup.
	 */
	public function render_connect_settings() {
		ob_start();

		FLBuilder::render_settings_field( 'api_username', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'API Username', 'fl-builder' ),
			'help'      => __( 'The username associated with your GoDaddy Email Marketing account.', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		));

		FLBuilder::render_settings_field( 'api_key', array(
			'row_class'   => 'fl-builder-service-connect-row',
			'class'       => 'fl-builder-service-connect-input',
			'type'        => 'text',
			'label'       => __( 'API Key', 'fl-builder' ),
			'help'        => __( 'Your API key from your GoDaddy Email Marketing account.', 'fl-builder' ),
			/* translators: 1: Godaddy account page: 2: Godaddy signup page */
			'description' => sprintf( __( '<a%1$s>Sign in</a> to get your username and API key. <a%2$s>Signup</a> if you don\'t have a GoDaddy Email Marketing account.', 'fl-builder' ), ' href="https://gem.godaddy.com/mwp/accounts" target="_blank"', ' href="https://sso.godaddy.com/account/create?path=/wordpress_plugin&app=gem&realm=idp&ssoreturnpath=/%3Fpath%3D%2Fwordpress_plugin%26app%3Dgem%26realm%3Didp" target="_blank"' ),
			'preview'     => array(
				'type' => 'none',
			),
		));

		return ob_get_clean();
	}

	/**
	 * Render the markup for service specific fields.
	 *
	 * @since 1.10.5
	 * @param string $account The name of the saved account.
	 * @param object $settings Saved module settings.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 *      @type string $html The field markup.
	 * }
	 */
	public function render_fields( $account, $settings ) {
		$account_data = $this->get_account_data( $account );
		$api          = $this->get_api( $account_data['api_username'], $account_data['api_key'] );
		$response     = array(
			'error' => false,
			'html'  => '',
		);

		$result = $api::get_forms();

		if ( ! $result ) {
			$response['error'] = __( 'There was a problem retrieving your lists. Please check your API credentials.', 'fl-builder' );
		} else {
			$response['html'] = $this->render_list_field( $result, $settings );
		}

		return $response;
	}

	/**
	 * Render markup for the list field.
	 *
	 * @since 1.10.5
	 * @param array $forms GoDaddy Signup Forms data from the API.
	 * @param object $settings Saved module settings.
	 * @return string The markup for the list field.
	 * @access private
	 */
	private function render_list_field( $forms, $settings ) {
		ob_start();

		$options = array(
			'' => __( 'Choose...', 'fl-builder' ),
		);

		if ( ! empty( $forms->signups ) ) {
			foreach ( $forms->signups as $form ) {
				$options[ $form->id ] = esc_attr( $form->name );
			}
		}

		FLBuilder::render_settings_field( 'form_id', array(
			'row_class' => 'fl-builder-service-field-row',
			'class'     => 'fl-builder-service-list-select',
			'type'      => 'select',
			'label'     => _x( 'Form', 'A signup form from your GoDaddy Email Marketing account.', 'fl-builder' ),
			'options'   => $options,
			'preview'   => array(
				'type' => 'none',
			),
		), $settings);

		return ob_get_clean();
	}

	/**
	 * Subscribe an email address to GoDaddy Email Marketing.
	 *
	 * @since 1.10.5
	 * @param object $settings A module settings object.
	 * @param string $email The email to subscribe.
	 * @param string $name Optional. The full name of the person subscribing.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 * }
	 */
	public function subscribe( $settings, $email, $name = false ) {
		$account_data = $this->get_account_data( $settings->service_account );
		$response     = array(
			'error' => false,
		);

		if ( ! $account_data ) {
			$response['error'] = __( 'There was an error subscribing to GoDaddy Email Marketing. The account is no longer connected.', 'fl-builder' );
		} else {

			$api  = $this->get_api( $account_data['api_username'], $account_data['api_key'] );
			$data = array(
				'email'   => $email,
				'form_id' => $settings->form_id,
			);

			if ( $name ) {

				$names = explode( ' ', $name );

				if ( isset( $names[0] ) ) {
					$data['first_name'] = $names[0];
				}
				if ( isset( $names[1] ) ) {
					$data['last_name'] = $names[1];
				}
			}

			$result = $api->add_subscriber( $data );

			if ( ! $result ) {
				$response['error'] = __( 'There was an error subscribing to GoDaddy Email Marketing. The account is no longer connected.', 'fl-builder' );
			}
		}

		return $response;
	}
}
