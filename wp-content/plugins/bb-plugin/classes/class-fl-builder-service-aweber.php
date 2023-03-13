<?php

/**
 * Helper class for the AWeber API.
 *
 * @since 1.5.4
 */
final class FLBuilderServiceAWeber extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.5.4
	 * @var string $id
	 */
	public $id = 'aweber';

	/**
	 * @since 1.5.4
	 * @var object $api_instance
	 * @access private
	 */
	private $api_instance = null;

	private $lists = false;

	/**
	 * Get an instance of the API.
	 *
	 * @since 1.5.4
	 * @param string $auth_code A valid authorization code.
	 * @return object The API instance.
	 */
	public function get_api( $auth_code ) {
		if ( $this->api_instance ) {
			return $this->api_instance;
		}
		if ( ! class_exists( 'AWeberAPI' ) ) {
			require_once FL_BUILDER_DIR . 'includes/vendor/aweber/aweber_api.php';
		}

		list( $auth_key, $auth_token, $req_key, $req_token, $oauth ) = explode( '|', $auth_code );

		$this->api_instance                     = new AWeberAPI( $auth_key, $auth_token );
		$this->api_instance->user->requestToken = $req_key;
		$this->api_instance->user->tokenSecret  = $req_token;
		$this->api_instance->user->verifier     = $oauth;

		return $this->api_instance;
	}

	/**
	 * Test the API connection.
	 *
	 * @since 1.5.4
	 * @param array $fields {
	 *      @type string $auth_code A valid authorization code.
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

		// Make sure we have an authorization code.
		if ( ! isset( $fields['auth_code'] ) || empty( $fields['auth_code'] ) ) {
			$response['error'] = __( 'Error: You must provide an Authorization Code.', 'fl-builder' );
		} elseif ( 6 != count( explode( '|', $fields['auth_code'] ) ) ) {
			$response['error'] = __( 'Error: Please enter a valid Authorization Code.', 'fl-builder' );
		} else { // Try to connect and store the connection data.

			$api = $this->get_api( $fields['auth_code'] );

			// Get an access token from the API.
			try {
				list( $access_token, $access_token_secret ) = $api->getAccessToken();
			} catch ( AWeberException $e ) {
				$response['error'] = $e->getMessage();
			}

			// Make sure we can get the account.
			try {
				$account = $api->getAccount();
			} catch ( AWeberException $e ) {
				$response['error'] = $e->getMessage();
			}

			// Build the response data.
			if ( ! $response['error'] ) {

				$response['data'] = array(
					'auth_code'     => $fields['auth_code'],
					'access_token'  => $access_token,
					'access_secret' => $access_token_secret,
				);
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

		FLBuilder::render_settings_field( 'auth_code', array(
			'row_class'   => 'fl-builder-service-connect-row',
			'class'       => 'fl-builder-service-connect-input',
			'type'        => 'text',
			'label'       => __( 'Authorization Code', 'fl-builder' ),
			/* translators: %s: register url */
			'description' => sprintf( __( 'Please register this website with AWeber to get your Authorization Code. <a%s>Register Now</a>', 'fl-builder' ), ' href="https://auth.aweber.com/1.0/oauth/authorize_app/baa1f131" target="_blank"' ),
			'preview'     => array(
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
		$api          = $this->get_api( $account_data['auth_code'] );
		$response     = array(
			'error' => false,
			'html'  => '',
		);

		try {
			$account = $api->getAccount( $account_data['access_token'], $account_data['access_secret'] );
			$this->fetch_lists( $account );
			if ( is_object( $this->lists ) && is_array( $this->lists->data['entries'] ) ) {
				$response['html']  = $this->render_list_field( $this->lists, $settings );
				$response['html'] .= $this->render_tags_field( $settings );
			} else {
				$response['error'] = 'Unable to fetch lists';
			}
		} catch ( AWeberException $e ) {
			$response['error'] = $e->getMessage();
		}

		return $response;
	}

	private function fetch_lists( $account, $offset = false ) {

		$offset_txt = '';
		try {
			if ( $offset ) {
				$offset_txt = '?ws.start=' . $offset;
			}
			$list = $account->loadFromUrl( '/accounts/' . $account->id . '/lists' . $offset_txt );
		} catch ( AWeberException $e ) {
			return false;
		}

		if ( ! $this->lists ) {
			$this->lists = $list;
		} else {
			$this->lists->data['entries'] = array_merge( $this->lists->data['entries'], $list->data['entries'] );
		}

		if ( count( $list->data['entries'] ) === 100 ) {
			$offset = count( $this->lists->data['entries'] ) + 1;
			$this->fetch_lists( $account, $offset );
		}
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
	private function render_list_field( $lists, $settings ) {
		ob_start();

		$options = array(
			'' => __( 'Choose...', 'fl-builder' ),
		);

		foreach ( $lists->data['entries'] as $list ) {
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
	 * Render markup for the tags field.
	 *
	 * @since 1.8.8
	 * @param object $settings Saved module settings.
	 * @return string The markup for the tags field.
	 * @access private
	 */
	private function render_tags_field( $settings ) {
		ob_start();

		FLBuilder::render_settings_field( 'tags', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => _x( 'Tags', 'A comma separated list of tags.', 'fl-builder' ),
			'help'      => __( 'A comma separated list of tags.', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		), $settings);

		return ob_get_clean();
	}

	/**
	 * Subscribe an email address to AWeber.
	 *
	 * @since 1.5.4
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
			$response['error'] = __( 'There was an error subscribing to AWeber. The account is no longer connected.', 'fl-builder' );
		} else {

			$api  = $this->get_api( $account_data['auth_code'] );
			$data = array(
				'email' => $email,
			);

			if ( isset( $settings->tags ) ) {
				$data['tags'] = array( $settings->tags );
			}

			if ( $name ) {
				$data['name'] = $name;
			}

			try {

				$account = $api->getAccount( $account_data['access_token'], $account_data['access_secret'] );
				$url     = '/accounts/' . $account->id . '/lists/' . $settings->list_id . '/subscribers';

				// Try to check if subscribe already exists.
				$get_subscriber = $api->adapter->request( 'GET', $url,
					array(
						'ws.op' => 'find',
						'email' => $email,
					)
				);

				// Update
				if ( isset( $get_subscriber['entries'] ) && count( $get_subscriber['entries'] ) > 0 ) {
					$subscriber_id = $get_subscriber['entries'][0]['id'];
					if ( isset( $data['tags'] ) ) {
						$data['tags'] = array(
							'add' => $data['tags'],
						);
					}

					try {
						$result = $api->adapter->request( 'PATCH', $url . '/' . $subscriber_id, $data, array(
							'return' => 'headers',
						) );

						if ( is_array( $result ) && isset( $result['Status-Code'] ) && 209 == $result['Status-Code'] ) {
							return $response;
						} else {
							$response['error'] = __( 'There was an error connecting to AWeber. Please try again.', 'fl-builder' );
						}
					} catch ( AWeberAPIException $e ) {
						$response['error'] = sprintf(
							/* translators: %s: error */
							__( 'There was an error subscribing to AWeber. %s', 'fl-builder' ),
							$e->getMessage()
						);
					}
				} else {
					$data['ws.op'] = 'create';
					$result        = $api->adapter->request( 'POST', $url, $data, array(
						'return' => 'headers',
					) );

					if ( is_array( $result ) && isset( $result['Status-Code'] ) && 201 == $result['Status-Code'] ) {
						return $response;
					} else {
						$response['error'] = __( 'There was an error connecting to AWeber. Please try again.', 'fl-builder' );
					}
				}
			} catch ( AWeberAPIException $e ) {
				$response['error'] = sprintf(
					/* translators: %s: error */
					__( 'There was an error subscribing to AWeber. %s', 'fl-builder' ),
					$e->getMessage()
				);
			}
		}

		return $response;
	}
}
