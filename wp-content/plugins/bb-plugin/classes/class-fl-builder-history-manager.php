<?php

/**
 * Handles undo/redo history for the builder.
 */
final class FLBuilderHistoryManager {

	static private $states_cache = false;

	/**
	 * Initialize hooks.
	 */
	static public function init() {
		if ( ! defined( 'FL_BUILDER_HISTORY_STATES' ) ) {
			define( 'FL_BUILDER_HISTORY_STATES', 20 );
		}

		// Filters
		add_filter( 'fl_builder_ui_js_config', __CLASS__ . '::ui_js_config' );
		add_filter( 'fl_builder_main_menu', __CLASS__ . '::main_menu_config' );

		// Actions
		add_action( 'fl_builder_init_ui', __CLASS__ . '::init_states' );
	}

	/**
	 * Adds history data to the UI JS config.
	 */
	static public function ui_js_config( $config ) {
		$labels = array(
			// Layout
			'draft_created'           => __( 'Draft Created', 'fl-builder' ),
			'changes_discarded'       => __( 'Changes Discarded', 'fl-builder' ),
			'revision_restored'       => __( 'Revision Restored', 'fl-builder' ),

			// Save settings
			'row_edited'              => esc_attr__( 'Row Edited', 'fl-builder' ),
			'column_edited'           => esc_attr__( 'Column Edited', 'fl-builder' ),
			/* translators: %s: Module name */
			'module_edited'           => esc_attr_x( '%s Edited', 'Module name', 'fl-builder' ),
			'global_settings_edited'  => esc_attr__( 'Global Settings Edited', 'fl-builder' ),
			'layout_settings_edited'  => esc_attr__( 'Layout Settings Edited', 'fl-builder' ),

			// Add nodes
			'row_added'               => esc_attr__( 'Row Added', 'fl-builder' ),
			'columns_added'           => esc_attr__( 'Columns Added', 'fl-builder' ),
			'column_added'            => esc_attr__( 'Column Added', 'fl-builder' ),
			/* translators: %s: Module name */
			'module_added'            => esc_attr_x( '%s Added', 'Module name', 'fl-builder' ),

			// Delete nodes
			'row_deleted'             => esc_attr__( 'Row Deleted', 'fl-builder' ),
			'column_deleted'          => esc_attr__( 'Column Deleted', 'fl-builder' ),
			/* translators: %s: Module name */
			'module_deleted'          => esc_attr_x( '%s Deleted', 'Module name', 'fl-builder' ),

			// Duplicate nodes
			'row_duplicated'          => esc_attr__( 'Row Duplicated', 'fl-builder' ),
			'column_duplicated'       => esc_attr__( 'Column Duplicated', 'fl-builder' ),
			/* translators: %s: Module name */
			'module_duplicated'       => esc_attr_x( '%s Duplicated', 'Module name', 'fl-builder' ),

			// Move nodes
			'row_moved'               => esc_attr__( 'Row Moved', 'fl-builder' ),
			'column_moved'            => esc_attr__( 'Column Moved', 'fl-builder' ),
			/* translators: %s: Module name */
			'module_moved'            => esc_attr_x( '%s Moved', 'Module name', 'fl-builder' ),

			// Resize nodes
			'row_resized'             => esc_attr__( 'Row Resized', 'fl-builder' ),
			'columns_resized'         => esc_attr__( 'Columns Resized', 'fl-builder' ),
			'column_resized'          => esc_attr__( 'Column Resized', 'fl-builder' ),

			// Templates
			'template_applied'        => esc_attr__( 'Template Applied', 'fl-builder' ),
			'row_template_applied'    => esc_attr__( 'Row Template Added', 'fl-builder' ),
			'column_template_applied' => esc_attr__( 'Column Template Added', 'fl-builder' ),
			'history_disabled'        => __( 'Undo/Redo history is currently disabled.', 'fl-builder' ),
		);

		$hooks = array(
			// Layout
			'didDiscardChanges'             => 'changes_discarded',
			'didRestoreRevisionComplete'    => 'revision_restored',

			// Save settings
			'didSaveRowSettingsComplete'    => 'row_edited',
			'didSaveColumnSettingsComplete' => 'column_edited',
			'didSaveModuleSettingsComplete' => 'module_edited',
			'didSaveGlobalSettingsComplete' => 'global_settings_edited',
			'didSaveLayoutSettingsComplete' => 'layout_settings_edited',

			// Add nodes
			'didAddRow'                     => 'row_added',
			'didAddColumnGroup'             => 'columns_added',
			'didAddColumn'                  => 'column_added',
			'didAddModule'                  => 'module_added',

			// Delete nodes
			'didDeleteRow'                  => 'row_deleted',
			'didDeleteColumn'               => 'column_deleted',
			'didDeleteModule'               => 'module_deleted',

			// Duplicate nodes
			'didDuplicateRow'               => 'row_duplicated',
			'didDuplicateColumn'            => 'column_duplicated',
			'didDuplicateModule'            => 'module_duplicated',

			// Move nodes
			'didMoveRow'                    => 'row_moved',
			'didMoveColumn'                 => 'column_moved',
			'didMoveModule'                 => 'module_moved',

			// Resize nodes
			'didResizeRow'                  => 'row_resized',
			'didResetRowWidth'              => 'row_resized',
			'didResizeColumn'               => 'column_resized',
			'didResetColumnWidthsComplete'  => 'columns_resized',

			// Templates
			'didApplyTemplateComplete'      => 'template_applied',
			'didApplyRowTemplateComplete'   => 'row_template_applied',
			'didApplyColTemplateComplete'   => 'column_template_applied',
		);

		$config['history'] = array(
			'states'   => self::get_state_labels(),
			'position' => self::get_position(),
			'hooks'    => $hooks,
			'labels'   => $labels,
			'enabled'  => FL_BUILDER_HISTORY_STATES && FL_BUILDER_HISTORY_STATES > 0 ? true : false,
		);
		return $config;
	}

