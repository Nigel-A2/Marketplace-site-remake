<?php
/**
 * @package WPBDP\FieldTypes\TextArea
 * @since 4.0
 */
class WPBDP__Shortcodes {

    private $shortcodes = array();

    public function __construct() {
        add_action( 'wpbdp_loaded', array( $this, 'register' ) );
        add_action( 'wpbdp_query_flags', array( $this, 'maybe_set_shortcode_query_flags' ), 10, 1 );
    }

    /**
     * Returns shortcodes being handled by Business Directory.
	 *
     * @return array array of `shortcode => callback` items.
     */
    public function get_shortcodes() {
        return $this->shortcodes;
    }

    public function register() {
        if ( ! empty( $this->shortcodes ) )
            return;

        /*
         * WordPress Shortcode:
         *  [businessdirectory], [business-directory], [WPBUSDIRMANUI]
         * Used for:
         *  Displaying the main directory page and all directory content.
         * Notes:
         *  Required. Installed by BD automatically. Cannot be removed from site unless you plan to uninstall BD.
         * Example:
         *  `[businessdirectory]`
         */
		$this->add(
			'businessdirectory',
			array( &$this, 'sc_main' ),
			array( 'business-directory', 'WPBUSDIRMANUI' )
		);

        /*
         * WordPress Shortcode:
         *  [businessdirectory-submit-listing], [WPBUSDIRMANADDLISTING]
         * Used for:
         *  Creating a separate "Submit Listing" page for BD.
         * Notes:
         *  Optional. Not needed if you are just using the standard directory links and buttons. This allows you to have a separate page if you want to have some special content around the page.
         * Example:
         *  `[businessdirectory-submitlisting]`
         */
		$this->add(
			'businessdirectory-submit-listing',
			array( &$this, 'sc_submit_listing' ),
			array( 'businessdirectory-submitlisting', 'business-directory-submitlisting', 'business-directory-submit-listing', 'WPBUSDIRMANADDLISTING' )
		);

        /*
         * WordPress Shortcode:
         *  [businessdirectory-manage-listings], [business-directory-managelistings], [WPBUSDIRMANMANAGELISTING]
         * Used for:
         *  Bulk listing editor page for users to see and manage their listings when logged in.
         * Parameters:
         *  - showsearchbar  Allows you to control the visibility of the search bar at the top of the page. Default is 1 if not specified. (Allowed Values: 0 or 1.)
         * Example:
         *  `[businessdirectory-manage-listings]`
         */
		$this->add(
			'businessdirectory-manage-listings',
			array( &$this, 'sc_manage_listings' ),
			array( 'businessdirectory-managelistings', 'business-directory-manage-listings', 'businessdirectory-manage_listings', 'WPBUSDIRMANMANAGELISTING' )
		);

        /*
         * WordPress Shortcode:
         *  [businessdirectory-listings], [businessdirectory-viewlistings], [WPBUSDIRMANVIEWLISTINGS], [WPBUSDIRMANMVIEWLISTINGS]
         * Used for:
         *  Showing listings with a certain type, tag or filter.
         * Notes:
         *  Good for displaying listings in a single category or from a single tag.
         * Parameters:
         *  - tag       Shows the listings with a certain tag name. (Allowed Values: Any valid tag name within the directory. Can be a comma separated list too (eg. "New, Hot").)
         *  - category  Shows the listings with a certain category. (Allowed Values: Any valid category slug or ID you have configured under Directory -> Directory Categories. Can be a comma separated list too (e.g. "Dentists, Doctors" or 1,2,56).)
         *  - items_per_page The number of listings to show per page. If not present value will be set to "Listings per page" setting (Allowed Values: A positive integer)
         *  - pagination Enable pagination for shortcode. Default to 0. (Allowed values: To disable: 0, false, no. To enable: 1, true, yes)
         * Example:
         *  - Display listings from category "Dentists" with tag "New" and include a title.
         *
         *    `[businessdirectory-listings tag="New" category="Dentists"]`
         *
         */
		$this->add(
			'businessdirectory-listings',
			array( &$this, 'sc_listings' ),
			array( 'WPBUSDIRMANVIEWLISTINGS', 'WPBUSDIRMANMVIEWLISTINGS', 'businessdirectory-view_listings', 'businessdirectory-viewlistings' )
		);

        /*
         * WordPress Shortcode: [businessdirectory-search], [business-directory-search]
         * Used for: Shows the Advanced Search Screen on any single page.
         * Parameters:
         *  - form_only   Display search form only even after search is performed. Default is 0. (Allowed values: To disable: 0, false, no. To enable: 1, true, yes)
         *  - return_url  After the search is performed, when no results are found, a "Return to Search" link is shown with this parameter as target. Default value is the URL of the Advanced Search screen. (Allowed Values: Any valid URL or 'auto' to mean the URL of the page where the shortcode is being used.)
         * Example:
         *  `[businessdirectory-search]`
         */
		$this->add(
			'businessdirectory-search',
			array( &$this, 'sc_search' ),
			array( 'business-directory-search', 'businessdirectory_search', 'business-directory_search' )
		);

        /*
         * WordPress Shortcode: [businessdirectory-featuredlistings]
         * Used for: To show all of the featured listings within your directory on a single page.
         * Parameters:
         *  - number_of_listings  Maximum number of listings to display. (Allowed Values: Any positive integer or 0 for no limit)
         * Example:
         *  `[businessdirectory-featuredlistings]`
         */
        $this->add( 'businessdirectory-featuredlistings', array( &$this, 'sc_featured_listings' ) );

        /*
         * WordPress Shortcode: [businessdirectory-listing]
         * Used for: Displaying a single listing from the directory (by slug or ID).
         * Parameters:
         *  - id   Post ID of the listing. (Allowed Values: Any valid listing ID.)
         *  - slug Slug for the listing. (Allowed Values: Any valid listing slug.)
         * Notes:
         *  At least one of the parameters `id` or `slug` must be provided.
         * Example:
         *  `[businessdirectory-listing slug="my-listing"]`
         * @since 3.6.10
         */
        $this->add( 'businessdirectory-listing', array( &$this, 'sc_single_listing' ) );

		/*
		 * WordPress Shortcode: [businessdirectory-images]
		 * Used for: Displaying the field images for a single listing (by slug or ID).
		 * Parameters:
		 *  - id   Post ID or slug of the listing.
		 *
		 * @since 5.9
		 */
		$this->add( 'businessdirectory-images', array( &$this, 'single_listing_images' ) );

		/*
		 * @since 5.9
		 */
		$this->add( 'businessdirectory-socials', array( &$this, 'single_listing_socials' ) );

		/*
		 * @since 5.9
		 */
		$this->add( 'businessdirectory-buttons', array( &$this, 'single_listing_actions' ) );

		/*
		 * @since 5.9
		 */
		$this->add( 'businessdirectory-details', array( &$this, 'single_listing_details' ) );

		/*
		 * Parameters:
		 *  - id
		 *  - section. Can be comments, contact_form, googlemaps, reviews, and more.
		 *
		 * @since 5.9
		 */
		$this->add( 'businessdirectory-section', array( &$this, 'single_listing_section' ) );

        /*
         * WordPress Shortcode: [businessdirectory-categories]
         * Used for: Displaying the list of categories in a similar fashion as the main page.
         * Parameters:
         *  - parent    Parent directory category ID. (Allowed Values: A directory category term ID)
         *  - orderby What value to use for odering the categories. Default is taken from current BD settings. (Allowed Values: "name", "slug", "id", "description", "count" (listing count).)
         *  - order   Whether to order in ascending or descending order. Default is taken from current BD settings. (Allowed Values: "ASC" or "DESC")
         *  - show_count Whether to display the listing count next to each category or not. Default is taken from current BD settings. (Allowed Values: 0 or 1)
         *  - hide_empty Whether to hide empty categories or not. Default is 0. (Allowed Values: 0 or 1)
         *  - parent_only Whether to only display direct childs of parent category or make a recursive list. Default is 0. (Allowed Values: 0 or 1)
         *  - no_items_msg Message to display when there are no categories found. (Allowed Values: Any non-blank string)
         * Example:
         *  - Display the list of categories starting at the one with ID 20 and ordering by slug.
         *    `[businessdirectory-categories parent=20 order="slug"]`
         */
        $this->add( 'businessdirectory-categories', array( &$this, 'sc_categories' ) );

        /*
         * WordPress Shortcode: [businessdirectory-listing-count]
         * Used for: Outputs the listing count for a given category or region.
         * Parameters:
         *  - category  What category to use. (Allowed Values: A valid category ID, name or slug.)
         *  - region    What region to use. (Allowed Values: A valid region ID, name or slug.)
         * Notes:
         *  If both parameters are provided the result is the number of listings inside the given category located in the given region.
         * Example:
         *  - To count how many listings you have in the "Restaurants" category that are located in "New York"
         *
         *    `[businessdirectory-listing-count category="Restaurants" region="New York"]`
         */
        $this->add( 'businessdirectory-listing-count', array( &$this, 'sc_count' ), array( 'bd-listing-count', 'business-directory-listing-count' ) );

        /*
         * WordPress Shortcode: [businessdirectory-quick-search], [business-directory-quick-search]
         * Used for: Displaying the quick search box on any page.
         * Parameters:
         *  - buttons  Which menu buttons to show inside the box. Default is none. (Allowed Values: "all", "none", or a comma-separated list from the set "create", "directory" and "listings").
         * Example:
         *  `[businessdirectory-quick-search buttons="create,listings"]`
         * @since 4.1.13
         */
        $this->add( 'businessdirectory-quick-search', array( &$this, 'sc_quick_search' ), array( 'business-directory-quick-search' ) );

        /*
         * WordPress Shortcode: [businessdirectory-latest-listings]
         * Used for: Displaying all or a set of latest listings from the directory.
         * Parameters:
         *  - menu Whether to include the quick search and menu bar as part of the output. Defaults to 0. (Allowed Values: 0 or 1)
         *  - buttons  Which menu buttons to show inside the menu (applies only when `menu` is `1`). Default is none. (Allowed Values: "all", "none", or a comma-separated list from the set "create", "directory" and "listings").
         *  - items_per_page The number of listings to show per page. If not present value will be set to "Listings per page" setting (Allowed Values: A positive integer)
         *  - pagination Enable pagination for shortcode. Default to 0. (Allowed values: To disable: 0, false, no. To enable: 1, true, yes)
         * Examples:
         *  - Display the latest 5 listings submitted to the directory:
         *    `[businessdirectory-latest-listings items_per_page=5 pagination=0]`
         *  - Display all listings, started from most recent, submitted to the directory, 4 listings per page:
         *    `[businessdirectory-latest-listings items_per_page=4 pagination=1]`
         * @since 4.1.13
         */
        $this->add( 'businessdirectory-latest-listings', array( &$this, 'sc_listings_latest' ) );

        /*
         * WordPress Shortcode:
         *  [businessdirectory-random-listings]
         * Used for:
         *  Displaying a set of random listings from the directory.
         * Parameters:
         *  - menu Whether to include the quick search and menu bar as part of the output. Defaults to 0. (Allowed Values: 0 or 1)
         *  - buttons  Which menu buttons to show inside the menu (applies only when `menu` is `1`). Default is none. (Allowed Values: "all", "none", or a comma-separated list from the set "create", "directory" and "listings").
         *  - items_per_page The number of listings to show per page. If not present value will be set to "Listings per page" setting (Allowed Values: A positive integer)
         *  - pagination Enable pagination for shortcode. Default to 0. (Allowed values: To disable: 0, false, no. To enable: 1, true, yes)
         * Example:
         *  - Display a set of 10 random listings, including the directory menu with only the "Add Listing" button:
         *
         *    `[businessdirectory-random-listings items_per_page=10 menu=1 buttons="create"]`
         * @since 4.1.13
         */
        $this->add( 'businessdirectory-random-listings', array( &$this, 'sc_listings_random' ) );

        /*
         * WordPress Shortcode: [businessdirectory-featured-listings]
         * Used for: Displaying all or a set of featured listings from the directory.
         * Parameters:
         *  - menu Whether to include the quick search and menu bar as part of the output. Defaults to 0. (Allowed Values: 0 or 1)
         *  - buttons  Which menu buttons to show inside the menu (applies only when `menu` is `1`). Default is none. (Allowed Values: "all", "none", or a comma-separated list from the set "create", "directory" and "listings").
         *  - items_per_page The number of listings to show per page. If not present value will be set to "Listings per page" setting (Allowed Values: A positive integer)
         *  - pagination Enable pagination for shortcode. Default to 0. (Allowed values: To disable: 0, false, no. To enable: 1, true, yes)
		 *  - category The listing category. Multiple categories are comma separated. (Allowed Values: A valid category ID, name or slug.)
         * Example:
         *  `[businessdirectory-featured-listings items_per_page=5]`
         * @since 4.1.13
		 * @since 5.18  Added category attribute comment
         */
        $this->add( 'businessdirectory-featured-listings', array( &$this, 'sc_listings_featured' ) );

        do_action_ref_array( 'wpbdp_shortcodes_register', array( &$this ) );

        $this->shortcodes = apply_filters( 'wpbdp_shortcodes', $this->shortcodes );

        foreach ( $this->shortcodes as $shortcode => &$handler )
            add_shortcode( $shortcode, $handler );
    }

