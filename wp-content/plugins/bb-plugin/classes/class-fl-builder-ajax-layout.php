<?php

/**
 * Handles the rendering of the layout for AJAX refreshes.
 *
 * @since 1.7
 */
final class FLBuilderAJAXLayout {

	/**
	 * An array with data for partial refreshes.
	 *
	 * @since 1.7
	 * @access private
	 * @var array $partial_refresh_data
	 */
	static private $partial_refresh_data = null;

	/**
	 * Renders the layout data to be passed back to the builder.
	 *
	 * @since 1.7
	 * @param string $node_id The ID of a node to try and render instead of the entire layout.
	 * @param string $old_node_id The ID of a node that has been replaced in the layout.
	 * @return array
	 */
	static public function render( $node_id = null, $old_node_id = null ) {
		/**
		 * Before ajax layout rendered.
		 * @see fl_builder_before_render_ajax_layout
		 */
		do_action( 'fl_builder_before_render_ajax_layout' );

		// Update the node ID in the post data?
		if ( $node_id ) {
			FLBuilderModel::update_post_data( 'node_id', $node_id );
		}

		// Register scripts needed for shortcodes and widgets.
		self::register_scripts();

		// Dequeue scripts and styles to only capture those that are needed.
		self::dequeue_scripts_and_styles();

		// Get the partial refresh data.
		$partial_refresh_data = self::get_partial_refresh_data();

		// Render the markup.
		$html = self::render_html();

		// Render scripts and styles.
		$scripts_styles = self::render_scripts_and_styles();

		// Render the assets.
		$assets = self::render_assets();

		/**
		 * After ajax layout rendered.
		 * @see fl_builder_after_render_ajax_layout
		 */
		do_action( 'fl_builder_after_render_ajax_layout' );

		/**
		 * Return filtered response.
		 * @see fl_builder_ajax_layout_response
		 */
		return apply_filters( 'fl_builder_ajax_layout_response', array(
			'partial'       => $partial_refresh_data['is_partial_refresh'],
			'nodeId'        => $partial_refresh_data['node_id'],
			'nodeType'      => $partial_refresh_data['node_type'],
			'moduleType'    => $partial_refresh_data['module_type'],
			'oldNodeId'     => $old_node_id,
			'html'          => $html,
			'scriptsStyles' => $scripts_styles,
			'css'           => $assets['css'],
			'js'            => $assets['js'],
		) );
	}

	/**
	 * Renders the layout data for a new row.
	 *
	 * @since 1.7
	 * @param string $cols The type of column layout to use.
	 * @param int $position The position of the new row in the layout.
	 * @param string $module Optional. The node ID of an existing module to move to this row.
	 * @return array
	 */
	static public function render_new_row( $cols = '1-col', $position = false, $module = null ) {
		// Add the row.
		$row = FLBuilderModel::add_row( $cols, $position, $module );

		/**
		 * Render the row.
		 * @see fl_builder_before_render_ajax_layout_html
		 */
		do_action( 'fl_builder_before_render_ajax_layout_html' );
		ob_start();
		FLBuilder::render_row( $row );
		$html = ob_get_clean();

		/**
		 * After rendering row.
		 * @see fl_builder_after_render_ajax_layout_html
		 */
		do_action( 'fl_builder_after_render_ajax_layout_html' );

		/**
		 * New Nodes
		 *
		 * We need whole objects for any newly-created nodes.
		*/
		$new_nodes               = FLBuilderModel::get_nested_nodes( $row->node );
		$new_nodes[ $row->node ] = $row;

		/**
		 * Get Siblings who's positions have changed.
		 * Only need node fragments for this.
		 */
		$siblings      = FLBuilderModel::get_nodes( 'row' );
		$updated_nodes = array();
		foreach ( $siblings as $sibling ) {
			if ( is_object( $sibling ) && isset( $sibling->node ) && $sibling->node !== $row->node ) {

				// Sibling position should be the only thing that has changed
				$updated_nodes[ $sibling->node ]           = new StdClass();
				$updated_nodes[ $sibling->node ]->position = $sibling->position;
			}
		}

		// Return the response.
		return array(
			'partial'      => true,
			'nodeType'     => $row->type,
			'html'         => $html,
			'js'           => 'FLBuilder._renderLayoutComplete();',
			'newNodes'     => $new_nodes,
			'updatedNodes' => $updated_nodes,
		);
	}

