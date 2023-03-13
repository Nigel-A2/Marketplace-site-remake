<?php
/**
 * Compatibility code for Advanced Excerpt plugin.
 */

/**
 * Integration with Advanced Excerpt plugin.
 */
class WPBDP_Advanced_Excerpt_Integration {

    /**
     * @since 5.0.2
     */
    public function __construct() {
        add_filter( 'advanced_excerpt_skip_page_types', array( $this, 'filter_skip_page_types' ) );
    }

    /**
     * @param array $page_types A list of page types that are already skipped.
     * @since 5.0.2
     */
    public function filter_skip_page_types( $page_types ) {
        return array_merge( array( WPBDP_CATEGORY_TAX, WPBDP_TAGS_TAX ), $page_types );
    }
}

