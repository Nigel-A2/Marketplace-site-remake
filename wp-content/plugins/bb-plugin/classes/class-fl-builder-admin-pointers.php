<?php

/**
 * Class that handles showing admin pointers.
 *
 * @since 1.10.3
 */
final class FLBuilderAdminPointers {

	/**
	 * @since 1.10.3
	 * @var array $pointers
	 */
	static private $pointers = array();

	/**
	 * Initialize.
	 *
	 * @since 1.10.3
	 * @return void
	 */
	static public function init() {

		add_action( 'admin_enqueue_scripts', __CLASS__ . '::enqueue_scripts' );
	}

	/**
	 * Register a pointer.
	 *
	 * @since 1.10.3
	 * @param array $pointer
	 * @return void
	 */
	static public function register_pointer( $pointer ) {

		self::$pointers[] = $pointer;
	}

	/**
	 * Enqueue scripts for showing pointers.
	 *
	 * @since 1.10.3
	 * @return void
	 */
	static public function enqueue_scripts() {

		$pointers = array();

		foreach ( self::$pointers as $pointer ) {

			if ( ! current_user_can( $pointer['cap'] ) || self::is_dismissed( $pointer['id'] ) ) {
				continue;
			}

			$pointers[] = $pointer;
		}

		if ( empty( $pointers ) ) {
			return;
		}

		wp_enqueue_style( 'wp-pointer' );
		wp_enqueue_script( 'wp-pointer' );

		wp_enqueue_script(
			'fl-builder-admin-pointers',
			FL_BUILDER_URL . 'js/fl-builder-admin-pointers.js',
			array( 'jquery', 'wp-pointer' ),
			FL_BUILDER_VERSION,
			true
		);

		wp_localize_script( 'fl-builder-admin-pointers', 'FLBuilderAdminPointersConfig', array(
			'pointers' => $pointers,
			'ajaxurl'  => admin_url( 'admin-ajax.php' ),
		) );
	}

	/**
	 * Check if a pointer has been dismissed by the current user.
	 *
	 * @since 1.10.3
	 * @param string $pointer_id
	 * @return bool
	 */
	static private function is_dismissed( $pointer_id ) {

		$dismissed = explode( ',', (string) get_user_meta( get_current_user_id(), 'dismissed_wp_pointers', true ) );

		return in_array( $pointer_id, $dismissed );
	}
}

FLBuilderAdminPointers::init();