	/**
	 * Renders the layout data for a new row template.
	 *
	 * @since 2.2
	 * @param int $position The position of the new row in the layout.
	 * @param string $template_id The ID of a row template to render.
	 * @param string $template_type The type of template. Either "user" or "core".
	 * @return array
	 */
	static public function render_new_row_template( $position, $template_id, $template_type = 'user' ) {
		if ( class_exists( 'FLBuilderTemplatesOverride' ) && FLBuilderTemplatesOverride::show_rows() && FLBuilderTemplatesOverride::get_source_site_id() ) {
			$row = FLBuilderModel::apply_node_template( $template_id, null, $position );
		} elseif ( 'core' == $template_type ) {
			$template = FLBuilderModel::get_template( $template_id, 'row' );
			$row      = FLBuilderModel::apply_node_template( $template_id, null, $position, $template );
		} else {
			$row = FLBuilderModel::apply_node_template( $template_id, null, $position );
		}

		/**
		 * New Nodes
		 *
		 * We need whole objects for any newly-created nodes.
		*/
		$new_nodes               = FLBuilderModel::get_nested_nodes( $row->node );
		$new_nodes[ $row->node ] = $row;

		/**
		 * Get Siblings who's positions have changed.
		 * Only need node fragments for this.
		 */
		$siblings      = FLBuilderModel::get_nodes( 'row' );
		$updated_nodes = array();
		foreach ( $siblings as $sibling ) {
			if ( is_object( $sibling ) && isset( $sibling->node ) && $sibling->node !== $row->node ) {

				// Sibling position should be the only thing that has changed
				$updated_nodes[ $sibling->node ]           = new StdClass();
				$updated_nodes[ $sibling->node ]->position = $sibling->position;
			}
		}

		return array(
			'layout'       => self::render( $row->node ),
			'config'       => FLBuilderUISettingsForms::get_node_js_config(),
			'newNodes'     => $new_nodes,
			'updatedNodes' => $updated_nodes,
		);
	}

	/**
	 * Renders the layout data for a copied row.
	 *
	 * @since 1.7
	 * @param string $node_id The ID of a row to copy.
	 * @param object $settings These settings will be used for the copy if present.
	 * @param string $settings_id The ID of the node who's settings were passed.
	 * @return array
	 */
	static public function copy_row( $node_id, $settings = null, $settings_id = null ) {
		$row      = FLBuilderModel::copy_row( $node_id, $settings, $settings_id );
		$response = self::render( $row->node );

		/**
		 * New Nodes
		 *
		 * We need whole objects for any newly-created nodes.
		*/
		$new_nodes               = FLBuilderModel::get_nested_nodes( $row->node );
		$new_nodes[ $row->node ] = $row;

		/**
		 * Get Siblings who's positions have changed.
		 * Only need node fragments for this.
		 */
		$siblings      = FLBuilderModel::get_nodes( 'row' );
		$updated_nodes = array();
		foreach ( $siblings as $sibling ) {
			if ( is_object( $sibling ) && isset( $sibling->node ) && $sibling->node !== $row->node ) {

				// Sibling position should be the only thing that has changed
				$updated_nodes[ $sibling->node ]           = new StdClass();
				$updated_nodes[ $sibling->node ]->position = $sibling->position;
			}
		}

		$affected_nodes = array(
			'newNodes'     => $new_nodes,
			'updatedNodes' => $updated_nodes,
		);

		return array_merge( $response, $affected_nodes );
	}

