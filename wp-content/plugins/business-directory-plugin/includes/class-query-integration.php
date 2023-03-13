<?php
/**
 * @since 4.0
 * @package BDP/Includes/Query Integration
 */

/**
 * Class WPBDP__Query_Integration
 */
class WPBDP__Query_Integration {

    public function __construct() {
        add_filter( 'query_vars', array( $this, 'add_query_vars' ) );

        add_action( 'parse_query', array( $this, 'set_query_flags' ), 50 );
        add_action( 'template_redirect', array( $this, 'set_404_flag' ), 0 );

        add_action( 'pre_get_posts', array( $this, 'pre_get_posts' ), 10 );
        add_filter( 'posts_clauses', array( $this, 'posts_clauses' ), 10, 2 );

        // Core sorting options.
        add_filter( 'wpbdp_listing_sort_options', array( &$this, 'sortbar_sort_options' ) );
        add_filter( 'wpbdp_query_fields', array( &$this, 'sortbar_query_fields' ) );
        add_filter( 'wpbdp_query_orderby', array( &$this, 'sortbar_orderby' ) );
    }

    public function add_query_vars( $vars ) {
        array_push( $vars, 'listing' );
        array_push( $vars, 'category_id' ); // TODO: are we really using this var?
        array_push( $vars, 'category' );
        array_push( $vars, 'wpbdpx' );
        array_push( $vars, 'wpbdp-listener' );
        array_push( $vars, 'region' );
        array_push( $vars, 'wpbdp_view' );
        array_push( $vars, 'listing_slug' );

        if ( wpbdp_get_option( 'disable-cpt' ) ) {
            array_push( $vars, '_' . wpbdp_get_option( 'permalinks-directory-slug' ) );
            array_push( $vars, '_' . wpbdp_get_option( 'permalinks-category-slug' ) );
            array_push( $vars, '_' . wpbdp_get_option( 'permalinks-tags-slug' ) );
        }

        return $vars;
    }

    public function set_query_flags( $query ) {
        if ( is_admin() ) {
            return;
        }

        $main_query = ( ! $query->is_main_query() && isset( $query->query_vars['wpbdp_main_query'] ) && $query->query_vars['wpbdp_main_query'] ) || $query->is_main_query();

        if ( ! $main_query ) {
            return;
        }

        $this->set_defaults_on_query( $query );

        // Is this a listing query?
        // FIXME: this results in false positives frequently.
        $types = ( ! empty( $query->query_vars['post_type'] ) ? (array) $query->query_vars['post_type'] : array() );
        if ( $query->is_single && in_array( WPBDP_POST_TYPE, $types ) && count( $types ) < 2 ) {
            $query->wpbdp_is_listing = true;
            $query->wpbdp_view       = 'show_listing';
        }

        // Is this a category query?
        $category_slug = wpbdp_get_option( 'permalinks-category-slug' );
        if ( ! empty( $query->query_vars[ WPBDP_CATEGORY_TAX ] ) ) {
            $query->wpbdp_is_category = true;
            $query->wpbdp_view        = 'show_category';
        }

        $tags_slug = wpbdp_get_option( 'permalinks-tags-slug' );
        if ( ! empty( $query->query_vars[ WPBDP_TAGS_TAX ] ) ) {
            $query->wpbdp_is_tag = true;
            $query->wpbdp_view   = 'show_tag';
        }

        if ( $this->is_main_page( $query ) ) {
            $query->wpbdp_is_main_page = true;
        }

        if ( ! $query->wpbdp_view ) {
            if ( $query->get( 'wpbdp_view' ) ) {
                $query->wpbdp_view = $query->get( 'wpbdp_view' );
            } elseif ( $query->wpbdp_is_main_page ) {
                $query->wpbdp_view = 'main';
            }
        }

        $query->wpbdp_our_query = ( $query->wpbdp_is_listing || $query->wpbdp_is_category || $query->wpbdp_is_tag );

        if ( ! empty( $query->query_vars['wpbdp_main_query'] ) ) {
            $query->wpbdp_our_query = true;
        }

        // Normalize view name.
        if ( ! empty( $query->wpbdp_view ) ) {
            $query->wpbdp_view = WPBDP_Utils::normalize( $query->wpbdp_view );
        }

        do_action_ref_array( 'wpbdp_query_flags', array( $query ) );
    }

