<?php

/**
 * Handles rendering layouts in an iframe preview.
 *
 * @since 2.0.6
 */
final class FLBuilderIframePreview {

	/**
	 * Initialize on plugins loaded.
	 *
	 * @since 2.0.6
	 * @return void
	 */
	static public function init() {
		add_action( 'plugins_loaded', __CLASS__ . '::hook' );
	}

	/**
	 * Setup hooks.
	 *
	 * @since 2.1
	 * @return void
	 */
	static public function hook() {
		if ( ! FLBuilderModel::is_builder_draft_preview() ) {
			return;
		}

		add_filter( 'show_admin_bar', '__return_false' );
		add_filter( 'fl_builder_node_status', __CLASS__ . '::filter_node_status' );
	}

	/**
	 * Forces draft node status for layout previews.
	 *
	 * @since 2.0.6
	 * @param string $status
	 * @return string
	 */
	static public function filter_node_status( $status ) {
		return 'draft';
	}
}

FLBuilderIframePreview::init();
