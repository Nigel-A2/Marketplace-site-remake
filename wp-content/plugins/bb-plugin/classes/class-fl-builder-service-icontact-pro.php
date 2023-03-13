<?php

/**
 * Helper class for the iContact PRO API.
 *
 * @since 1.10.6
 */
final class FLBuilderServiceIContactPro extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.10.6
	 * @var string $id
	 */
	public $id = 'icontact-pro';

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
	 * @param array $data {
	 *      @type string $username A valid username.
	 *      @type string $app_id A valid app ID.
	 *      @type string $app_password A valid app password.
	 *      @type string $company_id A valid iContact Pro Company ID.
	 *      @type string $profile_id A valid iContact Pro Profile ID.
	 * }
	 * @return object The API instance.
	 */
	public function get_api( $data ) {
		if ( $this->api_instance ) {
			return $this->api_instance;
		}
		if ( ! class_exists( 'iContactProApi' ) ) {
			require_once FL_BUILDER_DIR . 'includes/vendor/icontact/iContactProApi.php';
		}

		iContactProApi::getInstance()->setConfig( $data );

		$this->api_instance = iContactProApi::getInstance();

		return $this->api_instance;
	}

	/**
	 * Test the API connection.
	 *
	 * @since 1.10.6
	 * @param array $fields {
	 *      @type string $username A valid username.
	 *      @type string $app_id A valid app ID.
	 *      @type string $app_password A valid app password.
	 *      @type string $company_id A valid iContact Pro Company ID.
	 *      @type string $profile_id A valid iContact Pro Profile ID.
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

		// Make sure we have a username.
		if ( ! isset( $fields['username'] ) || empty( $fields['username'] ) ) {
			$response['error'] = __( 'Error: You must provide a username.', 'fl-builder' );
		} elseif ( ! isset( $fields['app_id'] ) || empty( $fields['app_id'] ) ) {
			$response['error'] = __( 'Error: You must provide an app ID.', 'fl-builder' );
		} elseif ( ! isset( $fields['app_password'] ) || empty( $fields['app_password'] ) ) { // Make sure we have an app password.
			$response['error'] = __( 'Error: You must provide an app password.', 'fl-builder' );
		} elseif ( ! isset( $fields['company_id'] ) || empty( $fields['company_id'] ) ) { // Make sure we have a company ID
			$response['error'] = __( 'Error: You must provide a company ID.', 'fl-builder' );
		} elseif ( ! isset( $fields['profile_id'] ) || empty( $fields['profile_id'] ) ) { // Make sure we have a profile ID
			$response['error'] = __( 'Error: You must provide a profile ID.', 'fl-builder' );
		} else { // Try to connect and store the connection data.

			$api = $this->get_api( array(
				'apiUsername' => $fields['username'],
				'appId'       => $fields['app_id'],
				'apiPassword' => $fields['app_password'],
				'companyId'   => $fields['company_id'],
				'profileId'   => $fields['profile_id'],
			));

			try {
				$api->getLists();
				$response['data'] = array(
					'username'     => $fields['username'],
					'app_id'       => $fields['app_id'],
					'app_password' => $fields['app_password'],
					'company_id'   => $fields['company_id'],
					'profile_id'   => $fields['profile_id'],
				);
			} catch ( Exception $e ) {
				$errors = $api->getErrors();
				/* translators: %s: error */
				$response['error'] = sprintf( __( 'Error: Could not connect to iContact Pro. %s', 'fl-builder' ), $errors[0] );
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

		FLBuilder::render_settings_field( 'username', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'Username', 'fl-builder' ),
			'help'      => __( 'Your iContact Pro username.', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		));

		FLBuilder::render_settings_field( 'app_id', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'App ID', 'fl-builder' ),
			'help'      => __( 'Your iContact Pro app ID.', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		));

		FLBuilder::render_settings_field( 'app_password', array(
			'row_class'   => 'fl-builder-service-connect-row',
			'class'       => 'fl-builder-service-connect-input',
			'type'        => 'text',
			'label'       => __( 'App Password', 'fl-builder' ),
			'help'        => __( 'Your iContact Pro app password.', 'fl-builder' ),
			/* translators: 1: create app url: 2: register url */
			'description' => sprintf( __( 'You must <a%1$s>create an app</a> in iContact Pro to obtain an app ID and password. Please see <a%2$s>the iContact docs</a> for complete instructions.', 'fl-builder' ), ' href="https://app.icontactpro.com/MKT/Settings/Api?returnUrl=/MKT/Settings" target="_blank"', ' href="http://www.icontact.com/developerportal/api-documentation/vocus-register-your-app/" target="_blank"' ),
			'preview'     => array(
				'type' => 'none',
			),
		));

		FLBuilder::render_settings_field( 'company_id', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'Company ID', 'fl-builder' ),
			'help'      => __( 'Your iContact Pro Company ID.', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		));

		FLBuilder::render_settings_field( 'profile_id', array(
			'row_class'   => 'fl-builder-service-connect-row',
			'class'       => 'fl-builder-service-connect-input',
			'type'        => 'text',
			'label'       => __( 'Profile ID', 'fl-builder' ),
			'help'        => __( 'Your iContact Pro Profile ID.', 'fl-builder' ),
			/* translators: 1: api link */
			'description' => sprintf( __( 'Your Company and Profile ID can also be found in the <a%1$s>iContact Pro API settings</a> under Account Information.', 'fl-builder' ), ' href="https://app.icontactpro.com/MKT/Settings/Api?returnUrl=/MKT/Settings" target="_blank"' ),
			'preview'     => array(
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
			'apiUsername' => $account_data['username'],
			'appId'       => $account_data['app_id'],
			'apiPassword' => $account_data['app_password'],
			'companyId'   => $account_data['company_id'],
			'profileId'   => $account_data['profile_id'],
		));
		$response     = array(
			'error' => false,
			'html'  => '',
		);

		try {
			$lists            = $api->getLists();
			$response['html'] = $this->render_list_field( $lists, $settings );
		} catch ( Exception $e ) {
			$errors = $api->getErrors();
			/* translators: %s: error */
			$response['error'] = sprintf( __( 'Error: Could not connect to iContact Pro. %s', 'fl-builder' ), $errors[0] );
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

		foreach ( $lists as $id => $list ) {
			$options[ $list->listId ] = esc_attr( $list->name ); // @codingStandardsIgnoreLine
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
	 * Subscribe an email address to iContact.
	 *
	 * @since 1.10.6
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
			$response['error'] = __( 'There was an error subscribing to iContact. The account is no longer connected.', 'fl-builder' );
		} else {

			$data = array(
				'email' => $email,
			);
			$api  = $this->get_api( array(
				'apiUsername' => $account_data['username'],
				'appId'       => $account_data['app_id'],
				'apiPassword' => $account_data['app_password'],
				'companyId'   => $account_data['company_id'],
				'profileId'   => $account_data['profile_id'],
			));

			try {

				if ( $name ) {

					$names              = explode( ' ', $name );
					$data['first_name'] = null;
					$data['last_name']  = null;

					if ( isset( $names[0] ) ) {
						$data['first_name'] = $names[0];
					}
					if ( isset( $names[1] ) ) {
						$data['last_name'] = $names[1];
					}

					$result = $api->addContact( $data['email'], 'normal', null, $data['first_name'], $data['last_name'] );
				} else {
					$result = $api->addContact( $data['email'] );
				}

				$api->subscribeContactToList( $result->contactId, $settings->list_id ); // @codingStandardsIgnoreLine
			} catch ( Exception $e ) {
				$errors = $api->getErrors();
				/* translators: %s: error */
				$response['error'] = sprintf( __( 'There was an error subscribing to iContact Pro. %s', 'fl-builder' ), $errors[0] );
			}
		}

		return $response;
	}
}
