<?php

/**
 * Add support for SEO plugins
 * @since 2.2.4
 */
class FLBuilderSeoPlugins {

	function __construct() {

		add_action( 'admin_init', array( $this, 'init' ) );
		add_action( 'admin_head', array( $this, 'remove_yoast_meta_box_on_edit' ), 999 );

		add_filter( 'wpseo_sitemap_exclude_post_type', array( $this, 'sitemap_exclude_post_type' ), 10, 2 );
		add_filter( 'wpseo_sitemap_exclude_taxonomy', array( $this, 'sitemap_exclude_taxonomy' ), 10, 2 );
		add_filter( 'manage_edit-fl-builder-template_columns', array( $this, 'remove_columns' ) );

		add_filter( 'the_seo_framework_post_type_disabled', array( $this, 'sf_type' ), 10, 2 );
		add_filter( 'the_seo_framework_sitemap_exclude_cpt', array( $this, 'sf_sitemap' ) );

		add_filter( 'rank_math/sitemap/excluded_post_types', array( $this, 'rankmath_types' ) );

		add_filter( 'seopress_content_analysis_content', array( $this, 'sp_content_analysis_content' ), 10, 2 );

	}

	function init() {
		global $pagenow;
		if ( FLBuilderAJAX::doing_ajax() || 'post.php' !== $pagenow ) {
			return;
		}

		if ( defined( 'WPSEO_VERSION' ) ) {
			$this->enqueue_script( 'yoast' );
		} elseif ( class_exists( 'RankMath' ) ) {
			$this->enqueue_script( 'rankmath' );
		}
	}

	function rankmath_types( $post_types ) {
		unset( $post_types['fl-builder-template'] );
		return $post_types;
	}

	function remove_columns( $columns ) {

		// remove the Yoast SEO columns
		unset( $columns['wpseo-score'] );
		unset( $columns['wpseo-title'] );
		unset( $columns['wpseo-links'] );
		unset( $columns['wpseo-metadesc'] );
		unset( $columns['wpseo-focuskw'] );
		unset( $columns['wpseo-score-readability'] );
		// RankMath columns
		unset( $columns['rank_math_seo_details'] );
		unset( $columns['rank_math_title'] );
		unset( $columns['rank_math_description'] );
		return $columns;
	}

	function remove_yoast_meta_box_on_edit() {
		if ( function_exists( 'remove_meta_box' ) ) {
			remove_meta_box( 'wpseo_meta', 'fl-builder-template', 'normal' );
		}
	}

	function sitemap_exclude_post_type( $value, $post_type ) {
		if ( 'fl-builder-template' === $post_type ) {
			return true;
		}
		return $value;
	}

	function sitemap_exclude_taxonomy( $value, $taxonomy ) {
		if ( 'fl-builder-template-category' === $taxonomy ) {
			return true;
		}
		return $value;
	}

	function enqueue_script( $plugin ) {

		global $post;
		$orig = $post;

		if ( ! isset( $_GET['post'] ) ) {
			return false;
		}

		$post_id = $_GET['post'];

		$post_type = get_post_type( $post_id );

		if ( in_array( $post_type, array( 'fl-theme-layout', 'fl-builder-template' ) ) ) {
				return false;
		}

		add_action( 'admin_enqueue_scripts', array( $this, 'dequeue_layout_scripts' ), 10000 );

		if ( 'yoast' === $plugin ) {
			$deps = array();
		} else {
			$deps = array( 'wp-hooks', 'rank-math-analyzer' );
		}

		$data = $this->content_data();
		$post = $orig;

		if ( $data ) {
			wp_enqueue_script( 'bb-seo-scripts', FL_BUILDER_SEO_PLUGINS_URL . "js/plugin-$plugin.js", $deps, false, true );
			wp_localize_script( 'bb-seo-scripts', 'bb_seo_data', array( 'content' => $data ) );
		}
	}

	function dequeue_layout_scripts() {
		global $wp_scripts;
		foreach ( $wp_scripts->queue as $item ) {
			if ( false !== strpos( $item, 'fl-builder-layout' ) ) {
				wp_dequeue_script( $item );
			}
		}
	}

	function content_data( $post_id = false ) {

		if ( ! $post_id && ! isset( $_GET['post'] ) ) {
			return false;
		}

		$id = ( false === $post_id ) ? $_GET['post'] : $post_id;

		if ( ! get_post_meta( $id, '_fl_builder_enabled', true ) ) {
			return false;
		}
		ob_start();
		echo do_shortcode( "[fl_builder_insert_layout id=$id]" );
		$data   = ob_get_clean();
		$handle = 'fl-builder-layout-' . $id;
		wp_dequeue_script( $handle );
		wp_dequeue_style( $handle );
		wp_deregister_script( $handle );
		wp_deregister_style( $handle );
		return str_replace( PHP_EOL, '', $data );
	}

	public function sf_type( $value, $post_type ) {
		if ( 'fl-builder-template' === $post_type ) {
			return true;
		}
		return $value;
	}

	public function sf_sitemap( $types ) {
		$types[] = 'fl-builder-template';
		return $types;
	}

	function sp_content_analysis_content( $content, $id ) {
		if ( get_post_meta( $id, '_fl_builder_enabled', true ) ) {
			return $this->content_data( $id );
		}
		return $content;
	}

}

new FLBuilderSeoPlugins();
