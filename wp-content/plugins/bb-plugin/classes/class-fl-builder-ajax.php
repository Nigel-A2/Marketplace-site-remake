<?php

/**
 * Front-end AJAX handler for the builder interface. We use this
 * instead of wp_ajax because that only works in the admin and
 * certain things like some shortcodes won't render there. AJAX
 * requests handled through this method only run for logged in users
 * for extra security. Developers creating custom modules that need
 * AJAX should use wp_ajax instead.
 *
 * @since 1.7
 */
final class FLBuilderAJAX {

	/**
	 * An array of registered action data.
	 *
	 * @since 1.7
	 * @access private
	 * @var array $actions
	 */
	static private $actions = array();

	/**
	 * Initializes hooks.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function init() {
		add_action( 'wp', __CLASS__ . '::run' );
	}

	/**
	 * Runs builder's frontend AJAX.
	 *
	 * @since 1.7
	 * @return void
	 */
	static public function run() {
		self::add_actions();
		self::call_action();
	}

	/**
	 * Adds a callable AJAX action.
	 *
	 * @since 1.7
	 * @param string $action The action name.
	 * @param string $method The method to call.
	 * @param array $args An array of method arg names that are present in the post data.
	 * @return void
	 */
	static public function add_action( $action, $method, $args = array() ) {
		self::$actions[ $action ] = array(
			'action' => $action,
			'method' => $method,
			'args'   => $args,
		);
	}

	/**
	 * Removes an AJAX action.
	 *
	 * @since 1.8
	 * @param string $action The action to remove.
	 * @return void
	 */
	static public function remove_action( $action ) {
		if ( isset( self::$actions[ $action ] ) ) {
			unset( self::$actions[ $action ] );
		}
	}

