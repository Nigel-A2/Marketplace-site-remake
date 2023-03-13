<?php

/**
 * @class FLPostGridModule
 */
class FLPostGridModule extends FLBuilderModule {

	/**
	 * @method __construct
	 */
	public function __construct() {
		parent::__construct(array(
			'name'            => __( 'Posts', 'fl-builder' ),
			'description'     => __( 'Display a grid of your WordPress posts.', 'fl-builder' ),
			'category'        => __( 'Posts', 'fl-builder' ),
			'editor_export'   => false,
			'partial_refresh' => true,
			'icon'            => 'schedule.svg',
		));
	}

	/**
	 * Ensure backwards compatibility with old settings.
	 *
	 * @since 2.2
	 * @param object $settings A module settings object.
	 * @param object $helper A settings compatibility helper.
	 * @return object
	 */
	public function filter_settings( $settings, $helper ) {

		// Handle old opacity inputs.
		$helper->handle_opacity_inputs( $settings, 'bg_opacity', 'bg_color' );
		$helper->handle_opacity_inputs( $settings, 'text_bg_opacity', 'text_bg_color' );

		// Handle old border inputs.
		if ( isset( $settings->border_type ) && isset( $settings->border_color ) && isset( $settings->border_size ) ) {
			$settings->border = array(
				'style' => $settings->border_type,
				'color' => $settings->border_color,
				'width' => array(
					'top'    => $settings->border_size,
					'right'  => $settings->border_size,
					'bottom' => $settings->border_size,
					'left'   => $settings->border_size,
				),
			);
			unset( $settings->border_type );
			unset( $settings->border_color );
			unset( $settings->border_size );
		}

		// Handle old title font size.
		if ( isset( $settings->title_font_size ) ) {
			$settings->title_typography              = array();
			$settings->title_typography['font_size'] = array(
				'length' => $settings->title_font_size,
				'unit'   => 'px',
			);
			unset( $settings->title_font_size );
		}

		// Handle old info font size.
		if ( isset( $settings->info_font_size ) ) {
			$settings->info_typography              = array();
			$settings->info_typography['font_size'] = array(
				'length' => $settings->info_font_size,
				'unit'   => 'px',
			);
			unset( $settings->info_font_size );
		}

		// Handle old content font size.
		if ( isset( $settings->content_font_size ) ) {
			$settings->content_typography              = array();
			$settings->content_typography['font_size'] = array(
				'length' => $settings->content_font_size,
				'unit'   => 'px',
			);
			unset( $settings->content_font_size );
		}

		// Handle old button module settings.
		$helper->filter_child_module_settings( 'button', $settings, array(
			'more_btn_3d'                 => 'three_d',
			'more_btn_style'              => 'style',
			'more_btn_padding'            => 'padding',
			'more_btn_padding_top'        => 'padding_top',
			'more_btn_padding_bottom'     => 'padding_bottom',
			'more_btn_padding_left'       => 'padding_left',
			'more_btn_padding_right'      => 'padding_right',
			'more_btn_mobile_align'       => 'mobile_align',
			'more_btn_align_responsive'   => 'align_responsive',
			'more_btn_font_size'          => 'font_size',
			'more_btn_font_size_unit'     => 'font_size_unit',
			'more_btn_typography'         => 'typography',
			'more_btn_bg_color'           => 'bg_color',
			'more_btn_bg_hover_color'     => 'bg_hover_color',
			'more_btn_bg_opacity'         => 'bg_opacity',
			'more_btn_bg_hover_opacity'   => 'bg_hover_opacity',
			'more_btn_border'             => 'border',
			'more_btn_border_hover_color' => 'border_hover_color',
			'more_btn_border_radius'      => 'border_radius',
			'more_btn_border_size'        => 'border_size',
		) );

		return $settings;
	}

	/**
	 * @method enqueue_scripts
	 */
	public function enqueue_scripts() {
		if ( FLBuilderModel::is_builder_active() || 'columns' == $this->settings->layout ) {
			$this->add_js( 'imagesloaded' );
		}
		if ( FLBuilderModel::is_builder_active() || 'grid' == $this->settings->layout ) {
			$this->add_js( 'imagesloaded' );
			$this->add_js( 'jquery-masonry' );
			$this->add_js( 'jquery-throttle' );
		}
		if ( FLBuilderModel::is_builder_active() || 'gallery' == $this->settings->layout ) {
			$this->add_js( 'fl-gallery-grid' );
		}
		if ( FLBuilderModel::is_builder_active() || 'scroll' == $this->settings->pagination || 'load_more' == $this->settings->pagination ) {
			$this->add_js( 'jquery-infinitescroll' );
		}

		if ( FLBuilderModel::is_builder_active() || ( in_array( $this->settings->layout, array( 'grid', 'columns' ), true ) && $this->settings->show_comments_grid ) ) {
			$this->add_css( 'font-awesome-5' );
		}

		// Jetpack sharing has settings to enable sharing on posts, post types and pages.
		// If pages are disabled then jetpack will still show the share button in this module
		// but will *not* enqueue its scripts and fonts.
		// This filter forces jetpack to enqueue the sharing scripts.
		add_filter( 'sharing_enqueue_scripts', '__return_true' );
	}

	/**
	 * @since 1.10.7
	 */
	public function update( $settings ) {
		global $wp_rewrite;
		$wp_rewrite->flush_rules( false );
		return $settings;
	}

	/**
	 * Returns the slug for the posts layout.
	 *
	 * @since 1.10
	 * @return string
	 */
	public function get_layout_slug() {
		return 'columns' == $this->settings->layout ? 'grid' : $this->settings->layout;
	}

