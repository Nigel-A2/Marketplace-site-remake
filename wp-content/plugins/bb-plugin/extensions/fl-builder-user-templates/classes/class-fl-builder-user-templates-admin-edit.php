<?php

/**
 * Logic for the user templates admin edit screen.
 *
 * @since 1.10
 */
final class FLBuilderUserTemplatesAdminEdit {

	/**
	 * Initialize hooks.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function init() {
		/* Actions */
		add_action( 'plugins_loaded', __CLASS__ . '::redirect' );
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts' );
		add_action( 'edit_form_after_title', __CLASS__ . '::render_global_node_message' );
		add_action( 'add_meta_boxes', __CLASS__ . '::add_meta_boxes', 1 );

		/* Filters */
		add_filter( 'fl_builder_render_admin_edit_ui', __CLASS__ . '::remove_builder_edit_ui' );
		add_filter( 'redirect_post_location', __CLASS__ . '::redirect_template_location' );
	}

	/**
	 * Redirect template to the Page Builder editor when the 'Launch Beaver Builder' button is clicked.
	 *
	 * @since 2.4.3
	 * @param string $location
	 * @return string The location to redirect this template on save.
	 */
	static function redirect_template_location( $location ) {
		if ( ! empty( $_POST['fl-builder-launch'] ) && ( 'true' === $_POST['fl-builder-launch'] ) ) {
			$location = FLBuilderModel::get_edit_url( absint( $_POST['fl-builder-template-redirect'] ) );
		}
		return $location;
	}

	/**
	 * Redirects the post-new.php page to our custom add new page.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function redirect() {
		global $pagenow;

		$post_type = isset( $_GET['post_type'] ) ? $_GET['post_type'] : null;
		$args      = $_GET;

		if ( 'post-new.php' == $pagenow && 'fl-builder-template' == $post_type ) {

			$args['page'] = 'fl-builder-add-new';
			wp_redirect( admin_url( '/edit.php?' . http_build_query( $args ) ) );
			exit;
		}
	}

	/**
	 * Enqueue scripts and styles for user templates.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function admin_enqueue_scripts() {
		global $pagenow;
		global $post;

		$screen  = get_current_screen();
		$slug    = 'fl-builder-user-templates-admin-';
		$url     = FL_BUILDER_USER_TEMPLATES_URL;
		$version = FL_BUILDER_VERSION;

		if ( 'post.php' == $pagenow && 'fl-builder-template' == $screen->post_type ) {

			wp_enqueue_style( $slug . 'edit', $url . 'css/' . $slug . 'edit.css', array(), $version );
			wp_enqueue_script( $slug . 'edit', $url . 'js/' . $slug . 'edit.js', array(), $version );

			wp_localize_script( $slug . 'edit', 'FLBuilderConfig', array(
				'pageTitle'        => self::get_page_title(),
				'userTemplateType' => FLBuilderModel::get_user_template_type( $post->ID ),
				'addNewURL'        => admin_url( '/edit.php?post_type=fl-builder-template&page=fl-builder-add-new' ),
			) );
		}
	}

	/**
	 * Returns the page title for the edit screen.
	 *
	 * @since 1.10
	 * @return string
	 */
	static public function get_page_title() {
		global $post;

		$type   = FLBuilderModel::get_user_template_type( $post->ID );
		$action = __( 'Edit', 'fl-builder' );

		if ( 'row' == $type ) {
			/* translators: %s: add/edit or view */
			$label = sprintf( _x( '%s Saved Row', '%s is an action like Add, Edit or View.', 'fl-builder' ), $action );
		} elseif ( 'module' == $type ) {
			/* translators: %s: add/edit or view */
			$label = sprintf( _x( '%s Saved Module', '%s is an action like Add, Edit or View.', 'fl-builder' ), $action );
		} else {
			/* translators: %s: add/edit or view */
			$label = sprintf( _x( '%s Template', '%s is an action like Add, Edit or View.', 'fl-builder' ), $action );
		}

		return $label;
	}

	/**
	 * Renders a notice div for global nodes.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_global_node_message() {
		global $pagenow;
		global $post;

		$screen = get_current_screen();

		if ( 'post.php' == $pagenow && 'fl-builder-template' == $screen->post_type ) {

			if ( FLBuilderModel::is_post_global_node_template( $post->ID ) ) {

				$type = FLBuilderModel::get_user_template_type( $post->ID );

				include FL_BUILDER_USER_TEMPLATES_DIR . 'includes/admin-edit-global-message.php';
			}
		}
	}

	/**
	 * Callback for adding meta boxes to the user template
	 * post edit screen.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function add_meta_boxes() {
		add_meta_box(
			'fl-builder-user-template-buttons',
			FLBuilderModel::get_branding(),
			__CLASS__ . '::render_buttons_meta_box',
			'fl-builder-template',
			'normal',
			'high'
		);
	}

	/**
	 * Adds custom buttons to the edit screen for launching the builder
	 * or viewing a template.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function render_buttons_meta_box() {
		global $post;

		$type = FLBuilderModel::get_user_template_type( $post->ID );
		/* translators: %s: branded builder name */
		$edit = sprintf( _x( 'Launch %s', '%s stands for custom branded "Page Builder" name.', 'fl-builder' ), FLBuilderModel::get_branding() );
		$view = __( 'View', 'fl-builder' );

		if ( 'fl-builder-template' == $post->post_type ) {

			if ( 'row' == $type ) {
				/* translators: %s: add/edit or view */
				$view = sprintf( _x( '%s Saved Row', '%s is an action like Add, Edit or View.', 'fl-builder' ), $view );
			} elseif ( 'module' == $type ) {
				/* translators: %s: add/edit or view */
				$view = sprintf( _x( '%s Saved Module', '%s is an action like Add, Edit or View.', 'fl-builder' ), $view );
			} else {
				/* translators: %s: add/edit or view */
				$view = sprintf( _x( '%s Template', '%s is an action like Add, Edit or View.', 'fl-builder' ), $view );
			}
		} else {
			$object = get_post_type_object( $post->post_type );
			/* translators: 1: add/edit or view: 2: post type label */
			$view = sprintf( _x( '%1$s %2$s', '%1$s is an action like Add, Edit or View. %2$s is post type label.', 'fl-builder' ), $view, $object->labels->singular_name );

		}

		include FL_BUILDER_USER_TEMPLATES_DIR . 'includes/admin-edit-buttons.php';
	}

	/**
	 * Prevents the standard builder admin edit UI from rendering.
	 *
	 * @since 1.10
	 * @param bool $render_ui
	 * @return bool
	 */
	static public function remove_builder_edit_ui( $render_ui ) {
		return 'fl-builder-template' == FLBuilderAdminPosts::get_post_type() ? false : $render_ui;
	}
}

FLBuilderUserTemplatesAdminEdit::init();