	/**
	 * Adds history data to the main menu config.
	 */
	static public function main_menu_config( $config ) {

		$config['main']['items'][36] = array(
			'label' => __( 'History', 'fl-builder' ),
			'type'  => 'view',
			'view'  => 'history',
		);

		$config['history'] = array(
			'name'       => __( 'History', 'fl-builder' ),
			'isShowing'  => false,
			'isRootView' => false,
			'items'      => array(),
		);

		return $config;
	}

	/**
	 * Adds an initial state if no states exist
	 * when the builder is active.
	 */
	static public function init_states() {
		if ( FL_BUILDER_HISTORY_STATES && FL_BUILDER_HISTORY_STATES > 0 && ! isset( $_GET['nohistory'] ) ) {
			$states = self::get_states();

			if ( empty( $states ) ) {
				self::save_current_state( 'draft_created' );
			}
		} else {
			$post_id = FLBuilderModel::get_post_id();
			self::delete_states( $post_id );
		}
	}

	/**
	 * Returns an array of saved layout states.
	 */
	static public function get_states() {

		if ( self::$states_cache ) {
			return self::$states_cache;
		}

		global $wpdb;

		$post_id = FLBuilderModel::get_post_id();
		$results = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND post_id = %d ORDER BY meta_id", '%_fl_builder_history_state%', $post_id ) );
		$states  = array();

		foreach ( $results as $result ) {
			$value = maybe_unserialize( $result->meta_value );
			if ( is_array( $value ) ) {
				$states[] = $value;
			}
		}
		self::$states_cache = $states;
		return $states;
	}

	/**
	 * Saves an array of layout states to post meta.
	 */
	static public function set_states( $states ) {
		$post_id = FLBuilderModel::get_post_id();

		self::delete_states( $post_id );
		self::$states_cache = false;

		foreach ( $states as $i => $state ) {
			update_post_meta( $post_id, "_fl_builder_history_state_{$i}", $state );
		}
	}

	/**
	 * Deletes all history states for a post.
	 */
	static public function delete_states( $post_id ) {
		global $wpdb;

		$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key LIKE %s AND post_id = %d", '%_fl_builder_history_state%', $post_id ) );

		self::set_position( 0 );
	}

	/**
	 * Returns an array of saved layout states.
	 */
	static public function get_state_labels() {
		$states = self::get_states();
		$labels = array();
		foreach ( $states as $state ) {
			$labels[] = array(
				'label'      => $state['label'],
				'moduleType' => isset( $state['module_type'] ) ? $state['module_type'] : null,
			);
		}
		return $labels;
	}

	/**
	 * Returns the current history position.
	 */
	static public function get_position() {
		$post_id  = FLBuilderModel::get_post_id();
		$position = get_post_meta( $post_id, '_fl_builder_history_position', true );
		return $position ? $position : 0;
	}

	/**
	 * Saves the current history position to post meta.
	 */
	static public function set_position( $position ) {
		$post_id = FLBuilderModel::get_post_id();
		update_post_meta( $post_id, '_fl_builder_history_position', $position );
	}

	/**
	 * Appends the current layout state to the builder's
	 * history post meta. Pops off any trailing states if
	 * the last state isn't the current.
	 */
	static public function save_current_state( $label, $module_type = null ) {
		$position = self::get_position();
		$states   = array_slice( self::get_states(), 0, $position + 1 );
		$states[] = array(
			'label'       => $label,
			'module_type' => $module_type,
			'nodes'       => FLBuilderModel::get_layout_data( 'draft' ),
			'settings'    => array(
				'global' => FLBuilderModel::get_global_settings(),
				'layout' => FLBuilderModel::get_layout_settings( 'draft' ),
			),
		);

		if ( count( $states ) > FL_BUILDER_HISTORY_STATES ) {
			array_shift( $states );
		}

		self::set_states( $states );
		self::set_position( count( $states ) - 1 );

		return array(
			'states'   => self::get_state_labels(),
			'position' => self::get_position(),
		);
	}

	/**
	 * Renders the layout for the state at the given position.
	 */
	static public function render_state( $new_position = 0 ) {
		$states   = self::get_states();
		$position = self::get_position();

		if ( 'prev' === $new_position ) {
			$position = $position <= 0 ? 0 : $position - 1;
		} elseif ( 'next' === $new_position ) {
			$position = $position >= count( $states ) - 1 ? count( $states ) - 1 : $position + 1;
		} else {
			$position = $new_position < 0 || ! is_numeric( $new_position ) ? 0 : $new_position;
		}

		if ( ! isset( $states[ $position ] ) ) {
			return array(
				'error' => true,
			);
		}

		$state = $states[ $position ];
		self::set_position( $position );
		FLBuilderModel::save_global_settings( (array) $state['settings']['global'] );
		FLBuilderModel::update_layout_settings( (array) $state['settings']['layout'], 'draft' );
		FLBuilderModel::update_layout_data( (array) $state['nodes'], 'draft' );

		return array(
			'position' => $position,
			'config'   => FLBuilderUISettingsForms::get_node_js_config(),
			'layout'   => FLBuilderAJAXLayout::render(),
			'settings' => array(
				'global' => FLBuilderModel::get_global_settings(),
				'layout' => FLBuilderModel::get_layout_settings( 'draft' ),
			),
			'newNodes' => FLBuilderModel::get_layout_data(),
		);
	}
}

FLBuilderHistoryManager::init();
