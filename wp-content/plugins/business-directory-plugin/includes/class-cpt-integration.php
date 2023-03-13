<?php
/**
 * @since 5.0
 */
class WPBDP__CPT_Integration {

    public function __construct() {
        $this->register_post_type();
    }

    private function register_post_type() {
        // Listing type.
        $args = array(
            'labels'       => array(
                'name'               => _x( 'Directory', 'post type general name', 'business-directory-plugin' ),
                'singular_name'      => _x( 'Listing', 'post type singular name', 'business-directory-plugin' ),
                'add_new'            => _x( 'Add New Listing', 'listing', 'business-directory-plugin' ),
                'add_new_item'       => _x( 'Add New Listing', 'post type', 'business-directory-plugin' ),
                'edit_item'          => __( 'Edit Listing', 'business-directory-plugin' ),
                'new_item'           => __( 'New Listing', 'business-directory-plugin' ),
                'view_item'          => __( 'View Listing', 'business-directory-plugin' ),
                'search_items'       => __( 'Search Listings', 'business-directory-plugin' ),
                'all_items'          => __( 'Directory Listings', 'business-directory-plugin' ),
                'not_found'          => __( 'No listings found', 'business-directory-plugin' ),
                'not_found_in_trash' => __( 'No listings found in trash', 'business-directory-plugin' ),
            ),
            'public'       => true,
            'show_ui'      => true,
            'menu_icon'    => self::menu_icon(),
            'supports'     => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments', 'custom-fields' ),
			'show_in_rest' => true,
            'rewrite'      => array(
                'slug'       => wpbdp_get_option( 'permalinks-directory-slug', WPBDP_POST_TYPE ),
                'with_front' => true,
                'feeds'      => true,
            ),
            'taxonomies' => array(
                WPBDP_CATEGORY_TAX,
                WPBDP_TAGS_TAX,
            )
        );
        register_post_type( WPBDP_POST_TYPE, $args );

        // Category tax.
        $cat_args = array(
            'labels'       => array(
                'name'          => __( 'Directory Categories', 'business-directory-plugin' ),
                'singular_name' => __( 'Directory Category', 'business-directory-plugin' ),
            ),
            'hierarchical' => true,
            'public'       => true,
            'rewrite'      => array( 'slug' => wpbdp_get_option( 'permalinks-category-slug', WPBDP_CATEGORY_TAX ) ),
			'show_in_rest' => true,
        );
        register_taxonomy( WPBDP_CATEGORY_TAX, WPBDP_POST_TYPE, $cat_args );

        // Tag tax.
        $tags_args = array(
            'labels'       => array(
                'name'          => __( 'Directory Tags', 'business-directory-plugin' ),
                'singular_name' => __( 'Directory Tag', 'business-directory-plugin' ),
            ),
            'hierarchical' => false,
            'public'       => true,
            'rewrite'      => array( 'slug' => wpbdp_get_option( 'permalinks-tags-slug', WPBDP_TAGS_TAX ) ),
			'show_in_rest' => true,
        );

        $tags_slug = wpbdp_get_option( 'permalinks-tags-slug', WPBDP_TAGS_TAX );
        register_taxonomy( WPBDP_TAGS_TAX, WPBDP_POST_TYPE, $tags_args );
    }

    public function register_hooks() {
        add_filter( 'post_type_link', array( &$this, '_post_link' ), 10, 3 );
        add_filter( 'get_shortlink', array( &$this, '_short_link' ), 10, 4 );

        add_filter( 'preview_post_link', array( $this, '_preview_post_link' ), 10, 2 );

        add_filter( 'term_link', array( $this, '_category_link' ), 10, 3 );
        add_filter( 'term_link', array( $this, '_tag_link' ), 10, 3 );

        add_filter( 'comments_open', array( $this, '_allow_comments' ), 10, 2 );

        add_filter( 'bulk_post_updated_messages', array( $this, 'bulk_post_updated_messages' ), 10, 2 );

        add_filter( 'pre_trash_post', array( &$this, 'pre_listing_delete' ), 10, 2 );
        add_filter( 'pre_delete_post', array( &$this, 'pre_listing_delete' ), 10, 2 );
        add_action( 'before_delete_post', array( &$this, 'after_listing_delete' ) );
        add_action( 'delete_term', array( &$this, 'handle_delete_term' ), 10, 3 );

        add_action( 'save_post', array( $this, 'save_post' ), 10, 3 );
    }