    public function add( $shortcode, $callback, $aliases = array() ) {
        foreach ( array_merge( array( $shortcode ), $aliases ) as $alias )
            $this->shortcodes[ $alias ] = $callback;
    }

    //
    // {{ Built-in shortcodes.
    //

    public function sc_main( $atts ) {
        global $wp_query;

        // if ( empty( $wp_query->wpbdp_is_main_page ) )
        //     return '';

        return wpbdp_current_view_output();
    }

    public function sc_submit_listing() {
        if ( $content = wpbdp_current_view_output() ) {
            return $content;
        } else {
            // This shouldn't happen... but just in case.
            $v = wpbdp_load_view( 'submit_listing' );
            $v->enqueue_resources();
            return $v->dispatch();
        }
    }

    public function sc_listings( $atts ) {
        require_once WPBDP_INC . 'controllers/pages/class-all-listings.php';

        $sc_atts = shortcode_atts(
            array(
                'tag'            => '',
                'tags'           => '', // Deprecated.
                'category'       => '',
                'categories'     => '', // Deprecated.
                'operator'       => 'OR',
                'author'         => '',
                'menu'           => null,
                'items_per_page' => -1,
            ),
            $atts
        );

        if ( ! is_null( $sc_atts['menu'] ) )
            $sc_atts['menu'] = ( 1 === $sc_atts['menu'] || 'true' === $sc_atts['menu'] ) ? true : false;

        $this->validate_attributes( $sc_atts, $atts );

        $query_args = array();
        $query_args['items_per_page'] = intval( $sc_atts['items_per_page'] );

		$this->process_category_atts( $sc_atts, $query_args );

		$this->process_tag_atts( $sc_atts, $query_args );

        if ( ! empty( $sc_atts['author'] ) ) {
            $u = false;
            $u = is_numeric( $sc_atts['author'] ) ? get_user_by( 'id', absint( $sc_atts['author'] ) ) : get_user_by( 'login', $sc_atts['author'] );

            if ( $u )
                $query_args['author'] = $u->ID;
        }

        $v = new WPBDP__Views__All_Listings(
            array(
                'menu' => $sc_atts['menu'],
                'query_args' => $query_args,
                'in_shortcode' => true,
                'pagination' => $sc_atts['items_per_page'] > 0 && isset( $sc_atts['pagination'] ) && $sc_atts['pagination'],
            ) );
        return $v->dispatch();
    }

