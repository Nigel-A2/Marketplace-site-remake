<?php

/**
 * Helper class for the ActiveCampaign API.
 *
 * @since 1.6.0
 */
final class FLBuilderServiceActiveCampaign extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.6.0
	 * @var string $id
	 */
	public $id = 'activecampaign';

	/**
	 * @since 1.6.0
	 * @var object $api_instance
	 * @access private
	 */
	private $api_instance = null;

	/**
	 * Get an instance of the API.
	 *
	 * @since 1.6.0
	 * @param string $api_url A valid API url.
	 * @param string $api_key A valid API key.
	 * @return object The API instance.
	 */
	public function get_api( $api_url, $api_key ) {
		if ( $this->api_instance ) {
			return $this->api_instance;
		}
		if ( ! class_exists( 'ActiveCampaign' ) ) {
			require_once FL_BUILDER_DIR . 'includes/vendor/activecampaign/ActiveCampaign.class.php';
		}

		$this->api_instance = new ActiveCampaign( $api_url, $api_key );

		return $this->api_instance;
	}

	/**
	 * Test the API connection.
	 *
	 * @since 1.6.0
	 * @param array $fields {
	 *      @type string $api_url A valid API url.
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

		// Make sure we have an API url.
		if ( ! isset( $fields['api_url'] ) || empty( $fields['api_url'] ) ) {
			$response['error'] = __( 'Error: You must provide an API URL.', 'fl-builder' );
		} elseif ( ! isset( $fields['api_key'] ) || empty( $fields['api_key'] ) ) {
			$response['error'] = __( 'Error: You must provide an API key.', 'fl-builder' );
		} else { // Try to connect and store the connection data.

			$api = $this->get_api( $fields['api_url'], $fields['api_key'] );

			if ( ! (int) $api->credentials_test() ) {
				$response['error'] = __( 'Error: Please check your API URL and API key.', 'fl-builder' );
			} else {
				$response['data'] = array(
					'api_url' => $fields['api_url'],
					'api_key' => $fields['api_key'],
				);
			}
		}

		return $response;
	}

	/**
	 * Renders the markup for the connection settings.
	 *
	 * @since 1.6.0
	 * @return string The connection settings markup.
	 */
	public function render_connect_settings() {
		ob_start();

		FLBuilder::render_settings_field( 'api_url', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'API URL', 'fl-builder' ),
			'help'      => __( 'Your API URL can be found in your ActiveCampaign account under My Settings > Developer > API.', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		));

		FLBuilder::render_settings_field( 'api_key', array(
			'row_class' => 'fl-builder-service-connect-row',
			'class'     => 'fl-builder-service-connect-input',
			'type'      => 'text',
			'label'     => __( 'API Key', 'fl-builder' ),
			'help'      => __( 'Your API key can be found in your ActiveCampaign account under My Settings > Developer > API.', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		));

		return ob_get_clean();
	}

	/**
	 * Render the markup for service specific fields.
	 *
	 * @since 1.6.0
	 * @param string $account The name of the saved account.
	 * @param object $settings Saved module settings.
	 * @return array {
	 *      @type bool|string $error The error message or false if no error.
	 *      @type string $html The field markup.
	 * }
	 */
	public function render_fields( $account, $settings ) {
		$post_data    = FLBuilderModel::get_post_data();
		$account_data = $this->get_account_data( $account );
		$api          = $this->get_api( $account_data['api_url'], $account_data['api_key'] );
		$response     = array(
			'error' => false,
			'html'  => '',
		);

		if ( ! isset( $post_data['list_type'] ) ) {
			$response['html'] = $this->render_list_type_field( $settings );
		}

		$lists            = $api->api( 'list/list?ids=all' );
		$render_type_html = $this->render_list_field( $lists, $settings );

		if ( isset( $post_data['list_type'] ) || isset( $settings->list_type ) ) {
			$list_type = isset( $post_data['list_type'] ) ? $post_data['list_type'] : $settings->list_type;

			if ( ! empty( $list_type ) && 'form' == $list_type ) {
				$forms            = $api->api( 'form/getforms' );
				$render_type_html = $this->render_form_field( $forms, $settings );
			}
		}

		$response['html'] .= $render_type_html;

		if ( ! isset( $post_data['list_type'] ) ) {
			$response['html'] .= $this->render_tags_field( $settings );
		}

		return $response;
	}

	/**
	 * Render markup for the list type.
	 *
	 * @since 1.8.3
	 * @param object $settings Saved module settings.
	 * @return string The markup for the list field.
	 * @access private
	 */
	private function render_list_type_field( $settings ) {
		ob_start();
		FLBuilder::render_settings_field( 'list_type', array(
			'row_class' => 'fl-builder-service-field-row',
			'class'     => 'fl-builder-activecampaign-list_type-select',
			'type'      => 'select',
			'label'     => _x( 'Type', 'Select the list type.', 'fl-builder' ),
			'default'   => 'list',
			'options'   => array(
				'list' => __( 'List', 'fl-builder' ),
				'form' => __( 'Form', 'fl-builder' ),
			),
			'preview'   => array(
				'type' => 'none',
			),
		), $settings);
		return ob_get_clean();
	}

	/**
	 * Render markup for the form field
	 *
	 * @since 1.8.3
	 * @param array $forms Form data from the API.
	 * @param object $settings Saved module settings.
	 * @return string The markup for the form field.
	 * @access private
	 */
	private function render_form_field( $forms, $settings ) {
		ob_start();
		$options = array(
			'' => __( 'Choose...', 'fl-builder' ),
		);

		foreach ( (array) $forms as $form ) {
			if ( is_object( $form ) && isset( $form->id ) ) {
				$options[ $form->id ] = $form->name;
			}
		}
		FLBuilder::render_settings_field( 'form_id', array(
			'row_class' => 'fl-builder-service-field-row',
			'class'     => 'fl-builder-service-list-select',
			'type'      => 'select',
			'label'     => _x( 'Form', 'Select a form a ActiveCampaign.', 'fl-builder' ),
			'options'   => $options,
			'preview'   => array(
				'type' => 'none',
			),
		), $settings);
		return ob_get_clean();
	}

	/**
	 * Render markup for the list field.
	 *
	 * @since 1.6.0
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

		foreach ( (array) $lists as $list ) {
			if ( is_object( $list ) && isset( $list->id ) ) {
				$options[ $list->id ] = esc_attr( $list->name );
			}
		}

		FLBuilder::render_settings_field( 'list_id', array(
			'row_class' => 'fl-builder-service-field-row',
			'class'     => 'fl-builder-service-list-select',
			'type'      => 'select',
			'label'     => _x( 'List', 'An email list from ActiveCampaign.', 'fl-builder' ),
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
			'default'   => '',
			'label'     => _x( 'Tags', 'A comma separated list of tags.', 'fl-builder' ),
			'help'      => __( 'A comma separated list of tags.', 'fl-builder' ),
			'preview'   => array(
				'type' => 'none',
			),
		), $settings);

		return ob_get_clean();
	}

	/**
	 * Subscribe an email address to ActiveCampaign.
	 *
	 * @since 1.6.0
	 * @since 1.8.6  Changed contact_add method to contact_sync
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
			$response['error'] = __( 'There was an error subscribing to ActiveCampaign. The account is no longer connected.', 'fl-builder' );
		} else {

			$api = $this->get_api( $account_data['api_url'], $account_data['api_key'] );

			$data['email'] = $email;
			if ( isset( $settings->list_type ) && 'form' == $settings->list_type ) {
				$data['form'] = $settings->form_id;

				// Get list ID associated with the form.
				$forms = $api->api( 'form/getforms' );

				if ( $forms ) {
					foreach ( $forms as $form ) {
						if ( is_object( $form ) ) {
							if ( $settings->form_id != $form->id ) {
								continue;
							}

							if ( ! isset( $form->lists ) || 0 === count( (array) $form->lists ) ) {
								continue;
							}

							$list_id = $form->lists[0];
						}
					}
				}
			} else {
				$data['p'] = array( $settings->list_id );
				$list_id   = $settings->list_id;
			}

			if ( $list_id ) {
				$data['status']            = array(
					$list_id => 1,
				);
				$data['instantresponders'] = array(
					$list_id => 1,
				);
			}

			// Name
			if ( $name ) {

				$names = explode( ' ', $name );

				if ( isset( $names[0] ) ) {
					$data['first_name'] = $names[0];
				}
				if ( isset( $names[1] ) ) {
					$data['last_name'] = $names[1];
				}
			}

			// Tags
			if ( isset( $settings->tags ) && ! empty( $settings->tags ) ) {
				$data['tags'] = $settings->tags;
			}

			// Subscribe
			$result = $api->api( 'contact/sync', $data );

			if ( ! $result->success && isset( $result->error ) ) {

				if ( stristr( $result->error, 'access' ) ) {
					$response['error'] = __( 'Error: Invalid API data.', 'fl-builder' );
				} else {
					$response['error'] = $result->error;
				}
			}
		}

		return $response;
	}
}