	public function _category_link( $link, $category, $taxonomy ) {
        if ( WPBDP_CATEGORY_TAX != $taxonomy ) {
            return $link;
        }

        if ( ! wpbdp_rewrite_on() ) {
            if ( wpbdp_get_option( 'disable-cpt' ) ) {
                return wpbdp_url( '/' ) . '&_' . wpbdp_get_option( 'permalinks-category-slug' ) . '=' . $category->slug;
            }

            return $link;
        }

        $link = wpbdp_url(
            sprintf(
                '/%s/%s%s',
                wpbdp_get_option( 'permalinks-category-slug' ),
                $category->slug,
                substr( $link, -1 ) === '/' ? '/' : ''
            )
        );

        return apply_filters( 'wpbdp_category_link', $link, $category );
    }

	public function _tag_link( $link, $tag, $taxonomy ) {
        if ( WPBDP_TAGS_TAX != $taxonomy ) {
            return $link;
        }

        if ( ! wpbdp_rewrite_on() ) {
            if ( wpbdp_get_option( 'disable-cpt' ) ) {
                $link = wpbdp_url( '/' ) . '&_' . wpbdp_get_option( 'permalinks-tags-slug' ) . '=' . $tag->slug;
            }

            return $link;
        }

        $link = wpbdp_url(
            sprintf(
                '/%s/%s%s',
                wpbdp_get_option( 'permalinks-tags-slug' ),
                $tag->slug,
                substr( $link, -1 ) === '/' ? '/' : ''
            )
        );

        return apply_filters( 'wpbdp_tag_link', $link, $tag );
    }

    public function _post_link( $link, $post = null, $leavename = false ) {
        if ( WPBDP_POST_TYPE != get_post_type( $post ) ) {
            return $link;
        }

        if ( $querystring = parse_url( $link, PHP_URL_QUERY ) ) {
            $querystring = '?' . $querystring;
        } else {
            $querystring = '';
        }

        $querystring = substr( $link, -1 ) === '/' || $querystring ? '/' : '' . $querystring;

        if ( ! wpbdp_rewrite_on() ) {
            if ( wpbdp_get_option( 'disable-cpt' ) ) {
                $link = wpbdp_url( '/' ) . '&_' . wpbdp_get_option( 'permalinks-directory-slug' ) . '=' . $post->post_name;
            }
        } else {
            if ( $leavename ) {
                return wpbdp_url( '/%' . WPBDP_POST_TYPE . '%' . $querystring );
            }

            if ( wpbdp_get_option( 'permalinks-no-id' ) ) {
                if ( $post->post_name ) {
                    $link = wpbdp_url( '/' . $post->post_name );
                } else {
                    // Use default $link.
                    return $link;
                }
            } else {
                $link = wpbdp_url( '/' . $post->ID . '/' . $post->post_name );
            }

            $link .= $querystring;
        }

        return apply_filters( 'wpbdp_listing_link', $link, $post->ID );
    }

    public function _short_link( $shortlink, $id = 0, $context = 'post', $allow_slugs = true ) {
        if ( 'post' !== $context || WPBDP_POST_TYPE != get_post_type( $id ) ) {
            return $shortlink;
        }

        $post = get_post( $id );
        return $this->_post_link( $shortlink, $post );
    }

