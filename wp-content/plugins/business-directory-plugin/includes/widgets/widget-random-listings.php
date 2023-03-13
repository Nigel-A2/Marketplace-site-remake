<?php
require_once WPBDP_PATH . 'includes/widgets/class-listings-widget.php';

/**
 * Random listings widget.
 *
 * @since 2.1
 */
class WPBDP_RandomListingsWidget extends WPBDP_Listings_Widget {

    public function __construct() {
		parent::__construct(
			_x( 'Business Directory - Random Listings', 'widgets', 'business-directory-plugin' ),
			_x( 'Displays a list of random listings from the Business Directory.', 'widgets', 'business-directory-plugin' )
		);

        $this->set_default_option_value( 'title', _x( 'Random Listings', 'widgets', 'business-directory-plugin' ) );
    }

    public function get_listings( $instance ) {
        $posts = new WP_Query(
            array(
                'post_type' => WPBDP_POST_TYPE,
                'post_status' => 'publish',
                'suppress_filters' => false,
                'posts_per_page'   => -1
            )
        );

        $posts       = $posts->posts;
        $posts_count = count( $posts );

        if ( ! $posts_count ) {
            return;
        }
        $number_of_listings = $this->get_field_value( $instance, 'number_of_listings' );
        $keys = (array) array_rand( $posts, $number_of_listings < $posts_count ? $number_of_listings : $posts_count );
        $rand = array();

        foreach ( $keys as $key ) {
			$rand[] = $posts[ $key ];
        }

        return $rand;
    }

}
