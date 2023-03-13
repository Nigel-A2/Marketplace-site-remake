<?php
/**
 * @since 5.5.8
 */
class WPBDP_Cornerstone_Compat {

    public function __construct() {
        add_filter( 'wpbdp_has_shortcode', array( &$this, 'cornerstone_wpbdp_has_shortcode' ), 10, 3 );
    }

    public function cornerstone_wpbdp_has_shortcode( $has_shortcode, $post, $shortcode ) {
		if ( $has_shortcode ) {
            return $has_shortcode;
        }

        return wpbdp_has_shortcode( get_post_meta( $post->ID, '_cornerstone_data' )[0], $shortcode );
    }
}