	/**
	 * Renders the CSS class for each post item.
	 *
	 * @since 1.10
	 * @return void
	 */
	public function render_post_class() {
		$settings      = $this->settings;
		$layout        = $this->get_layout_slug();
		$show_image    = $settings->show_image;
		$has_thumbnail = has_post_thumbnail();
		$has_fallback  = ! $has_thumbnail && '' !== $settings->image_fallback && $settings->show_image ? true : false;
		$classes       = array( 'fl-post-' . $layout . '-post' );

		if ( $show_image && $has_thumbnail ) {
			if ( 'feed' == $layout ) {
				$classes[] = 'fl-post-feed-image-' . $settings->image_position;
			}
			if ( 'grid' == $layout ) {
				$classes[] = 'fl-post-grid-image-' . $settings->grid_image_position;
			}
			if ( 'columns' == $settings->layout ) {
				$classes[] = 'fl-post-columns-post';
			}
		}

		if ( $show_image && $has_fallback ) {
			if ( 'feed' == $layout ) {
				$classes[] = 'fl-post-feed-image-' . $settings->image_position;
				$classes[] = 'fl-post-feed-image-fallback';
			}
		}

		if ( in_array( $layout, array( 'grid', 'feed' ) ) ) {
			$align     = empty( $settings->post_align ) ? 'default' : $settings->post_align;
			$classes[] = 'fl-post-align-' . $align;
		}

		if ( '' != $settings->posts_container_class ) {
			$classes[] = $settings->posts_container_class;
		}

		post_class( apply_filters( 'fl_builder_posts_module_classes', $classes, $settings ) );
	}

	/**
	 * Renders the featured image for a post.
	 *
	 * @since 1.10
	 * @param string|array $position
	 * @return void
	 */
	public function render_featured_image( $position = 'above' ) {
		$settings = $this->settings;
		$render   = false;
		$position = ! is_array( $position ) ? array( $position ) : $position;
		$layout   = $this->get_layout_slug();
		/**
		 * @since 2.2.5
		 * @see fl_render_featured_image_fallback
		 */
		$fallback_image = apply_filters( 'fl_render_featured_image_fallback', $settings->image_fallback, $settings );
		$fallback       = ! has_post_thumbnail() && '' !== $fallback_image && $settings->show_image ? true : false;
		if ( ( has_post_thumbnail() || $fallback ) && $settings->show_image ) {

			if ( 'feed' == $settings->layout && in_array( $settings->image_position, $position ) ) {
				$render = true;
			} elseif ( 'columns' == $settings->layout && in_array( $settings->grid_image_position, $position ) ) {
				$render = true;
			} elseif ( 'grid' == $settings->layout && in_array( $settings->grid_image_position, $position ) ) {
				$render = true;
			}
			if ( $render ) {
				if ( $fallback ) {
					include $this->dir . 'includes/featured-image-fallback.php';
				} else {
					include $this->dir . 'includes/featured-image.php';
				}
			}
		}
	}

	/**
	 * Checks to see if a featured image exists for a position.
	 *
	 * @since 1.10
	 * @param string|array $position
	 * @return void
	 */
	public function has_featured_image( $position = 'above' ) {
		$settings = $this->settings;
		$result   = false;
		$position = ! is_array( $position ) ? array( $position ) : $position;

		if ( ( has_post_thumbnail() && $settings->show_image ) || ( $settings->image_fallback && $settings->show_image ) ) {

			if ( 'feed' == $settings->layout && in_array( $settings->image_position, $position ) ) {
				$result = true;
			} elseif ( 'columns' == $settings->layout && in_array( $settings->grid_image_position, $position ) ) {
				$result = true;
			} elseif ( 'grid' == $settings->layout && in_array( $settings->grid_image_position, $position ) ) {
				$result = true;
			}
		}

		return $result;
	}

	/**
	 * Renders the_content for a post.
	 *
	 * @since 1.10
	 * @return void
	 */
	public function render_content() {

		global $post;
		if ( ! has_filter( 'the_content', 'wpautop' ) && empty( $this->settings->content_length ) ) {
			add_filter( 'the_content', 'wpautop' );
		}

		if ( get_post_meta( $post->ID, '_fl_builder_enabled', true ) ) {

			/**
			 * Replace WP content with our layout data.
			 */
			ob_start();
			FLBuilder::render_content_by_id( $post->ID );
			$post->post_content = ob_get_clean();
			$content            = get_the_content( null, false, $post );
		} else {
			ob_start();
			the_content();
			$content = ob_get_clean();
		}
		echo $content;
	}

	/**
	 * Renders the_excerpt for a post.
	 *
	 * @since 1.10
	 * @return void
	 */
	public function render_excerpt() {

		global $post;
		if ( ! empty( $this->settings->content_length ) ) {
			add_filter( 'excerpt_length', array( $this, 'set_custom_excerpt_length' ), 9999 );
		}

		FLBuilderLoop::the_excerpt();

		if ( ! empty( $this->settings->content_length ) ) {
			remove_filter( 'excerpt_length', array( $this, 'set_custom_excerpt_length' ), 9999 );
		}
	}

	/**
	 * Renders 404 Message
	 */
	public function render_404() {
		echo '<div class="fl-post-grid-empty">';
		echo '<p>' . $this->settings->no_results_message . '</p>';

		if ( $this->settings->show_search ) {
			get_search_form();
		}

		echo '</div>';
	}

	/**
	 * Renders the excerpt for a post.
	 *
	 * @since 1.10
	 * @return void
	 */
	public function set_custom_excerpt_length( $length ) {
		return $this->settings->content_length;
	}