	/**
	 * Renders the layout data for a new column group.
	 *
	 * @since 1.7
	 * @param string $node_id The node ID of a row to add the new group to.
	 * @param string $cols The type of column layout to use.
	 * @param int $position The position of the new column group in the row.
	 * @param string $module Optional. The node ID of an existing module to move to this group.
	 * @return array
	 */
	static public function render_new_column_group( $node_id, $cols = '1-col', $position = false, $module = null ) {
		// Add the group.
		$group = FLBuilderModel::add_col_group( $node_id, $cols, $position, $module );

		/**
		 * Render the group.
		 * @see fl_builder_before_render_ajax_layout_html
		 */
		do_action( 'fl_builder_before_render_ajax_layout_html' );
		ob_start();
		FLBuilder::render_column_group( $group );
		$html = ob_get_clean();

		/**
		 * After rendering group.
		 * @see fl_builder_after_render_ajax_layout_html
		 */
		do_action( 'fl_builder_after_render_ajax_layout_html' );

		/**
		 * New Nodes
		 *
		 * We need whole objects for any newly-created nodes.
		*/
		$new_nodes                 = FLBuilderModel::get_nested_nodes( $group->node );
		$new_nodes[ $group->node ] = $group;

		/**
		 * Get Siblings who's positions have changed.
		 * Only need node fragments for this.
		 */
		$siblings      = FLBuilderModel::get_nodes( 'column-group', $group->parent );
		$updated_nodes = array();
		foreach ( $siblings as $sibling ) {
			if ( is_object( $sibling ) && isset( $sibling->node ) && $sibling->node !== $group->node ) {

				// Sibling position should be the only thing that has changed
				$updated_nodes[ $sibling->node ]           = new StdClass();
				$updated_nodes[ $sibling->node ]->position = $sibling->position;
			}
		}

		// Return the response.
		return array(
			'partial'      => true,
			'nodeType'     => $group->type,
			'html'         => $html,
			'js'           => 'FLBuilder._renderLayoutComplete();',
			'newNodes'     => $new_nodes,
			'updatedNodes' => $updated_nodes,
		);
	}

	/**
	 * Renders the layout data for a new column or columns.
	 *
	 * @since 1.7
	 * @param string $node_id Node ID of the column to insert before or after.
	 * @param string $insert Either before or after.
	 * @param string $type The type of column(s) to insert.
	 * @param boolean $nested Whether these columns are nested or not.
	 * @param string $module Optional. The node ID of an existing module to move to this group.
	 * @return array
	 */
	static public function render_new_columns( $node_id, $insert, $type, $nested, $module = null ) {
		// Add the column(s).
		$group    = FLBuilderModel::add_cols( $node_id, $insert, $type, $nested, $module );
		$response = self::render( $group->node );

		/**
		 * Ideally we'd only ship full nodes for the newly-created columns and only position info for the siblings
		 * but its pretty tough to determine the new nodes from just the $group here.
		 */
		$new_nodes = FLBuilderModel::get_nested_nodes( $group->node );

		$affected_nodes = array(
			'newNodes' => $new_nodes,
		);

		// Return the response.
		return array_merge( $response, $affected_nodes );
	}