    public function sc_listings_latest( $atts ) {
        $sc_atts = shortcode_atts(
            array(
                'menu'            => 0,
                'buttons'         => 'none',
                'limit'           => 0,
                'items_per_page'  => -1,
            ),
            $atts,
            'businessdirectory-latest-listings'
        );

        $this->validate_attributes( $sc_atts, $atts );

        // Remove sortbar from latests listings shortcode.
        add_filter( 'wpbdp_get_option_listings-sortbar-enabled', '__return_false', 9999 );

        return $this->display_listings(
            array(
                'orderby' => 'date',
                'order' => 'DESC'
            ),
            $sc_atts
        );
    }

    public function sc_listings_random( $atts ) {
        $sc_atts = shortcode_atts(
            array(
                'menu'      => 0,
                'buttons'   => 'none',
                'limit'     => 0,
                'items_per_page'  => -1
            ),
            $atts,
            'businessdirectory-random-listings'
        );

        $this->validate_attributes( $sc_atts, $atts );

        return $this->display_listings(
            array(
                'orderby' => 'rand'
            ),
            $sc_atts
        );
    }

    public function sc_listings_featured( $atts ) {
        $sc_atts = shortcode_atts(
            array(
                'menu'            => 0,
                'buttons'         => 'none',
                'limit'           => 0,
                'items_per_page'  => -1,
				'category'        => '',
            ),
            $atts,
            'businessdirectory-featured-listings'
        );

        $this->validate_attributes( $sc_atts, $atts );

        global $wpdb;
        $q = $wpdb->prepare(
            "SELECT DISTINCT {$wpdb->posts}.ID FROM {$wpdb->posts}
             JOIN {$wpdb->prefix}wpbdp_listings lp ON lp.listing_id = {$wpdb->posts}.ID
             WHERE {$wpdb->posts}.post_status = %s AND {$wpdb->posts}.post_type = %s AND lp.is_sticky = 1
             ORDER BY RAND()",
            'publish',
            WPBDP_POST_TYPE
        );
		$featured   = $wpdb->get_col( $q );
		$query_args = array(
			'post__in' => ! empty( $featured ) ? $featured : array( 0 ),
			'orderby'  => 'post__in',
		);
		$this->process_category_atts( $sc_atts, $query_args );
        return $this->display_listings(
			$query_args,
            $sc_atts
        );
    }

