<?php

/**
 * Logic for the user templates post type.
 *
 * @since 1.10
 */
final class FLBuilderUserTemplatesPostType {

	/**
	 * Initialize hooks.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function init() {
		/* Actions */
		add_action( 'init', __CLASS__ . '::register' );
		add_action( 'init', __CLASS__ . '::register_taxonomies' );
		add_action( 'init', __CLASS__ . '::register_pointer' );
	}

	/**
	 * Registers the custom post type for user templates.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function register() {
		$admin_access    = FLBuilderUserAccess::current_user_can( 'builder_admin' );
		$can_edit        = FLBuilderUserAccess::current_user_can( 'unrestricted_editing' );
		$can_edit_global = FLBuilderUserAccess::current_user_can( 'global_node_editing' );
		$menu_name       = FLBuilderModel::get_branding();

		// Use "Builder" for the menu name if we have custom branding.
		if ( __( 'Beaver Builder', 'fl-builder' ) !== $menu_name ) {
			$menu_name = __( 'Builder', 'fl-builder' );
		}

		$args = apply_filters( 'fl_builder_register_template_post_type_args', array(
			'public'              => $admin_access && $can_edit ? true : false,
			'labels'              => array(
				'name'               => _x( 'Templates', 'Custom post type label.', 'fl-builder' ),
				'singular_name'      => _x( 'Template', 'Custom post type label.', 'fl-builder' ),
				'menu_name'          => $menu_name,
				'name_admin_bar'     => _x( 'Template', 'Custom post type label.', 'fl-builder' ),
				'add_new'            => _x( 'Add New', 'Custom post type label.', 'fl-builder' ),
				'add_new_item'       => _x( 'Add New', 'Custom post type label.', 'fl-builder' ),
				'new_item'           => _x( 'New', 'Custom post type label.', 'fl-builder' ),
				'edit_item'          => _x( 'Edit', 'Custom post type label.', 'fl-builder' ),
				'view_item'          => _x( 'View', 'Custom post type label.', 'fl-builder' ),
				'all_items'          => _x( 'All', 'Custom post type label.', 'fl-builder' ),
				'search_items'       => _x( 'Search', 'Custom post type label.', 'fl-builder' ),
				'parent_item_colon'  => _x( 'Parent:', 'Custom post type label.', 'fl-builder' ),
				'not_found'          => _x( 'Nothing found.', 'Custom post type label.', 'fl-builder' ),
				'not_found_in_trash' => _x( 'Nothing found in Trash.', 'Custom post type label.', 'fl-builder' ),
			),
			'supports'            => array(
				'title',
				'revisions',
				'page-attributes',
				'thumbnail',
			),
			'taxonomies'          => array(
				'fl-builder-template-category',
			),
			'menu_icon'           => 'dashicons-welcome-widgets-menus',
			'menu_position'       => 64,
			'publicly_queryable'  => $can_edit || $can_edit_global,
			'exclude_from_search' => true,
			'show_in_rest'        => true,
		) );

		register_post_type( 'fl-builder-template', $args );
	}

	/**
	 * Registers the taxonomies for user templates.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function register_taxonomies() {
		/**
		 * Register the template category taxonomy.
		 * @see fl_builder_register_template_category_args
		 */
		$args = apply_filters( 'fl_builder_register_template_category_args', array(
			'labels'            => array(
				/* translators: %s: branded builder name */
				'name'              => sprintf( _x( '%s Categories', 'Custom taxonomy label.', 'fl-builder' ), FLBuilderModel::get_branding() ),
				'singular_name'     => _x( 'Category', 'Custom taxonomy label.', 'fl-builder' ),
				'search_items'      => _x( 'Search Categories', 'Custom taxonomy label.', 'fl-builder' ),
				'all_items'         => _x( 'All Categories', 'Custom taxonomy label.', 'fl-builder' ),
				'parent_item'       => _x( 'Parent Category', 'Custom taxonomy label.', 'fl-builder' ),
				'parent_item_colon' => _x( 'Parent Category:', 'Custom taxonomy label.', 'fl-builder' ),
				'edit_item'         => _x( 'Edit Category', 'Custom taxonomy label.', 'fl-builder' ),
				'update_item'       => _x( 'Update Category', 'Custom taxonomy label.', 'fl-builder' ),
				'add_new_item'      => _x( 'Add New Category', 'Custom taxonomy label.', 'fl-builder' ),
				'new_item_name'     => _x( 'New Category Name', 'Custom taxonomy label.', 'fl-builder' ),
				'menu_name'         => _x( 'Categories', 'Custom taxonomy label.', 'fl-builder' ),
			),
			'hierarchical'      => true,
			'public'            => true,
			'show_admin_column' => true,
		) );

		register_taxonomy( 'fl-builder-template-category', array( 'fl-builder-template', 'fl-theme-layout' ), $args );

		/**
		 * Register the template type taxonomy.
		 * @see fl_builder_register_template_type_args
		 */
		$args = apply_filters( 'fl_builder_register_template_type_args', array(
			'label'             => _x( 'Type', 'Custom taxonomy label.', 'fl-builder' ),
			'hierarchical'      => false,
			'public'            => false,
			'show_admin_column' => false,
		) );

		register_taxonomy( 'fl-builder-template-type', array( 'fl-builder-template' ), $args );
	}

	/**
	 * Registers an admin pointer pointing out the changes
	 * to the templates admin in 1.10.
	 *
	 * @since 1.10.3
	 * @return void
	 */
	static public function register_pointer() {
		$admin_access = FLBuilderUserAccess::current_user_can( 'builder_admin' );
		$update_info  = get_site_option( '_fl_builder_update_info', false );

		if ( ! $admin_access || ! is_array( $update_info ) ) {
			return;
		}
		if ( ! version_compare( $update_info['from'], '1.10.3', '<' ) ) {
			return;
		}

		FLBuilderAdminPointers::register_pointer( array(
			'id'      => 'fl_builder_templates_menu_upgrade',
			'target'  => 'li.menu-icon-fl-builder-template',
			'cap'     => 'edit_posts',
			'options' => array(
				'content'  => wp_kses_post( sprintf(
					'<h3>%s</h3><p style="margin:13px 0;">%s</p>',
					__( 'Builder Admin Menu', 'fl-builder' ),
					__( 'The Templates admin menu has been renamed to Builder and split into useful sections for working with templates, rows and modules.', 'fl-builder' )
				) ),
				'position' => array(
					'edge'  => 'left',
					'align' => 'left',
				),
			),
		) );
	}
}

FLBuilderUserTemplatesPostType::init();
