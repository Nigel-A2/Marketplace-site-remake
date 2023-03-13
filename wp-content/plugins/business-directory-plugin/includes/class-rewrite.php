<?php
/**
 * @since 5.0
 */
class WPBDP__Rewrite {

    public function __construct() {
		add_filter( 'rewrite_rules_array', array( $this, '_rewrite_rules' ) );
		add_filter( 'redirect_canonical', array( $this, '_redirect_canonical' ), 10, 2 );
		add_action( 'template_redirect', array( $this, '_template_redirect' ) );
		add_action( 'wp_loaded', array( $this, '_wp_loaded' ) );
    }

    private function get_rewrite_rules() {
        global $wpdb;
        global $wp_rewrite;

        $rules = array();

        // TODO: move this to WPML Compat.
        if ( $page_ids = wpbdp_get_page_ids( 'main' ) ) {
            foreach ( $page_ids as $page_id ) {
                $page_link = _get_page_link( $page_id );
                $page_link = preg_replace( '/\?.*/', '', $page_link ); // Remove querystring from page link.

                $page_link = apply_filters( 'wpbdp_url_base_url', $page_link, $page_id );

                $home_url = home_url();
                $home_url = preg_replace( '/\?.*/', '', $home_url ); // Remove querystring from home URL.

                $rewrite_base = str_replace( 'index.php/', '', rtrim( str_replace( trailingslashit( $home_url ), '', $page_link ), '/' ) );

                $dir_slug = urlencode( wpbdp_get_option( 'permalinks-directory-slug' ) );
                $category_slug = urlencode( wpbdp_get_option( 'permalinks-category-slug' ) );
                $tags_slug = urlencode( wpbdp_get_option( 'permalinks-tags-slug' ) );

				$rules[ '(' . $rewrite_base . ')/' . $wp_rewrite->pagination_base . '/?([0-9]{1,})/?$' ] = 'index.php?page_id=' . $page_id . '&paged=$matches[2]';

				$rules[ '(' . $rewrite_base . ')/' . $category_slug . '/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$' ] = 'index.php?' . WPBDP_CATEGORY_TAX . '=$matches[2]&feed=$matches[3]';
				$rules[ '(' . $rewrite_base . ')/' . $category_slug . '/(.+?)/(feed|rdf|rss|rss2|atom)/?$' ]      = 'index.php?' . WPBDP_CATEGORY_TAX . '=$matches[2]&feed=$matches[3]';

                if ( ! wpbdp_get_option( 'disable-cpt' ) ) {
					$rules[ '(' . $rewrite_base . ')/' . $category_slug . '/(.+?)/' . $wp_rewrite->pagination_base . '/?([0-9]{1,})/?$' ] = 'index.php?' . WPBDP_CATEGORY_TAX . '=$matches[2]&paged=$matches[3]';
					$rules[ '(' . $rewrite_base . ')/' . $category_slug . '/(.+?)/?$' ] = 'index.php?' . WPBDP_CATEGORY_TAX . '=$matches[2]';
                } else {
					$rules[ '(' . $rewrite_base . ')/' . $category_slug . '/(.+?)/' . $wp_rewrite->pagination_base . '/?([0-9]{1,})/?$' ] = 'index.php?page_id=' . $page_id . '&_' . $category_slug . '=$matches[2]&paged=$matches[3]';
					$rules[ '(' . $rewrite_base . ')/' . $category_slug . '/(.+?)/?$' ] = 'index.php?page_id=' . $page_id . '&_' . $category_slug . '=$matches[2]';
                }

				$rules[ '(' . $rewrite_base . ')/' . $tags_slug . '/(.+?)/feed/(feed|rdf|rss|rss2|atom)/?$' ] = 'index.php?' . WPBDP_TAGS_TAX . '=$matches[2]&feed=$matches[3]';
				$rules[ '(' . $rewrite_base . ')/' . $tags_slug . '/(.+?)/(feed|rdf|rss|rss2|atom)/?$' ] = 'index.php?' . WPBDP_TAGS_TAX . '=$matches[2]&feed=$matches[3]';

				if ( ! wpbdp_get_option( 'disable-cpt' ) ) {
					$rules[ '(' . $rewrite_base . ')/' . $tags_slug . '/(.+?)/' . $wp_rewrite->pagination_base . '/?([0-9]{1,})/?$' ] = 'index.php?' . WPBDP_TAGS_TAX . '=$matches[2]&paged=$matches[3]';
					$rules[ '(' . $rewrite_base . ')/' . $tags_slug . '/(.+?)$' ] = 'index.php?' . WPBDP_TAGS_TAX . '=$matches[2]';
                } else {
					$rules[ '(' . $rewrite_base . ')/' . $tags_slug . '/(.+?)/' . $wp_rewrite->pagination_base . '/?([0-9]{1,})/?$' ] = 'index.php?page_id=' . $page_id . '&_' . $tags_slug . '=$matches[2]&paged=$matches[3]';
					$rules[ '(' . $rewrite_base . ')/' . $tags_slug . '/(.+?)$' ] = 'index.php?page_id=' . $page_id . '&_' . $tags_slug . '=$matches[2]';
                }

                if ( wpbdp_get_option( 'permalinks-no-id' ) ) {
                    if ( ! wpbdp_get_option( 'disable-cpt' ) ) {
						$rules[ '(' . $rewrite_base . ')/(.*)/feed/(feed|rdf|rss|rss2|atom)/?$' ] = 'index.php?' . WPBDP_POST_TYPE . '=$matches[2]&feed=$matches[3]';
						$rules[ '(' . $rewrite_base . ')/(.*)/(feed|rdf|rss|rss2|atom)/?$' ] = 'index.php?' . WPBDP_POST_TYPE . '=$matches[2]&feed=$matches[3]';

						$rules[ '(' . $rewrite_base . ')/(.*)/?$' ] = 'index.php?' . WPBDP_POST_TYPE . '=$matches[2]';
                    } else {
						$rules[ '(' . $rewrite_base . ')/(.*)/?$' ] = 'index.php?page_id=' . $page_id . '&_' . $dir_slug . '=$matches[2]';
                    }
                } else {
                    if ( ! wpbdp_get_option( 'disable-cpt' ) ) {
						$rules[ '(' . $rewrite_base . ')/([0-9]{1,})/?(.*)/?$' ] = 'index.php?p=$matches[2]&listing_slug=$matches[3]&post_type=' . WPBDP_POST_TYPE; // FIXME: post_type shouldn't be required. Fix Query_Integration too.
                    } else {
						$rules[ '(' . $rewrite_base . ')/([0-9]{1,})/?(.*)/?$' ] = 'index.php?page_id=' . $page_id . '&_' . $dir_slug . '=$matches[2]&listing_slug=$matches[3]';
                    }
                }
            }
        }

        $rules = apply_filters( 'wpbdp_rewrite_rules', $rules );

        // Create uppercase versions of rules involving octets (support for cyrillic characters).
        foreach ( $rules as $def => $redirect ) {
            $upper_r = $def;

            preg_match_all( '/%[0-9a-zA-Z]{2}/', $def, $matches );

            foreach ( $matches[0] as $match ) {
                $upper_r = str_replace( $match, strtoupper( $match ), $upper_r );
            }

            if ( 0 !== strcmp( $def, $upper_r ) ) {
                $rules[ $upper_r ] = $redirect;
            }
        }

        return $rules;
    }