	/**
	 * Renders a new column template.
	 *
	 * @since 2.1
	 * @param string $template_id The ID of a column template to render.
	 * @param string $parent_id A column node ID.
	 * @param int $position The new column position.
	 * @param string $template_type The type of template. Either "user" or "core".
	 * @return array
	 */
	static public function render_new_col_template( $template_id, $parent_id = null, $position = false, $template_type = 'user' ) {
		if ( 'core' == $template_type ) {
			$template = FLBuilderModel::get_template( $template_id, 'column' );
			$column   = FLBuilderModel::apply_node_template( $template_id, $parent_id, $position, $template );
		} else {
			$column = FLBuilderModel::apply_node_template( $template_id, $parent_id, $position );
		}

		// Get the new column parent.
		$parent = ! $parent_id ? null : FLBuilderModel::get_node( $parent_id );

		// Get the root node to render.
		if ( ! $parent ) {
			$row       = FLBuilderModel::get_col_parent( 'row', $column );
			$render_id = $row->node;
			$root      = $row;
		} elseif ( 'row' == $parent->type ) {
			$group     = FLBuilderModel::get_col_parent( 'column-group', $column );
			$render_id = $group->node;
			$root      = $group;
		} elseif ( 'column-group' == $parent->type ) {
			$render_id = $parent->node;
			$root      = $column;
		} else {
			$render_id = $column->node;
			$root      = $column;
		}

		/**
		 * New Nodes
		 *
		 * We need whole objects for any newly-created nodes.
		*/
		$new_nodes                = FLBuilderModel::get_nested_nodes( $root->node );
		$new_nodes[ $root->node ] = $root;

		/**
		 * Get Siblings who's positions have changed.
		 * Only need node fragments for this.
		 */
		$siblings      = FLBuilderModel::get_nodes( $root->type, $root->parent );
		$updated_nodes = array();
		foreach ( $siblings as $sibling ) {
			if ( is_object( $sibling ) && isset( $sibling->node ) && $sibling->node !== $root->node ) {

				// Sibling position should be the only thing that has changed
				$updated_nodes[ $sibling->node ]           = new StdClass();
				$updated_nodes[ $sibling->node ]->position = $sibling->position;
			}
		}

		// Return the response.
		return array(
			'layout'       => self::render( $render_id ),
			'config'       => FLBuilderUISettingsForms::get_node_js_config(),
			'newNodes'     => $new_nodes,
			'updatedNodes' => $updated_nodes,
		);
	}

	/**
	 * Renders the layout data for a copied column.
	 *
	 * @since 2.0
	 * @param string $node_id The ID of a column to copy.
	 * @param object $settings These settings will be used for the copy if present.
	 * @param string $settings_id The ID of the node who's settings were passed.
	 * @return array
	 */
	static public function copy_col( $node_id, $settings = null, $settings_id = null ) {
		$col      = FLBuilderModel::copy_col( $node_id, $settings, $settings_id );
		$response = self::render( $col->node );

		/**
		 * New Nodes
		 *
		 * We need whole objects for any newly-created nodes.
		*/
		$new_nodes               = FLBuilderModel::get_nested_nodes( $col->node );
		$new_nodes[ $col->node ] = $col;

		/**
		 * Get Siblings who's positions and sizes have changed.
		 * Only need node fragments for this.
		 */
		$siblings      = FLBuilderModel::get_nodes( 'column', $col->parent );
		$updated_nodes = array();
		foreach ( $siblings as $sibling ) {
			if ( is_object( $sibling ) && isset( $sibling->node ) && $sibling->node !== $col->node ) {

				// Sibling position and size should be the only things that have changed
				$updated_nodes[ $sibling->node ]           = new StdClass();
				$updated_nodes[ $sibling->node ]->position = $sibling->position;
				$updated_nodes[ $sibling->node ]->settings = array(
					'size' => $sibling->settings->size,
				);
			}
		}

		$affected_nodes = array(
			'newNodes'     => $new_nodes,
			'updatedNodes' => $updated_nodes,
		);

		return array_merge( $response, $affected_nodes );
	}

