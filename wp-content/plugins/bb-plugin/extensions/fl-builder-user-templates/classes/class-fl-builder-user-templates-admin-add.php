<?php

/**
 * Logic for the user templates admin add new form.
 *
 * @since 1.10
 */
final class FLBuilderUserTemplatesAdminAdd {

	/**
	 * Initialize hooks.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function init() {
		/* Actions */
		add_action( 'init', __CLASS__ . '::process_form', 11 );
		add_action( 'admin_enqueue_scripts', __CLASS__ . '::admin_enqueue_scripts' );
	}

	/**
	 * Enqueue scripts and styles for user templates.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function admin_enqueue_scripts() {
		$slug    = 'fl-builder-user-templates-admin-';
		$url     = FL_BUILDER_USER_TEMPLATES_URL;
		$version = FL_BUILDER_VERSION;
		$page    = isset( $_GET['page'] ) ? $_GET['page'] : null;
		$action  = __( 'Add', 'fl-builder' );

		if ( 'fl-builder-add-new' == $page ) {
			wp_enqueue_style( 'fl-jquery-tiptip', FL_BUILDER_URL . 'css/jquery.tiptip.css', array(), $version );
			wp_enqueue_script( 'fl-jquery-tiptip', FL_BUILDER_URL . 'js/jquery.tiptip.min.js', array( 'jquery' ), $version, true );
			wp_enqueue_script( 'jquery-validate', FL_BUILDER_URL . 'js/jquery.validate.min.js', array(), $version, true );
			wp_enqueue_style( $slug . 'add', $url . 'css/' . $slug . 'add.css', array(), $version );
			wp_enqueue_script( $slug . 'add', $url . 'js/' . $slug . 'add.js', array(), $version, true );

			wp_localize_script( $slug . 'add', 'FLBuilderConfig', apply_filters( 'fl_builder_user_templates_add_new_config', array(
				'strings' => array(
					'addButton' => array(
						'add'    => _x( 'Add', 'Generic add button label for adding new content.', 'fl-builder' ),
						/* translators: %s: add/edit or view */
						'layout' => sprintf( _x( '%s Saved Template', '%s is an action like Add, Edit or View.', 'fl-builder' ), $action ),
						/* translators: %s: add/edit or view */
						'row'    => sprintf( _x( '%s Saved Row', '%s is an action like Add, Edit or View.', 'fl-builder' ), $action ),
						/* translators: %s: add/edit or view */
						'module' => sprintf( _x( '%s Saved Module', '%s is an action like Add, Edit or View.', 'fl-builder' ), $action ),
					),
				),
			) ) );
		}
	}

	/**
	 * Renders the Add New page.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function render() {
		$modules       = FLBuilderModel::get_categorized_modules();
		$selected_type = isset( $_GET['fl-builder-template-type'] ) ? $_GET['fl-builder-template-type'] : '';

		$types = apply_filters( 'fl_builder_user_templates_add_new_types', array(
			100 => array(
				'key'   => 'layout',
				'label' => __( 'Template', 'fl-builder' ),
			),
			200 => array(
				'key'   => 'row',
				'label' => __( 'Saved Row', 'fl-builder' ),
			),
			300 => array(
				'key'   => 'module',
				'label' => __( 'Saved Module', 'fl-builder' ),
			),
		) );

		include FL_BUILDER_USER_TEMPLATES_DIR . 'includes/admin-add-new-form.php';
	}

	/**
	 * Adds a new template if the add new form was submitted.
	 *
	 * @since 1.10
	 * @return void
	 */
	static public function process_form() {
		$page = isset( $_GET['page'] ) ? $_GET['page'] : null;

		if ( 'fl-builder-add-new' != $page ) {
			return;
		}
		if ( ! isset( $_POST['fl-add-template'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST['fl-add-template'], 'fl-add-template-nonce' ) ) {
			return;
		}

		$title     = sanitize_text_field( $_POST['fl-template']['title'] );
		$type      = sanitize_text_field( $_POST['fl-template']['type'] );
		$post_type = apply_filters( 'fl_builder_user_templates_add_new_post_type', 'fl-builder-template', $type );

		// Insert the post.
		$post_id = wp_insert_post( array(
			'post_title'     => $title,
			'post_type'      => $post_type,
			'post_status'    => 'draft',
			'ping_status'    => 'closed',
			'comment_status' => 'closed',
		) );

		// Enable the builder.
		update_post_meta( $post_id, '_fl_builder_enabled', true );

		/**
		 * Let extensions hook additional logic for custom types.
		 * @see fl_builder_user_templates_add_new_submit
		 */
		do_action( 'fl_builder_user_templates_add_new_submit', $type, $title, $post_id );

		// Setup a new layout, row or module template if we have one.
		self::setup_new_template( $type, $post_id );

		// Redirect to the new post.
		wp_redirect( admin_url( "/post.php?post={$post_id}&action=edit" ) );

		exit;
	}

	/**
	 * Sets the needed info for new templates.
	 *
	 * @since 1.10
	 * @private
	 * @return void
	 */
	static private function setup_new_template( $type, $post_id ) {
		// Make sure we have a template.
		if ( ! in_array( $type, array( 'layout', 'row', 'module' ) ) ) {
			return;
		}

		$template_id = FLBuilderModel::generate_node_id();
		$global      = isset( $_POST['fl-template']['global'] ) ? 1 : 0;
		$module      = sanitize_text_field( $_POST['fl-template']['module'] );

		// Set the template type.
		wp_set_post_terms( $post_id, $type, 'fl-builder-template-type' );

		// Set row and module template meta.
		if ( in_array( $type, array( 'row', 'module' ) ) ) {
			update_post_meta( $post_id, '_fl_builder_template_id', $template_id );
			update_post_meta( $post_id, '_fl_builder_template_global', $global );
		}

		// Force the builder to use this post ID.
		FLBuilderModel::set_post_id( $post_id );

		// Add a new row or module?
		if ( 'row' == $type ) {
			$saved_node = FLBuilderModel::add_row();
		} elseif ( 'module' == $type ) {
			$settings   = FLBuilderModel::get_module_defaults( $module );
			$saved_node = FLBuilderModel::add_module( $module, $settings );
		}

		// Make the new template global?
		if ( $global && isset( $saved_node ) ) {

			$data = FLBuilderModel::get_layout_data();

			foreach ( $data as $node_id => $node ) {

				if ( $node_id == $saved_node->node ) {
					$data[ $node_id ]->template_root_node = true;
				}

				$data[ $node_id ]->template_id      = $template_id;
				$data[ $node_id ]->template_node_id = $node_id;
			}

			FLBuilderModel::update_layout_data( $data );
		}

		// Reset the builder's post ID.
		FLBuilderModel::reset_post_id();
	}
}

FLBuilderUserTemplatesAdminAdd::init();
