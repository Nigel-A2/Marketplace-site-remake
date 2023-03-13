<?php

/**
 * Helper class for the A API.
 *
 * @since 1.9
 */
final class FLBuilderServiceMailerLite extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.9
	 * @var string $id
	 */
	public $id = 'mailerlite';

	/**
	 * The API URL
	 *
	 * @since 1.9
	 * @var string $api_url
	 */
	public $api_url = 'https://app.mailerlite.com/api/v2/';

	/**
	 * @since 1.9
	 * @var object $api_instance
	 * @access private
	 */
	private $api_instance = null;

	/**
	 * Get an instance of the API.
	 *
	 * @since 1.9
	 * @param string $api_key A valid API token.
	 * @return object The API instance.
	 */
	public function get_api( $api_key ) {
		if ( $this->api_instance ) {
			return $this->api_instance;
		}

		require_once FL_BUILDER_DIR . 'includes/vendor/mailerlite/autoload.php';
		require FL_BUILDER_DIR . 'includes/vendor/mailerlite/guzzlehttp/psr7/src/functions_include.php';

		$groupsapi = new \MailerLiteApi\MailerLite( $api_key );
		return $groupsapi;
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

		// Make sure we have an API token.
		if ( ! isset( $fields['api_key'] ) ) {
			$response['error'] = __( 'Error: You must provide an API token.', 'fl-builder' );
		} else {

			$api = $this->get_api( $fields['api_key'] );

			$get_api_response = $api->groups()->count();

			if ( isset( $get_api_response->count ) ) {
				$response['data'] = array(
					'api_key' => $fields['api_key'],
				);
			} else {
				$response['error'] = __( 'Error: Could not connect to MailerLite.', 'fl-builder' );
			}
		}

		return $response;
	}

	/**
	 * Renders the markup for the connection settings.
	 *
	 * @since 1.9
	 * @return string The connection settings markup.
	 */
	public function render_connect_settings() {
		ob_start();

		FLBuilder::render_settings_field( 'api_key', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'API Key', 'fl-builder' ),
			'help'      => __( 'Found in your MailerLite account under Integrations > Developer API.', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		));

		return ob_get_clean();
	}

	/**
	 * Render the markup for service specific fields.
	 *
	 * @since 1.9
	 * @param string $account The name of the saved account.
	 * @param object $settings Saved module settings.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 *      @type string $html The field markup.
	 * }
	 */
	public function render_fields( $account, $settings ) {
		$account_data = $this->get_account_data( $account );
		$api          = $this->get_api( $account_data['api_key'] )->groups();
		$get_lists    = $api->get();
		$lists        = array();

		if ( $get_lists && ! isset( $get_lists->error ) && count( $get_lists ) > 0 ) {
			$lists = $get_lists;
		}

		$response = array(
			'error' => false,
			'html'  => $this->render_list_field( $lists, $settings ),
		);

		return $response;
	}

	/**
	 * Render markup for the list field.
	 *
	 * @since 1.9
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
				$options[ $list->id ] = esc_attr( $list->name );
			}
		}

		FLBuilder::render_settings_field( 'list_id', array(
			'row_class' => 'fl-builder-service-field-row',
			'class'     => 'fl-builder-service-list-select',
			'type'      => 'select',
			'label'     => _x( 'Group', 'An email list from a third party provider.', 'fl-builder' ),
			'options'   => $options,
			'preview'   => array(
				'type' => 'none',
			),
		), $settings);

		return ob_get_clean();
	}

	/**
	 * Subscribe an email address to Drip.
	 *
	 * @since 1.9
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
			$response['error'] = __( 'There was an error subscribing to MailerLite. The account is no longer connected.', 'fl-builder' );
		} else {

			$api = $this->get_api( $account_data['api_key'] );

			$data['email'] = $email;

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

			// Search if it's an existing subscriber
			/*
			$api->setPath('subscribers/search?query='. $email);
			$search_results = json_decode($api->getAll());
			if ( $search_results && isset($search_results[0]->id) ) {
				echo 'existing email...';
				// Update subscriber
				$api->setPath('subscribers/'. $search_results[0]->id);

				$response = $api->put($data);
			}*/

			// Add new
			$result = $api->groups()->addSubscriber( $settings->list_id, $data );

			if ( ! is_object( $result ) || ! isset( $result->id ) ) {
				$response['error'] = __( 'There was an error subscribing to MailerLite.', 'fl-builder' );
			}
		}

		return $response;
	}
}
