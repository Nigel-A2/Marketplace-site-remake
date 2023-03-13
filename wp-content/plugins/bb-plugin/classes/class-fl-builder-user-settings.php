<?php

/**
 * Handles logic for user specific settings.
 *
 * @since 2.0
 */
class FLBuilderUserSettings {

	/**
	 * @since 2.0
	 * @return void
	 */
	static public function init() {
		FLBuilderAJAX::add_action( 'save_ui_skin', __CLASS__ . '::save_ui_skin', array( 'skin_name' ) );
		FLBuilderAJAX::add_action( 'save_lightbox_position', __CLASS__ . '::save_lightbox_position', array( 'data' ) );
		FLBuilderAJAX::add_action( 'save_pinned_ui_position', __CLASS__ . '::save_pinned_ui_position', array( 'data' ) );
	}

	/**
	 * @since 2.0
	 * @return array
	 */
	static public function get() {
		$meta     = get_user_meta( get_current_user_id(), 'fl_builder_user_settings', true );
		$defaults = array(
			'skin'     => 'light',
			'lightbox' => null,
		);

		if ( ! $meta ) {
			$meta = array();
		}

		return array_merge( $defaults, $meta );
	}

	/**
	 * @since 2.0
	 * @param array $data
	 * @return mixed
	 */
	static public function update( $data ) {
		return update_user_meta( get_current_user_id(), 'fl_builder_user_settings', $data );
	}

	/**
	 * Handle saving UI Skin type.
	 *
	 * @since 2.0
	 * @param string $name
	 * @return array
	 */
	static public function save_ui_skin( $name ) {
		$settings         = self::get();
		$settings['skin'] = $name;

		return array(
			'saved' => self::update( $settings ),
			'name'  => $name,
		);
	}

	/**
	 * Handle saving the lightbox position.
	 *
	 * @since 2.0
	 * @param array $data
	 * @return array
	 */
	static public function save_lightbox_position( $data ) {
		$settings             = self::get();
		$settings['lightbox'] = $data;

		return self::update( $settings );
	}

	/**
	 * Handle saving the lightbox position.
	 *
	 * @since 2.0
	 * @param array $data
	 * @return array
	 */
	static public function save_pinned_ui_position( $data ) {
		$settings = self::get();
		$settings = array_merge( $settings, $data );

		return self::update( $settings );
	}
}

FLBuilderUserSettings::init();