	/**
	 * Get the terms for the current post.
	 *
	 * @since 1.10.8
	 * @return string|null
	 */
	public function get_post_terms() {
		$post_type       = get_post_type();
		$taxonomies      = get_object_taxonomies( $post_type, 'objects' );
		$terms_list      = array();
		$terms_separator = '<span class="fl-sep-term">' . $this->settings->terms_separator . '</span>';

		if ( ! $taxonomies || empty( $taxonomies ) ) {
			return;
		}

		foreach ( $taxonomies as $name => $tax ) {
			if ( ! $tax->hierarchical ) {
				continue;
			}

			$term_list = get_the_term_list( get_the_ID(), $name, '', $terms_separator, '' );
			if ( ! empty( $term_list ) ) {
				$terms_list[] = $term_list;
			}
		}

		if ( count( $terms_list ) > 0 ) {
			return join( $terms_separator, $terms_list );
		}
	}

	/**
	 * prints schema if enabled.
	 * @since 2.2.2
	 */
	static public function print_schema( $schema ) {

		if ( self::schema_enabled() ) {
			echo $schema;
		}
	}

	/**
	 * Renders the schema itemtype for the collection
	 *
	 * @since 2.2.5
	 * @return string
	 */
	static public function schema_collection_type( $data_source = 'custom_query', $post_type = 'post' ) {
		$schema = '';

		if ( ! self::schema_enabled() ) {
			return $schema;
		}

		if ( is_archive() && 'main_query' === $data_source ) {
			$schema = is_post_type_archive( 'post' ) ? 'https://schema.org/Blog' : 'https://schema.org/Collection';
		} else {
			$schema = ( 'post' === $post_type ) ? 'https://schema.org/Blog' : 'https://schema.org/Collection';
		}

		return $schema;
	}

	/**
	 * Is schema enabled
	 * @since 2.2.2
	 */
	static public function schema_enabled() {

		/**
		 * Disable all post-grid schema markup
		 * @see fl_post_grid_disable_schema
		 */
		if ( false !== apply_filters( 'fl_post_grid_disable_schema', false ) ) {
			return false;
		} else {
			return FLBuilder::is_schema_enabled();
		}
	}

	/**
	 * Renders the schema structured data for the current
	 * post in the loop.
	 *
	 * @since 1.7.4
	 * @return void
	 */
	static public function schema_meta() {

		/**
		 * Disable all post-grid schema markup
		 * @see fl_post_grid_disable_schema
		 */
		if ( ! self::schema_enabled() ) {
			return false;
		}
		/**
		 * Before schema meta
		 * @see fl_before_schema_meta
		 */
		do_action( 'fl_before_schema_meta' );

		// General Schema Meta
		ob_start();
		echo '<meta itemscope itemprop="mainEntityOfPage" itemtype="https://schema.org/WebPage" itemid="' . esc_url( get_permalink() ) . '" content="' . the_title_attribute( array(
			'echo' => false,
		) ) . '" />';
		echo '<meta itemprop="datePublished" content="' . get_the_time( 'Y-m-d' ) . '" />';
		echo '<meta itemprop="dateModified" content="' . get_the_modified_date( 'Y-m-d' ) . '" />';

		/**
		 * General meta
		 * @see fl_schema_meta_general
		 */
		echo apply_filters( 'fl_schema_meta_general', ob_get_clean() );

		// Publisher Schema Meta
		ob_start();
		echo '<div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">';
		echo '<meta itemprop="name" content="' . get_bloginfo( 'name' ) . '">';

		// Fetch logo from theme or filter.
		$image = '';
		if ( class_exists( 'FLTheme' ) && 'image' == FLTheme::get_setting( 'fl-logo-type' ) ) {
			$image = FLTheme::get_setting( 'fl-logo-image' );
		} elseif ( has_custom_logo() ) {
			$custom_logo_id = get_theme_mod( 'custom_logo' );
			$logo           = wp_get_attachment_image_src( $custom_logo_id, 'full' );
			$image          = $logo[0];
		}

		/**
		 * Publisher image url.
		 * @see fl_schema_meta_publisher_image_url
		 */
		$image = apply_filters( 'fl_schema_meta_publisher_image_url', $image );
		if ( $image ) {
			echo '<div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">';
			echo '<meta itemprop="url" content="' . $image . '">';
			echo '</div>';
		}

		echo '</div>';
		/**
		 * Publisher meta.
		 * @see fl_schema_meta_publisher
		 */
		echo apply_filters( 'fl_schema_meta_publisher', ob_get_clean() );

		// Author Schema Meta
		ob_start();
		echo '<div itemscope itemprop="author" itemtype="https://schema.org/Person">';
		echo '<meta itemprop="url" content="' . get_author_posts_url( get_the_author_meta( 'ID' ) ) . '" />';
		echo '<meta itemprop="name" content="' . get_the_author_meta( 'display_name', get_the_author_meta( 'ID' ) ) . '" />';
		echo '</div>';
		/**
		 * Author meta.
		 * @see fl_schema_meta_author
		 */
		echo apply_filters( 'fl_schema_meta_author', ob_get_clean() );

		// Image Schema Meta
		if ( has_post_thumbnail() ) {

			$image = wp_get_attachment_image_src( get_post_thumbnail_id( get_the_ID() ), 'full' );
			if ( is_array( $image ) ) {
				ob_start();
				echo '<div itemscope itemprop="image" itemtype="https://schema.org/ImageObject">';
				echo '<meta itemprop="url" content="' . $image[0] . '" />';
				echo '<meta itemprop="width" content="' . $image[1] . '" />';
				echo '<meta itemprop="height" content="' . $image[2] . '" />';
				echo '</div>';
				/**
				 * Image meta.
				 * @see fl_schema_meta_thumbnail
				 */
				echo apply_filters( 'fl_schema_meta_thumbnail', ob_get_clean() );
			}
		}

		// Comment Schema Meta
		ob_start();
		echo '<div itemprop="interactionStatistic" itemscope itemtype="https://schema.org/InteractionCounter">';
		echo '<meta itemprop="interactionType" content="https://schema.org/CommentAction" />';
		echo '<meta itemprop="userInteractionCount" content="' . wp_count_comments( get_the_ID() )->approved . '" />';
		echo '</div>';
		/**
		 * Comments meta
		 * @see fl_schema_meta_comments
		 */
		echo apply_filters( 'fl_schema_meta_comments', ob_get_clean() );

		/**
		 * After schema meta.
		 * @see fl_after_schema_meta
		 */
		do_action( 'fl_after_schema_meta' );
	}