    public function _wp_loaded() {
        $rules = get_option( 'rewrite_rules' );
		if ( $rules ) {
			foreach ( $this->get_rewrite_rules() as $k => $v ) {
				if ( ! isset( $rules[ $k ] ) || $rules[ $k ] != $v ) {
                    global $wp_rewrite;
                    $wp_rewrite->flush_rules();
                    return;
                }
            }
        }
    }

	public function _rewrite_rules( $rules ) {
        $newrules = $this->get_rewrite_rules();
        return $newrules + $rules;
    }

    /**
     * Workaround for issue WP bug #16373.
     * See http://wordpress.stackexchange.com/questions/51530/rewrite-rules-problem-when-rule-includes-homepage-slug.
     */
    public function _redirect_canonical( $redirect_url, $requested_url ) {
        global $wp_query;

        if ( $main_page_id = wpbdp_get_page_id( 'main' ) ) {
			if ( is_page() && ! is_feed() && isset( $wp_query->queried_object ) &&
                 get_option( 'show_on_front' ) == 'page' &&
                 get_option( 'page_on_front' ) == $wp_query->queried_object->ID ) {
                return $requested_url;
            }
        }

        return $redirect_url;
    }

    public function _template_redirect() {
        global $wp_query;

        if ( $wp_query->get( 'wpbdpx' ) ) {
            // Handle some special wpbdpx actions.
            $wpbdpx = $wp_query->get( 'wpbdpx' );

            if ( isset( $this->{$wpbdpx} ) && method_exists( $this->{$wpbdpx}, 'process_request' ) ) {
                $this->{$wpbdpx}->process_request();
                exit();
            }

            if ( 'payments' == $wpbdpx ) {
                require_once WPBDP_PATH . 'includes/compatibility/class-wpbdpx-payments-compat.php';
                $payments_compat = new WPBDP__WPBDPX_Payments_Compat();
                $payments_compat->dispatch();
                exit;
            }
        }

        if ( is_feed() )
            return;

        // Redirect some old views.
		$action = wpbdp_get_var( array( 'param' => 'action' ) );
		if ( 'main' === wpbdp_current_view() && $action ) {
			switch ( $action ) {
                case 'submitlisting':
                    $newview = 'submit_listing';
                    break;
                case 'search':
                    $newview = 'search';
                    break;
                default:
                    $newview = '';
                    break;
            }

            wp_redirect( add_query_arg( 'wpbdp_view', $newview, remove_query_arg( 'action' ) ) );
            exit();
        }
    }

}
