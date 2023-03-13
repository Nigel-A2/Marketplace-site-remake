<?php

/**
 * Template data exporter for the builder.
 *
 * @since 1.8
 */
final class FLBuilderTemplateDataExporter {

	/**
	 * Initializes the exporter.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function init() {
		add_action( 'plugins_loaded', __CLASS__ . '::init_hooks' );
		add_action( 'after_setup_theme', __CLASS__ . '::register_user_access_setting' );
	}

	/**
	 * Init actions and filters.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function init_hooks() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'admin_menu', __CLASS__ . '::menu' );

		if ( isset( $_REQUEST['page'] ) && 'fl-builder-template-data-exporter' == $_REQUEST['page'] ) {
			add_action( 'admin_enqueue_scripts', __CLASS__ . '::styles_scripts' );
			add_action( 'init', __CLASS__ . '::export' );
		}
	}

	/**
	 * Registers the user access setting.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function register_user_access_setting() {
		FLBuilderUserAccess::register_setting( 'template_data_exporter', array(
			'default'     => false,
			'group'       => __( 'Admin', 'fl-builder' ),
			'label'       => __( 'Template Data Exporter', 'fl-builder' ),
			'description' => __( 'The selected roles will be able to access the template data exporter under Tools > Template Exporter.', 'fl-builder' ),
			'order'       => '120',
		) );
	}

	/**
	 * Checks to see whether the exporter is enabled or not.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function is_enabled() {
		return FLBuilderUserAccess::current_user_can( 'template_data_exporter' );
	}

	/**
	 * Enqueues scripts and styles for the exporter.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function styles_scripts() {
		wp_enqueue_script(
			'fl-builder-template-data-exporter',
			FL_BUILDER_TEMPLATE_DATA_EXPORTER_URL . 'js/fl-builder-template-data-exporter.js',
			array(),
			FL_BUILDER_VERSION
		);
	}

	/**
	 * Renders the admin settings menu.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function menu() {
		if ( self::is_enabled() ) {

			$title = __( 'Template Exporter', 'fl-builder' );
			$cap   = 'edit_posts';
			$slug  = 'fl-builder-template-data-exporter';
			$func  = __CLASS__ . '::render';

			add_submenu_page( 'tools.php', $title, $title, $cap, $slug, $func );
		}
	}

	/**
	 * Renders the exporter UI.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function render() {
		$theme   = self::get_ui_data( 'theme' );
		$layouts = self::get_ui_data();
		$rows    = self::get_ui_data( 'row' );
		$modules = self::get_ui_data( 'module' );
		$columns = self::get_ui_data( 'column' );
		$other   = apply_filters( 'fl_builder_exporter_ui_data', array() );

		include FL_BUILDER_TEMPLATE_DATA_EXPORTER_DIR . 'includes/template-data-exporter.php';
	}

	/**
	 * Run the exporter.
	 *
	 * @since 1.8
	 * @return void
	 */
	static public function export() {
		if ( ! self::is_enabled() ) {
			return;
		}
		if ( ! isset( $_POST['fl-builder-template-data-exporter-nonce'] ) ) {
			return;
		}
		if ( ! wp_verify_nonce( $_POST['fl-builder-template-data-exporter-nonce'], 'fl-builder-template-data-exporter' ) ) {
			return;
		}

		$templates = array();

		if ( isset( $_POST['fl-builder-export-theme'] ) && is_array( $_POST['fl-builder-export-theme'] ) ) {
			$templates = self::get_theme_layout_export_data( $templates );
		}
		if ( isset( $_POST['fl-builder-export-layout'] ) && is_array( $_POST['fl-builder-export-layout'] ) ) {
			$templates['layout'] = self::get_template_export_data( $_POST['fl-builder-export-layout'] );
		}
		if ( isset( $_POST['fl-builder-export-row'] ) && is_array( $_POST['fl-builder-export-row'] ) ) {
			$templates['row'] = self::get_template_export_data( $_POST['fl-builder-export-row'] );
		}
		if ( isset( $_POST['fl-builder-export-module'] ) && is_array( $_POST['fl-builder-export-module'] ) ) {
			$templates['module'] = self::get_template_export_data( $_POST['fl-builder-export-module'] );
		}
		if ( isset( $_POST['fl-builder-export-column'] ) && is_array( $_POST['fl-builder-export-column'] ) ) {
			$templates['column'] = self::get_template_export_data( $_POST['fl-builder-export-column'] );
		}

		$templates = apply_filters( 'fl_builder_exporter_templates', $templates );

		header( 'X-Robots-Tag: noindex, nofollow', true );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Description: File Transfer' );
		header( 'Content-Disposition: attachment; filename="templates.dat";' );
		header( 'Content-Transfer-Encoding: binary' );
		echo serialize( $templates );
		die();
	}

