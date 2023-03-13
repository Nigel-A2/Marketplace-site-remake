<?php
/**
 * @since 4.0
 */
class WPBDP__WordPress_Template_Integration {

    private $displayed = false;

	/**
	 * @var int $post_id
	 */
	private $post_id = 0;

    public function __construct() {
        add_action( 'body_class', array( $this, 'add_basic_body_classes' ) );
        add_filter( 'body_class', array( &$this, 'add_advanced_body_classes' ), 10 );

        if ( wpbdp_get_option( 'disable-cpt' ) ) {
			add_filter( 'comments_template', array( &$this, '_comments_template' ) );
			add_filter( 'taxonomy_template', array( &$this, '_category_template' ) );
			add_filter( 'single_template', array( &$this, '_single_template' ) );

            return;
        }

        add_filter( 'template_include', array( $this, 'template_include' ), 20 );
        add_filter( 'post_class', array( $this, 'post_class' ), 10, 3 );
		$this->remove_theme_thumbnail();
    }

    public function template_include( $template ) {
        global $wp_query;

        if ( ! $wp_query->wpbdp_our_query )
            return $template;

        if ( is_404() )
            return get_404_template();

        global $post;
        if ( empty( $wp_query->wpbdp_view ) && ( ! isset( $post ) || ! $post instanceof WP_Post ) )
            return $template;

		$allow_override = apply_filters( 'wpbdp_allow_template_override', true );

		/**
		 * Some themes work better without using the page template.
		 *
		 * @since 6.2.7
		 */
		if ( $wp_query->is_tax() && apply_filters( 'wpbdp_use_single', false ) ) {
			// Force some themes to use the page template.
			$wp_query->is_singular = true;

			// Prevent a PHP error when WP gets confused.
			add_filter( 'pre_get_shortlink', '__return_empty_string' );
		}

		if ( $allow_override ) {
			add_action( 'loop_start', array( $this, 'setup_post_hooks' ) );
			$page_template = get_query_template( 'page', $this->get_template_alternatives() );
			if ( $page_template ) {
				$template = $page_template;
			}
		}

        return $template;
    }

    private function get_template_alternatives() {
        $templates = array( 'page.php', 'single.php', 'singular.php' );

        $main_page_id = wpbdp_get_page_id( 'main' );

        if ( ! $main_page_id ) {
            return $templates;
        }

        $main_page_template = get_page_template_slug( $main_page_id );

        if ( $main_page_template ) {
            array_unshift( $templates, $main_page_template );
        }

        return $templates;
    }

    public function setup_post_hooks( $query ) {
		if ( ! $query->is_main_query() ) {
			return;
		}

		if ( $query->is_tax() ) {
			$this->prep_tax_head();
		}

		// Run last so other hooks don't break our output.
		add_filter( 'the_content', array( $this, 'display_view_in_content' ), 999 );
        remove_action( 'loop_start', array( $this, 'setup_post_hooks' ) );
    }

	/**
	 * Prevent a listing title from being used as the category title.
	 *
	 * @since 6.2.5
	 */
	public function prep_tax_head() {
		add_filter( 'the_title', array( &$this, 'set_tax_title' ) );
		add_filter( 'post_thumbnail_html', array( &$this, 'remove_tax_thumbnail' ) );
	}

	/**
	 * Since the category page thinks it's a normal post, override the global post.
	 * This would be better to change the category output, rather than using a "page".
	 * See WPBDP__Dispatcher.
	 *
	 * @since 6.2.3
	 * @return string
	 */
	public function set_tax_title( $title ) {
		if ( $this->in_the_loop() ) {
			// If this is not a category, don't change it.
			return $title;
		}

		remove_filter( 'the_title', array( &$this, 'set_tax_title' ) );
		$term = get_queried_object();
		return is_object( $term ) ? $term->name : $title;
	}

	/**
	 * Prevent a post thumbnail from showing on the page before the loop.
	 *
	 * @since 6.2.3
	 * @return string
	 */
	public function remove_tax_thumbnail( $thumbnail ) {
		remove_filter( 'post_thumbnail_html', array( &$this, 'remove_tax_thumbnail' ) );

		if ( $this->in_the_loop() ) {
			return $thumbnail;
		}

		// The caption shows in 2021 theme.
		add_filter( 'wp_get_attachment_caption', '__return_false' );

		return '';
	}

	/**
	 * Some themes run the taxonomy title in the loop too.
	 * Check our custom loop flag.
	 *
	 * @since 6.2.6
	 * @return bool
	 */
	private function in_the_loop() {
		global $wp_query;
		return $wp_query->wpbdp_in_the_loop;
	}

    public function display_view_in_content( $content = '' ) {
		remove_filter( 'the_content', array( $this, 'display_view_in_content' ), 999 );
        if ( $this->displayed ) {
            return '';
        }

        $html = wpbdp_current_view_output();
		$this->after_content_processed( $html );

		if ( is_tax() ) {
			$this->end_query();
		}

        $this->displayed = true;

        return $html;
    }

	/**
	 * Allow themes and plugins to override the final content when needed.
	 *
	 * @since 6.2.7
	 */
	private function after_content_processed( &$content ) {
		if ( class_exists( 'Elementor\Plugin' ) ) {
			$content = Elementor\Plugin::$instance->frontend->apply_builder_in_content( $content );
		}

		/**
		 * @since 6.2.7
		 */
		$content = apply_filters( 'wpbdp_the_content', $content );
	}

    public function add_basic_body_classes( $classes = array() ) {
		if ( 'theme' === wpbdp_get_option( 'themes-button-style' ) ) {
            $classes[] = 'wpbdp-with-button-styles';
        }

        return $classes;
    }