	/**
	 * Renders the layout data for a new module.
	 *
	 * @since 1.7
	 * @param string $parent_id A column node ID.
	 * @param int $position The new module position.
	 * @param string $type The type of module.
	 * @param string $alias Module alias slug if this module is an alias.
	 * @param string $template_id The ID of a module template to render.
	 * @param string $template_type The type of template. Either "user" or "core".
	 * @return array
	 */
	static public function render_new_module( $parent_id, $position = false, $type = null, $alias = null, $template_id = null, $template_type = 'user' ) {
		// Add a module template?
		if ( null !== $template_id ) {

			if ( 'core' == $template_type ) {
				$template = FLBuilderModel::get_template( $template_id, 'module' );
				$module   = FLBuilderModel::apply_node_template( $template_id, $parent_id, $position, $template );
			} else {
				$module = FLBuilderModel::apply_node_template( $template_id, $parent_id, $position );
			}
		} else {
			$defaults = FLBuilderModel::get_module_alias_settings( $alias );
			$module   = FLBuilderModel::add_default_module( $parent_id, $type, $position, $defaults );
		}

		// Maybe render the module's parent for a partial refresh?
		if ( $module->partial_refresh ) {

			// Get the new module parent.
			$parent = ! $parent_id ? null : FLBuilderModel::get_node( $parent_id );

			// Get the node to render.
			if ( ! $parent ) {
				$row       = FLBuilderModel::get_module_parent( 'row', $module );
				$render_id = $row->node;
			} elseif ( 'row' == $parent->type ) {
				$group     = FLBuilderModel::get_module_parent( 'column-group', $module );
				$render_id = $group->node;
			} elseif ( 'column-group' == $parent->type ) {
				$render_id = $parent->node;
			} else {
				$render_id = $module->node;
			}
		} else {
			$render_id = null;
		}

		// Get node data for redux store
		if ( $module->partial_refresh ) {
			if ( ! $parent ) {
				$scope    = 'new-row';
				$root     = FLBuilderModel::get_module_parent( 'row', $module );
				$siblings = FLBuilderModel::get_nodes( 'row' );
			} elseif ( 'row' == $parent->type ) {
				$scope    = 'new-column-group';
				$root     = FLBuilderModel::get_module_parent( 'column-group', $module );
				$siblings = FLBuilderModel::get_nodes( 'column-group', $root->parent );
			} elseif ( 'column-group' == $parent->type ) {
				$scope    = 'new-column';
				$root     = FLBuilderModel::get_module_parent( 'column', $module );
				$siblings = FLBuilderModel::get_nodes( 'column', $root->parent );
			} else {
				$scope    = 'new-module';
				$root     = $module;
				$siblings = FLBuilderModel::get_nodes( 'module', $root->parent );
			}

			/**
			 * New Nodes
			 *
			 * We need whole nodes for any newly-created nodes.
			 * Depending on where the module was dropped, $root is the top-most new node.
			*/
			$children  = FLBuilderModel::get_nested_nodes( $root->node );
			$new_nodes = array_merge( array( $root->node => $root ), $children );
			$new_nodes = FLBuilderModel::clean_layout_data( $new_nodes );

			/**
			 * Get Siblings who's positions have changed.
			 * Only need node fragments for this.
			 */
			$updated_nodes = array();
			foreach ( $siblings as $sibling ) {
				if ( is_object( $sibling ) && isset( $sibling->node ) && $sibling->node !== $root->node ) {

					// Sibling position should be the only thing that has changed
					$updated_nodes[ $sibling->node ]           = new StdClass();
					$updated_nodes[ $sibling->node ]->position = $sibling->position;
				}
			}
		} else {
			$new_nodes     = FLBuilderModel::get_layout_data();
			$updated_nodes = array();
		}

		return array(
			'type'         => $module->settings->type,
			'nodeId'       => $module->node,
			'parentId'     => $module->parent,
			'global'       => FLBuilderModel::is_node_global( $module ),
			'layout'       => self::render( $render_id ),
			'settings'     => $module->settings,
			'legacy'       => FLBuilderUISettingsForms::pre_render_legacy_module_settings( $module->settings->type, $module->settings ),
			'newNodes'     => $new_nodes,
			'updatedNodes' => $updated_nodes,
		);
	}

	/**
	 * Renders the layout data for a copied module.
	 *
	 * @since 1.7
	 * @param string $node_id The ID of a module to copy.
	 * @param object $settings These settings will be used for the copy if present.
	 * @return array
	 */
	static public function copy_module( $node_id, $settings = null ) {
		$module   = FLBuilderModel::copy_module( $node_id, $settings );
		$response = self::render( $module->node );

		/**
		 * New Nodes
		 *
		 * We need whole nodes for any newly-created nodes.
		 * Depending on where the module was dropped, $root is the top-most new node.
		*/
		$new_nodes = FLBuilderModel::clean_layout_data( array( $module->node => $module ) );

		/**
		 * Get Siblings who's positions have changed.
		 * Only need node fragments for this.
		 */
		$siblings      = FLBuilderModel::get_nodes( 'module', $module->parent );
		$siblings      = FLBuilderModel::clean_layout_data( $siblings );
		$updated_nodes = array();
		foreach ( $siblings as $sibling ) {
			if ( is_object( $sibling ) && isset( $sibling->node ) && $sibling->node !== $module->node ) {

				// Sibling position should be the only thing that has changed
				$updated_nodes[ $sibling->node ]           = new StdClass();
				$updated_nodes[ $sibling->node ]->position = $sibling->position;
			}
		}

		$affected_nodes = array(
			'newNodes'     => $new_nodes,
			'updatedNodes' => $updated_nodes,
		);

		return array_merge( $response, $affected_nodes );
	}

