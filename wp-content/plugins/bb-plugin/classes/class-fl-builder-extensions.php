<?php

/**
 * Helper class for builder extensions.
 *
 * @since 1.0
 */
final class FLBuilderExtensions {

	/**
	 * Initializes any extensions found in the extensions directory.
	 *
	 * @since 1.8
	 * @param string $path Path to extensions to initialize.
	 * @return void
	 */
	static public function init( $path = null ) {
		$path       = $path ? trailingslashit( $path ) : FL_BUILDER_DIR . 'extensions/';
		$extensions = glob( $path . '*' );

		if ( ! is_array( $extensions ) ) {
			return;
		}

		foreach ( $extensions as $extension ) {

			if ( ! is_dir( $extension ) ) {
				continue;
			}

			$path = trailingslashit( $extension ) . basename( $extension ) . '.php';

			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}
}

FLBuilderExtensions::init();