    public function _post_link_qtranslate( $url, $post ) {
        if ( is_admin() || ! function_exists( 'qtrans_convertURL' ) ) {
            return $url;
        }

        global $q_config;

        $lang         = wpbdp_get_var( array( 'param' => 'lang', 'default' => $q_config['language'] ) );
        $default_lang = $q_config['default_language'];

        if ( $lang != $default_lang ) {
            return add_query_arg( 'lang', $lang, $url );
        }

        return $url;
    }

    public function _preview_post_link( $url, $post = null ) {
        if ( is_null( $post ) && isset( $GLOBALS['post'] ) ) {
            $post = $GLOBALS['post'];
        }

        if ( WPBDP_POST_TYPE != get_post_type( $post ) ) {
            return $url;
        }

        if ( wpbdp_rewrite_on() ) {
            if ( ! wpbdp_get_option( 'permalinks-no-id' ) || ! empty( $post->post_name ) ) {
                $url = remove_query_arg( array( 'post_type', 'p' ), $url );
            }
        }

        return $url;
    }

	public function _allow_comments( $open, $post_id ) {
        // comments on directory pages
        if ( $post_id == wpbdp_get_page_id( 'main' ) ) {
            return false;
        }

        // comments on listings
        if ( get_post_type( $post_id ) == WPBDP_POST_TYPE ) {
            return in_array(
                wpbdp_get_option( 'allow-comments-in-listings' ),
                array( 'allow-comments', 'allow-comments-and-insert-template' )
            );
        }

        return $open;
    }

    /**
	 * Specify custom bulk actions messages for WPBDP post type.
	 *
	 * @param  array $bulk_messages Array of messages.
	 * @param  array $bulk_counts Array of how many objects were updated.
	 * @return array
     *
     * @since 5.5.11
     */
    public function bulk_post_updated_messages( $bulk_messages, $bulk_counts ) {
        $bulk_messages[ WPBDP_POST_TYPE ] = array(
			/* translators: %s: listing count */
			'updated'   => _n( '%s listing updated.', '%s listings updated.', $bulk_counts['updated'], 'business-directory-plugin' ),
			/* translators: %s: listing count */
			'locked'    => _n( '%s listing not updated, somebody is editing it.', '%s listings not updated, somebody is editing them.', $bulk_counts['locked'], 'business-directory-plugin' ),
			/* translators: %s: listing count */
			'deleted'   => _n( '%s listing permanently deleted.', '%s listings permanently deleted.', $bulk_counts['deleted'], 'business-directory-plugin' ),
			/* translators: %s: listing count */
			'trashed'   => _n( '%s listing moved to the Trash.', '%s listings moved to the Trash.', $bulk_counts['trashed'], 'business-directory-plugin' ),
			/* translators: %s: listing count */
			'untrashed' => _n( '%s listing restored from the Trash.', '%s listings restored from the Trash.', $bulk_counts['untrashed'], 'business-directory-plugin' ),
        );

        return $bulk_messages;
    }

    /**
     *
     * @since 5.5.11
     */
    public function pre_listing_delete( $check, $post ) {
        if ( WPBDP_POST_TYPE !== $post->post_type ) {
            return $check;
        }

        $listing = wpbdp_get_listing( $post->ID );

        if ( ! $listing->has_subscription() ) {
            return $check;
        }

        $subscription = $listing->get_subscription();

        if ( ! $subscription ) {
            global $wpdb;
            $subscription_id = $wpdb->get_var( $wpdb->prepare( "SELECT subscription_id FROM {$wpdb->prefix}wpbdp_listings WHERE listing_id = %d AND is_recurring = %d", $this->id, 1 ) );

            if ( ! $subscription_id ) {
                return $check;
            }

            try {
                $subscription = new WPBDP__Listing_Subscription( 0, $subscription_id );
            } catch ( Exception $e ) {
                return $check;
            }
        }

        if ( ! $subscription->get_parent_payment() ) {
            return $check;
        }

        global $wpbdp;

        try {
            // TODO: Implement cancel_subscription in gateways, otherwise listing won't be removed.
            $wpbdp->payments->cancel_subscription( $listing, $subscription );
            delete_post_meta( $listing->get_id(), '_gateway_suscription_cancel_status' );
        } catch ( Exception $e ) {
            update_post_meta( $listing->get_id(), '_gateway_suscription_cancel_status', 'not_canceled' );
            if ( 'pre_delete_post' === current_filter() && 'wpbdp_uninstall' !== wpbdp_get_var( array( 'param' => 'page' ), 'request' ) ) {
                wp_die( wp_kses_post( $e->getMessage() ) );
                return false;
            }
        }

        return $check;

    }