	/**
	 * Returns an array of partial refresh data.
	 *
	 * @since 1.7
	 * @access private
	 * @return array
	 */
	static private function get_partial_refresh_data() {
		// Get the data if it's not cached.
		if ( ! self::$partial_refresh_data ) {

			$post_data       = FLBuilderModel::get_post_data();
			$partial_refresh = false;
			$node_type       = null;
			$module_type     = null;

			// Check for partial refresh if we have a node ID.
			if ( isset( $post_data['node_id'] ) ) {

				// Get the node.
				$node_id   = $post_data['node_id'];
				$node      = FLBuilderModel::get_node( $post_data['node_id'] );
				$node_type = null;

				// Check a module for partial refresh.
				if ( $node && 'module' == $node->type ) {
					$node            = FLBuilderModel::get_module( $node_id );
					$node_type       = 'module';
					$module_type     = $node->settings->type;
					$partial_refresh = $node->partial_refresh;
				} elseif ( $node ) {
					$node_type       = $node->type;
					$partial_refresh = self::node_modules_support_partial_refresh( $node );
				}
			} else {
				$node_id   = null;
				$node      = null;
				$node_type = null;
			}

			// Cache the partial refresh data.
			self::$partial_refresh_data = array(
				'is_partial_refresh' => $partial_refresh,
				'node_id'            => $node_id,
				'node'               => $node,
				'node_type'          => $node_type,
				'module_type'        => $module_type,
			);
		}

		// Return the data.
		return self::$partial_refresh_data;
	}

	/**
	 * Checks to see if all modules in a node support partial refresh.
	 *
	 * @since 1.7
	 * @access private
	 * @param object $node The node to check.
	 * @return bool
	 */
	static private function node_modules_support_partial_refresh( $node ) {
		$nodes = FLBuilderModel::get_categorized_nodes();

		if ( 'row' == $node->type ) {

			$template_post_id = FLBuilderModel::is_node_global( $node );

			foreach ( $nodes['groups'] as $group ) {
				if ( $node->node == $group->parent || ( $template_post_id && $node->template_node_id == $group->parent ) ) {
					foreach ( $nodes['columns'] as $column ) {
						if ( $group->node == $column->parent ) {
							foreach ( $nodes['modules'] as $module ) {
								if ( $column->node == $module->parent ) {
									if ( ! $module->partial_refresh ) {
										return false;
									}
								}
							}
						}
					}
				}
			}
		} elseif ( 'column-group' == $node->type ) {
			foreach ( $nodes['columns'] as $column ) {
				if ( $node->node == $column->parent ) {
					foreach ( $nodes['modules'] as $module ) {
						if ( $column->node == $module->parent ) {
							if ( ! $module->partial_refresh ) {
								return false;
							}
						}
					}
				}
			}
		} elseif ( 'column' == $node->type ) {
			foreach ( $nodes['modules'] as $module ) {
				if ( $node->node == $module->parent ) {
					if ( ! $module->partial_refresh ) {
						return false;
					}
				}
			}
		}

		return true;
	}