	/**
	 * @since 6.2.7
	 */
	private function set_defaults_on_query( &$query ) {
		$query->wpbdp_view         = '';
		$query->wpbdp_is_main_page = false;
		$query->wpbdp_is_listing   = false;
		$query->wpbdp_is_category  = false;
		$query->wpbdp_is_tag       = false;
		$query->wpbdp_our_query    = false;
		$query->wpbdp_is_shortcode = false;
		$query->wpbdp_in_the_loop  = false;
	}

    /**
     * Uses the current query and the main query objects to determine if the current
     * request is for plugin's main page.
     *
     * FIXME: Can we make this more robust?
     *
     * @since 5.1.8
     */
    private function is_main_page( $query ) {
        global $wp_query;

        if ( ! $wp_query->is_page ) {
            return false;
        }

        $plugin_page_ids = array_map( 'absint', wpbdp_get_page_ids() );

        if ( in_array( (int) $wp_query->get_queried_object_id(), $plugin_page_ids, true ) ) {
            return true;
        }

        if ( in_array( (int) $query->get_queried_object_id(), $plugin_page_ids, true ) ) {
            return true;
        }

        if ( in_array( (int) $query->get( 'page_id' ), $plugin_page_ids, true ) ) {
            return true;
        }

        return false;
    }

    public function set_404_flag() {
        global $wp_query;

        if ( ! $wp_query->wpbdp_our_query ) {
            return;
        }

        if ( 'show_listing' == $wp_query->wpbdp_view && empty( $wp_query->posts ) ) {
            $wp_query->is_404 = true;
        }
    }

	/**
	 * @param WP_Query $query
	 */
    public function pre_get_posts( &$query ) {
        $this->verify_unique_listing_url( $query );
        if ( is_admin() || ! isset( $query->wpbdp_our_query ) || ! $query->wpbdp_our_query ) {
            return;
        }

		/** @var WP_Query $query */
        if ( ! $query->get( 'posts_per_page' ) ) {
            $query->set( 'posts_per_page', wpbdp_get_option( 'listings-per-page' ) > 0 ? wpbdp_get_option( 'listings-per-page' ) : -1 );
        }

        if ( ! $query->get( 'orderby' ) ) {
            $query->set( 'orderby', wpbdp_get_option( 'listings-order-by', 'title' ) );
        }

        if ( ! $query->get( 'order' ) ) {
            $query->set( 'order', wpbdp_get_option( 'listings-sort', 'ASC' ) );
        }

        if ( $query->wpbdp_is_category || $query->wpbdp_is_tag ) {
            $post_type = $query->get( 'post_type' );
            $current_post_types = $post_type ? $post_type : array();

            if ( ! is_array( $current_post_types ) ) {
                $current_post_types = array( $current_post_types );
            }

            if ( ! in_array( WPBDP_POST_TYPE, $current_post_types ) ) {
                $current_post_types [] = WPBDP_POST_TYPE;
            }

            $query->set( 'post_type', $current_post_types );
		} elseif ( 'show_listing' === $query->wpbdp_view && $query->is_main_query() ) {
			add_filter( 'posts_results', array( $this, 'check_child_page' ), 10, 2 );
		}
    }

	/**
	 * If a listing wasn't found, check for a child page instead.
	 *
	 * @since 6.2.7
	 * @return array
	 */
	public function check_child_page( $posts, $query ) {
		if ( ! $query->is_main_query() ) {
			return $posts;
		}

		remove_filter( 'posts_results', array( $this, 'check_child_page' ) );
		if ( ! empty( $posts ) ) {
			return $posts;
		}

		// Check for child page.
		$is_page = get_posts(
			array(
				'post_parent' => wpbdp_get_page_id(),
				'post_type'   => 'page',
				'name'        => $query->query_vars['name'],
			)
		);

		if ( $is_page ) {
			global $wp_query;
			$wp_query->found_posts = 1;
			$this->set_defaults_on_query( $wp_query );
			$posts = $is_page;
		}
		return $posts;
	}