	/**
	 * Process category query attributes.
	 * This checks if the `category` or `categories` attributes are passed in the shortcode and includes them in the loop query.
	 *
	 * @param array $atts Shortcode attributes.
	 * @param array $query_args The query args used to search based on attributes.
	 *
	 * @since 5.18
	 */
	private function process_category_atts( $atts, &$query_args ) {
		$this->combine_shortcode_atts( array( 'category', 'categories' ), $atts );
		if ( empty( $atts['category'] ) ) {
			return;
		}

		$atts['category'] = explode( ',', $atts['category'] );
		$categories = array();

		foreach ( $atts['category'] as $cat ) {
			$term = false;
			if ( is_numeric( $cat ) ) {
				$term = get_term_by( 'id', $cat, WPBDP_CATEGORY_TAX );
			}

			if ( ! $term ) {
				$term = get_term_by( 'slug', $cat, WPBDP_CATEGORY_TAX );
			}

			if ( $term ) {
				$categories[] = $term->term_id;
			}
		}

		if ( empty( $categories ) ) {
			return;
		}

		$query_args['tax_query'][] = array(
			array(
				'taxonomy' => WPBDP_CATEGORY_TAX,
				'field'    => 'id',
				'terms'    => $categories,
			)
		);
	}

	/**
	 * Process tag query attributes.
	 * This checks if the `tag` or `tags` attributes are passed in the shortcode and includes them in the loop query.
	 *
	 * @param array $atts Shortcode attributes.
	 * @param array $query_args The query args used to search based on attributes.
	 *
	 * @since 6.0
	 */
	private function process_tag_atts( $atts, &$query_args ) {
		$this->combine_shortcode_atts( array( 'tag', 'tags' ), $atts );
		if ( empty( $atts['tag'] ) ) {
			return;
		}

		$query_args['tax_query'][] = array(
			array(
				'taxonomy' => WPBDP_TAGS_TAX,
				'field'    => 'slug',
				'terms'    => explode( ',', $atts['tag'] ),
			)
		);
	}