	/**
	 * Renders the html for the layout or node.
	 *
	 * @since 1.7
	 * @access private
	 * @return string
	 */
	static private function render_html() {
		/**
		 * Before html for layout or node is rendered.
		 * @see fl_builder_before_render_ajax_layout_html
		 */
		do_action( 'fl_builder_before_render_ajax_layout_html' );

		// Get the partial refresh data.
		$partial_refresh_data = self::get_partial_refresh_data();

		// Start the output buffer.
		ob_start();

		// Render a node?
		if ( $partial_refresh_data['is_partial_refresh'] ) {

			switch ( $partial_refresh_data['node']->type ) {

				case 'row':
					FLBuilder::render_row( $partial_refresh_data['node'] );
					break;

				case 'column-group':
					FLBuilder::render_column_group( $partial_refresh_data['node'] );
					break;

				case 'column':
					FLBuilder::render_column( $partial_refresh_data['node'] );
					break;

				case 'module':
					FLBuilder::render_module( $partial_refresh_data['node'] );
					break;
			}
		} else {
			FLBuilder::render_nodes();
		}

		// Get the rendered HTML.
		$html = ob_get_clean();

		/**
		 * Use this filter to prevent the builder from rendering shortcodes.
		 * It is useful if you don't want shortcodes rendering while the builder UI is active.
		 * @see fl_builder_render_shortcodes
		 * @link https://docs.wpbeaverbuilder.com/beaver-builder/developer/tutorials-guides/common-beaver-builder-filter-examples
		 */
		if ( apply_filters( 'fl_builder_render_shortcodes', true ) ) {
			/**
			 * Used with fl_builder_render_shortcodes shortcode.
			 * @see fl_builder_before_render_shortcodes
			 */
			$html = apply_filters( 'fl_builder_before_render_shortcodes', $html );
			ob_start();
			echo do_shortcode( $html );
			$html = ob_get_clean();
		}
		/**
		 * After html for layout or node is rendered.
		 * @see fl_builder_after_render_ajax_layout_html
		 */
		do_action( 'fl_builder_after_render_ajax_layout_html' );

		// Return the rendered HTML.
		return $html;
	}

	/**
	 * Renders the assets for the layout or a node.
	 *
	 * @since 1.7
	 * @access private
	 * @return array
	 */
	static private function render_assets() {
		$partial_refresh_data = self::get_partial_refresh_data();
		$asset_info           = FLBuilderModel::get_asset_info();
		$asset_ver            = FLBuilderModel::get_asset_version();
		$enqueuemethod        = FLBuilderModel::get_asset_enqueue_method();
		$assets               = array(
			'js'  => '',
			'css' => '',
		);

		// Ensure global assets are rendered.
		FLBuilder::clear_enqueued_global_assets();

		// Render the JS.
		if ( $partial_refresh_data['is_partial_refresh'] ) {

			if ( ! class_exists( 'FLJSMin' ) ) {
				include FL_BUILDER_DIR . 'classes/class-fl-jsmin.php';
			}

			switch ( $partial_refresh_data['node']->type ) {

				case 'row':
					$assets['js']  = FLBuilder::render_row_js( $partial_refresh_data['node'] );
					$assets['js'] .= FLBuilder::render_row_modules_js( $partial_refresh_data['node'] );
					break;

				case 'column-group':
					$assets['js'] = FLBuilder::render_column_group_modules_js( $partial_refresh_data['node'] );
					break;

				case 'column':
					$assets['js'] = FLBuilder::render_column_modules_js( $partial_refresh_data['node'] );
					break;

				case 'module':
					$assets['js'] = FLBuilder::render_module_js( $partial_refresh_data['node'] );
					break;
			}

			$assets['js'] .= 'FLBuilder._renderLayoutComplete();';

			try {
				$min = FLJSMin::minify( $assets['js'] );
			} catch ( Exception $e ) {
			}

			if ( $min ) {
				$assets['js'] = $min;
			}
		} elseif ( 'inline' === $enqueuemethod ) {
			$assets['js'] = FLBuilder::render_js();
		} else {
			FLBuilder::render_js();
			$assets['js'] = $asset_info['js_url'] . '?ver=' . $asset_ver;
		}

		// Render the CSS.
		if ( 'inline' === $enqueuemethod ) {
			$assets['css'] = FLBuilder::render_css();
		} else {
			FLBuilder::render_css();
			$assets['css'] = $asset_info['css_url'] . '?ver=' . $asset_ver;
		}

		// Return the assets.
		return $assets;
	}

