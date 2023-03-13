<?php

/**
 * Beaver Builder support for WordPress blocks.
 *
 * @since 2.1
 */
final class FLBuilderWPBlocks {

	/**
	 * @since 2.1
	 * @return void
	 */
	static public function init() {
		add_action( 'init', __CLASS__ . '::setup' );
	}

	/**
	 * @since 2.1
	 * @return void
	 */
	static public function setup() {
		if ( ! function_exists( 'register_block_type' ) ) {
			return;
		}

		// Actions
		add_action( 'enqueue_block_editor_assets', __CLASS__ . '::enqueue_block_editor_assets' );

		// Filters
		add_filter( 'excerpt_allowed_blocks', __CLASS__ . '::excerpt_allowed_blocks' );

		// Block Files
		require_once FL_BUILDER_DIR . 'classes/class-fl-builder-wp-blocks-layout.php';
	}

	/**
	 * Enqueues scripts and styles for the block editor.
	 *
	 * @since 2.1
	 * @return void
	 */
	static public function enqueue_block_editor_assets() {
		global $wp_version;
		global $post;

		if ( ! is_object( $post ) ) {
			return;
		} elseif ( ! in_array( $post->post_type, FLBuilderModel::get_post_types() ) ) {
			return;
		}

		$branding         = FLBuilderModel::get_branding();
		$post_type_object = get_post_type_object( $post->post_type );
		$post_type_name   = $post_type_object->labels->singular_name;
		$min              = ( ! FLBuilder::is_debug() ) ? '.min' : '';

		wp_enqueue_style(
			'fl-builder-wp-editor',
			FL_BUILDER_URL . 'css/build/wp-editor.bundle' . $min . '.css',
			array(),
			FL_BUILDER_VERSION
		);

		wp_enqueue_script(
			'fl-builder-wp-editor',
			FL_BUILDER_URL . 'js/build/wp-editor.bundle' . $min . '.js',
			array( 'wp-edit-post' ),
			FL_BUILDER_VERSION
		);

		wp_localize_script( 'fl-builder-wp-editor', 'FLBuilderConfig', array(
			'builder' => array(
				'access'       => FLBuilderUserAccess::current_user_can( 'builder_access' ),
				'enabled'      => FLBuilderModel::is_builder_enabled( $post->ID ),
				'nonce'        => wp_create_nonce( 'fl_ajax_update' ),
				'unrestricted' => FLBuilderUserAccess::current_user_can( 'unrestricted_editing' ),
				'showui'       => apply_filters( 'fl_builder_render_admin_edit_ui', true ),
			),
			'post'    => array(
				'id' => $post->ID,
			),
			'strings' => array(
				/* translators: 1: branded builder name: 2: post type name */
				'active'      => sprintf( _x( '%1$s is currently active for this %2$s.', '%1$s branded builder name. %2$s post type name.', 'fl-builder' ), $branding, strtolower( $post_type_name ) ),
				/* translators: %s: post type name */
				'convert'     => sprintf( _x( 'Convert to %s', '%s branded builder name.', 'fl-builder' ), $branding ),
				/* translators: %s: branded builder name */
				'description' => sprintf( _x( '%s lets you drag and drop your layout on the frontend.', '%s branded builder name.', 'fl-builder' ), $branding ),
				'editor'      => __( 'Use Standard Editor', 'fl-builder' ),
				/* translators: %s: branded builder name */
				'launch'      => sprintf( _x( 'Launch %s', '%s branded builder name.', 'fl-builder' ), $branding ),
				'title'       => $branding,
				/* translators: %s: post type name */
				'view'        => sprintf( _x( 'View %s', '%s post type name.', 'fl-builder' ), $post_type_name ),
				'warning'     => __( 'Switching to the native WordPress editor will disable your Beaver Builder layout until it is enabled again. Any edits made in the WordPress editor will not be converted to your Page Builder layout. Do you want to continue?', 'fl-builder' ),
			),
			'urls'    => array(
				'edit' => FLBuilderModel::get_edit_url( $post->ID ),
				'view' => get_permalink( $post->ID ),
			),
			'wp'      => array(
				'version' => $wp_version,
			),
		) );
	}

	/**
	 * Adds our block(s) to the allowed blocks for excerpts.
	 *
	 * @since 2.1.7.1
	 * @param array $blocks
	 * @return array
	 */
	static public function excerpt_allowed_blocks( $blocks ) {
		$blocks[] = 'fl-builder/layout';
		return $blocks;
	}
}

FLBuilderWPBlocks::init();
