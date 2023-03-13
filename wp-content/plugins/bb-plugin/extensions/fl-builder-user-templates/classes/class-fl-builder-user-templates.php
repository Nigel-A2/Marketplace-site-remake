<?php

/**
 * User defined templates for the builder.
 *
 * @since 1.8
 */
final class FLBuilderUserTemplates {

	/**
	 * Initialize hooks.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function init() {
		/* Actions */
		add_action( 'plugins_loaded', __CLASS__ . '::init_ajax' );
		add_action( 'after_setup_theme', __CLASS__ . '::register_user_access_settings' );
		add_action( 'init', __CLASS__ . '::load_settings', 1 );
		add_action( 'wp_footer', __CLASS__ . '::render_ui_js_templates' );

		/* Filters */
		add_filter( 'template_include', __CLASS__ . '::template_include', 999 );
		add_filter( 'fl_builder_has_templates', __CLASS__ . '::has_templates', 10, 2 );
		add_filter( 'fl_builder_template_selector_data', __CLASS__ . '::selector_data', 999 );
		add_filter( 'fl_builder_ui_bar_buttons', __CLASS__ . '::ui_bar_buttons' );
		add_filter( 'fl_builder_ui_js_config', __CLASS__ . '::ui_js_config' );
		add_filter( 'fl_builder_settings_form_config', __CLASS__ . '::settings_form_config' );
		add_filter( 'fl_builder_content_classes', __CLASS__ . '::content_classes' );
		add_filter( 'fl_builder_render_nodes', __CLASS__ . '::render_nodes' );
		add_filter( 'fl_builder_row_attributes', __CLASS__ . '::row_attributes', 10, 2 );
		add_filter( 'fl_builder_column_attributes', __CLASS__ . '::column_attributes', 10, 2 );
		add_filter( 'fl_builder_module_attributes', __CLASS__ . '::module_attributes', 10, 2 );
		add_filter( 'fl_builder_content_panel_data', __CLASS__ . '::filter_content_panel_data' );
		add_filter( 'fl_builder_content_elements_data', __CLASS__ . '::filter_content_items_data' );
	}

	/**
	 * Initialize AJAX actions.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function init_ajax() {
		FLBuilderAJAX::add_action( 'save_user_template', 'FLBuilderModel::save_user_template', array( 'settings' ) );
		FLBuilderAJAX::add_action( 'delete_user_template', 'FLBuilderModel::delete_user_template', array( 'template_id' ) );
		FLBuilderAJAX::add_action( 'save_node_template', 'FLBuilderModel::save_node_template', array( 'node_id', 'settings' ) );
		FLBuilderAJAX::add_action( 'delete_node_template', 'FLBuilderModel::delete_node_template', array( 'template_id' ) );
	}

	/**
	 * Registers the user access settings for user templates.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function register_user_access_settings() {
		FLBuilderUserAccess::register_setting( 'builder_admin', array(
			'default'     => array( 'administrator' ),
			'group'       => __( 'Admin', 'fl-builder' ),
			'label'       => __( 'Builder Admin', 'fl-builder' ),
			'description' => __( 'The selected roles will be able to access the builder admin menu.', 'fl-builder' ),
			'order'       => '100',
		) );

		FLBuilderUserAccess::register_setting( 'global_node_editing', array(
			'default'     => 'all',
			'group'       => __( 'Frontend', 'fl-builder' ),
			'label'       => __( 'Global Rows, Columns and Modules Editing', 'fl-builder' ),
			'description' => __( 'The selected roles will be able to edit global rows, columns and modules.', 'fl-builder' ),
			'order'       => '10',
		) );
	}

	/**
	 * Loads files for the template settings.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function load_settings() {
		require_once FL_BUILDER_USER_TEMPLATES_DIR . 'includes/user-template-settings.php';
		require_once FL_BUILDER_USER_TEMPLATES_DIR . 'includes/node-template-settings.php';
	}

	/**
	 * Tries to load page.php for editing a builder template.
	 *
	 * @since 1.0
	 * @param string $template The current template to be loaded.
	 * @return string
	 */
	static public function template_include( $template ) {
		global $post;

		if ( 'string' == gettype( $template ) && $post && 'fl-builder-template' == $post->post_type ) {

			$page = locate_template( array( 'page.php' ) );

			if ( ! empty( $page ) ) {
				return $page;
			}
		}

		return $template;
	}

	/**
	 * Hook into the fl_builder_has_templates filter and always return true
	 * so the template selector shows even if there are no core templates
	 * or third party theme templates available.
	 *
	 * @since 1.8
	 * @param bool $has_templates
	 * @return bool
	 */
	static public function has_templates( $has_templates ) {
		$enabled_templates = FLBuilderModel::get_enabled_templates();

		if ( 'core' == $enabled_templates ) {
			$templates = FLBuilderModel::get_template_selector_data();
			return ( count( $templates['templates'] ) > 0 );
		} elseif ( 'user' == $enabled_templates ) {
			return true;
		} elseif ( 'enabled' == $enabled_templates ) {
			return true;
		} elseif ( 'disabled' == $enabled_templates ) {
			return false;
		}

		return true;
	}

	/**
	 * Disables core or third party templates if all templates are disabled
	 * or only user templates are enabled.
	 *
	 * @since 1.8
	 * @param array $data
	 * @return array
	 */
	static public function selector_data( $data ) {
		if ( in_array( FLBuilderModel::get_enabled_templates(), array( 'user', 'disabled' ) ) ) {
			$data = array(
				'templates'   => array(),
				'categorized' => array(),
			);
		}

		return $data;
	}

	/**
	 * Modifies the UI bar buttons for user templates if needed.
	 *
	 * @since 1.8
	 * @param array $buttons
	 * @return array
	 */
	static public function ui_bar_buttons( $buttons ) {
		$is_module_template = FLBuilderModel::is_post_user_template( 'module' );

		if ( isset( $buttons['content-panel'] ) && $is_module_template ) {
			$buttons['content-panel']['show'] = false;
		}

		return $buttons;
	}

	/**
	 * Sets the JS config variables for user templates.
	 *
	 * @since 1.8
	 * @param array $config
	 * @return array
	 */
	static public function ui_js_config( $config ) {
		return array_merge( $config, array(
			'enabledTemplates'           => FLBuilderModel::get_enabled_templates(),
			'isUserTemplate'             => FLBuilderModel::is_post_user_template() ? true : false,
			'userCanEditGlobalTemplates' => FLBuilderUserAccess::current_user_can( 'global_node_editing' ) ? true : false,
			'userTemplateType'           => FLBuilderModel::get_user_template_type(),
		) );
	}

	/**
	* Filter the data structure for the content panel.
	*
	* @since 2.0
	* @param array $data The existing panel data
	* @return array The filtered panel data
	*/
	static public function filter_content_panel_data( $data ) {

		if ( FLBuilderModel::node_templates_enabled() ) {

			$saved_layouts = FLBuilderModel::get_user_templates( 'layout' );
			$saved_rows    = FLBuilderModel::get_user_templates( 'row' );
			$saved_cols    = FLBuilderModel::get_user_templates( 'column' );
			$saved_modules = FLBuilderModel::get_user_templates( 'module' );

			// Saved modules view
			$data['tabs']['modules']['views'][] = array(
				'type' => 'separator',
			);

			$data['tabs']['modules']['views'][] = array(
				'handle'       => 'savedModules',
				'name'         => __( 'Saved Modules', 'fl-builder' ),
				'templateName' => 'fl-content-panel-saved-modules',
				'hasChildren'  => count( $saved_modules['categorized'] ) > 1,
				'query'        => array(
					'kind'        => 'template',
					'type'        => 'user',
					'content'     => 'module',
					'categorized' => true,
				),
			);

			if ( count( $saved_modules['categorized'] ) > 1 ) {
				foreach ( $saved_modules['categorized'] as $handle => $category ) {
					$data['tabs']['modules']['views'][] = array(
						'handle'       => 'user-' . $handle,
						'name'         => $category['name'],
						'templateName' => 'fl-content-panel-saved-modules',
						'isSubItem'    => true,
						'parent'       => 'savedModules',
						'query'        => array(
							'kind'     => 'template',
							'type'     => 'user',
							'content'  => 'module',
							'category' => $handle,
						),
					);
				}
			}

			$is_col_template = FLBuilderModel::is_post_user_template( 'column' );

			if ( ! $is_col_template ) {

				// Saved columns view
				$data['tabs']['rows']['views'][] = array(
					'type' => 'separator',
				);

				$data['tabs']['rows']['views'][] = array(
					'handle'       => 'savedColumns',
					'name'         => __( 'Saved Columns', 'fl-builder' ),
					'templateName' => 'fl-content-panel-saved-columns',
					'hasChildren'  => count( $saved_cols['categorized'] ) > 1,
					'query'        => array(
						'kind'        => 'template',
						'type'        => 'user',
						'content'     => 'column',
						'categorized' => true,
					),
				);

				if ( count( $saved_cols['categorized'] ) > 1 ) {
					foreach ( $saved_cols['categorized'] as $handle => $category ) {
						$data['tabs']['rows']['views'][] = array(
							'handle'       => 'user-' . $handle . '-savedColumns',
							'name'         => $category['name'],
							'templateName' => 'fl-content-panel-saved-columns',
							'isSubItem'    => true,
							'parent'       => 'savedColumns',
							'query'        => array(
								'kind'     => 'template',
								'type'     => 'user',
								'content'  => 'column',
								'category' => $handle,
							),
						);
					}
				}
			}

			$is_row_template    = FLBuilderModel::is_post_user_template( 'row' );
			$is_module_template = FLBuilderModel::is_post_user_template( 'module' );

			if ( ! $is_row_template && ! $is_col_template && ! $is_module_template ) {

				// Saved rows view
				$data['tabs']['rows']['views'][] = array(
					'type' => 'separator',
				);

				$data['tabs']['rows']['views'][] = array(
					'handle'       => 'savedRows',
					'name'         => __( 'Saved Rows', 'fl-builder' ),
					'templateName' => 'fl-content-panel-saved-rows',
					'hasChildren'  => count( $saved_rows['categorized'] ) > 1,
					'query'        => array(
						'kind'        => 'template',
						'type'        => 'user',
						'content'     => 'row',
						'categorized' => false,
					),
				);

				if ( count( $saved_rows['categorized'] ) > 1 ) {
					foreach ( $saved_rows['categorized'] as $handle => $category ) {
						$data['tabs']['rows']['views'][] = array(
							'handle'       => 'user-' . $handle,
							'name'         => $category['name'],
							'templateName' => 'fl-content-panel-saved-rows',
							'isSubItem'    => true,
							'parent'       => 'savedRows',
							'query'        => array(
								'kind'     => 'template',
								'type'     => 'user',
								'content'  => 'row',
								'category' => $handle,
							),
						);
					}
				}

				// Save templates view
				$data['tabs']['templates']['views'][45] = array(
					'type' => 'separator',
				);

				$data['tabs']['templates']['views'][50] = array(
					'handle'       => 'user-templates',
					'name'         => __( 'Saved Templates', 'fl-builder' ),
					'hasChildren'  => count( $saved_layouts['categorized'] ) > 1,
					'query'        => array(
						'kind'        => 'template',
						'type'        => 'user',
						'content'     => 'layout',
						'categorized' => true,
					),
					'templateName' => 'fl-content-panel-saved-templates',
				);

				if ( count( $saved_layouts['categorized'] ) > 1 ) {
					foreach ( $saved_layouts['categorized'] as $handle => $category ) {
						$data['tabs']['templates']['views'][] = array(
							'handle'       => 'fl-user-' . $handle,
							'name'         => $category['name'],
							'isSubItem'    => true,
							'parent'       => 'user-templates',
							'query'        => array(
								'kind'        => 'template',
								'type'        => 'user',
								'content'     => 'layout',
								'category'    => $handle,
								'categorized' => false,
							),
							'templateName' => 'fl-content-panel-saved-templates',
						);
					}
				}
			}

			// Saved tab view
			$data['tabs']['saved'] = array(
				'handle' => 'saved',
				'name'   => __( 'Saved', 'fl-builder' ),
				'views'  => array(
					'main' => array(
						'handle' => 'saved',
						'name'   => __( 'Saved', 'fl-builder' ),
						'query'  => array(
							'kind'     => 'template',
							'type'     => 'user',
							'category' => 'uncategorized',
						),
					),
				),
			);
		}

		return $data;
	}

	/**
	 * Filter the content items js data.
	 *
	 * @param array $data The existing content data.
	 * @return array The filtered data.
	 */
	static public function filter_content_items_data( $data ) {
		$layouts          = FLBuilderModel::get_user_templates( 'layout' );
		$layout_templates = $layouts['templates'];

		foreach ( $layout_templates as $template ) {
			$data['template'][] = $template;
		}

		$rows          = FLBuilderModel::get_user_templates( 'row' );
		$row_templates = $rows['templates'];

		foreach ( $row_templates as $template ) {
			$data['template'][] = $template;
		}

		$cols          = FLBuilderModel::get_user_templates( 'column' );
		$col_templates = $cols['templates'];

		foreach ( $col_templates as $template ) {
			$data['template'][] = $template;
		}

		$modules          = FLBuilderModel::get_user_templates( 'module' );
		$module_templates = $modules['templates'];

		foreach ( $module_templates as $template ) {
			$data['template'][] = $template;
		}

		return $data;
	}

	/**
	 * Renders the markup for the JavaScript UI templates.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function render_ui_js_templates() {
		if ( FLBuilderModel::is_builder_active() ) {
			include FL_BUILDER_USER_TEMPLATES_DIR . 'includes/ui-js-templates.php';
		}
	}

	/**
	 * Modifies the config of settings forms for user templates if needed.
	 *
	 * @since 1.8
	 * @param array $config
	 * @return array
	 */
	static public function settings_form_config( $config ) {
		$is_row    = stristr( $config['class'], 'fl-builder-row-settings' );
		$is_col    = stristr( $config['class'], 'fl-builder-col-settings' );
		$is_module = stristr( $config['class'], 'fl-builder-module-settings' );

		if ( $is_row || $is_col || $is_module ) {

			$post_data = FLBuilderModel::get_post_data();
			$global    = false;

			if ( isset( $post_data['node_id'] ) ) {
				$global = FLBuilderModel::is_node_global( FLBuilderModel::get_node( $post_data['node_id'] ) );
			} elseif ( isset( $post_data['template_id'] ) ) {
				$template_post_id = FLBuilderModel::get_node_template_post_id( $post_data['template_id'] );
				$global           = ! $template_post_id ? false : FLBuilderModel::is_post_global_node_template( $template_post_id );
			}

			if ( $global ) {
				$config['badges']['global'] = _x( 'Global', 'Indicator for global node templates.', 'fl-builder' );
			}
			if ( ( $is_row || $is_col || $is_module ) && ! $global && ! FLBuilderModel::is_post_node_template() && FLBuilderModel::node_templates_enabled() ) {
				$config['buttons'][] = 'save-as';
			}
		}

		return $config;
	}

	/**
	 * Adds template classes to the builder's content classes.
	 *
	 * @since 1.8
	 * @param string $classes
	 * @return string
	 */
	static public function content_classes( $classes ) {
		// Add template classes to the content class.
		if ( FLBuilderModel::is_post_user_template() ) {
			$classes .= ' fl-builder-template';
			$classes .= ' fl-builder-' . FLBuilderModel::get_user_template_type() . '-template';
		}

		// Add the global templates locked class.
		if ( ! FLBuilderUserAccess::current_user_can( 'global_node_editing' ) ) {
			$classes .= ' fl-builder-global-templates-locked';
		}

		return $classes;
	}

	/**
	 * Short circuits node rendering and renders modules if this
	 * is a module template.
	 *
	 * @since 1.8
	 * @param bool $render
	 * @return bool
	 */
	static public function render_nodes( $render ) {
		if ( FLBuilderModel::is_post_user_template( 'module' ) ) {
			FLBuilder::render_modules();
			return false;
		} elseif ( FLBuilderModel::is_post_user_template( 'column' ) ) {

			$root_node = FLBuilderModel::get_node_template_root( 'column' );

			// Renders the column root node.
			if ( $root_node ) {
				FLBuilder::render_column( $root_node );
				return false;
			}
		}

		return $render;
	}

	/**
	 * Adds template specific attributes for rows.
	 *
	 * @since 1.8
	 * @param array $attrs
	 * @param object $row
	 * @return array
	 */
	static public function row_attributes( $attrs, $row ) {
		$global = FLBuilderModel::is_node_global( $row );
		$active = FLBuilderModel::is_builder_active();

		if ( $global && $active ) {
			$attrs['class'][] = 'fl-node-global';
		}
		if ( $global && $active ) {
			$attrs['data-template']      = $row->template_id;
			$attrs['data-template-node'] = $row->template_node_id;
			$attrs['data-template-url']  = FLBuilderModel::get_node_template_edit_url( $row->template_id );
		}

		return $attrs;
	}

	/**
	 * Adds template specific attributes for columns.
	 *
	 * @since 1.8
	 * @param array $attrs
	 * @param object $col
	 * @return array
	 */
	static public function column_attributes( $attrs, $col ) {
		$global = FLBuilderModel::is_node_global( $col );
		$active = FLBuilderModel::is_builder_active();

		if ( $global && $active ) {
			$attrs['class'][] = 'fl-node-global';
		}
		if ( $global && $active ) {
			$attrs['data-template']      = $col->template_id;
			$attrs['data-template-node'] = $col->template_node_id;

			if ( isset( $col->template_root_node ) ) {
				$attrs['data-template-url'] = FLBuilderModel::get_node_template_edit_url( $col->template_id );
			}
		}

		return $attrs;
	}

	/**
	 * Adds template specific attributes for modules.
	 *
	 * @since 1.8
	 * @param array $attrs
	 * @param object $module
	 * @return array
	 */
	static public function module_attributes( $attrs, $module ) {
		$global = FLBuilderModel::is_node_global( $module );
		$active = FLBuilderModel::is_builder_active();

		if ( $global && $active ) {
			$attrs['class'][] = 'fl-node-global';
		}
		if ( $global && $active ) {
			$attrs['data-template']      = $module->template_id;
			$attrs['data-template-node'] = $module->template_node_id;
		}

		return $attrs;
	}

	/**
	 * @since 1.8
	 * @deprecated 2.0
	 */
	static public function selector_filter_data( $data ) {
		_deprecated_function( __METHOD__, '2.0' );

		return $data;
	}

	/**
	 * @since 1.6.3
	 * @deprecated 2.0
	 */
	static public function render_ui_panel_node_templates() {
		_deprecated_function( __METHOD__, '2.0' );
	}

	/**
	 * @since 1.8
	 * @deprecated 2.0
	 */
	static public function render_selector_content() {
		_deprecated_function( __METHOD__, '2.0' );
	}

	/**
	 * @since 1.0
	 * @deprecated 2.0
	 */
	static public function render_settings() {
		_deprecated_function( __METHOD__, '2.0' );
	}

	/**
	 * @since 1.6.3
	 * @deprecated 2.0
	 */
	static public function render_node_settings( $node_id = null ) {
		_deprecated_function( __METHOD__, '2.0' );
	}
}

FLBuilderUserTemplates::init();
