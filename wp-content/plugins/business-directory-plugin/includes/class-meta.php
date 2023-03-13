<?php
/**
 * @since 5.0
 */
class WPBDP__Meta {

    public function __construct() {
        add_action( 'wp_head', array( $this, '_rss_feed' ), 2 );
        add_filter( 'feed_links_show_posts_feed', array( $this, 'should_show_posts_feed_links' ) );
        add_filter( 'feed_links_show_comments_feed', array( $this, 'should_show_posts_feed_links' ) );

		if ( ! wpbdp_get_option( 'disable-cpt' ) ) {
            add_filter( 'document_title_parts', array( &$this, 'set_view_title' ), 10 );
        }
    }

    public function _rss_feed() {
        $current_view = wpbdp_current_view();

        if ( ! $current_view ) {
            return;
        }

        if ( ! in_array( $current_view, array( 'main', 'show_category', 'all_listings' ), true ) ) {
            return;
        }

        $main_page_title = get_the_title( wpbdp_get_page_id() );
        $link_template = '<link rel="alternate" type="application/rss+xml" title="%s" href="%s" />' . PHP_EOL;
        $feed_links = array();

        if ( 'main' === $current_view || 'all_listings' === $current_view ) {
            $feed_title = sprintf( _x( '%s Feed', 'rss feed', 'business-directory-plugin' ), $main_page_title );
            $feed_url = esc_url( add_query_arg( 'post_type', WPBDP_POST_TYPE, get_bloginfo( 'rss2_url' ) ) );

            $feed_links[] = sprintf( $link_template, $feed_title, $feed_url );
        }

        if ( 'show_category' === $current_view ) {
            $term = _wpbpd_current_category();

            if ( $term ) {
                $taxonomy = get_taxonomy( $term->taxonomy );
                $feed_title = sprintf( '%s &raquo; %s %s Feed', $main_page_title, $term->name, $taxonomy->labels->singular_name );
                $query_args = array( 'post_type' => WPBDP_POST_TYPE, WPBDP_CATEGORY_TAX => $term->slug );
                $feed_url = esc_url( add_query_arg( $query_args, get_bloginfo( 'rss2_url' ) ) );

                $feed_links[] = sprintf( $link_template, $feed_title, $feed_url );

                // Add dummy action to prevent https://core.trac.wordpress.org/ticket/40906
                add_action( 'wp_head', '__return_null', 3 );
                // Avoid two RSS URLs in Category pages.
                remove_action( 'wp_head', 'feed_links_extra', 3 );
            }
        }

        if ( $feed_links ) {
            echo '<!-- Business Directory RSS feed -->' . PHP_EOL;
            echo implode( '', $feed_links );
            echo '<!-- /Business Directory RSS feed -->' . PHP_EOL;
        }
    }

    public function should_show_posts_feed_links( $should ) {
        $current_view = wpbdp_current_view();

        if ( ! $current_view ) {
            return $should;
        }

        if ( ! in_array( $current_view, array( 'main', 'show_category', 'all_listings' ), true ) ) {
            return $should;
        }

        return false;
    }

    public function set_view_title( $title ) {
        global $wp_query;

        if ( empty( $wp_query->wpbdp_view ) || ! is_array( $title ) )
            return $title;

        $current_view = wpbdp()->dispatcher->current_view_object();

        if ( ! $current_view )
            return $title;

        if ( $view_title = $current_view->get_title() )
            $title['title'] = $view_title;

        return $title;
    }

	public function _meta_keywords() {
		_deprecated_function( __METHOD__, '6.1' );
	}

	public function _meta_rel_canonical() {
		_deprecated_function( __METHOD__, '6.1' );
	}

	public function _meta_title( $title = '' ) {
		_deprecated_function( __METHOD__, '6.1' );
		return $title;
	}

	public function _meta_setup() {
		_deprecated_function( __METHOD__, '6.1' );
	}

	public function listing_opentags() {
		_deprecated_function( __METHOD__, '6.1' );
	}
}
