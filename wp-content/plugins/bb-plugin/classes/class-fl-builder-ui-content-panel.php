<?php

/**
 * Handles logic for the builder's content panel UI.
 *
 * @since 2.0
 */
class FLBuilderUIContentPanel {

	/**
	 * Get data structure required by the content panel.
	 *
	 * @since 2.0
	 * @access public
	 * @return array
	 */
	public static function get_panel_data() {

		// Don't load the panel for module templates.
		if ( FLBuilderModel::is_post_user_template( 'module' ) ) {
			return array();
		}

		$data = array(
			'tabs' => array(),
		);

		$modules_data = self::get_modules_tab_data();
		if ( $modules_data['should_display'] ) {
			$modules_tab             = array(
				'handle'          => 'modules',
				'name'            => __( 'Modules', 'fl-builder' ),
				'views'           => $modules_data['views'],
				'isSearchEnabled' => true,
			);
			$data['tabs']['modules'] = $modules_tab;
		}

		$rows_data = self::get_rows_tab_data();
		if ( $rows_data['should_display'] ) {
			$rows_tab             = array(
				'handle' => 'rows',
				'name'   => __( 'Rows', 'fl-builder' ),
				'views'  => $rows_data['views'],
			);
			$data['tabs']['rows'] = $rows_tab;
		}

		$templates_data = self::get_templates_tab_data();
		if ( $templates_data['should_display'] ) {
			$templates_tab             = array(
				'handle' => 'templates',
				'name'   => __( 'Templates', 'fl-builder' ),
				'views'  => $templates_data['views'],
			);
			$data['tabs']['templates'] = $templates_tab;
		}

		/**
		* Filter the tabs/views structure
		*
		* @since 2.0
		* @param Array $data the initial tab data
		* @see fl_builder_content_panel_data
		*/
		return apply_filters( 'fl_builder_content_panel_data', $data );
	}

	/**
	 * Get module views for panel.
	 *
	 * @since 2.0
	 * @access private
	 * @return array
	 */
	private static function get_modules_tab_data() {

		$data = array(
			'should_display' => ! FLBuilderModel::is_post_user_template( 'module' ),
			'views'          => array(),
		);

		// Standard Modules View
		$data['views'][] = array(
			'handle'              => 'standard',
			'name'                => __( 'Standard Modules', 'fl-builder' ),
			'query'               => array(
				'kind'        => 'module',
				'categorized' => true,
				'group'       => 'standard',
			),
			'orderedSectionNames' => array_keys( FLBuilderModel::get_module_categories() ),
		);

		// Third Party Module Groups
		$groups = FLBuilderModel::get_module_groups();
		if ( ! empty( $groups ) ) {

			$data['views'][] = array(
				'type' => 'separator',
			);

			foreach ( $groups as $slug => $name ) {
				$data['views'][] = array(
					'handle'       => $slug,
					'name'         => $name,
					'query'        => array(
						'kind'        => array( 'module', 'template' ),
						'content'     => 'module',
						'type'        => 'core',
						'categorized' => true,
						'group'       => $slug,
					),
					'templateName' => 'fl-content-panel-modules-view',
				);
			}
		}

		return $data;
	}

	/**
	 * Get data for the rows tab.
	 *
	 * @since 2.0
	 * @access private
	 * @return array
	 */
	private static function get_rows_tab_data() {

		$data = array(
			'should_display' => true, /* rows tab shows even if row template */
			'views'          => array(),
		);

		// Columns View
		$data['views'][] = array(
			'handle'       => 'columns',
			'name'         => __( 'Columns', 'fl-builder' ),
			'query'        => array(
				'kind' => 'colGroup',
			),
			'templateName' => 'fl-content-panel-col-groups-view',
		);

		// Row Templates View
		$templates          = FLBuilderModel::get_row_templates_data();
		$is_row_template    = FLBuilderModel::is_post_user_template( 'row' );
		$is_column_template = FLBuilderModel::is_post_user_template( 'column' );

		if ( ! $is_row_template && ! $is_column_template && isset( $templates['groups'] ) && ! empty( $templates['groups'] ) ) {

			$data['views'][] = array(
				'type' => 'separator',
			);

			foreach ( $templates['groups'] as $slug => $group ) {

				$data['views'][] = array(
					'handle'      => $slug,
					'name'        => $group['name'],
					'hasChildren' => count( $group['categories'] ) > 1,
					'query'       => array(
						'kind'        => 'template',
						'type'        => 'core',
						'group'       => $slug,
						'content'     => 'row',
						'categorized' => true,
					),
				);

				if ( count( $group['categories'] ) < 2 ) {
					continue;
				}

				foreach ( $group['categories'] as $cat_slug => $category ) {
					$data['views'][] = array(
						'handle'    => $cat_slug,
						'name'      => $category['name'],
						'isSubItem' => true,
						'parent'    => $slug,
						'query'     => array(
							'kind'        => 'template',
							'type'        => 'core',
							'content'     => 'row',
							'group'       => $slug,
							'category'    => $cat_slug,
							'categorized' => true,
						),
					);
				}
			}
		}

		return $data;
	}