	/**
	 * Combine two shortcode attributes into one. The combination of the two is
	 * saved in the first param.
	 *
	 * @param array $combine The names of two shortcode atts to combine. ie. `category` and `categories`.
	 * @param array $atts    Shortcode attributes.
	 *
	 * @since 6.0
	 */
	private function combine_shortcode_atts( $combine, &$atts ) {
		$first  = $combine[0];
		$second = $combine[1];

		$atts[ $first ] = isset( $atts[ $first ] ) ? $atts[ $first ] : '';
		$second         = isset( $atts[ $second ] ) ? $atts[ $second ] : '';
		if ( $second !== '' ) {
			// Combine with the extra parameter.
			$atts[ $first ] .= $atts[ $first ] ? ',' : '';
			$atts[ $first ] .= $second;
		}
	}

    private function display_listings( $query_args, $args = array() ) {
        $query_args = array_merge(
            array(
                'post_type'   => WPBDP_POST_TYPE,
                'post_status' => 'publish'
            ),
            $query_args
        );
        $args = array_merge(
            array(
                'menu'           => 0,
                'buttons'        => 'none',
                'items_per_page' => -1,
            ),
            $args
        );

        if ( ! empty( $args['pagination'] ) ) {
            $paged = get_query_var( 'page' ) ? get_query_var( 'page' ) : ( get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1 );
            $query_args['paged'] = intval( $paged );
        }

        $query_args['posts_per_page'] = intval( $args['items_per_page'] );

        $query = new WP_Query( $query_args );

        // Try to trick pagination to remove it when processing a shortcode.
        if ( empty( $args['pagination'] ) ) {
            $query->max_num_pages = 1;
        }

        wpbdp_push_query( $query );

        $html  = '';

        if ( $query->have_posts() ) {
            $vars = array();
            $vars['query'] = $query;

            if ( $args['menu'] ) {
                $vars['_wrapper']  = 'page';
				$vars['_bar']      = true;
				$vars['_bar_args'] = array( 'buttons' => $args['buttons'] );
            }

            $this->maybe_paginate_frontpage( $query );

            $html .= wpbdp_x_render( 'listings', $vars );
        }

        wp_reset_postdata();
        wpbdp_pop_query();

        return $html;
    }