	/**
	 * Adds all callable AJAX actions.
	 *
	 * @since 1.7
	 * @access private
	 * @return void
	 */
	static private function add_actions() {

		// FLBuilderModel
		self::add_action( 'get_node_settings', 'FLBuilderModel::get_node_settings', array( 'node_id' ) );
		self::add_action( 'delete_node', 'FLBuilderModel::delete_node', array( 'node_id' ) );
		self::add_action( 'delete_col', 'FLBuilderModel::delete_col', array( 'node_id', 'new_width' ) );
		self::add_action( 'reorder_node', 'FLBuilderModel::reorder_node', array( 'node_id', 'position' ) );
		self::add_action( 'reorder_col', 'FLBuilderModel::reorder_col', array( 'node_id', 'position' ) );
		self::add_action( 'move_node', 'FLBuilderModel::move_node', array( 'node_id', 'new_parent', 'position' ) );
		self::add_action( 'move_col', 'FLBuilderModel::move_col', array( 'node_id', 'new_parent', 'position', 'resize' ) );
		self::add_action( 'resize_cols', 'FLBuilderModel::resize_cols', array( 'col_id', 'col_width', 'sibling_id', 'sibling_width' ) );
		self::add_action( 'reset_col_widths', 'FLBuilderModel::reset_col_widths', array( 'group_id' ) );
		self::add_action( 'resize_row_content', 'FLBuilderModel::resize_row_content', array( 'node', 'width' ) );
		self::add_action( 'save_settings', 'FLBuilderModel::save_settings', array( 'node_id', 'settings' ) );
		self::add_action( 'verify_settings', 'FLBuilderModel::verify_settings', array( 'settings' ) );
		self::add_action( 'save_layout_settings', 'FLBuilderModel::save_layout_settings', array( 'settings' ) );
		self::add_action( 'save_global_settings', 'FLBuilderModel::save_global_settings', array( 'settings' ) );
		self::add_action( 'save_color_presets', 'FLBuilderModel::save_color_presets', array( 'presets' ) );
		self::add_action( 'duplicate_post', 'FLBuilderModel::duplicate_post' );
		self::add_action( 'duplicate_wpml_layout', 'FLBuilderModel::duplicate_wpml_layout', array( 'original_post_id', 'post_id' ) );
		self::add_action( 'apply_user_template', 'FLBuilderModel::apply_user_template', array( 'template_id', 'append' ) );
		self::add_action( 'apply_template', 'FLBuilderModel::apply_template', array( 'template_id', 'append' ) );
		self::add_action( 'save_layout', 'FLBuilderModel::save_layout', array( 'publish', 'exit' ) );
		self::add_action( 'save_draft', 'FLBuilderModel::save_draft' );
		self::add_action( 'clear_draft_layout', 'FLBuilderModel::clear_draft_layout' );
		self::add_action( 'disable_builder', 'FLBuilderModel::disable' );
		self::add_action( 'clear_cache', 'FLBuilderModel::delete_all_asset_cache' );

		// FLBuilderAJAXLayout
		self::add_action( 'get_layout', 'FLBuilderAJAXLayout::get_layout' );
		self::add_action( 'render_layout', 'FLBuilderAJAXLayout::render' );
		self::add_action( 'render_node', 'FLBuilderAJAXLayout::render', array( 'node_id' ) );
		self::add_action( 'render_new_row', 'FLBuilderAJAXLayout::render_new_row', array( 'cols', 'position', 'module' ) );
		self::add_action( 'render_new_row_template', 'FLBuilderAJAXLayout::render_new_row_template', array( 'position', 'template_id', 'template_type' ) );
		self::add_action( 'copy_row', 'FLBuilderAJAXLayout::copy_row', array( 'node_id', 'settings', 'settings_id' ) );
		self::add_action( 'render_new_column_group', 'FLBuilderAJAXLayout::render_new_column_group', array( 'node_id', 'cols', 'position', 'module' ) );
		self::add_action( 'render_new_columns', 'FLBuilderAJAXLayout::render_new_columns', array( 'node_id', 'insert', 'type', 'nested', 'module' ) );
		self::add_action( 'render_new_col_template', 'FLBuilderAJAXLayout::render_new_col_template', array( 'template_id', 'parent_id', 'position', 'template_type' ) );
		self::add_action( 'copy_col', 'FLBuilderAJAXLayout::copy_col', array( 'node_id', 'settings', 'settings_id' ) );
		self::add_action( 'render_new_module', 'FLBuilderAJAXLayout::render_new_module', array( 'parent_id', 'position', 'type', 'alias', 'template_id', 'template_type' ) );
		self::add_action( 'copy_module', 'FLBuilderAJAXLayout::copy_module', array( 'node_id', 'settings' ) );

		// FLBuilderUISettingsForms
		self::add_action( 'render_legacy_settings', 'FLBuilderUISettingsForms::render_legacy_settings', array( 'data', 'form', 'group', 'lightbox' ) );
		self::add_action( 'render_settings_form', 'FLBuilderUISettingsForms::render_settings_form', array( 'type', 'settings' ) );
		self::add_action( 'render_icon_selector', 'FLBuilderUISettingsForms::render_icon_selector' );

		// FLBuilderRevisions
		self::add_action( 'render_revision_preview', 'FLBuilderRevisions::render_preview', array( 'revision_id' ) );
		self::add_action( 'restore_revision', 'FLBuilderRevisions::restore', array( 'revision_id' ) );
		self::add_action( 'refresh_revision_items', 'FLBuilderRevisions::get_config', array( 'post_id' ) );

		// FLBuilderHistoryManager
		self::add_action( 'save_history_state', 'FLBuilderHistoryManager::save_current_state', array( 'label', 'module_type' ) );
		self::add_action( 'render_history_state', 'FLBuilderHistoryManager::render_state', array( 'position' ) );
		self::add_action( 'clear_history_states', 'FLBuilderHistoryManager::delete_states', array( 'post_id' ) );

		// FLBuilderServices
		self::add_action( 'render_service_settings', 'FLBuilderServices::render_settings' );
		self::add_action( 'render_service_fields', 'FLBuilderServices::render_fields' );
		self::add_action( 'connect_service', 'FLBuilderServices::connect_service' );
		self::add_action( 'delete_service_account', 'FLBuilderServices::delete_account' );
		self::add_action( 'delete_service_account', 'FLBuilderServices::delete_account' );

		// FLBuilderAutoSuggest
		self::add_action( 'fl_builder_autosuggest', 'FLBuilderAutoSuggest::init' );
		self::add_action( 'get_autosuggest_values', 'FLBuilderAutoSuggest::get_values', array( 'fields' ) );

		self::add_action( 'save_browser_stats', 'FLBuilderUsage::browser_stats', array( 'browser_data' ) );
		//	self::add_action( 'clear_cache_for_layout', 'FLBuilderAJAXLayout::refresh_layout_cache' );
	}