	/**
	 * Get data for the templates tab.
	 *
	 * @since 2.0
	 * @access private
	 * @return array
	 */
	private static function get_templates_tab_data() {
		$enabled            = FLBuilderModel::get_enabled_templates();
		$is_module_template = FLBuilderModel::is_post_user_template( 'module' );
		$is_column_template = FLBuilderModel::is_post_user_template( 'column' );
		$is_row_template    = FLBuilderModel::is_post_user_template( 'row' );
		$data               = array(
			'should_display' => ( ! $is_module_template && ! $is_column_template && ! $is_row_template && 'disabled' !== $enabled ),
			'views'          => array(),
		);

		$templates = FLBuilderModel::get_template_selector_data();

		if ( ! isset( $templates['groups'] ) || empty( $templates['groups'] ) ) {

			if ( true === FL_BUILDER_LITE ) {
				$data['views'][] = array(
					'handle'       => 'standard',
					'name'         => __( 'Upgrade', 'fl-builder' ),
					'templateName' => 'fl-content-lite-templates-upgrade-view',
				);
			}

			return $data;
		}

		foreach ( $templates['groups'] as $slug => $group ) {

			$data['views'][] = array(
				'handle'      => $slug,
				'name'        => $group['name'],
				'hasChildren' => count( $group['categories'] ) > 1,
				'query'       => array(
					'kind'        => 'template',
					'type'        => 'core',
					'content'     => 'layout',
					'group'       => $slug,
					'categorized' => true,
				),
			);

			if ( count( $group['categories'] ) < 2 ) {
				continue;
			}

			foreach ( $group['categories'] as $cat_slug => $category ) {
				$data['views'][] = array(
					'handle'    => $cat_slug,
					'name'      => $category['name'],
					'isSubItem' => true,
					'parent'    => $slug,
					'query'     => array(
						'kind'        => 'template',
						'type'        => 'core',
						'content'     => 'layout',
						'group'       => $slug,
						'category'    => $cat_slug,
						'categorized' => true,
					),
				);
			}
		}

		return $data;
	}

	/**
	 * Get all the insertable content elements that make up the content library.
	 *
	 * @since 2.0
	 * @access public
	 * @return array
	 */
	public static function get_content_elements() {

		$data = array(

			/* Get all modules */
			'module'   => FLBuilderModel::get_uncategorized_modules(),

			/* Get all column groups */
			'colGroup' => FLBuilderModel::get_column_groups(),

			/* Get all templates */
			'template' => array(),

			/* Lite only: Get all pro modules */
			'pro'      => FLBuilderModel::get_pro_modules_config(),
		);

		$static_modules   = FLBuilderModel::get_module_templates_data();
		$module_templates = $static_modules['templates'];

		foreach ( $module_templates as $template ) {
			$data['template'][] = $template;
		}

		$static_columns   = FLBuilderModel::get_column_templates_data();
		$column_templates = $static_columns['templates'];

		foreach ( $column_templates as $template ) {
			$data['template'][] = $template;
		}

		$static_rows   = FLBuilderModel::get_row_templates_data();
		$row_templates = $static_rows['templates'];

		foreach ( $row_templates as $template ) {
			$data['template'][] = $template;
		}

		$static_templates = FLBuilderModel::get_template_selector_data();
		$layout_templates = $static_templates['templates'];

		foreach ( $layout_templates as $template ) {
			$data['template'][] = $template;
		}

		/**
		* Filter the available content elements
		*
		* @since 2.0
		* @param Array $data the initial content elements
		* @see fl_builder_content_elements_data
		*/
		return apply_filters( 'fl_builder_content_elements_data', $data );
	}
}