    public function sc_featured_listings( $atts ) {
        global $wpdb;

        $atts = shortcode_atts( array( 'number_of_listings' => wpbdp_get_option( 'listings-per-page' ) ), $atts );
        $atts['number_of_listings'] = max( 0, intval( $atts['number_of_listings'] ) );
        $order_by = wpbdp_get_option( 'listings-order-by', 'title' );
        $order = wpbdp_get_option( 'listings-sort', 'ASC' );

        $q = $wpdb->prepare(
            "SELECT DISTINCT {$wpdb->posts}.ID FROM {$wpdb->posts}
             JOIN {$wpdb->prefix}wpbdp_listings lp ON lp.listing_id = {$wpdb->posts}.ID
			 WHERE {$wpdb->posts}.post_status = %s AND {$wpdb->posts}.post_type = %s AND lp.is_sticky = 1 " . ( $atts['number_of_listings'] > 0 ? sprintf( 'LIMIT %d', $atts['number_of_listings'] ) : '' ),
            'publish',
            WPBDP_POST_TYPE
        );
        $featured = $wpdb->get_col( $q );

        shuffle( $featured );

        $args = array(
            'post_type' => WPBDP_POST_TYPE,
            'post_status' => 'publish',
            'post__in' => ! empty( $featured ) ? $featured : array( 0 ),
            'posts_per_page' => $atts['number_of_listings'],
            'orderby' => $order_by,
            'order' => $order
        );

		$q = new WP_Query( $args );
        wpbdp_push_query( $q );

        $html = wpbdp_x_render( 'listings', array( 'query' => $q ) );

        wpbdp_pop_query();

        return $html;
    }

    /**
     * @since 3.6.10
     */
    function sc_single_listing( $atts ) {
        $atts = shortcode_atts( array( 'id' => null, 'slug' => null ), $atts );
        $listing_id = wpbdp_get_post_by_id_or_slug( $atts['id'] ? $atts['id'] : $atts['slug'], 'id', 'id' );

        if ( ! $listing_id )
            return '';

        return wpbdp_render_listing( $listing_id, 'single' );
    }

	/**
	 * @since v5.9
	 */
	public function single_listing_images( $atts ) {
		$this->add_current_listing_id( $atts );
		if ( empty( $atts['id'] ) ) {
			return '';
		}

		$vars = WPBDP_Listing_Display_Helper::single_listing_vars( array( 'images_vars', 'id' => $atts['id'] ) );

		return wpbdp_x_part(
			'parts/listing-images',
			array(
				'extra_images' => $vars['images']->extra,
				'echo'         => false,
			)
		);
	}

	/**
	 * Show the socials for a listing.
	 *
	 * @since v5.9
	 */
	public function single_listing_socials( $atts ) {
		$this->add_current_listing_id( $atts );
		if ( empty( $atts['id'] ) ) {
			return '';
		}

		$vars = WPBDP_Listing_Display_Helper::single_listing_vars( array( 'fields_vars', 'id' => $atts['id'] ) );

		return wpbdp_x_part(
			'parts/listing-socials',
			array(
				'fields' => $vars['fields'],
				'echo'   => false,
			)
		);
	}

	/**
	 * Show the listing action buttons (edit, delete).
	 *
	 * @since v5.9
	 */
	public function single_listing_actions( $atts ) {
		$this->add_current_listing_id( $atts );
		if ( empty( $atts['id'] ) ) {
			return '';
		}

		return wpbdp_x_part(
			'parts/listing-buttons',
			array(
				'listing_id' => $atts['id'],
				'view'       => 'single',
				'echo'       => false,
			)
		);
	}