	/**
	 * @param string[] $pieces
	 * @param WP_Query $query
	 */
    public function posts_clauses( $pieces, $query ) {
        global $wpdb;

        if ( is_admin() || ! isset( $query->wpbdp_our_query ) || ! $query->wpbdp_our_query ) {
            return $pieces;
        }

        $pieces = apply_filters( 'wpbdp_query_clauses', $pieces, $query );

        // Sticky listings.
        $is_sticky_query = "(SELECT is_sticky FROM {$wpdb->prefix}wpbdp_listings wls WHERE wls.listing_id = {$wpdb->posts}.ID LIMIT 1) AS wpbdp_is_sticky";

        if ( in_array( wpbdp_current_view(), wpbdp_get_option( 'prevent-sticky-on-directory-view' ), true ) ) {
            $is_sticky_query = '';
        }

        $pieces['fields'] .= $is_sticky_query ? ', ' . $is_sticky_query : '';
        $order_by          = $query->get( 'orderby' );
        $order             = $query->get( 'order' );

        switch ( $order_by ) {
			case 'paid':
			case 'paid-title':
				$pieces['fields'] .= ", (SELECT fee_price FROM {$wpdb->prefix}wpbdp_listings lp WHERE lp.listing_id = {$wpdb->posts}.ID LIMIT 1) AS wpbdp_plan_amount";
				$next_order        = $order_by === 'paid' ? 'post_date DESC' : 'post_title ASC';
				$pieces['orderby'] = 'wpbdp_plan_amount ' . $order . ", {$wpdb->posts}." . $next_order . ', ' . $pieces['orderby'];

				break;
			case 'plan-order-date':
			case 'plan-order-title':
				$plan_order = wpbdp_get_option( 'fee-order' );

				if ( 'custom' === $plan_order['method'] ) {
					$next_order        = $order_by === 'plan-order-date' ? 'post_date' : 'post_title';
					$pieces['fields'] .= ", (SELECT po.weight FROM {$wpdb->prefix}wpbdp_plans po JOIN {$wpdb->prefix}wpbdp_listings pol ON po.id = pol.fee_id";
					$pieces['fields'] .= " WHERE pol.listing_id = {$wpdb->posts}.ID ) AS wpbdp_plan_weight";
					$pieces['orderby'] = "wpbdp_plan_weight DESC, {$wpdb->posts}." . $next_order . ' ' . $order . ', ' . $pieces['orderby'];
				}

                break;
        }

        $pieces['fields']         = apply_filters( 'wpbdp_query_fields', $pieces['fields'] );
        $pieces['custom_orderby'] = apply_filters( 'wpbdp_query_orderby', ( $is_sticky_query ? 'wpbdp_is_sticky DESC' : '' ) );
        $pieces['orderby']        = ( $pieces['custom_orderby'] ? $pieces['custom_orderby'] . ', ' : '' ) . $pieces['orderby'];

        return $pieces;
    }

    // {{ Sort bar.
    public function sortbar_sort_options( $options ) {
        $sortbar_fields = wpbdp_sortbar_get_field_options();
        $sortbar        = wpbdp_get_option( 'listings-sortbar-fields' );

        // Using the default argument for wpbdp_get_option does not work,
        // because a non-array value may already be stored in the settings array.
        if ( ! is_array( $sortbar ) ) {
            $sortbar = array();
        }

        foreach ( $sortbar as $field_id ) {
            if ( ! array_key_exists( $field_id, $sortbar_fields ) ) {
                continue;
            }
            $options[ 'field-' . $field_id ] = array( $sortbar_fields[ $field_id ], '', 'ASC' );
        }

        return $options;
    }

