<?php

if ( ! class_exists( '\Mailjet\Config' ) ) {
	/**
	 * Autoloader is generated via Composer.
	 *
	 * For details, check here:
	 * https://github.com/mailjet/mailjet-apiv3-php
	 *
	 */
	require_once FL_BUILDER_DIR . 'includes/vendor/mailjet/autoload.php';
}

/**
 * Helper class for the A API.
 *
 * @since 2.4
 */
final class FLBuilderServiceMailjet extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 2.4
	 * @var string $id
	 */
	public $id = 'mailjet';

	/**
	 * The API URL
	 *
	 * @since 2.4
	 * @var string $api_url
	 */
	public $api_url = 'https://api.mailjet.com/v3/';

	/**
	 * @since 2.4
	 * @var object $api_instance
	 * @access private
	 */
	private $api_instance = null;

	/**
	 * Get an instance of the API.
	 *
	 * @since 2.4
	 * @param string $api_key A valid API token.
	 * @return object The API instance.
	 */
	public function get_api( $api_key, $secret_key ) {

		if ( $this->api_instance ) {
			return $this->api_instance;
		}

		$this->api_instance = new \Mailjet\Client( $api_key, $secret_key );

		return $this->api_instance;
	}

	/**
	 * Test the API connection.
	 *
	 * @since 1.9
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

		// Make sure both API and Secret Keys are provided.
		if ( empty( $fields['api_key'] ) || empty( $fields['secret_key'] ) ) {
			$response['error'] = __( 'Error: Both Mailjet API and Secret Keys are required.', 'fl-builder' );
			return $response;
		}

		try {

			$api = $this->get_api( $fields['api_key'], $fields['secret_key'] );
			// phpcs:disable
			$mj_response = $api->get( \Mailjet\Resources::$Contactmetadata, array(
				'filters' => array(
					'Limit' => '1',
				),
			));
			// phpcs:enable

			if ( $mj_response->success() ) {
				$response['data'] = array(
					'api_key'    => $fields['api_key'],
					'secret_key' => $fields['secret_key'],
				);
			} else {
				$mj_status = $mj_response->getStatus();

				/* translators: %s: Mailjet Error Code */
				$response['error'] = sprintf( __( 'Error Code %s: Could not connect to Mailerjet.', 'fl-builder' ), $mj_status );

				if ( 401 == $mj_status ) {
					/* translators: %s: Mailjet Error Code */
					$response['error'] = sprintf( __( 'Error Code %s: You have specified an incorrect API Key / API Secret Key pair.', 'fl-builder' ), $mj_status );
				}
			}
		} catch ( ConnectException $e ) {
			$response['error'] = $e->getMessage();

			return $response;
		}

		return $response;
	}

	/**
	 * Renders the markup for the connection settings.
	 *
	 * @since 2.4
	 * @return string The connection settings markup.
	 */
	public function render_connect_settings() {
		ob_start();

		FLBuilder::render_settings_field( 'api_key', array(
			'row_class'   => 'fl-builder-service-connect-row',
			'class'       => 'fl-builder-service-connect-input',
			'type'        => 'text',
			'label'       => __( 'API Key', 'fl-builder' ),
			'help'        => __( 'Found in your Mailjet account under Account Settings > Rest API > Master API Key & Sub API Key Management.', 'fl-builder' ),
			'description' => sprintf( '<a target="_blank" href="https://app.mailjet.com/account/api_keys">%s</a>', __( 'Mailjet API settings', 'fl-builder' ) ),
			'preview'     => array(
				'type' => 'none',
			),
		));

		FLBuilder::render_settings_field( 'secret_key', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'Secret Key', 'fl-builder' ),
			'help'      => __( 'Found in your Mailjet account under Account Settings > Rest API > Master API Key & Sub API Key Management.', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		));

		return ob_get_clean();
	}

	/**
	 * Render the markup for service specific fields.
	 *
	 * @since 2.4
	 * @param string $account The name of the saved account.
	 * @param object $settings Saved module settings.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 *      @type string $html The field markup.
	 * }
	 */
	public function render_fields( $account, $settings ) {
		$mj_contact_list = array();

		try {
			$account_data = $this->get_account_data( $account );
			$api          = $this->get_api( $account_data['api_key'], $account_data['secret_key'] );
			// phpcs:disable
			$mj_response  = $api->get( \Mailjet\Resources::$Contactslist, array(
				'filters' => array(
					'Limit' => '100',
				),
			));
			// phpcs:enable

			if ( $mj_response->success() ) {
				$lists = $mj_response->getData();
				foreach ( $lists as $list ) {
					$mj_contact_list[] = (object) $list;
				}
			}
		} catch ( ConnectionException $e ) {
			$response = array(
				'error' => $e->getMessage(),
			);
		}

		$response = array(
			'error' => false,
			'html'  => $this->render_list_field( $mj_contact_list, $settings ),
		);

		return $response;
	}

	/**
	 * Render markup for the list field.
	 *
	 * @since 2.4
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

		if ( $lists ) {
			foreach ( $lists as $list ) {
				// phpcs:disable
				$options[ $list->ID ] = esc_attr( $list->Name );
				// phpcs:enable
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
	 * Subscribe an email address to Mailjet.
	 *
	 * @since 2.4
	 * @param object $settings A module settings object.
	 * @param string $email The email to subscribe.
	 * @param string $name Optional. The full name of the person subscribing.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 * }
	 */
	public function subscribe( $settings, $email, $name = '' ) {
		$data = array(
			'email'     => $email,
			'name'      => '',
			'last_name' => '',
			'list_id'   => $settings->list_id,
		);

		$response = array(
			'error' => false,
		);

		$account_data = $this->get_account_data( $settings->service_account );

		if ( ! $account_data ) {
			$response['error'] = __( 'There was an error subscribing to Mailerjet. The account is no longer connected.', 'fl-builder' );
		} else {

			$api = $this->get_api( $account_data['api_key'], $account_data['secret_key'] );

			// Add the name to the data array if we have one.
			if ( $name ) {

				$names = explode( ' ', $name );

				if ( isset( $names[0] ) ) {
					$data['name'] = $names[0];
				}
				if ( isset( $names[1] ) ) {
					$data['last_name'] = $names[1];
				}
			}

			try {
				$exists            = false;
				$exists_subscribed = false;

				// phpcs:disable
				$mj_response = $api->get(\Mailjet\Resources::$Listrecipient, array(
					'filters' => array(
						'ContactEmail' => $data['email'],
						'ContactsList' => $data['list_id'],
					),
				));
				// phpcs:enable

				if ( $mj_response->success() && $mj_response->getCount() > 0 ) {
					$mj_data = $mj_response->getData();
					$exists  = true;

					if ( isset( $mj_data[0]['IsUnsubscribed'] ) && false == $mj_data[0]['IsUnsubscribed'] ) {
						$exists_subscribed = true;
					}
				}

				if ( $exists && $exists_subscribed ) {
					/* translators: %1$s for email address %2$s for list_id */
					$response['error'] = sprintf( __( 'Email address (%1$s) already exists and subscribed to the list (%2$s).', 'fl-builder' ), $data['email'], $data['list_id'] );
				} else {

					// phpcs:disable
					$mj_response = $api->post(\Mailjet\Resources::$ContactslistManagecontact, array(
						'id'   => $data['list_id'],
						'body' => array(
							'Name'       => $data['name'],
							'Properties' => 'object',
							'Action'     => 'addnoforce',
							'Email'      => $data['email'],
						),
					));
					// phpcs:enable

					if ( ! $mj_response->success() ) {
						/* translators: %1$s for email address %2$s for list_id */
						$response['error'] = sprintf( __( 'Mailjet subscription failed. Email address = %1$s; List ID = %2$s. ', 'fl-builder' ), $data['email'], $data['list_id'] );
					}
				}
			} catch ( ConnectException $e ) {
				$response['error'] = $e->getMessage();
			}
		}

		return $response;
	}
}
