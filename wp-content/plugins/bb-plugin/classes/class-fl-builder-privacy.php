<?php
/**
 * Helper class for working with privacy.
 *
 * @since 2.1
 */
final class FLBuilderPrivacy {
	static public function init() {
		add_action( 'admin_init', array( 'FLBuilderPrivacy', 'admin_init' ) );
	}

	static public function admin_init() {
		if ( ! function_exists( 'wp_add_privacy_policy_content' ) ) {
			return false;
		}
		add_filter( 'wp_privacy_personal_data_exporters', array( 'FLBuilderPrivacy', 'register_exporter' ) );
		self::register_policy();
	}

	static public function register_exporter( $exporters ) {
		$exporters[] = array(
			'exporter_friendly_name' => __( 'Beaver Builder Plugin', 'fl-builder' ),
			'callback'               => array( 'FLBuilderPrivacy', 'exporter' ),
		);
		return $exporters;
	}

	static public function exporter( $email, $page = 1 ) {

		$export_items = array();
		$data         = array();

		$user = get_user_by( 'email', $email );
		$meta = (array) get_user_meta( $user->ID, 'fl_builder_user_settings', true );

		$result = self::array_flatten( $meta );

		foreach ( $result as $key => $setting ) {

			if ( ! $key ) {
				continue;
			}

			if ( ! is_array( $setting ) ) {

				if ( '' == $setting ) {
					$setting = 'false';
				}
				$data[] = array(
					'name'  => $key,
					'value' => $setting,
				);
			}
		}

		if ( empty( $data ) ) {
			return array(
				'data' => array(),
				'done' => true,
			);
		}

		$export_items[] = array(
			'group_id'    => 'bb-settings',
			'group_label' => 'Beaver Builder Settings',
			'item_id'     => 'bb-settings',
			'data'        => $data,
		);

		return array(
			'data' => $export_items,
			'done' => true,
		);
	}

	static public function array_flatten( $array ) {

		$return = array();
		foreach ( $array as $key => $value ) {
			if ( is_array( $value ) ) {
				$return = array_merge( $return, self::array_flatten( $value ) );
			} else {
				$return[ $key ] = $value;
			}
		}
		return $return;
	}

	static public function register_policy() {
		wp_add_privacy_policy_content( 'Beaver Builder', sprintf( '<p>%s</p>', __( 'In terms of GDPR, Beaver Builder products do not collect any personal information from your users. However some modules such as videos and maps might need you to update your privacy policy accordingly.', 'fl-builder' ) ) );
	}
}
FLBuilderPrivacy::init();