	/**
	 * Returns user template data of a certain type for the UI.
	 *
	 * @since 1.8
	 * @access private
	 * @param string $type
	 * @return array
	 */
	static private function get_ui_data( $type = 'layout' ) {
		$templates = array();

		if ( 'theme' == $type ) {
			$args = array(
				'post_type'      => 'fl-theme-layout',
				'orderby'        => 'title',
				'order'          => 'ASC',
				'posts_per_page' => '-1',
			);
		} else {
			$args = array(
				'post_type'      => 'fl-builder-template',
				'orderby'        => 'title',
				'order'          => 'ASC',
				'posts_per_page' => '-1',
				'tax_query'      => array(
					array(
						'taxonomy' => 'fl-builder-template-type',
						'field'    => 'slug',
						'terms'    => $type,
					),
				),
			);
		}

		foreach ( get_posts( $args ) as $post ) {
			$templates[] = array(
				'id'   => $post->ID,
				'name' => $post->post_title,
			);
		}

		return $templates;
	}

	/**
	 * Returns theme layout export data for the specified post ids.
	 *
	 * @since 1.10
	 * @access private
	 * @param array $templates
	 * @return array
	 */
	static private function get_theme_layout_export_data( $templates ) {
		$posts = get_posts( array(
			'post_type'      => 'fl-theme-layout',
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
			'posts_per_page' => '-1',
			'post__in'       => array_map( 'sanitize_text_field', $_POST['fl-builder-export-theme'] ),
		) );

		// Get all theme layouts.
		$data = self::get_export_data( $posts );

		// Store in the templates array by type.
		foreach ( $data as $template ) {

			if ( ! isset( $templates[ $template->type ] ) ) {
				$templates[ $template->type ] = array();
			}

			$templates[ $template->type ][] = $template;
		}

		// Reset the index for each template.
		foreach ( $templates as $data ) {
			foreach ( $data as $index => $template ) {
				$template->index = $index;
			}
		}

		return $templates;
	}

	/**
	 * Returns template export data for the specified post ids.
	 *
	 * @since 1.10
	 * @access private
	 * @param array $post_ids
	 * @return array
	 */
	static private function get_template_export_data( $post_ids = array() ) {
		if ( empty( $post_ids ) ) {
			return array();
		}

		$posts = get_posts( array(
			'post_type'      => 'fl-builder-template',
			'orderby'        => 'menu_order title',
			'order'          => 'ASC',
			'posts_per_page' => '-1',
			'post__in'       => $post_ids,
		) );

		return self::get_export_data( $posts );
	}

	/**
	 * Returns export data for the specified posts.
	 *
	 * @since 1.8
	 * @access private
	 * @param array $posts
	 * @return array
	 */
	static private function get_export_data( $posts ) {
		if ( empty( $posts ) ) {
			return array();
		}

		$templates = array();
		$index     = 0;

		foreach ( $posts as $post ) {

			// Build the template object.
			$template             = new StdClass();
			$template->name       = $post->post_title;
			$template->slug       = $post->post_name;
			$template->index      = $index++;
			$template->global     = false;
			$template->image      = '';
			$template->categories = array();
			$template->nodes      = FLBuilderModel::generate_new_node_ids( FLBuilderModel::get_layout_data( 'published', $post->ID ) );
			$template->settings   = FLBuilderModel::get_layout_settings( 'published', $post->ID );

			// Get the template type.
			if ( 'fl-theme-layout' == $post->post_type ) {
				$template->type = get_post_meta( $post->ID, '_fl_theme_layout_type', true );
			} else {
				$template->type = FLBuilderModel::get_user_template_type( $post->ID );
			}

			// Get the template categories.
			$categories = wp_get_post_terms( $post->ID, 'fl-builder-template-category' );

			if ( 0 === count( $categories ) || is_wp_error( $categories ) ) {
				$template->categories['uncategorized'] = 'Uncategorized';
			} else {
				foreach ( $categories as $category ) {
					$template->categories[ $category->slug ] = $category->name;
				}
			}

			// Get the template thumbnail.
			if ( has_post_thumbnail( $post->ID ) ) {
				$attachment_image_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium_large' );
				$template->image      = apply_filters( 'fl_builder_exporter_template_thumb_src', $attachment_image_src[0], $post, $template );
			}

			/**
			 * Add the template to the templates array.
			 * @see fl_builder_exporter_template
			 */
			$templates[] = apply_filters( 'fl_builder_exporter_template', $template, $post );
		}

		return $templates;
	}
}

FLBuilderTemplateDataExporter::init();