    /**
     * Handles cleanup after a listing is deleted.
     *
     * @since 3.4
     */
    public function after_listing_delete( $post_id ) {
        if ( WPBDP_POST_TYPE != get_post_type( $post_id ) ) {
            return;
        }

        $listing = wpbdp_get_listing( $post_id );
        $listing->after_delete( 'delete_post' );
    }

    /**
     * @since 5.0
     */
    public function save_post( $post_id, $post, $update ) {
        if ( WPBDP_POST_TYPE != $post->post_type ) {
            return;
        }

        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
            return;
        }

        if ( ! $update ) {
            wpbdp_insert_log( array( 'log_type' => 'listing.created', 'object_id' => $post_id ) );
        }

        if ( 'auto-draft' == $post->post_status ) {
            return;
        }

        $listing = wpbdp_get_listing( $post_id );
        $listing->_after_save( $update ? 'save_post' : 'submit-new' );
    }

    public function handle_delete_term( $term_id, $tt_id, $taxonomy ) {
        global $wpdb;

        if ( WPBDP_CATEGORY_TAX != $taxonomy ) {
            return;
        }
    }

    public static function menu_icon( $atts = array() ) {
        $defaults = array(
            'height' => 18,
            'width'  => 18,
            'fill'   => '#a0a5aa',
        );
        $atts     = array_merge( $defaults, $atts );

        $icon = '<svg aria-hidden="true" focusable="false" data-prefix="far" data-icon="address-card" class="svg-inline--fa fa-address-card fa-w-18" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 576 512" width="' . esc_attr( $atts['width'] ) . '" height="' . esc_attr( $atts['height'] ) . '" >
            <path fill="' . $atts['fill'] . '" d="M528 32H48C21.5 32 0 53.5 0 80v352c0 26.5 21.5 48 48 48h480c26.5 0 48-21.5 48-48V80c0-26.5-21.5-48-48-48zm0 400H48V80h480v352zM208 256c35.3 0 64-28.7 64-64s-28.7-64-64-64-64 28.7-64 64 28.7 64 64 64zm-89.6 128h179.2c12.4 0 22.4-8.6 22.4-19.2v-19.2c0-31.8-30.1-57.6-67.2-57.6-10.8 0-18.7 8-44.8 8-26.9 0-33.4-8-44.8-8-37.1 0-67.2 25.8-67.2 57.6v19.2c0 10.6 10 19.2 22.4 19.2zM360 320h112c4.4 0 8-3.6 8-8v-16c0-4.4-3.6-8-8-8H360c-4.4 0-8 3.6-8 8v16c0 4.4 3.6 8 8 8zm0-64h112c4.4 0 8-3.6 8-8v-16c0-4.4-3.6-8-8-8H360c-4.4 0-8 3.6-8 8v16c0 4.4 3.6 8 8 8zm0-64h112c4.4 0 8-3.6 8-8v-16c0-4.4-3.6-8-8-8H360c-4.4 0-8 3.6-8 8v16c0 4.4 3.6 8 8 8z">
            </path>
            </svg>';

        return 'data:image/svg+xml;base64,' . base64_encode( $icon );
    }
}
