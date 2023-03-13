<?php

/**
 * Helper class for the SendinBlue API.
 *
 * @since 1.5.6
 */
final class FLBuilderServiceSendinBlue extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.5.6
	 * @var string $id
	 */
	public $id = 'sendinblue';

	/**
	 * @since 1.5.6
	 * @var object $api_instance
	 * @access private
	 */
	private $api_instance = null;

	/**
	 * Get an instance of the API.
	 *
	 * @since 1.5.6
	 * @param string $access_key A valid access key.
	 * @return object The API instance.
	 */
	public function get_api( $access_key ) {
		if ( $this->api_instance ) {
			return $this->api_instance;
		}
		if ( ! class_exists( 'Mailin_Rest' ) ) {
			require_once FL_BUILDER_DIR . 'includes/vendor/sendinblue/Mailin.php';
		}

		$this->api_instance = new Mailin_Rest( 'https://api.sendinblue.com/v3', $access_key );

		return $this->api_instance;
	}

	/**
	 * Test the API connection.
	 *
	 * @since 1.5.6
	 * @param array $fields {
	 *      @type string $access_key A valid access key.
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

		// Make sure we have an access key.
		if ( ! isset( $fields['access_key'] ) || empty( $fields['access_key'] ) ) {
			$response['error'] = __( 'Error: You must provide an API Key.', 'fl-builder' );
		} elseif ( ! strstr( $fields['access_key'], 'xkeysib-' ) ) {
			$response['error'] = __( 'Error: API key needs to be upgraded to v3.', 'fl-builder' );
		} else {

			$api    = $this->get_api( $fields['access_key'] );
			$result = $api->get_account();

			if ( ! is_array( $result ) ) {
				$response['error'] = __( 'There was an error connecting to SendinBlue. Please try again.', 'fl-builder' );
			} elseif ( isset( $result['code'] ) && 'unauthorized' == $result['code'] ) {
				/* translators: %s: error */
				$response['error'] = sprintf( __( 'Error: Could not connect to SendinBlue. %s', 'fl-builder' ), $result['message'] );
			} else {
				$response['data'] = array(
					'access_key' => $fields['access_key'],
				);
			}
		}

		return $response;
	}

	/**
	 * Renders the markup for the connection settings.
	 *
	 * @since 1.5.6
	 * @return string The connection settings markup.
	 */
	public function render_connect_settings() {
		ob_start();

		FLBuilder::render_settings_field( 'access_key', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'API Key', 'fl-builder' ),
			'help'      => __( 'Your API Key can be found in your SendinBlue account under SMTP & API > API Keys > Version v3 > API KEY.', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		));

		return ob_get_clean();
	}

	/**
	 * Render the markup for service specific fields.
	 *
	 * @since 1.5.6
	 * @param string $account The name of the saved account.
	 * @param object $settings Saved module settings.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 *      @type string $html The field markup.
	 * }
	 */
	public function render_fields( $account, $settings ) {
		$account_data = $this->get_account_data( $account );
		$api          = $this->get_api( $account_data['access_key'] );
		$response     = array(
			'error' => false,
			'html'  => '',
		);

		$result = $api->get_lists( 50 );

		if ( ! is_array( $result ) ) {
			$response['error'] = __( 'There was an error connecting to SendinBlue. Please try again.', 'fl-builder' );
		} elseif ( isset( $result['code'] ) && 'unauthorized' == $result['code'] ) {
			/* translators: %s: error */
			$response['error'] = sprintf( __( 'Error: Could not connect to SendinBlue. %s', 'fl-builder' ), $result['message'] );
		} else {
			$response['html'] = $this->render_list_field( $result['lists'], $settings );
		}

		return $response;
	}

	/**
	 * Render markup for the list field.
	 *
	 * @since 1.5.6
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
	 * Subscribe an email address to SendinBlue.
	 *
	 * @since 1.5.6
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
			$response['error'] = __( 'There was an error subscribing to SendinBlue. The account is no longer connected.', 'fl-builder' );
		} else {

			$api  = $this->get_api( $account_data['access_key'] );
			$data = new stdClass();

			if ( $name ) {

				$contact_attrs = $api->get_contact_attributes();
				$names         = explode( ' ', $name );
				if ( isset( $names[0] ) ) {
					$data->NAME = $names[0]; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

					if ( in_array( 'FIRSTNAME', $contact_attrs ) ) {
						$data->FIRSTNAME = $names[0]; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					}
				}
				if ( isset( $names[1] ) ) {
					$data->SURNAME = $names[1]; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase

					if ( in_array( 'LASTNAME', $contact_attrs ) ) {
						$data->LASTNAME = $names[1]; // phpcs:ignore WordPress.NamingConventions.ValidVariableName.UsedPropertyNotSnakeCase
					}
				}
			}
			$settings->list_id = (int) $settings->list_id;
			$result            = $api->create_update_user( $email, $data, false, array( $settings->list_id ), true, array(), false );

			if ( isset( $result['code'] ) && 'unauthorized' == $result['code'] ) {
				if ( 'Key not found' === $result['message'] ) {
					$response['error'] = 'Error: API key needs to be upgraded to v3.';
				} else {
					/* translators: %s: error */
					$response['error'] = sprintf( __( 'Error: Could not subscribe to SendinBlue. %s', 'fl-builder' ), $result['message'] );
				}
			}
		}

		return $response;
	}
}
