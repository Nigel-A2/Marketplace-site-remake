<?php

/**
 * Helper class for the MailChimp API.
 *
 * @since 1.5.4
 */
final class FLBuilderServiceMailChimp extends FLBuilderService {

	/**
	 * The ID for this service.
	 *
	 * @since 1.5.4
	 * @var string $id
	 */
	public $id = 'mailchimp';

	/**
	 * @since 1.5.4
	 * @var object $api_instance
	 * @access private
	 */
	private $api_instance = null;

	/**
	 * @since 2.2.6
	 * @var object $status
	 * @access private
	 */
	private $status = null;

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
		if ( ! class_exists( 'FLBuilderMailChimp' ) ) {
			require_once FL_BUILDER_DIR . 'includes/vendor/mailchimp/mailchimp.php';
		}

		$this->api_instance = new FLBuilderMailChimp( $api_key );

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

			try {
				$api = $this->get_api( $fields['api_key'] );

				$ping = $api->get( 'ping' );
				if ( ! isset( $ping['health_status'] ) && isset( $ping['title'] ) ) {
					$response['error'] = $ping['title'];
				} else {
					$response['data'] = array(
						'api_key' => $fields['api_key'],
					);
				}
			} catch ( Exception $e ) {
				$response['error'] = $e->getMessage();
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
			'help'      => __( 'Your API key can be found in your MailChimp account under Account > Extras > API Keys.', 'fl-builder' ),
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
		$post_data    = FLBuilderModel::get_post_data();
		$account_data = $this->get_account_data( $account );
		$response     = array(
			'error' => false,
			'html'  => '',
		);

		// Lists field
		try {
			$api = $this->get_api( $account_data['api_key'] );

			if ( ! isset( $post_data['list_id'] ) ) {
				$lists             = $api->getLists();
				$response['html'] .= $this->render_list_field( $lists, $settings );
			}
		} catch ( Exception $e ) {
			$response['error'] = $e->getMessage();
		}

		// Groups field
		try {

			if ( isset( $post_data['list_id'] ) || isset( $settings->list_id ) ) {

				if ( isset( $post_data['list_id'] ) ) {
					$list_id = $post_data['list_id'];
				} else {
					$list_id = $settings->list_id;
				}

				$groups            = $api->interestGroupings( $list_id );
				$response['html'] .= $this->render_groups_field( $list_id, $groups, $settings );
			}
		} catch ( Exception $e ) {
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
	private function render_list_field( $lists, $settings ) {
		ob_start();

		$options = array(
			'' => __( 'Choose...', 'fl-builder' ),
		);

		if ( is_array( $lists ) && count( $lists ) > 0 ) {
			foreach ( $lists as $list ) {
				$options[ $list['id'] ] = esc_attr( $list['name'] );
			}
		}

		FLBuilder::render_settings_field( 'list_id', array(
			'row_class' => 'fl-builder-service-field-row',
			'class'     => 'fl-builder-service-list-select fl-builder-mailchimp-list-select',
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
	 * Render markup for the groups field.
	 *
	 * @since 1.6.0
	 * @param string $list_id The ID of the list for this groups.
	 * @param array $groups An array of group data.
	 * @param object $settings Saved module settings.
	 * @return string The markup for the group field.
	 * @access private
	 */
	private function render_groups_field( $list_id, $groups, $settings ) {
		if ( ! is_array( $groups ) || 0 === count( $groups ) ) {
			return;
		}

		ob_start();

		$options = array(
			'' => __( 'No Group', 'fl-builder' ),
		);

		foreach ( $groups as $group ) {
			foreach ( $group['groups'] as $subgroup ) {
				$options[ $list_id . '_' . $group['id'] . '_' . $subgroup['id'] ] = $group['title'] . ' - ' . $subgroup['name'];
			}
		}

		FLBuilder::render_settings_field( 'groups', array(
			'row_class'    => 'fl-builder-service-field-row',
			'class'        => 'fl-builder-mailchimp-group-select',
			'type'         => 'select',
			'label'        => _x( 'Groups', 'MailChimp list group.', 'fl-builder' ),
			'multi-select' => true,
			'options'      => $options,
			'preview'      => array(
				'type' => 'none',
			),
		), $settings);

		return ob_get_clean();
	}

	/**
	 * Subscribe an email address to MailChimp.
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
			$response['error'] = __( 'There was an error subscribing to MailChimp. The account is no longer connected.', 'fl-builder' );
		} else {

			try {
				$api = $this->get_api( $account_data['api_key'] );
				/**
				 * Use this filter to enable double opt-ins for MailChimp integrations.
				 * Returning true enables double opt-ins; returning false enables single opt-ins.
				 * The default return value for this filter is false.
				 * @see fl_builder_mailchimp_double_option
				 * @link https://docs.wpbeaverbuilder.com/beaver-builder/developer/tutorials-guides/common-beaver-builder-filter-examples
				 */
				$double = apply_filters( 'fl_builder_mailchimp_double_option', false );
				$data   = array(
					'email'        => $email,
					'double_optin' => (bool) $double,
				);

				// Name
				if ( $name ) {
					$names = explode( ' ', $name );

					if ( isset( $names[0] ) ) {
						$data['FNAME'] = $names[0];
						$data['LNAME'] = ltrim( str_replace( $names[0], '', $name ) );
					}
				}

				// Groups
				if ( isset( $settings->groups ) && is_array( $settings->groups ) ) {

					$groups = array();

					// Build the array of saved group data.
					for ( $i = 0; $i < count( $settings->groups ); $i++ ) {

						if ( empty( $settings->groups[ $i ] ) ) {
							continue;
						}

						$group_data = explode( '_', $settings->groups[ $i ] );

						if ( $group_data[0] != $settings->list_id ) {
							continue;
						}
						if ( ! isset( $groups[ $group_data[1] ] ) ) {
							$groups[ $group_data[1] ] = array();
						}

						$groups[ $group_data[1] ][] = $group_data[2];
					}

					// Get the subgroup names from the API and add to the $data array.
					if ( count( $groups ) > 0 ) {

						$subgroup_ids  = array();
						$groups_result = $api->interestGroupings( $settings->list_id );

						if ( is_array( $groups_result ) && count( $groups_result ) > 0 ) {

							foreach ( $groups_result as $group ) {

								if ( ! isset( $groups[ $group['id'] ] ) ) {
									continue;
								}

								foreach ( $group['groups'] as $subgroup ) {

									if ( in_array( $subgroup['id'], $groups[ $group['id'] ] ) ) {
										$subgroup_ids[ $subgroup['id'] ] = true;
									}
								}
							}
						}

						$data['groups'] = $subgroup_ids;

					}
				}

				// Get email status if already subscribed.
				$member = $api->get_member( $settings->list_id, $email );
				if ( ! $api->getLastError() ) {
					$this->status = $member['status'];
				}

				$api->subscribe( $settings->list_id, $data );

				if ( $api->getLastError() ) {
					$response['error'] = sprintf(
						/* translators: %s: error */
						__( 'There was an error subscribing to MailChimp. %s', 'fl-builder' ),
						$api->getLastError()
					);
				}
			} catch ( Exception $e ) {
				$response['error'] = $e->getMessage();
			}// Try catch().
		}

		return $response;
	}

	/**
	 * Get the subscriber's email status.
	 *
	 * @since 2.2.6
	 * @return array string
	 */
	public function subscriber_status() {
		return $this->status;
	}
}
