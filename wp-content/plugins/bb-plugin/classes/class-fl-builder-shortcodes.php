<?php

/**
 * Helper class for builder shortcodes
 *
 * @since 1.7
 */
final class FLBuilderShortcodes {

	/**
	 * Adds all shortcodes for the builder.
	 *
	 * @since 1.7
	 * @return void
	 */
	static public function init() {
		add_shortcode( 'fl_builder_insert_layout', 'FLBuilderShortcodes::insert_layout' );
		add_shortcode( 'fl-safe', array( __CLASS__, 'safe_shortcode' ) );
	}

	/**
	 * Renders a layout with the provided post ID and enqueues the
	 * necessary styles and scripts.
	 *
	 * @since 1.7
	 * @param array $attrs The shortcode attributes.
	 * @return string
	 */
	static public function insert_layout( $attrs ) {
		$builder_active = FLBuilderModel::is_builder_active();
		$post_type      = isset( $attrs['type'] ) ? $attrs['type'] : get_post_types();
		$site_id        = isset( $attrs['site'] ) ? absint( $attrs['site'] ) : null;
		$inline_assets  = apply_filters( 'fl_builder_render_assets_inline', false );
		$args           = array(
			'post_type'      => $post_type,
			'posts_per_page' => -1,
		);

		// Build the args array.
		if ( isset( $attrs['id'] ) ) {

			$args['orderby']             = 'post__in';
			$args['ignore_sticky_posts'] = true;

			if ( is_numeric( $attrs['id'] ) ) {
				$args['post__in'] = array( $attrs['id'] );
			} else {
				$args['post__in'] = explode( ',', $attrs['id'] );
			}
		} elseif ( isset( $attrs['slug'] ) && '' !== $attrs['slug'] ) {
			$args['orderby'] = 'name';
			$args['name']    = $attrs['slug'];
		} else {
			return;
		}

		$render = apply_filters( 'fl_builder_insert_layout_render', true, $attrs, $args );

		if ( ! $render ) {
			return;
		}

		// Render and return the layout.
		ob_start();

		if ( $builder_active ) {
			echo '<div class="fl-builder-shortcode-mask-wrap"><div class="fl-builder-shortcode-mask"></div>';
		}
		if ( ! $inline_assets ) {
			add_filter( 'fl_builder_render_assets_inline', '__return_true' );
		}

		FLBuilder::render_query( $args, $site_id );
		if ( ! $inline_assets ) {
			add_filter( 'fl_builder_render_assets_inline', '__return_false' );
		}

		if ( $builder_active ) {
			echo '</div>';
		}

		return ob_get_clean();
	}

	/**
	 * Allow users to wrap code that breaks the builder in a shortcode.
	 * @since 2.4.2
	 */
	static public function safe_shortcode( $atts, $content ) {
		if ( $content ) {
			if ( ! FLBuilderModel::is_builder_active() ) {
				return do_shortcode( $content );
			} else {
				$refresh = '<script>jQuery(function(){window.FLBuilderConfig.shouldRefreshOnPublish=true;});</script>';
				return __( 'Content not rendered while builder is active', 'fl-builder' ) . $refresh;
			}
		}
	}
}

FLBuilderShortcodes::init();
