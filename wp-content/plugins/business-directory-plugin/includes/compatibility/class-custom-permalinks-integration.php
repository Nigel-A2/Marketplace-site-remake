<?php
/**
 * Compatibility code for Custom Permalinks plugin.
 */

/**
 * Integration with Custom Permalinks plugin.
 */
class WPBDP_Custom_Permalink_Integration {

    /**
     * @since 5.1.10
     */
    public function __construct() {
        add_filter( 'wpbdp_url_base_url', array( $this, 'wpbdp_cp_base_url'), 10, 2 );
    }

    /**
     * @param string $page_link Current Page Link.
     * @param int    $page_id   Current Page ID.
     * @since 5.1.10
     */
	public function wpbdp_cp_base_url( $page_link, $page_id ) {
        return apply_filters( 'page_link', $page_link, $page_id );
    }
}

