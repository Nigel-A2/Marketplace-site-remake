<?php
use function Groundhogg\after_form_submit_handler;
use function Groundhogg\generate_contact_with_map;
use function Groundhogg\get_db;
use function Groundhogg\get_mappable_fields;

/**
 * Helper class for Groundhogg.
 *
 * @since 1.5.4
 */
final class FLBuilderServiceGroundhogg extends FLBuilderService {


	/**
	 * The ID for this service.
	 *
	 * @since 1.5.4
	 * @var string $id
	 */
	public $id = 'Groundhogg';

	/**
	 * Test the API connection.
	 *
	 * @param array $fields
	 * @return array{
	 * @type bool|string $error The error message or false if no error.
	 * @type array $data An array of data used to make the connection.
	 * }
	 * @since 1.5.4
	 */
	public function connect( $fields = array() ) {
		$response = array(
			'error' => false,
			'data'  => array(),
		);

		return $response;
	}

	/**
	 * Renders the markup for the connection settings.
	 *
	 * @return string The connection settings markup.
	 * @since 1.5.4
	 */
	public function render_connect_settings() {
		ob_start();
		if ( ! $this->groundhogg_is_active() ) {
			FLBuilder::render_settings_field( 'api_username', array(
				'row_class' => 'fl-builder-service-connect-row',
				'class'     => 'fl-builder-service-connect-input',
				'type'      => 'raw',
				'content'   => sprintf( '<p><strong>%s</strong></p><p>%s</p>', __( 'Groundhogg plugin must be installed and active to use this service.', 'fl-builder' ), '<a href="https://wordpress.org/plugins/groundhogg/">https://wordpress.org/plugins/groundhogg/</a>' ),
				'preview'   => array(
					'type' => 'none',
				),
			));
		}
		return ob_get_clean();
	}

	/**
	 * Render the markup for service specific fields.
	 *
	 * @param string $account The name of the saved account.
	 * @param object $settings Saved module settings.
	 * @return array {
	 * @type bool|string $error The error message or false if no error.
	 * @type string $html The field markup.
	 * }
	 * @since 1.5.4
	 */
	public function render_fields( $account, $settings ) {
		$response = array(
			'error' => false,
			'html'  => '',
		);

		$response['html'] = self::render_list_field( $settings );
		return $response;
	}

	/**
	 * Render markup for the list field.
	 *
	 * @param array $lists List data from the API.
	 * @param object $settings Saved module settings.
	 * @return string The markup for the list field.
	 * @access private
	 * @since 1.5.4
	 */
	private function render_list_field( $settings ) {
		ob_start();

		if ( $this->groundhogg_is_active() ) {
			$options = get_mappable_fields( array(
				'none ' => 'Do Not Map',
			) );

			FLBuilder::render_settings_field( 'apply_tag', array(
				'row_class'    => 'fl-builder-service-field-row',
				'class'        => 'fl-builder-service-list-select',
				'type'         => 'select',
				'label'        => __( 'Apply Tag', 'fl-builder' ),
				'multi-select' => true,
				'options'      => get_db( 'tags' )->get_tags_select(),

			), $settings );
		}

		return ob_get_clean();
	}

	protected function groundhogg_is_active() {
		return defined( 'GROUNDHOGG_VERSION' );
	}

	/**
	 * Subscribe an email address to Groundhogg.
	 *
	 * @param object $settings A module settings object.
	 * @param string $email The email to subscribe.
	 * @param string $name Optional. The full name of the person subscribing.
	 * @return array {
	 * @type bool|string $error The error message or false if no error.
	 * }
	 * @since 1.5.4
	 */
	public function subscribe( $settings, $email, $name = false ) {

		if ( ! $this->groundhogg_is_active() ) {
			$response['error'] = __( 'There was an error subscribing. Groundhogg is not active.', 'fl-builder' );

			return $response;
		}

		$response = array(
			'error' => false,
		);

		$field_map = array(
			'email' => 'email',
			'name'  => 'full_name',
		);

		$contact = generate_contact_with_map( $_POST, $field_map );

		if ( ! $contact || is_wp_error( $contact ) ) {
			$response['error'] = __( 'There was an error subscribing.', 'fl-builder' );
			return $response;
		}

		$contact->apply_tag( $settings->apply_tag );
		after_form_submit_handler( $contact );

		return $response;
	}
}
