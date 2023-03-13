<?php

/**
 * Handles logic for the post edit screen.
 *
 * @since 1.0
 */
final class FLBuilderAdminPosts {

	/**
	 * Initialize hooks.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function init() {
		/* Actions */
		add_action( 'current_screen', __CLASS__ . '::init_rendering' );

		if ( get_transient( 'fl_debug_mode' ) || ( defined( 'FL_ENABLE_META_CSS_EDIT' ) && FL_ENABLE_META_CSS_EDIT ) ) {
			add_action( 'add_meta_boxes', array( __CLASS__, 'add_meta_box' ) );
			add_action( 'save_post', array( __CLASS__, 'save_meta' ) );
		}

		/* Filters */
		add_filter( 'redirect_post_location', __CLASS__ . '::redirect_post_location' );
		add_filter( 'page_row_actions', __CLASS__ . '::render_row_actions_link' );
		add_filter( 'post_row_actions', __CLASS__ . '::render_row_actions_link' );
		add_action( 'pre_get_posts', __CLASS__ . '::sort_builder_enabled' );
		add_action( 'admin_init', __CLASS__ . '::duplicate_layout' );
	}

	/**
	 * @since 2.4
	 */
	public static function add_meta_box( $post_type ) {
		// Limit meta box to certain post types.
		$post_types = array( 'post', 'page' );

		if ( in_array( $post_type, FLBuilderModel::get_post_types() ) ) {
				add_meta_box(
					'fl_css_js',
					__( 'Builder CSS/JS', 'fl-builder' ),
					array( __CLASS__, 'render_meta_box_content' ),
					$post_type,
					'advanced',
					'high'
				);
		}
	}

	/**
	 * @since 2.4
	 * @param WP_Post $post The post object.
	 */
	public static function render_meta_box_content( $post ) {

		// Add an nonce field so we can check for it later.
		wp_nonce_field( 'fl_css_js', 'fl_css_js_nonce' );

		// Use get_post_meta to retrieve an existing value from the database.
		$data = get_post_meta( $post->ID, '_fl_builder_data_settings', true );

		if ( ! isset( $data->css ) ) {
			$css = '';
		} else {
			$css = $data->css;
		}
		if ( ! isset( $data->js ) ) {
			$js = '';
		} else {
			$js = $data->js;
		}
		?>
			<label for="fl_css">
					<?php _e( 'CSS', 'fl-builder' ); ?>
			</label><br />
			<textarea style="width:100%" rows=10 id="fl_css" name="fl_css" value="<?php echo esc_attr( $css ); ?>"><?php echo esc_attr( $css ); ?></textarea><br />

			<label for="fl_js">
					<?php _e( 'JS', 'fl-builder' ); ?>
			</label><br />
			<textarea style="width:100%" rows=10 id="fl_js" name="fl_js" value="<?php echo esc_attr( $js ); ?>"><?php echo esc_attr( $js ); ?></textarea>
			<?php
	}

	/**
 * Save the meta when the post is saved.
 *
 * @param int $post_id The ID of the post being saved.
 */
	public static function save_meta( $post_id ) {

		// Check if our nonce is set.
		if ( ! isset( $_POST['fl_css_js_nonce'] ) ) {
				return $post_id;
		}

		$nonce = $_POST['fl_css_js_nonce'];

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $nonce, 'fl_css_js' ) ) {
				return $post_id;
		}

		/*
		 * If this is an autosave, our form has not been submitted,
		 * so we don't want to do anything.
		 */
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return $post_id;
		}

		// Check the user's permissions.
		if ( 'page' == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) ) {
					return $post_id;
			}
		} else {
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
					return $post_id;
			}
		}

		$data = get_post_meta( $post_id, '_fl_builder_data_settings', true );

		if ( ! is_object( $data ) ) {
			$data = new StdClass;
		}
		$data->css = $_POST['fl_css'];
		$data->js  = $_POST['fl_js'];

		// Update the meta field.
		update_post_meta( $post_id, '_fl_builder_data_settings', $data );
		update_post_meta( $post_id, '_fl_builder_draft_settings', $data );

	}

	/**
	 * WordPress doesn't have a "right way" to get the current
	 * post type being edited and the new editor doesn't make
	 * this any easier. This method attempts to fix that.
	 *
	 * @since 2.1
	 * @return void
	 */
	static public function get_post_type() {
		global $post, $typenow, $current_screen;

		if ( is_object( $post ) && $post->post_type ) {
			return $post->post_type;
		} elseif ( $typenow ) {
			return $typenow;
		} elseif ( is_object( $current_screen ) && $current_screen->post_type ) {
			return $current_screen->post_type;
		} elseif ( isset( $_REQUEST['post_type'] ) ) {
			return sanitize_key( $_REQUEST['post_type'] );
		}

		return null;
	}

	/**
	 * Checks to see if a post type supports the
	 * WordPress block editor.
	 *
	 * @since 2.2
	 * @param string $post_type
	 * @return bool
	 */
	static public function post_type_supports_block_editor( $post_type ) {
		if ( ! function_exists( 'use_block_editor_for_post_type' ) || isset( $_GET['classic-editor'] ) ) {
			return false;
		}

		return use_block_editor_for_post_type( $post_type );
	}

	/**
	 * Allow sorting by builder enabled in pages list.
	 * @since 2.2.1
	 */
	static public function sort_builder_enabled( $query ) {
		global $pagenow;
		if ( is_admin()
		&& 'edit.php' == $pagenow
		&& ! isset( $_GET['orderby'] )
		&& isset( $_GET['post_type'] )
		&& isset( $_GET['bbsort'] ) ) {
			$query->set( 'meta_key', '_fl_builder_enabled' );
			$query->set( 'meta_value', '1' );
		}
	}

	/**
	 * Sets the body class, loads assets and renders the UI
	 * if we are on a post type that supports the builder.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function init_rendering() {
		global $pagenow;

		if ( in_array( $pagenow, array( 'post.php', 'post-new.php' ) ) ) {

			/**
			 * Enable/disable builder edit UI buttons
			 * @see fl_builder_render_admin_edit_ui
			 */
			$render_ui  = apply_filters( 'fl_builder_render_admin_edit_ui', true );
			$post_type  = self::get_post_type();
			$post_types = FLBuilderModel::get_post_types();

			if ( $render_ui && in_array( $post_type, $post_types ) ) {
				add_filter( 'admin_body_class', __CLASS__ . '::body_class', 99 );
				add_action( 'admin_enqueue_scripts', __CLASS__ . '::styles_scripts' );
				add_action( 'edit_form_after_title', __CLASS__ . '::render' );
			}
		}
		/**
		 * Enable/disable sorting by BB enabled.
		 * @see fl_builder_admin_edit_sort_bb_enabled
		 */
		if ( 'edit.php' == $pagenow && true === apply_filters( 'fl_builder_admin_edit_sort_bb_enabled', true ) ) {
			$post_types = FLBuilderModel::get_post_types();
			$post_type  = self::get_post_type();
			$block      = array(
				'fl-builder-template',
				'fl-theme-layout',
			);

			/**
			 * Array of types to not show filtering on.
			 * @see fl_builder_admin_edit_sort_blocklist
			 */
			if ( ! in_array( $post_type, apply_filters( 'fl_builder_admin_edit_sort_blocklist', $block ) ) && in_array( $post_type, $post_types ) ) {
				wp_enqueue_script( 'fl-builder-admin-posts-list', FL_BUILDER_URL . 'js/fl-builder-admin-posts-list.js', array( 'jquery' ), FL_BUILDER_VERSION );
				$args    = array(
					'post_type'      => $post_type,
					'posts_per_page' => -1,
					'fields'         => 'ids',
					'meta_query'     => array(
						array(
							'key'     => '_fl_builder_enabled',
							'compare' => '!=',
							'value'   => '',
						),
					),
				);
				$result  = new WP_Query( $args );
				$count   = is_array( $result->posts ) ? count( $result->posts ) : 0;
				$clicked = isset( $_GET['bbsort'] ) ? true : false;
				wp_localize_script( 'fl-builder-admin-posts-list',
					'fl_builder_enabled_count',
					array(
						'count'   => number_format_i18n( $count ),
						'brand'   => FLBuilderModel::get_branding(),
						'clicked' => $clicked,
						'type'    => $post_type,
					)
				);
			}
		}
	}

	/**
	 * Enqueues the CSS/JS for the post edit screen.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function styles_scripts() {
		global $wp_version;

		// Styles
		wp_enqueue_style( 'fl-builder-admin-posts', FL_BUILDER_URL . 'css/fl-builder-admin-posts.css', array(), FL_BUILDER_VERSION );

		// Legacy WP Styles (3.7 and below)
		if ( version_compare( $wp_version, '3.7', '<=' ) ) {
			wp_enqueue_style( 'fl-builder-admin-posts-legacy', FL_BUILDER_URL . 'css/fl-builder-admin-posts-legacy.css', array(), FL_BUILDER_VERSION );
		}

		// Scripts
		wp_enqueue_script( 'json2' );
		wp_enqueue_script( 'fl-builder-admin-posts', FL_BUILDER_URL . 'js/fl-builder-admin-posts.js', array(), FL_BUILDER_VERSION );
	}

	/**
	 * Adds classes to the post edit screen body class.
	 *
	 * @since 1.0
	 * @param string $classes The existing body classes.
	 * @return string The body classes.
	 */
	static public function body_class( $classes = '' ) {
		global $wp_version;

		// Builder body class
		if ( FLBuilderModel::is_builder_enabled() ) {
			$classes .= ' fl-builder-enabled';
		}

		// Pre WP 3.8 body class
		if ( version_compare( $wp_version, '3.8', '<' ) ) {
			$classes .= ' fl-pre-wp-3-8';
		}

		return $classes;
	}

	/**
	 * Renders the HTML for the post edit screen.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render() {
		global $post;

		$post_type_obj  = get_post_type_object( $post->post_type );
		$post_type_name = strtolower( $post_type_obj->labels->singular_name );
		$enabled        = FLBuilderModel::is_builder_enabled();

		include FL_BUILDER_DIR . 'includes/admin-posts.php';
	}

	/**
	 * Renders the action link for post listing pages.
	 *
	 * @since 1.0
	 * @param array $actions
	 * @return array The array of action data.
	 */
	static public function render_row_actions_link( $actions = array() ) {
		global $post;
		if ( 'trash' != $post->post_status && current_user_can( 'edit_post', $post->ID ) && ( function_exists( 'wp_check_post_lock' ) && wp_check_post_lock( $post->ID ) === false ) ) {

			/**
			 * Is post editable from admin post list
			 * @see fl_builder_is_post_editable
			 */
			$is_post_editable = (bool) apply_filters( 'fl_builder_is_post_editable', true, $post );
			$user_access      = FLBuilderUserAccess::current_user_can( 'builder_access' );
			$post_types       = FLBuilderModel::get_post_types();
			$typeobj          = get_post_type_object( $post->post_type );
			$singular_name    = ( isset( $_GET['fl-builder-template-type'] ) ) ? ucfirst( $_GET['fl-builder-template-type'] ) : $typeobj->labels->singular_name;
			$singular_name    = ( 'Layout' === $singular_name ) ? 'Template' : $singular_name;
			if ( in_array( $post->post_type, $post_types ) && $is_post_editable && $user_access ) {
				$enabled               = get_post_meta( $post->ID, '_fl_builder_enabled', true );
				$dot                   = '&nbsp;<span style="color:' . ( $enabled ? '#6bc373' : '#d9d9d9' ) . '; font-size:18px;">&bull;</span>';
				$actions['fl-builder'] = '<a href="' . FLBuilderModel::get_edit_url() . '">' . FLBuilderModel::get_branding() . $dot . '</a>';
				if ( $enabled ) {
					$url = add_query_arg( array(
						'post_type'        => $post->post_type,
						'post_id'          => $post->ID,
						'duplicate_layout' => true,
						'duplicate_nonce'  => wp_create_nonce( 'duplicate_nonce' ),
					), admin_url() );
					/* translators: %s: post type being duplicated */
					$duplicate_text = sprintf( __( 'Duplicate %s', 'fl-builder' ), $singular_name );
					/* translators: %1$s: post type being duplicated: %2$s: Branding name */
					$duplicate_alt                   = esc_attr( sprintf( __( 'Duplicate %1$s with %2$s', 'fl-builder' ), $singular_name, FLBuilderModel::get_branding() ) );
					$actions['fl-builder-duplicate'] = sprintf( '<a title="%s" href="%s">%s</a>%s', $duplicate_alt, $url, $duplicate_text, $dot );
				}
			}
		}

		return $actions;
	}

	static public function duplicate_layout() {
		if ( isset( $_GET['duplicate_layout'] ) ) {
			$id    = $_GET['post_id'];
			$nonce = $_GET['duplicate_nonce'];

			if ( wp_verify_nonce( $nonce, 'duplicate_nonce' ) ) {
				$post_id = FLBuilderModel::duplicate_post( $id );
				$url     = FLBuilderModel::get_edit_url( $post_id );
				wp_redirect( $url );
				exit;
			} else {
				wp_die( 'Unauthorized' );
			}
		}
	}

	/**
	 * Where to redirect this post on save.
	 *
	 * @since 1.0
	 * @param string $location
	 * @return string The location to redirect this post on save.
	 */
	static public function redirect_post_location( $location ) {
		if ( isset( $_POST['fl-builder-redirect'] ) ) {
			$location = FLBuilderModel::get_edit_url( absint( $_POST['fl-builder-redirect'] ) );
		}

		return $location;
	}
}

FLBuilderAdminPosts::init();