    public function add_advanced_body_classes( $classes = array() ) {
        global $wpbdp;

        // FIXME: we need a better way to handle this, since it might be that a shortcode is being used and not something
        // really dispatched through BD.
        $view = wpbdp_current_view();

        if ( ! $view )
            return $classes;

        $classes[] = 'business-directory';
        $classes[] = 'wpbdp-view-' . $view;

		$theme = wp_get_theme();
		$classes[] = 'wpbdp-wp-theme-' . $theme->get_stylesheet();
		$classes[] = 'wpbdp-wp-theme-' . $theme->get_template();

        if ( wpbdp_is_taxonomy() ) {
            $classes[] = 'wpbdp-view-taxonomy';
        }

        $classes[] = 'wpbdp-theme-' . $wpbdp->themes->get_active_theme();

        return $classes;
    }

    public function post_class( $classes, $more_classes, $post_id ) {
        if ( ! wpbdp_current_view() ) {
            return $classes;
        }

        $post = get_post();

        if ( $post && 0 == $post->ID && $post_id == $post->ID ) {
            $classes[] = 'wpbdp-view-content-wrapper';
        }

        return $classes;
    }

	/**
	 * @since 6.2.1
	 */
	private function remove_theme_thumbnail() {
		add_action( 'loop_start', array( &$this, 'set_thumbnail_visibility' ) );

		// Support for themes that render the post-featured-image before loop_start.
		add_filter( 'render_block_core/post-featured-image', array( &$this, 'remove_featured_image_block_thumb' ) );
	}

	/**
	 * Hide the featured image on single posts where the corresponding flag
	 * was set in the backend.
	 *
	 * @since 6.2.1
	 */
	public function set_thumbnail_visibility() {
		/**
		 * Remove the filters, if it's not the main query. This is the case,
		 * if the current query is executed after the main query.
		 */
		if ( is_embed() || ! $this->should_remove_theme_thumbnail() ) {
			remove_filter( 'get_post_metadata', array( &$this, 'hide_featured_image_in_the_loop' ) );
			$this->post_id = 0;
			return;
		}

		// Hide the featured image.
		$this->post_id = get_the_ID();
		add_filter( 'get_post_metadata', array( &$this, 'hide_featured_image_in_the_loop' ), 10, 3 );
	}

	/**
	 * Set the thumbnail_id to false if in the loop, to make the WordPress
	 * core believe there is no thumbnail/featured image.
	 *
	 * @param mixed $value given by the get_post_metadata filter
	 * @param int $object_id
	 * @param string $meta_key
	 *
	 * @return mixed
	 * @see has_post_thumbnail()
	 * @since 6.2.1
	 */
	public function hide_featured_image_in_the_loop( $value, $object_id, $meta_key ) {
		if ( '_thumbnail_id' === $meta_key && $object_id === $this->post_id && in_the_loop() ) {
			return false;
		}

		return $value;
	}

	/**
	 * Prevent block rendering needed.
	 *
	 * If the featured image is marked hidden, we are in the main query and
	 * the page is singular, the given block content is removed.
	 *
	 * @param string $block_content
	 * @return string
	 * @since 6.2.1
	 */
	public function remove_featured_image_block_thumb( $block_content ) {
		if ( $this->should_remove_theme_thumbnail() ) {
			return '';
		}

		return $block_content;
	}

	/**
	 * @return bool
	 * @since 6.2.1
	 */
	public function should_remove_theme_thumbnail() {
		if ( ! is_main_query() || ! is_singular( WPBDP_POST_TYPE ) ) {
			return false;
		}
		$which_thumbnail = wpbdp_get_option( 'which-thumbnail' );
		return $which_thumbnail !== 'theme';
	}

	private function end_query() {
		global $wp_query;

		$wp_query->current_post = -1;
		$wp_query->post_count   = 0;
	}

	public function _comments_template( $template ) {
        $is_single_listing = is_single() && get_post_type() == WPBDP_POST_TYPE;
        $is_main_page = get_post_type() == 'page' && get_the_ID() == wpbdp_get_page_id( 'main' );

        $comments_allowed = in_array(
            wpbdp_get_option( 'allow-comments-in-listings' ),
            array( 'allow-comments', 'allow-comments-and-insert-template' )
        );

        // disable comments in WPBDP pages or if comments are disabled for listings
        if ( ( $is_single_listing && ! $comments_allowed ) || $is_main_page ) {
            return WPBDP_TEMPLATES_PATH . '/empty-template.php';
        }

        return $template;
    }

	public function _category_template( $template ) {
		if ( get_query_var( WPBDP_CATEGORY_TAX ) && taxonomy_exists( WPBDP_CATEGORY_TAX ) ) {
			return wpbdp_locate_template( array( 'businessdirectory-category', 'wpbusdirman-category' ) );
        }

        return $template;
    }

	public function _single_template( $template ) {
		if ( is_single() && get_post_type() === WPBDP_POST_TYPE ) {
			return wpbdp_locate_template( array( 'businessdirectory-single', 'wpbusdirman-single' ) );
        }

        return $template;
    }

	/**
	 * @deprecated 6.1
	 */
	public function modify_global_post_title( $title = '' ) {
		_deprecated_function( __METHOD__, '6.1' );
		return $title;
	}

	/**
	 * @deprecated 6.1
	 */
	public function maybe_spoof_post() {
		_deprecated_function( __METHOD__, '6.1' );
	}

	/**
	 * @deprecated 6.1
	 */
	public function spoof_post() {
		_deprecated_function( __METHOD__, '6.1' );
	}

	/**
	 * @deprecated 6.1
	 */
	public function wp_head_done() {
		_deprecated_function( __METHOD__, '6.1' );
	}
}

