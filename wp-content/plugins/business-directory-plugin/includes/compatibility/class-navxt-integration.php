<?php
/**
 * @since 3.6.5
 */
class WPBDP_NavXT_Integration {

    private $state = array();
    private $doing = '';


    function __construct() {
        add_action( 'bcn_before_fill', array( &$this, 'prepare_state' ) );
        add_action( 'bcn_after_fill', array( &$this, 'restore_state' ) );
    }

    function prepare_state( $trail ) {
        if ( $this->doing )
            return;

        global $wpbdp;
        $action = wpbdp_current_view();

		$doing = array(
			'show_listing'   => 'listing',
			'show_category'  => 'category',
			'show_tag'       => 'tag',
			'edit_listing'   => 'edit',
			'submit_listing' => 'submit',
			'search'         => 'search',
		);

		if ( ! isset( $doing[ $action ] ) ) {
			$this->doing = '';
			return;
		}

		$this->doing = $doing[ $action ];

        if ( method_exists( $this, 'before_' . $this->doing ) )
            call_user_func( array( $this, 'before_' . $this->doing ), $trail );
    }

    function restore_state( $trail ) {
        if ( ! $this->doing )
            return;

        if ( method_exists( $this, 'after_' . $this->doing ) )
            call_user_func( array( $this, 'after_' . $this->doing ), $trail );

        $this->doing = '';
    }

    function main_page_breadcrumb( $trail ) {
		if ( $this->has_dir_page( $trail ) ) {
			return;
		}

		if ( $this->has_home_page( $trail ) ) {
			$home = array_pop( $trail->trail );
		}

		$trail->add(
			new bcn_breadcrumb(
				get_the_title( wpbdp_get_page_id() ),
				null,
				array(),
				wpbdp_get_page_link(),
				wpbdp_get_page_id(),
				true
			)
		);

		// Include the home link first.
		if ( isset( $home ) ) {
			$trail->add( $home );
		}
    }

	/**
	 * @since 6.2.7
	 */
	private function has_home_page( $trail ) {
		$last = end( $trail->trail );

		$types = array();
		if ( method_exists( $last, 'get_types' ) ) {
			$types = $last->get_types();
		}
		return in_array( 'home', $types, true );
	}

	/**
	 * Check if BD is already included to avoid a duplicate.
	 *
	 * @since 6.2.7
	 */
	private function has_dir_page( $trail ) {
		$page_id = wpbdp_get_page_id();
		foreach ( $trail->breadcrumbs as $link ) {
			if ( $link->get_id() === (int) $page_id ) {
				return true;
			}
		}
		return false;
	}

    function before_listing( $trail ) {
        $listing_id = $this->get_current_listing_id();

        if ( ! $listing_id )
            return;

        $this->state['post'] = $GLOBALS['post'];
        $GLOBALS['post'] = get_post( $listing_id );
    }

    /**
     * This should probably be an utility function.
     *
     * TODO: Can we replace wpbdp_current_listing_id with this?
     * TODO: Are 'listing' and 'id' still used to get the ID of the
     *       listing being displayed?
     *
     * @since 4.1.10
     */
    private function get_current_listing_id() {
        $id_or_slug = get_query_var( 'listing' );

        if ( ! $id_or_slug && isset( $_GET['listing'] ) ) {
            $id_or_slug = wpbdp_get_var( array( 'param' => 'listing' ) );
        }

        if ( ! $id_or_slug ) {
            $id_or_slug = get_query_var( 'id' );
        }

        if ( ! $id_or_slug && isset( $_GET['id'] ) ) {
            $id_or_slug = wpbdp_get_var( array( 'param' => 'id' ) );
        }

        if ( ! $id_or_slug ) {
            $id_or_slug = get_query_var( '_' . wpbdp_get_option( 'permalinks-directory-slug' ) );
        }

        if ( $id_or_slug ) {
            $listing_id = wpbdp_get_post_by_id_or_slug( $id_or_slug, 'id', 'id' );
        } else {
            $listing_id = get_queried_object_id();
        }

        return $listing_id;
    }

    function after_listing( $trail ) {
        $GLOBALS['post'] = $this->state['post'];
        unset( $this->state['post'] );

        $this->main_page_breadcrumb( $trail );
    }

	function before_category() {
		if ( ! apply_filters( 'wpbdp_use_single', false ) ) {
			// If the template hasn't been changed, no override is needed.
			return;
		}

		$this->before_tax( _wpbpd_current_category() );
	}

    function after_category( $trail ) {
		$this->main_page_breadcrumb( $trail );

		if ( empty( $this->state['queried'] ) ) {
            return;
        }

		global $wp_query;

        $wp_query->queried_object = $this->state['queried'];
        $wp_query->is_singular = true;
        unset( $this->state['queried'] );

    }

	function before_tag() {
		if ( ! apply_filters( 'wpbdp_use_single', false ) ) {
			// If the template hasn't bee changed, no override is needed.
			return;
		}

		$tag = get_term_by( 'id', wpbdp_current_tag_id(), WPBDP_TAGS_TAX );
		$this->before_tax( $tag );
	}

	/**
	 * @since 6.2.8
	 */
	private function before_tax( $term ) {
		if ( ! $term ) {
			return;
		}

		global $wp_query;
		$this->state['queried'] = $wp_query->get_queried_object();

		$wp_query->is_singular = false;
		$wp_query->queried_object = $term;
	}

    function after_tag( $trail ) {
        $this->after_category( $trail );
    }

    function before_submit( $trail ) {
        $trail->add( new bcn_breadcrumb( _x( 'Submit Listing', 'navxt', 'business-directory-plugin' ) ) );
    }

    function before_edit( $trail ) {
		$trail->add( new bcn_breadcrumb( __( 'Edit Listing', 'business-directory-plugin' ) ) );
    }

    function before_search( $trail ) {
		$trail->add( new bcn_breadcrumb( __( 'Search', 'business-directory-plugin' ) ) );
    }

}