	/**
	 * Renders the schema itemtype for the current
	 * post in the loop.
	 *
	 * @since 1.7.4
	 * @return void
	 */
	static public function schema_itemtype() {
		global $post;

		if ( ! self::schema_enabled() ) {
			return false;
		}

		$schema = 'https://schema.org/BlogPosting';
		if ( ! is_object( $post ) || ! isset( $post->post_type ) || 'post' != $post->post_type ) {
			$schema = 'https://schema.org/CreativeWork';
		}

		return $schema;
	}

	/**
	 * Returns an array of settings used to render a button module.
	 *
	 * @since 2.2
	 * @return array
	 */
	public function get_button_settings() {
		$settings = array(
			'align'       => 'center',
			'link'        => '#',
			'link_target' => '_self',
		);

		foreach ( $this->settings as $key => $value ) {
			if ( strstr( $key, 'more_btn_' ) ) {
				$key              = str_replace( 'more_btn_', '', $key );
				$settings[ $key ] = $value;
			}
		}

		return $settings;
	}

	public function get_posts_container() {
		return $this->settings->posts_container;
	}
}

/**
 * Register the module and its form settings.
 */
FLBuilder::register_module('FLPostGridModule', array(
	'layout'     => array(
		'title'    => __( 'Layout', 'fl-builder' ),
		'sections' => array(
			'general' => array(
				'title'  => '',
				'fields' => array(
					'layout' => array(
						'type'    => 'select',
						'label'   => __( 'Layout', 'fl-builder' ),
						'default' => 'grid',
						'options' => array(
							'columns' => __( 'Columns', 'fl-builder' ),
							'grid'    => __( 'Masonry', 'fl-builder' ),
							'gallery' => __( 'Gallery', 'fl-builder' ),
							'feed'    => __( 'List', 'fl-builder' ),
						),
						'toggle'  => array(
							'columns' => array(
								'sections' => array( 'posts', 'image', 'content', 'terms', 'post_style', 'text_style' ),
								'fields'   => array( 'match_height', 'post_columns', 'post_spacing', 'post_padding', 'image', 'grid_image_position', 'grid_image_spacing', 'show_author', 'show_comments_grid', 'info_separator', 'image_size', 'image_fallback', 'show_image' ),
							),
							'grid'    => array(
								'sections' => array( 'posts', 'image', 'content', 'terms', 'post_style', 'text_style' ),
								'fields'   => array( 'match_height', 'post_width', 'post_spacing', 'post_padding', 'grid_image_position', 'grid_image_spacing', 'show_author', 'show_comments_grid', 'info_separator', 'image_fallback', 'image_size', 'show_image' ),
							),
							'gallery' => array(
								'sections' => array( 'gallery_general', 'overlay_style', 'icons', 'image' ),
								'fields'   => array( 'image_fallback' ),
							),
							'feed'    => array(
								'sections' => array( 'posts', 'image', 'content', 'terms', 'post_style', 'text_style' ),
								'fields'   => array( 'feed_post_spacing', 'feed_post_padding', 'image_position', 'image_spacing', 'image_width', 'show_author', 'show_comments', 'info_separator', 'content_type', 'image_fallback', 'image_size', 'show_image' ),
							),
						),
					),
				),
			),
			'posts'   => array(
				'title'  => __( 'Posts', 'fl-builder' ),
				'fields' => array(
					'match_height'             => array(
						'type'       => 'select',
						'label'      => __( 'Equal Heights', 'fl-builder' ),
						'default'    => '0',
						'options'    => array(
							'1' => __( 'Yes', 'fl-builder' ),
							'0' => __( 'No', 'fl-builder' ),
						),
						'responsive' => true,
					),
					'post_width'               => array(
						'type'     => 'unit',
						'label'    => __( 'Post Width', 'fl-builder' ),
						'default'  => '300',
						'sanitize' => 'floatval',
						'units'    => array( 'px' ),
						'slider'   => array(
							'max'  => 500,
							'step' => 10,
						),
					),
					'post_columns'             => array(
						'type'       => 'unit',
						'label'      => __( 'Columns', 'fl-builder' ),
						'responsive' => array(
							'default' => array(
								'default'    => '3',
								'medium'     => '2',
								'responsive' => '1',
							),
						),
					),
					'post_spacing'             => array(
						'type'     => 'unit',
						'label'    => __( 'Post Spacing', 'fl-builder' ),
						'default'  => '60',
						'units'    => array( 'px' ),
						'slider'   => true,
						'sanitize' => 'floatval',
					),
					'feed_post_spacing'        => array(
						'type'    => 'unit',
						'label'   => __( 'Post Spacing', 'fl-builder' ),
						'default' => '40',
						'units'   => array( 'px' ),
						'slider'  => true,
						'preview' => array(
							'type'     => 'css',
							'selector' => '.fl-post-feed-post',
							'property' => 'margin-bottom',
							'unit'     => 'px',
						),
					),
					'post_padding'             => array(
						'type'    => 'unit',
						'label'   => __( 'Post Padding', 'fl-builder' ),
						'default' => '20',
						'units'   => array( 'px' ),
						'slider'  => true,
						'preview' => array(
							'type'     => 'css',
							'selector' => '.fl-post-grid-text',
							'property' => 'padding',
							'unit'     => 'px',
						),
					),
					'feed_post_padding'        => array(
						'type'    => 'unit',
						'label'   => __( 'Post Padding', 'fl-builder' ),
						'default' => '0',
						'units'   => array( 'px' ),
						'slider'  => true,
					),
					'posts_container'          => array(
						'type'    => 'select',
						'label'   => __( 'Posts Element', 'fl-builder' ),
						'default' => 'div',
						'options' => array(
							'div'     => '&lt;div&gt;',
							'article' => '&lt;article&gt;',
							'li'      => '&lt;li&gt;',
						),
						'help'    => __( 'Optional. Choose an appropriate HTML5 content sectioning element to use for each post to improve accessibility and machine-readability.', 'fl-builder' ),
						'toggle'  => array(
							'li' => array(
								'fields' => array( 'posts_container_ul_class' ),
							),
						),
					),
					'posts_container_class'    => array(
						'type'    => 'text',
						'label'   => __( 'Posts Element Class', 'fl-builder' ),
						'default' => '',
					),
					'posts_container_ul_class' => array(
						'type'    => 'text',
						'label'   => __( 'Posts Element Class for UL', 'fl-builder' ),
						'default' => '',
					),
					'posts_title_tag'          => array(
						'type'    => 'select',
						'label'   => __( 'Posts Title Tag', 'fl-builder' ),
						'default' => 'h2',
						'options' => array(
							'h1' => '&lt;h1&gt;',
							'h2' => '&lt;h2&gt;',
							'h3' => '&lt;h3&gt;',
							'h4' => '&lt;h4&gt;',
							'h5' => '&lt;h5&gt;',
							'h6' => '&lt;h6&gt;',
						),
						'help'    => __( 'Optional. Choose an appropriate Heading Tag for each Post Title.', 'fl-builder' ),
					),
				),

			),
			'image'   => array(
				'title'  => __( 'Featured Image', 'fl-builder' ),
				'fields' => array(
					'show_image'          => array(
						'type'    => 'select',
						'label'   => __( 'Image', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
					),
					'grid_image_position' => array(
						'type'    => 'select',
						'label'   => __( 'Image Position', 'fl-builder' ),
						'default' => 'above-title',
						'options' => array(
							'above-title' => __( 'Above Title', 'fl-builder' ),
							'above'       => __( 'Above Content', 'fl-builder' ),
						),
					),
					'image_position'      => array(
						'type'    => 'select',
						'label'   => __( 'Image Position', 'fl-builder' ),
						'default' => 'above',
						'options' => array(
							'above-title'          => __( 'Above Title', 'fl-builder' ),
							'above'                => __( 'Above Content', 'fl-builder' ),
							'beside'               => __( 'Left', 'fl-builder' ),
							'beside-content'       => __( 'Left Content', 'fl-builder' ),
							'beside-right'         => __( 'Right', 'fl-builder' ),
							'beside-content-right' => __( 'Right Content', 'fl-builder' ),
						),
						'toggle'  => array(
							'beside'               => array(
								'fields' => array( 'image_width' ),
							),
							'beside-content'       => array(
								'fields' => array( 'image_width' ),
							),
							'beside-right'         => array(
								'fields' => array( 'image_width' ),
							),
							'beside-content-right' => array(
								'fields' => array( 'image_width' ),
							),
						),
					),
					'image_size'          => array(
						'type'    => 'photo-sizes',
						'label'   => __( 'Image Size', 'fl-builder' ),
						'default' => 'medium',
					),
					'grid_image_spacing'  => array(
						'type'    => 'unit',
						'label'   => __( 'Image Spacing', 'fl-builder' ),
						'default' => '0',
						'units'   => array( 'px' ),
						'slider'  => true,
					),
					'image_spacing'       => array(
						'type'    => 'unit',
						'label'   => __( 'Image Spacing', 'fl-builder' ),
						'default' => '0',
						'units'   => array( 'px' ),
						'slider'  => true,
					),
					'image_width'         => array(
						'type'     => 'unit',
						'label'    => __( 'Image Width', 'fl-builder' ),
						'default'  => '33',
						'sanitize' => 'floatval',
						'units'    => array( '%' ),
						'slider'   => true,
					),
					'image_fallback'      => array(
						'default'     => '',
						'type'        => 'photo',
						'show_remove' => true,
						'label'       => __( 'Fallback Image', 'fl-builder' ),
					),
				),
			),
			'info'    => array(
				'title'  => __( 'Post Info', 'fl-builder' ),
				'fields' => array(
					'show_author'        => array(
						'type'    => 'select',
						'label'   => __( 'Author', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
					),
					'show_date'          => array(
						'type'    => 'select',
						'label'   => __( 'Date', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'date_format' ),
							),
						),
					),
					'date_format'        => array(
						'type'    => 'select',
						'label'   => __( 'Date Format', 'fl-builder' ),
						'default' => 'default',
						'options' => array(
							'default' => __( 'Default', 'fl-builder' ),
							'M j, Y'  => gmdate( 'M j, Y' ),
							'F j, Y'  => gmdate( 'F j, Y' ),
							'm/d/Y'   => gmdate( 'm/d/Y' ),
							'm-d-Y'   => gmdate( 'm-d-Y' ),
							'd M Y'   => gmdate( 'd M Y' ),
							'd F Y'   => gmdate( 'd F Y' ),
							'Y-m-d'   => gmdate( 'Y-m-d' ),
							'Y/m/d'   => gmdate( 'Y/m/d' ),
						),
					),
					'show_comments'      => array(
						'type'    => 'select',
						'label'   => __( 'Comments', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
					),
					'show_comments_grid' => array(
						'type'    => 'select',
						'label'   => __( 'Comments', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
					),
					'info_separator'     => array(
						'type'          => 'text',
						'label'         => __( 'Separator', 'fl-builder' ),
						'default'       => ' | ',
						'size'          => '4',
						'inline_editor' => false,
						'preview'       => array(
							'type'     => 'text',
							'selector' => '.fl-sep',
						),
					),
				),
			),
			'terms'   => array(
				'title'  => __( 'Post Terms', 'fl-builder' ),
				'fields' => array(
					'show_terms'       => array(
						'type'    => 'select',
						'label'   => __( 'Terms', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'terms_separator', 'terms_list_label' ),
							),
						),
					),
					'terms_list_label' => array(
						'type'    => 'text',
						'label'   => __( 'Terms Label', 'fl-builder' ),
						'default' => __( 'Posted in ', 'fl-builder' ),
						'preview' => array(
							'type'     => 'text',
							'selector' => '.fl-terms-label',
						),
					),
					'terms_separator'  => array(
						'type'          => 'text',
						'label'         => __( 'Terms Separator', 'fl-builder' ),
						'default'       => ', ',
						'size'          => '4',
						'inline_editor' => false,
						'preview'       => array(
							'type'     => 'text',
							'selector' => '.fl-sep-term',
						),
					),
				),
			),
			'content' => array(
				'title'  => __( 'Post Content', 'fl-builder' ),
				'fields' => array(
					'show_content'   => array(
						'type'    => 'select',
						'label'   => __( 'Content', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
					),
					'content_type'   => array(
						'type'    => 'select',
						'label'   => __( 'Content Type', 'fl-builder' ),
						'default' => 'excerpt',
						'options' => array(
							'excerpt' => __( 'Excerpt', 'fl-builder' ),
							'full'    => __( 'Full Text', 'fl-builder' ),
						),
						'toggle'  => array(
							'excerpt' => array(
								'fields' => array( 'content_length' ),
							),
						),
					),
					'content_length' => array(
						'type'    => 'unit',
						'label'   => __( 'Content Length', 'fl-builder' ),
						'default' => '',
						'units'   => array( 'words' ),
						'slider'  => array(
							'min'  => 0,
							'max'  => 1000,
							'step' => 1,
						),
					),
					'show_more_link' => array(
						'type'    => 'select',
						'label'   => __( 'More Link', 'fl-builder' ),
						'default' => '0',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'toggle'  => array(
							'1' => array(
								'fields' => array( 'more_link_text' ),
							),
						),
					),
					'more_link_text' => array(
						'type'    => 'text',
						'label'   => __( 'More Link Text', 'fl-builder' ),
						'default' => __( 'Read More', 'fl-builder' ),
					),
				),
			),
		),
	),
	'style'      => array(
		'title'    => __( 'Style', 'fl-builder' ),
		'sections' => array(
			'post_style'      => array(
				'title'  => __( 'Posts', 'fl-builder' ),
				'fields' => array(
					'post_align' => array(
						'type'    => 'align',
						'label'   => __( 'Post Alignment', 'fl-builder' ),
						'default' => '',
						'preview' => array(
							'type'     => 'css',
							'property' => 'text-align',
							'selector' => '.fl-post-grid-post, .fl-post-feed-post',
						),
					),
					'bg_color'   => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Post Background Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-post-feed-post, .fl-post-grid-post',
							'property' => 'background-color',
						),
					),
					'border'     => array(
						'type'       => 'border',
						'label'      => __( 'Border', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => '.fl-post-feed-post, .fl-post-grid-post',
						),
					),
				),
			),
			'text_style'      => array(
				'title'  => __( 'Text', 'fl-builder' ),
				'fields' => array(
					'title_color'        => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Title Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-post-feed-title a, .fl-post-grid-title a',
							'property' => 'color',
						),
					),
					'title_typography'   => array(
						'type'       => 'typography',
						'label'      => __( 'Title Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-post-feed-title, .fl-post-grid-title',
							'important' => true,
						),
					),
					'info_color'         => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Post Info Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => '{node} .fl-post-feed .fl-post-feed-header .fl-post-feed-meta, {node} .fl-post-feed .fl-post-feed-header .fl-post-feed-meta span, {node} .fl-post-feed .fl-post-feed-header .fl-post-feed-meta a, {node} .fl-post-feed .fl-post-feed-header .fl-post-feed-meta-terms span, {node} .fl-post-feed .fl-post-feed-header .fl-post-feed-meta-terms a, {node} .fl-post-grid-meta, {node} .fl-post-grid-meta span, {node} .fl-post-grid-meta a, {node} .fl-post-grid-meta-terms span, {node} .fl-post-grid-meta-terms a',
							'property'  => 'color',
							'important' => true,
						),
					),
					'info_typography'    => array(
						'type'       => 'typography',
						'label'      => __( 'Post Info Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-post-feed-meta, .fl-post-feed-meta a, .fl-post-grid-meta, .fl-post-grid-meta a',
							'important' => true,
						),
					),
					'content_color'      => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Content Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => '{node} .fl-post-feed-content, {node} .fl-post-feed-content p, {node} .fl-post-feed-content a, {node} .fl-post-grid-content, {node} .fl-post-grid-content p, {node} .fl-post-grid-content a, {node} .fl-builder-pagination ul.page-numbers li span, {node} .fl-builder-pagination ul.page-numbers li a',
							'property'  => 'color',
							'important' => true,
						),
					),
					'content_typography' => array(
						'type'       => 'typography',
						'label'      => __( 'Content Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-post-feed-content, .fl-post-feed-content p, .fl-post-grid-content, .fl-post-grid-content p',
							'important' => true,
						),
					),
					'link_color'         => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-post-feed-content a, .fl-post-grid-content a',
							'property' => 'color',
						),
					),
					'link_hover_color'   => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Link Hover Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-post-feed-content a:hover, .fl-post-grid-content a:hover',
							'property' => 'color',
						),
					),
				),
			),
			'gallery_general' => array(
				'title'  => '',
				'fields' => array(
					'hover_transition' => array(
						'type'    => 'select',
						'label'   => __( 'Hover Transition', 'fl-builder' ),
						'default' => 'fade',
						'options' => array(
							'fade'       => __( 'Fade', 'fl-builder' ),
							'slide-up'   => __( 'Slide Up', 'fl-builder' ),
							'slide-down' => __( 'Slide Down', 'fl-builder' ),
							'scale-up'   => __( 'Scale Up', 'fl-builder' ),
							'scale-down' => __( 'Scale Down', 'fl-builder' ),
						),
					),
				),
			),
			'overlay_style'   => array(
				'title'  => __( 'Overlay Colors', 'fl-builder' ),
				'fields' => array(
					'text_color'    => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Overlay Text Color', 'fl-builder' ),
						'default'     => 'ffffff',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-post-gallery-link, .fl-post-gallery-link .fl-post-gallery-title',
							'property' => 'color',
						),
					),
					'text_bg_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Overlay Background Color', 'fl-builder' ),
						'default'     => '333333',
						'help'        => __( 'The color applies to the overlay behind text over the background selections.', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-post-gallery-text-wrap',
							'property' => 'background-color',
						),
					),
				),
			),
			'icons'           => array(
				'title'  => __( 'Icons', 'fl-builder' ),
				'fields' => array(
					'has_icon'      => array(
						'type'    => 'select',
						'label'   => __( 'Use Icon for Posts', 'fl-builder' ),
						'default' => 'no',
						'options' => array(
							'yes' => __( 'Yes', 'fl-builder' ),
							'no'  => __( 'No', 'fl-builder' ),
						),
						'toggle'  => array(
							'yes' => array(
								'fields' => array( 'icon', 'icon_position', 'icon_color', 'icon_size' ),
							),
						),
					),
					'icon'          => array(
						'type'  => 'icon',
						'label' => __( 'Post Icon', 'fl-builder' ),
					),
					'duo_color1'    => array(
						'label'      => __( 'DuoTone Icon Primary Color', 'fl-builder' ),
						'type'       => 'color',
						'default'    => '',
						'show_reset' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-accordion-button-icon i.fad:before',
							'property'  => 'color',
							'important' => true,
						),
					),
					'duo_color2'    => array(
						'label'      => __( 'DuoTone Icon Secondary Color', 'fl-builder' ),
						'type'       => 'color',
						'default'    => '',
						'show_reset' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => '.fl-accordion-button-icon i.fad:after',
							'property'  => 'color',
							'important' => true,
						),
					),
					'icon_position' => array(
						'type'    => 'select',
						'label'   => __( 'Post Icon Position', 'fl-builder' ),
						'default' => 'above',
						'options' => array(
							'above' => __( 'Above Text', 'fl-builder' ),
							'below' => __( 'Below Text', 'fl-builder' ),
						),
					),
					'icon_color'    => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Post Icon Color', 'fl-builder' ),
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'     => 'css',
							'selector' => '.fl-post-gallery .fl-gallery-icon i, .fl-post-gallery .fl-gallery-icon i:before',
							'property' => 'color',
						),
					),
					'icon_size'     => array(
						'type'    => 'unit',
						'label'   => __( 'Post Icon Size', 'fl-builder' ),
						'default' => '24',
						'units'   => array( 'px' ),
						'slider'  => true,
					),
				),
			),
		),
	),
	'content'    => array(
		'title' => __( 'Content', 'fl-builder' ),
		'file'  => FL_BUILDER_DIR . 'includes/loop-settings.php',
	),
	'pagination' => array(
		'title'    => __( 'Pagination', 'fl-builder' ),
		'sections' => array(
			'pagination'       => array(
				'title'  => __( 'Pagination', 'fl-builder' ),
				'fields' => array(
					'pagination'         => array(
						'type'    => 'select',
						'label'   => __( 'Pagination Style', 'fl-builder' ),
						'default' => 'numbers',
						'options' => array(
							'numbers'   => __( 'Numbers', 'fl-builder' ),
							'scroll'    => __( 'Scroll', 'fl-builder' ),
							'load_more' => __( 'Load More Button', 'fl-builder' ),
							'none'      => _x( 'None', 'Pagination style.', 'fl-builder' ),
						),
						'toggle'  => array(
							'load_more' => array(
								'sections' => array( 'more_btn_general', 'more_btn_icon', 'more_btn_style', 'more_btn_text', 'more_btn_colors', 'more_btn_border' ),
							),
						),
					),
					'posts_per_page'     => array(
						'type'    => 'text',
						'label'   => __( 'Posts Per Page', 'fl-builder' ),
						'default' => '10',
						'size'    => '4',
					),
					'no_results_message' => array(
						'type'    => 'textarea',
						'label'   => __( 'No Results Message', 'fl-builder' ),
						'default' => __( "Sorry, we couldn't find any posts. Please try a different search.", 'fl-builder' ),
						'rows'    => 6,
					),
					'show_search'        => array(
						'type'    => 'select',
						'label'   => __( 'Show Search', 'fl-builder' ),
						'default' => '1',
						'options' => array(
							'1' => __( 'Show', 'fl-builder' ),
							'0' => __( 'Hide', 'fl-builder' ),
						),
						'help'    => __( 'Shows the search form if no posts are found.', 'fl-builder' ),
					),
				),
			),
			'more_btn_general' => array(
				'title'  => __( 'Load More Button', 'fl-builder' ),
				'fields' => array(
					'more_btn_text' => array(
						'type'    => 'text',
						'label'   => __( 'Button Text', 'fl-builder' ),
						'default' => __( 'Load More', 'fl-builder' ),
					),
				),
			),
			'more_btn_icon'    => array(
				'title'  => __( 'Button Icon', 'fl-builder' ),
				'fields' => array(
					'more_btn_icon'           => array(
						'type'        => 'icon',
						'label'       => __( 'Button Icon', 'fl-builder' ),
						'show_remove' => true,
						'show'        => array(
							'fields' => array( 'more_btn_icon_position', 'more_btn_icon_animation' ),
						),
					),
					'more_btn_icon_position'  => array(
						'type'    => 'select',
						'label'   => __( 'Button Icon Position', 'fl-builder' ),
						'default' => 'before',
						'options' => array(
							'before' => __( 'Before Text', 'fl-builder' ),
							'after'  => __( 'After Text', 'fl-builder' ),
						),
					),
					'more_btn_icon_animation' => array(
						'type'    => 'select',
						'label'   => __( 'Button Icon Visibility', 'fl-builder' ),
						'default' => 'disable',
						'options' => array(
							'disable' => __( 'Always Visible', 'fl-builder' ),
							'enable'  => __( 'Fade In On Hover', 'fl-builder' ),
						),
					),
				),
			),
			'more_btn_style'   => array(
				'title'  => __( 'Button Style', 'fl-builder' ),
				'fields' => array(
					'more_btn_width'   => array(
						'type'    => 'select',
						'label'   => __( 'Button Width', 'fl-builder' ),
						'default' => 'auto',
						'options' => array(
							'auto' => _x( 'Auto', 'Width.', 'fl-builder' ),
							'full' => __( 'Full Width', 'fl-builder' ),
						),
					),
					'more_btn_padding' => array(
						'type'       => 'dimension',
						'label'      => __( 'Button Padding', 'fl-builder' ),
						'responsive' => true,
						'slider'     => true,
						'units'      => array( 'px' ),
						'preview'    => array(
							'type'     => 'css',
							'selector' => 'a.fl-button',
							'property' => 'padding',
						),
					),
				),
			),
			'more_btn_text'    => array(
				'title'  => __( 'Button Text', 'fl-builder' ),
				'fields' => array(
					'more_btn_text_color'       => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Text Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => 'a.fl-button, a.fl-button *',
							'property'  => 'color',
							'important' => true,
						),
					),
					'more_btn_text_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Text Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type'      => 'css',
							'selector'  => 'a.fl-button:hover, a.fl-button:hover *, a.fl-button:focus, a.fl-button:focus *',
							'property'  => 'color',
							'important' => true,
						),
					),
					'more_btn_typography'       => array(
						'type'       => 'typography',
						'label'      => __( 'Button Typography', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'     => 'css',
							'selector' => 'a.fl-button',
						),
					),
				),
			),
			'more_btn_colors'  => array(
				'title'  => __( 'Button Background', 'fl-builder' ),
				'fields' => array(
					'more_btn_bg_color'          => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Background Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'more_btn_bg_hover_color'    => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Background Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
					'more_btn_style'             => array(
						'type'    => 'select',
						'label'   => __( 'Button Background Style', 'fl-builder' ),
						'default' => 'flat',
						'options' => array(
							'flat'     => __( 'Flat', 'fl-builder' ),
							'gradient' => __( 'Gradient', 'fl-builder' ),
						),
					),
					'more_btn_button_transition' => array(
						'type'    => 'select',
						'label'   => __( 'Button Background Animation', 'fl-builder' ),
						'default' => 'disable',
						'options' => array(
							'disable' => __( 'Disabled', 'fl-builder' ),
							'enable'  => __( 'Enabled', 'fl-builder' ),
						),
						'preview' => array(
							'type' => 'none',
						),
					),
				),
			),
			'more_btn_border'  => array(
				'title'  => __( 'Button Border', 'fl-builder' ),
				'fields' => array(
					'more_btn_border'             => array(
						'type'       => 'border',
						'label'      => __( 'Button Border', 'fl-builder' ),
						'responsive' => true,
						'preview'    => array(
							'type'      => 'css',
							'selector'  => 'a.fl-button',
							'important' => true,
						),
					),
					'more_btn_border_hover_color' => array(
						'type'        => 'color',
						'connections' => array( 'color' ),
						'label'       => __( 'Button Border Hover Color', 'fl-builder' ),
						'default'     => '',
						'show_reset'  => true,
						'show_alpha'  => true,
						'preview'     => array(
							'type' => 'none',
						),
					),
				),
			),
		),
	),
));