    public function sortbar_query_fields( $fields ) {
        global $wpdb;

        $sort = wpbdp_get_current_sort_option();

        if ( ! $sort || ! in_array( str_replace( 'field-', '', $sort->option ), wpbdp_get_option( 'listings-sortbar-fields' ) ) ) {
            return $fields;
        }

        $sname = str_replace( 'field-', '', $sort->option );
        $q     = '';

        switch ( $sname ) {
			case 'user_login':
				$q = "(SELECT user_login FROM {$wpdb->users} WHERE {$wpdb->users}.ID = {$wpdb->posts}.post_author) AS user_login";
                break;
			case 'user_registered':
				$q = "(SELECT user_registered FROM {$wpdb->users} WHERE {$wpdb->users}.ID = {$wpdb->posts}.post_author) AS user_registered";
                break;
			case 'date':
			case 'modified':
                break;
			default:
				$field = wpbdp_get_form_field( $sname );

				if ( ! $field || 'meta' != $field->get_association() ) {
					break;
				}

				$q = $wpdb->prepare( "(SELECT {$wpdb->postmeta}.meta_value FROM {$wpdb->postmeta} WHERE {$wpdb->postmeta}.post_id = {$wpdb->posts}.ID AND {$wpdb->postmeta}.meta_key = %s) AS field_{$sname}", '_wpbdp[fields][' . $field->get_id() . ']' );
                break;
        }

        if ( $q ) {
            return $fields . ', ' . $q;
        } else {
			return $fields;
        }
    }

    public function sortbar_orderby( $orderby ) {
        global $wpdb;

        $sort = wpbdp_get_current_sort_option();

        if ( ! $sort || ! in_array( str_replace( 'field-', '', $sort->option ), wpbdp_get_option( 'listings-sortbar-fields' ) ) ) {
            return $orderby;
        }

        $sname = str_replace( 'field-', '', $sort->option );
        $qn    = '';

        switch ( $sname ) {
			case 'user_login':
			case 'user_registered':
				$qn = $sname;
                break;
			case 'date':
			case 'modified':
				$qn = "{$wpdb->posts}.post_{$sname}";
                break;
			default:
				$field = wpbdp_get_form_field( $sname );

				if ( ! $field ) {
					break;
				}

				$mapping = $field->get_association();
				switch ( $mapping ) {
					case 'title':
					case 'excerpt':
					case 'content':
						$qn = $wpdb->posts . '.post_' . $mapping;
						break;
					case 'meta':
						$qn = 'field_' . $sname;
						break;
				}

				if ( $qn !== $orderby && $field->is_numeric() ) {
					$qn .= ' +0';
				}

                break;
        }

        if ( $qn && $qn !== $orderby ) {
			$orderby = $orderby . ( $orderby ? ', ' : '' ) . $qn . ' ' . $sort->order;
        }

		return $orderby;
    }

	/**
	 * @param WP_Query $query
	 */
    private function verify_unique_listing_url( &$query ) {
        if ( ! wpbdp_get_option( 'permalinks-no-id' ) && ! empty( $query->query['listing_slug'] ) ) {
            $wpbdp_404_query = false;
            if ( ! wpbdp_get_option( 'disable-cpt' ) ) {
                if ( 'show_listing' == $query->wpbdp_view ) {
                    if ( $query->query['listing_slug'] !== get_post_field( 'post_name', $query->query['p'] ) ) {
                        unset( $query->query['p'] );
                        unset( $query->query['post_type'] );
                        $wpbdp_404_query = true;
                    }
                }
            }

            $dir_slug = '_' . wpbdp_get_option( 'permalinks-directory-slug' );

			if ( 'main' === $query->wpbdp_view && ! empty( $query->query[ $dir_slug ] ) ) {
				if ( $query->query['listing_slug'] !== get_post_field( 'post_name', $query->query[ $dir_slug ] ) ) {
					unset( $query->query['page_id'] );
					unset( $query->query[ $dir_slug ] );
                    $wpbdp_404_query = true;
                }
            }

            if ( $wpbdp_404_query ) {
                $query->query( $query->query );
                $query->set_404();
				status_header( 404 );
            }
        }
    }
}

