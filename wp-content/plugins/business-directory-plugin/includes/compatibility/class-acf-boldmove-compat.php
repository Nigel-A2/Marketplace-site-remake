<?php
/**
 * @package WPBDP/Compatibility/ACF-Boldmove Compat
 */
class WPBDP_ACF_Compat {

    public function __construct() {
        add_action( 'body_class', array( $this, 'change_query' ), 9999 );
    }

	public function change_query( $classes = array() ) {
        global $wp_query;

		if ( ! $wp_query->wpbdp_our_query ) {
            return $classes;
        }

        $page_id = wpbdp_get_page_id( 'main' );

		if ( $page_id === $wp_query->queried_object_id ) {
            return $classes;
        }

        $page = get_post( $page_id );

        $wp_query->queried_object = $page;
        $wp_query->queried_object_id = $page_id;

        return $classes;
    }
}
