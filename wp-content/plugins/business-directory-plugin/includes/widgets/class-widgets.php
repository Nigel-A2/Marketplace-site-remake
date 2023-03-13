<?php
/**
 * @since 5.0
 */
class WPBDP__Widgets {

    public function __construct() {
        add_action( 'widgets_init', array( $this, 'register_widgets' ) );
    }

    public function register_widgets() {
		include_once WPBDP_INC . 'widgets/widget-featured-listings.php';
		register_widget( 'WPBDP_FeaturedListingsWidget' );

		include_once WPBDP_INC . 'widgets/widget-latest-listings.php';
		register_widget( 'WPBDP_LatestListingsWidget' );

		include_once WPBDP_INC . 'widgets/widget-random-listings.php';
		register_widget( 'WPBDP_RandomListingsWidget' );

		include_once WPBDP_INC . 'widgets/widget-search.php';
		register_widget( 'WPBDP_SearchWidget' );
    }

}
