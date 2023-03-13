<?php

/**
 * Logic for the user templates admin list table.
 *
 * @since 1.10
 */
final class FLBuilderUserTemplatesAdminList {

	/**
	 * Initialize hooks.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function init() {
		/* Actions */
		add_action( 'plugins_loaded', __CLASS__ . '::redirect' );
		add_action( 'wp', __CLASS__ . '::page_heading' );
		add_action( 'pre_get_posts', __CLASS__ . '::pre_get_posts' );
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts' );
		add_action( 'manage_fl-builder-template_posts_custom_column', __CLASS__ . '::add_column_content', 10, 2 );

		/* Filters */
		add_filter( 'views_edit-fl-builder-template', __CLASS__ . '::modify_views' );
		add_filter( 'manage_fl-builder-template_posts_columns', __CLASS__ . '::add_column_headings' );
		add_filter( 'post_row_actions', __CLASS__ . '::row_actions' );
		add_action( 'restrict_manage_posts', __CLASS__ . '::restrict_listings' );
	}

	/**
	 * Enqueue scripts and styles for user templates.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function admin_enqueue_scripts() {
		global $pagenow;

		$screen  = get_current_screen();
		$slug    = 'fl-builder-user-templates-admin-';
		$url     = FL_BUILDER_USER_TEMPLATES_URL;
		$version = FL_BUILDER_VERSION;
		$js_url  = FL_BUILDER_URL . 'js/';

		if ( 'edit.php' == $pagenow && 'fl-builder-template' == $screen->post_type ) {

			wp_enqueue_style( $slug . 'list', $url . 'css/' . $slug . 'list.css', array(), $version );
			wp_enqueue_script( $slug . 'list', $url . 'js/' . $slug . 'list.js', array(), $version );
			wp_enqueue_script( 'clipboard', $js_url . 'clipboard.min.js', array(), $version );

			wp_localize_script( $slug . 'list', 'FLBuilderConfig', array(
				'userTemplateType' => isset( $_GET['fl-builder-template-type'] ) ? $_GET['fl-builder-template-type'] : 'layout',
				'addNewURL'        => admin_url( '/edit.php?post_type=fl-builder-template&page=fl-builder-add-new' ),
			) );
		}
	}

	/**
	 * Redirects the list table to show layout templates if no
	 * template type is set. We never want to show all templates
	 * (layouts, rows, modules) in a list table together.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function redirect() {
		global $pagenow;

		$post_type     = isset( $_GET['post_type'] ) ? $_GET['post_type'] : null;
		$template_type = isset( $_GET['fl-builder-template-type'] ) ? $_GET['fl-builder-template-type'] : null;
		$page          = isset( $_GET['page'] ) ? $_GET['page'] : null;

		if ( 'edit.php' == $pagenow && 'fl-builder-template' == $post_type && ! $template_type && ! $page ) {

			$url = admin_url( '/edit.php?post_type=fl-builder-template&fl-builder-template-type=layout' );

			wp_redirect( $url );

			exit;
		}
	}

	/**
	 * Overrides the list table page headings for saved rows, cols and modules.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function page_heading() {
		global $pagenow;
		global $wp_post_types;

		if ( ! is_admin() ) {
			return;
		}

		$screen = get_current_screen();

		if ( 'edit.php' == $pagenow && isset( $_GET['fl-builder-template-type'] ) ) {

			if ( 'row' == $_GET['fl-builder-template-type'] ) {
				$wp_post_types['fl-builder-template']->labels->name = __( 'Saved Rows', 'fl-builder' );
			} elseif ( 'column' == $_GET['fl-builder-template-type'] ) {
				$wp_post_types['fl-builder-template']->labels->name = __( 'Saved Columns', 'fl-builder' );
			} elseif ( 'module' == $_GET['fl-builder-template-type'] ) {
				$wp_post_types['fl-builder-template']->labels->name = __( 'Saved Modules', 'fl-builder' );
			}
		}
	}

	/**
	 * Orders templates by title.
	 *
	 * @since 2.0.6
	 * @param object $query
	 * @return void
	 */
	static public function pre_get_posts( $query ) {
		if ( ! isset( $_GET['post_type'] ) || 'fl-builder-template' != $_GET['post_type'] ) {
			return;
		} elseif ( $query->is_main_query() && ! $query->get( 'orderby' ) ) {
			$query->set( 'orderby', 'title' );
			$query->set( 'order', 'ASC' );
		}
	}

	/**
	 * Modifies the views links to remove the counts since they
	 * are not correct for our list table approach.
	 *
	 * @since 1.10
	 * @param array $views
	 * @return array
	 */
	static public function modify_views( $views ) {
		$slug = 'fl-builder-template';
		$type = isset( $_GET['fl-builder-template-type'] ) ? $_GET['fl-builder-template-type'] : 'layout';

		foreach ( $views as $key => $view ) {

			if ( strstr( $view, $slug ) ) {
				$view          = str_replace( $slug, $slug . '&#038;fl-builder-template-type=' . $type, $view );
				$view          = preg_replace( '/<span(.*)span>/', '', $view );
				$views[ $key ] = $view;
			}
		}

		return $views;
	}

	/**
	 * Adds the custom list table column headings.
	 *
	 * @since 1.10
	 * @param array $columns
	 * @return array
	 */
	static public function add_column_headings( $columns ) {
		if ( ! isset( $_GET['fl-builder-template-type'] ) ) {
			return;
		}
		if ( in_array( $_GET['fl-builder-template-type'], array( 'row', 'column', 'module' ) ) ) {
			$columns['fl_global'] = __( 'Global', 'fl-builder' );
			$columns['code']      = __( 'ShortCode', 'fl-builder' );
		}

		if ( 'layout' === $_GET['fl-builder-template-type'] ) {
			$columns['code'] = __( 'ShortCode', 'fl-builder' );
		}

		$columns['taxonomy-fl-builder-template-category'] = __( 'Categories', 'fl-builder' );

		if ( isset( $_GET['post_status'] ) && 'trash' === $_GET['post_status'] ) {
			unset( $columns['code'] );
		}

		unset( $columns['date'] );

		return $columns;
	}

	/**
	 * Adds the custom list table column content.
	 *
	 * @since 1.10
	 * @param array $columns
	 * @return array
	 */
	static public function add_column_content( $column, $post_id ) {

		if ( 'code' === $column ) {
			$shortcode = sprintf( '[fl_builder_insert_layout id=%s]', $post_id );
			printf( '<pre class="shortcode" data-clipboard-text="%s">%s</pre>', $shortcode, $shortcode );
			return;
		}
		if ( 'fl_global' != $column ) {
			return;
		}

		if ( FLBuilderModel::is_post_global_node_template( $post_id ) ) {
			echo '<i class="dashicons dashicons-yes"></i>';
		} else {
			echo '&#8212;';
		}
	}

	/**
	 * Removes the quick edit link as we don't need it.
	 *
	 * @since 1.10
	 * @param array $actions
	 * @return array
	 */
	static public function row_actions( $actions = array() ) {
		if ( isset( $_GET['post_type'] ) && 'fl-builder-template' == $_GET['post_type'] ) {
			unset( $actions['inline hide-if-no-js'] );
		}

		return $actions;
	}

	/**
	 * Add filter dropdown for Categories
	 *
	 * @since 1.10.8
	 */
	static public function restrict_listings() {
		global $typenow;
		if ( 'fl-builder-template' == $typenow ) {
			$taxonomy = 'fl-builder-template-category';
			$tax      = get_taxonomy( $taxonomy );
			$term     = $_GET['fl-builder-template-type'];
			wp_dropdown_categories(
				array(
					'show_option_all' => __( 'Show All Categories', 'fl-builder' ),
					'taxonomy'        => $taxonomy,
					'value_field'     => 'slug',
					'orderby'         => 'name',
					'selected'        => $term,
					'name'            => $taxonomy,
					'depth'           => 1,
					'show_count'      => false,
					'hide_empty'      => false,
				)
			);
		}
	}

}

FLBuilderUserTemplatesAdminList::init();
