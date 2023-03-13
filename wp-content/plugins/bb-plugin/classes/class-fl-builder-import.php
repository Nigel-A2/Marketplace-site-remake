<?php

/**
 * The WordPress importer plugin has a few issues that break
 * serialized data in certain cases. This class overrides the
 * WordPress importer with our own patched version that fixes
 * these issues.
 *
 * @since 1.8
 */
final class FLBuilderImport {

	/**
	 * @since 1.8
	 * @return void
	 */
	static public function init() {
		if ( ! defined( 'WP_LOAD_IMPORTERS' ) || ! class_exists( 'WP_Import' ) || ! class_exists( 'WXR_Parser_Regex' ) ) {
			return;
		}

		if ( defined( 'FL_BUILDER_IMPORTER_FIX' ) && ! FL_BUILDER_IMPORTER_FIX ) {
			return;
		}

		require_once FL_BUILDER_DIR . 'classes/class-fl-builder-importer.php';

		// Remove the WordPress importer.
		remove_action( 'admin_init', 'wordpress_importer_init' );

		// Add our importer.
		add_action( 'admin_init', 'FLBuilderImport::load' );
	}

	/**
	 * @since 1.8
	 * @return void
	 */
	static public function load() {
		load_plugin_textdomain( 'wordpress-importer', false, 'wordpress-importer/languages' );

		$GLOBALS['wp_import'] = new FLBuilderImporter();

		register_importer( 'wordpress', 'WordPress', __( 'Import <strong>posts, pages, comments, custom fields, categories, and tags</strong> from a WordPress export file.', 'fl-builder' ), array( $GLOBALS['wp_import'], 'dispatch' ) );
	}
}

add_action( 'plugins_loaded', 'FLBuilderImport::init' );