	/**
	 * Runs the current AJAX action.
	 *
	 * @since 1.7
	 * @access private
	 * @return void
	 */
	static private function call_action() {
		// Only run for logged in users.
		if ( ! is_user_logged_in() ) {
			return;
		}

		// Verify the AJAX nonce.
		if ( ! self::verify_nonce() ) {
			return;
		}

		// Get the $_POST data.
		$post_data = FLBuilderModel::get_post_data();

		// Get the post ID.
		$post_id = FLBuilderModel::get_post_id();

		// Make sure we have a post ID.
		if ( ! $post_id ) {
			return;
		}

		// Make sure the user can edit this post.
		if ( ! current_user_can( 'edit_post', $post_id ) ) {
			return;
		}

		// Get the action.
		if ( ! empty( $_REQUEST['fl_action'] ) ) {
			$action = $_REQUEST['fl_action'];
		} elseif ( ! empty( $post_data['fl_action'] ) ) {
			$action = $post_data['fl_action'];
		} else {
			return;
		}

		/**
		 * Allow developers to modify actions before they are called.
		 * @see fl_ajax_before_call_action
		 */
		do_action( 'fl_ajax_before_call_action', $action );

		// Make sure the action exists.
		if ( ! isset( self::$actions[ $action ] ) ) {
			return;
		}

		// Get the action data.
		$action    = self::$actions[ $action ];
		$args      = array();
		$keys_args = array();

		// Build the args array.
		foreach ( $action['args'] as $arg ) {
			// @codingStandardsIgnoreLine
			$args[] = $keys_args[ $arg ] = isset( $post_data[ $arg ] ) ? $post_data[ $arg ] : null;
		}

		// Tell WordPress this is an AJAX request.
		if ( ! defined( 'DOING_AJAX' ) ) {
			define( 'DOING_AJAX', true );
		}

		/**
		 * Allow developers to hook before the action runs.
		 * @see fl_ajax_before_
		 * @link https://docs.wpbeaverbuilder.com/beaver-builder/developer/tutorials-guides/common-beaver-builder-filter-examples
		 */
		do_action( 'fl_ajax_before_' . $action['action'], $keys_args );

		/**
		 * Call the action and allow developers to filter the result.
		 * @see fl_ajax_
		 */
		$result = apply_filters( 'fl_ajax_' . $action['action'], call_user_func_array( $action['method'], $args ), $keys_args );

		/**
		 * Allow developers to hook after the action runs.
		 * @see fl_ajax_after_
		 * @link https://docs.wpbeaverbuilder.com/beaver-builder/developer/tutorials-guides/common-beaver-builder-filter-examples
		 */
		do_action( 'fl_ajax_after_' . $action['action'], $keys_args );

		/**
		 * Set header for JSON if headers have not been sent.
		 */
		if ( ! headers_sent() ) {
			header( 'Content-Type:text/plain' );
		}

		// JSON encode the result.
		echo FLBuilderUtils::json_encode( $result );

		// Complete the request.
		die();
	}

	/**
	 * Checks to make sure the AJAX nonce is valid.
	 *
	 * @since 1.7.2
	 * @access private
	 * @return bool
	 */
	static private function verify_nonce() {
		$post_data = FLBuilderModel::get_post_data();
		$nonce     = false;

		if ( isset( $post_data['_wpnonce'] ) ) {
			$nonce = $post_data['_wpnonce'];
		} elseif ( isset( $_REQUEST['_wpnonce'] ) ) {
			$nonce = $_REQUEST['_wpnonce'];
		}

		if ( ! $nonce || ! wp_verify_nonce( $nonce, 'fl_ajax_update' ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Is this an AJAX response?
	 * @since 2.0.7
	 * @return bool
	 */
	static public function doing_ajax() {
		if ( function_exists( 'wp_doing_ajax' ) ) {
			return wp_doing_ajax();
		}
		if ( defined( 'DOING_AJAX' ) ) {
			return DOING_AJAX;
		}
		return false;
	}
}

FLBuilderAJAX::init();
