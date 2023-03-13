<?php

/**
 * Helper class for the Drip API.
 *
 * @since 1.5.4
 */
final class FLBuilderServiceDrip extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.5.4
	 * @var string $id
	 */
	public $id = 'drip';

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
	 * @param string $api_key A valid API token.
	 * @return object The API instance.
	 */
	public function get_api( $api_key ) {
		if ( $this->api_instance ) {
			return $this->api_instance;
		}
		if ( ! class_exists( 'FL_Drip_Api' ) ) {
			require_once FL_BUILDER_DIR . 'includes/vendor/drip/Drip_API.class.php';
		}

		$this->api_instance = new FL_Drip_Api( $api_key );

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

		// Make sure we have an API token.
		if ( ! isset( $fields['api_key'] ) || empty( $fields['api_key'] ) ) {
			$response['error'] = __( 'Error: You must provide an API token.', 'fl-builder' );
		} elseif ( ! isset( $fields['api_account_id'] ) || empty( $fields['api_account_id'] ) ) {
			$response['error'] = __( 'Error: You must provide an Account ID.', 'fl-builder' );
		} else { // Try to connect and store the connection data.
			try {

				$api = $this->get_api( $fields['api_key'] );
				try {

					$account       = $api->fetch_account( $fields['api_account_id'] );
					$error_message = $api->get_error_message();

					if ( ! empty( $error_message ) ) {
						$response['error'] = $error_message;
					} else {
						$response['data'] = array(
							'api_key'        => $fields['api_key'],
							'api_account_id' => $fields['api_account_id'],
						);
					}
				} catch ( Exception $e ) {
					$response['error'] = sprintf(
						/* translators: %s: error */
						__( 'Error: Please check your Account ID. %s', 'fl-builder' ),
						$e->getMessage()
					);
				}
			} catch ( Exception $e ) {
				$response['error'] = sprintf(
					/* translators: %s: error */
					__( 'Error: Please check your API token. %s', 'fl-builder' ),
					$e->getMessage()
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

		FLBuilder::render_settings_field( 'api_key', array(
			'row_class'   => 'fl-builder-service-connect-row',
			'class'       => 'fl-builder-service-connect-input',
			'type'        => 'text',
			'label'       => __( 'API Token', 'fl-builder' ),
			/* translators: %s: api url */
			'description' => sprintf( __( 'Your API Token can be found in your Drip account under Settings > My User Settings. Or, you can click this <a%s>direct link</a>.', 'fl-builder' ), ' href="https://www.getdrip.com/user/edit" target="_blank"' ),
			'preview'     => array(
				'type' => 'none',
			),
		));

		FLBuilder::render_settings_field( 'api_account_id', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'Account ID', 'fl-builder' ),
			'help'      => __( 'Your Account ID can be found in your Drip account under Settings > Site Setup.', 'fl-builder' ),
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
		$campaigns    = $api->get_campaigns( array(
			'account_id' => $account_data['api_account_id'],
		) );

		$workflows = $api->get_workflows( array(
			'account_id' => $account_data['api_account_id'],
		) );

		$html  = $this->render_campaigns_field( $campaigns, $settings );
		$html .= $this->render_tag_field( $settings );
		$html .= $this->render_workflows_field( $workflows, $settings );

		$response = array(
			'error' => false,
			'html'  => $html,
		);

		return $response;
	}

	/**
	 * Render markup for the campaign field.
	 *
	 * @since 1.10.5
	 * @param array $campaigns Campaigns data from the API.
	 * @param object $settings Saved module settings.
	 * @return string The markup for the campaign field.
	 * @access private
	 */
	private function render_campaigns_field( $campaigns, $settings ) {
		ob_start();

		$options = array(
			'' => __( 'Choose...', 'fl-builder' ),
		);

		foreach ( $campaigns as $campaign ) {
			$options[ $campaign['id'] ] = esc_attr( $campaign['name'] );
		}

		FLBuilder::render_settings_field( 'campaign_id', array(
			'row_class' => 'fl-builder-service-field-row',
			'class'     => 'fl-builder-service-campaign-select',
			'type'      => 'select',
			'label'     => _x( 'Campaign', 'An email campaign from your GetDrip account.', 'fl-builder' ),
			'options'   => $options,
			'preview'   => array(
				'type' => 'none',
			),
		), $settings);

		return ob_get_clean();
	}

	/**
	 * Render markup for the tag field.
	 *
	 * @since 1.5.4
	 * @param object $settings Saved module settings.
	 * @return string The markup for the tag field.
	 * @access private
	 */
	private function render_tag_field( $settings ) {
		ob_start();

		FLBuilder::render_settings_field( 'list_id', array(
			'row_class' => 'fl-builder-service-field-row',
			'class'     => 'fl-builder-service-list-select',
			'type'      => 'text',
			'label'     => _x( 'Tags', 'A tag to add to contacts in Drip when they subscribe.', 'fl-builder' ),
			'help'      => __( 'For multiple tags, separate with comma.', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		), $settings);

		return ob_get_clean();
	}

	/**
	 * Render markup for the workflow field.
	 *
	 * @since 2.2.4
	 * @param array $workflows Workflows data from the API.
	 * @param object $settings Saved module settings.
	 * @return string The markup for the workflow field.
	 * @access private
	 */
	private function render_workflows_field( $workflows, $settings ) {
		ob_start();

		$options = array(
			'' => __( 'Choose...', 'fl-builder' ),
		);

		if ( ! empty( $workflows ) ) {
			foreach ( $workflows as $workflow ) {
				$options[ $workflow['id'] ] = $workflow['name'];
			}
		}

		FLBuilder::render_settings_field( 'workflow_id', array(
			'row_class' => 'fl-builder-service-field-row',
			'class'     => 'fl-builder-service-workflow-select',
			'type'      => 'select',
			'label'     => _x( 'Workflow', 'An email workflow from your GetDrip account.', 'fl-builder' ),
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
	 * @since 1.5.4
	 * @param object $settings A module settings object.
	 * @param string $email The email to subscribe.
	 * @param string $name Optional. The full name of the person subscribing.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 * }
	 */
	public function subscribe( $settings, $email, $name = '' ) {
		$account_data  = $this->get_account_data( $settings->service_account );
		$response      = array(
			'error' => false,
		);
		$subscriber_id = null;

		if ( ! $account_data ) {
			$response['error'] = __( 'There was an error subscribing to Drip. The account is no longer connected.', 'fl-builder' );
		} else {

			$api  = $this->get_api( $account_data['api_key'] );
			$args = array(
				'account_id' => $account_data['api_account_id'],
				'email'      => $email,
			);

			// Check if the contact already exists
			try {
				$result = $api->fetch_subscriber( $args );

				if ( $result && isset( $result['id'] ) ) {
					$subscriber_id = $result['id'];
				}
			} catch ( Exception $e ) {
				$response['error'] = sprintf(
					/* translators: %s: error */
					__( 'There was an error searching contact from Drip. %s', 'fl-builder' ),
					$e->getMessage()
				);
				return $response;
			}

			if ( $subscriber_id ) {
				$args['user_id'] = $subscriber_id;
			}

			if ( $settings->list_id ) {
				$args['tags'] = explode( ',', $settings->list_id );
			}

			if ( $name ) {
				$args['custom_fields'] = array(
					'name' => $name,
				);
			}

			// Create or update contact
			try {

				$result = $api->create_or_update_subscriber( $args );

				if ( isset( $result['id'] ) && isset( $settings->campaign_id ) ) {
					$args['campaign_id']  = $settings->campaign_id;
					$args['double_optin'] = false;
					$get_res              = $api->subscribe_subscriber( $args );
				}

				if ( isset( $result['id'] ) && isset( $settings->workflow_id ) && ! empty( $settings->workflow_id ) ) {
					unset( $args['campaign_id'] );
					unset( $args['double_optin'] );

					$args['id']          = $result['id'];
					$args['workflow_id'] = $settings->workflow_id;
					$workflow_res        = $api->subscribe_workflow( $args );
				}
			} catch ( Exception $e ) {
				$response['error'] = sprintf(
					/* translators: %s: error */
					__( 'There was an error subscribing to Drip. %s', 'fl-builder' ),
					$e->getMessage()
				);
			}
		}

		return $response;
	}
}
