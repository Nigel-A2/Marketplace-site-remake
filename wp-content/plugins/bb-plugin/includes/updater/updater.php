<?php

/* Only run if not already setup and not using a repo version. */
if ( ! class_exists( 'FLUpdater' ) && FL_BUILDER_LITE !== true ) {

	/* Defines */
	define( 'FL_UPDATER_DIR', trailingslashit( dirname( __FILE__ ) ) );

	/* Classes */
	require_once FL_UPDATER_DIR . 'classes/class-fl-updater.php';

	/* Actions */
	add_action( 'fl_themes_license_form', 'FLUpdater::render_form' );

	/* Initialize the updater. */
	FLUpdater::init();
}

/**
 * Show dummy license tab with links to docs/upgrade etc.
 */
if ( FL_BUILDER_LITE === true ) {
	add_action( 'fl_themes_license_form', 'FLBuilderAdmin::render_form_lite' );
}