	/**
	 * Do the wp_enqueue_scripts action to register any scripts or
	 * styles that might need to be registered for shortcodes or widgets.
	 *
	 * @since 1.7
	 * @access private
	 * @return void
	 */
	static private function register_scripts() {
		// Running these isn't necessary and can cause performance issues.
		remove_action( 'wp_enqueue_scripts', 'FLBuilder::register_layout_styles_scripts' );
		remove_action( 'wp_enqueue_scripts', 'FLBuilder::enqueue_ui_styles_scripts' );
		remove_action( 'wp_enqueue_scripts', 'FLBuilder::enqueue_all_layouts_styles_scripts' );

		ob_start();
		do_action( 'wp_enqueue_scripts' );
		ob_end_clean();
	}

	/**
	 * Dequeue scripts and styles so we can capture only those
	 * enqueued by shortcodes or widgets.
	 *
	 * @since 1.7
	 * @access private
	 * @return void
	 */
	static private function dequeue_scripts_and_styles() {
		global $wp_scripts;
		global $wp_styles;

		if ( isset( $wp_scripts ) ) {
			$wp_scripts->queue = array();
		}
		if ( isset( $wp_styles ) ) {
			$wp_styles->queue = array();
		}

		remove_action( 'wp_print_styles', 'print_emoji_styles' );
	}

	/**
	 * Renders scripts and styles enqueued by shortcodes or widgets.
	 *
	 * @since 1.7
	 * @access private
	 * @return string
	 */
	static private function render_scripts_and_styles() {
		global $wp_scripts;
		global $wp_styles;

		$partial_refresh_data = self::get_partial_refresh_data();
		$modules              = array();
		$scripts_styles       = '';

		// Enqueue module font styles.
		if ( ! $partial_refresh_data['is_partial_refresh'] ) {
			$modules = FLBuilderModel::get_all_modules();
		} elseif ( 'module' !== $partial_refresh_data['node']->type ) {
			$nodes = FLBuilderModel::get_nested_nodes( $partial_refresh_data['node'] );
			foreach ( $nodes as $node ) {
				if ( 'module' === $node->type && isset( FLBuilderModel::$modules[ $node->settings->type ] ) ) {
					$node->form = FLBuilderModel::$modules[ $node->settings->type ]->form;
					$modules[]  = $node;
				}
			}
		} else {
			$modules = array( $partial_refresh_data['node'] );
		}

		foreach ( $modules as $module ) {
			FLBuilderFonts::add_fonts_for_module( $module );
		}

		FLBuilderFonts::enqueue_styles();
		FLBuilderFonts::enqueue_google_fonts();

		// Start the output buffer.
		ob_start();

		// Print scripts and styles.
		if ( isset( $wp_scripts ) ) {
			$wp_scripts->done[] = 'jquery';
			wp_print_scripts( $wp_scripts->queue );
		}
		if ( isset( $wp_styles ) ) {
			wp_print_styles( $wp_styles->queue );
		}

		// Return the scripts and styles markup.
		return ob_get_clean();
	}

	/**
	 * Get full layout data
	 *
	 * @since 2.5
	 * @access public
	 * @return array
	 */
	static public function get_layout() {
		return array(
			'nodes'       => FLBuilderModel::get_layout_data(),
			'attachments' => array(), // @TODO - where do attachments initially come from???
		);
	}

	static public function refresh_layout_cache() {
		if ( isset( $_POST['fl_builder_data'] ) && 'clear_cache_for_layout' === $_POST['fl_builder_data']['action'] ) {
			add_filter( 'fl_builder_model_is_builder_active', '__return_false' );
			FLBuilder::render_css( false );
			FLBuilder::render_js( false );
			FLBuilder::render_css( true );
			FLBuilder::render_js( true );
			wp_send_json_success();
		} else {
			wp_send_json_error();
		}
	}
}