	/**
	 * Show the listing details in fields.
	 *
	 * @since v5.9
	 */
	public function single_listing_details( $atts ) {
		$this->add_current_listing_id( $atts );
		if ( empty( $atts['id'] ) ) {
			return '';
		}

		$vars   = WPBDP_Listing_Display_Helper::single_listing_vars( array( 'fields_vars', 'id' => $atts['id'] ) );
		$fields = $vars['fields'];

		$atts['exclude'] = empty( $atts['exclude'] ) ? array( 'social' ) : explode( ',', $atts['exclude'] );

		if ( in_array( 'social', $atts['exclude'], true ) ) {
			$fields = $fields->not( 'social' );
			$k = array_search( 'social', $atts['exclude'] );
			unset( $atts['exclude'][ $k ] );
		}

		$html = '';
		foreach ( $fields as $field ) {
			if ( $this->should_exclude_field( $atts['exclude'], $field ) ) {
				continue;
			}

			$html .= $field->html;
		}

		return $html;
	}

	/**
	 * Exclude fields using the field id, shortname, type, or mapping.
	 *
	 * @since v5.9
	 *
	 * @return bool
	 */
	private function should_exclude_field( $exclude, $field ) {
		if ( empty( $exclude ) ) {
			return false;
		}

		if ( in_array( $field->id, $exclude ) ) {
			return true;
		}

		// Are there are non-ids to check?
		$done = array_filter( $exclude, 'is_numeric' );
		if ( count( $done ) === count( $exclude ) ) {
			return false;
		}

		$type = $field->field->get_field_type_id();
		if ( in_array( $type, $exclude, true ) ) {
			return true;
		}

		$mapping = $field->field->get_association();
		if ( in_array( $mapping, $exclude, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get any section included on the single listing page.
	 *
	 * @since v5.9
	 */
	public function single_listing_section( $atts ) {
		$this->add_current_listing_id( $atts );
		if ( empty( $atts['id'] ) ) {
			return '';
		}

		$sections = empty( $atts['section'] ) ? '' : $atts['section'];
		$sections = explode( ',', $sections );

		$template_id = 'single';
		$vars = WPBDP_Listing_Display_Helper::single_listing_vars( array( 'id' => $atts['id'] ) );
		$vars = apply_filters( 'wpbdp_template_variables', $vars, $template_id );
		$vars = apply_filters( 'wpbdp_template_variables__' . $template_id, $vars, $template_id );

		$html = '';
		foreach ( $sections as $section ) {
			$add = isset( $vars[ '#' . $section ] ) ? $vars[ '#' . $section ] : ( isset( $vars[ $section ] ) ? $vars[ $section ] : '' );
			if ( $add ) {
				if ( ! empty( $add['value'] ) ) {
					$html .= $add['value'];
				} elseif ( isset( $add['callback'] ) ) {
					$send = array( $vars );
					if ( isset( $vars['_template'] ) ) {
						$send[] = $vars['_template'];
					}
					$html .= call_user_func_array( $add['callback'], $send );
				}
			}
		}
		return $html;
	}

	/**
	 * Get the id of the current listing to use for shortcodes.
	 *
	 * @since v5.9
	 */
	private function add_current_listing_id( &$vars ) {
		$vars = (array) $vars;
		if ( ! empty( $vars['id'] ) && ! is_numeric( $vars['id'] ) ) {
			$vars['id'] = wpbdp_get_post_by_id_or_slug( $vars['id'], 'slug', 'id' );
			return;
		}

		if ( ! is_singular( WPBDP_POST_TYPE ) || ! empty( $vars['id'] ) ) {
			return;
		}

		global $post;
		$vars['id'] = $post->ID;
	}

    /**
     * @since 4.0
     */
    function sc_categories( $atts ) {
        return wpbdp_list_categories( $atts );
    }

    /**
     * @since 4.0
     */
    public function sc_count( $atts ) {
        $atts = shortcode_atts( array( 'category' => false, 'region' => false ), $atts );
        extract( $atts );

        // All listings.
        if ( ! $category && ! $region ) {
            $count = wp_count_posts( WPBDP_POST_TYPE );
            return $count->publish;
        }

        if ( ! function_exists( 'wpbdp_regions_taxonomy' ) )
            $region = false;

        $term = false;
        $region_term = false;

        if ( $category ) {
            foreach ( array( 'id', 'name', 'slug' ) as $field ) {
                if ( $term = get_term_by( $field, $category, WPBDP_CATEGORY_TAX ) )
                    break;
            }
        }

        if ( $region ) {
            foreach ( array( 'id', 'name', 'slug' ) as $field ) {
                if ( $region_term = get_term_by( $field, $region, wpbdp_regions_taxonomy() ) )
                    break;
            }
        }

        if ( ( $region && ! $region_term ) || ( $category && ! $term ) )
            return '0';

        if ( $region ) {
            $regions_api = wpbdp_regions_api();
            return $regions_api->count_listings( (int) $region_term->term_id, $term ? (int) $term->term_id : 0 );
        } else {
            _wpbdp_padded_count( $term );
            return $term->count;
        }
    }

    public function sc_manage_listings( $atts, $content, $shortcode ) {
        $atts = shortcode_atts( array( 'showsearchbar' => true ), $atts, $shortcode );

        if ( in_array( $atts['showsearchbar'], array( 'no', 'false', '0' ), true ) ) {
            $atts['showsearchbar'] = false;
        } else {
            $atts['showsearchbar'] = true;
        }

        $v = wpbdp_load_view( 'manage_listings', array( 'show_search_bar' => $atts['showsearchbar'] ) );
        return $v->dispatch();
    }

    public function sc_search( $atts ) {
        $atts = shortcode_atts(
            array(
                'return_url' => '',
                'form_only'  => 0
            ),
            $atts,
            'businessdirectory-search'
        );

        if ( in_array( $atts['form_only'], array( 'yes', 'true', '1' ), true ) ) {
            $atts['form_only'] = true;
        } else {
            $atts['form_only'] = false;
        }

        if ( 'auto' == $atts['return_url'] ) {
            $atts['return_url'] = home_url( wpbdp_get_server_value( 'REQUEST_URI' ) );
        }

        $atts['in_shortcode'] = true;

        $v = wpbdp_load_view( 'search', $atts );
        return $v->dispatch();
    }

    public function sc_quick_search( $atts ) {
        $defaults = array(
            'buttons' => 'none'
        );
        $atts = shortcode_atts( $defaults, $atts, 'businessdirectory-quick-search' );

        switch ( $atts['buttons'] ) {
            case 'all':
                $buttons = array( 'directory', 'listings', 'create' );
                break;
            case 'none':
                $buttons = array();
                break;
            default:
                $buttons = array_filter( explode( ',', trim( $atts['buttons'] ) ) );
                break;
        }

        $box_args = array(
            'buttons' => $buttons,
            'in_shortcode' => true,
        );

        return wpbdp_main_box( $box_args );
    }

    public function validate_attributes( &$sc_atts, $atts = array() ) {

        if ( ! empty( $atts['pagination'] ) ) {
            switch ( strtolower( $atts['pagination'] ) ) {
                case '1':
                case 'true':
                case 'yes':
                    $sc_atts['pagination'] = true;
                    break;
                case '0':
                case 'false':
                case 'no':
                default:
                    $sc_atts['pagination'] = false;
            }
        }

        // Backward compatibility for `limit` parameter
        if ( ! empty( $sc_atts['limit'] ) ) {
            $sc_atts['items_per_page'] = intval( $sc_atts['items_per_page'] ) > 0 ? intval( $sc_atts['items_per_page'] ) : intval( $sc_atts['limit'] );
        }

        if ( 0 >= intval( $sc_atts['items_per_page'] ) ) {
            $sc_atts['items_per_page'] = ! isset( $sc_atts['pagination'] ) ? ( wpbdp_get_option( 'listings-per-page' ) > 0 ? wpbdp_get_option( 'listings-per-page' ) : -1 ) : -1;
        }

		if ( ! empty( $sc_atts['pagination'] ) && 0 > intval( $sc_atts['items_per_page'] ) ) {
            $sc_atts['items_per_page'] = wpbdp_get_option( 'listings-per-page' ) > 0 ? wpbdp_get_option( 'listings-per-page' ) : -1;
        }

        if ( isset( $sc_atts['pagination'] ) && ! $sc_atts['pagination'] ) {
            $sc_atts['items_per_page'] = -1;
        }
    }

    private function maybe_paginate_frontpage( $query ) {
		if ( ! function_exists( 'wp_pagenavi' ) && is_front_page() && isset( $query->query['paged'] ) ) {
            global $paged;
            $paged = $query->query['paged'];
        }
    }

    public function maybe_set_shortcode_query_flags( $query ) {
        if ( $query->wpbdp_is_main_page || ! $query->is_page || ! $query->queried_object || empty( $query->queried_object->post_content ) ) {
            return;
        }

        foreach ( array_keys( $this->get_shortcodes() ) as $shortcode ) {
            if ( wpbdp_has_shortcode( $query->queried_object->post_content, $shortcode ) ) {
                $query->wpbdp_is_shortcode = true;
            }
        }
    }
}
