<?php
require_once WPBDP_PATH . 'includes/widgets/class-listings-widget.php';

/**
 * Featured listings widget.
 *
 * @since 2.1
 */
class WPBDP_FeaturedListingsWidget extends WPBDP_Listings_Widget {

    public function __construct() {
		parent::__construct(
			_x( 'Business Directory - Featured Listings', 'widgets', 'business-directory-plugin' ),
			_x( 'Displays a list of the featured/sticky listings in the directory.', 'widgets', 'business-directory-plugin' )
		);

        $this->set_default_option_value( 'title', _x( 'Featured Listings', 'widgets', 'business-directory-plugin' ) );
    }

    protected function _form( $instance ) {
		printf(
			'<p><input id="%s" name="%s" type="checkbox" value="1" %s /> <label for="%s">%s</label></p>',
			esc_attr( $this->get_field_id( 'random_order' ) ),
			esc_attr( $this->get_field_name( 'random_order' ) ),
			! empty( $instance['random_order'] ) ? 'checked="checked"' : '',
			esc_attr( $this->get_field_id( 'random_order' ) ),
			esc_html__( 'Display listings in random order', 'business-directory-plugin' )
		);
    }

	public function update( $new, $old ) {
		$instance = parent::update( $new, $old );
		$instance['random_order'] = ! empty( $new['random_order'] ) ? 1 : 0;
		return $instance;
	}

    public function get_listings( $instance ) {
        global $wpdb;

        $q = $wpdb->prepare(
            "SELECT DISTINCT {$wpdb->posts}.ID FROM {$wpdb->posts}
             JOIN {$wpdb->prefix}wpbdp_listings lp ON lp.listing_id = {$wpdb->posts}.ID
             WHERE {$wpdb->posts}.post_status = %s AND {$wpdb->posts}.post_type = %s AND lp.is_sticky = 1
			 ORDER BY " . ( ! empty( $instance['random_order'] ) ? 'RAND()' : $wpdb->posts . '.post_date' ) .
			 ' LIMIT %d',
            'publish', WPBDP_POST_TYPE, $instance['number_of_listings'] );
        $featured = $wpdb->get_col( $q );

        $args = array(
            'post_type' => WPBDP_POST_TYPE,
            'post_status' => 'publish',
            'post__in' => $featured ? $featured : array( -1 ),
            'posts_per_page' => $instance['number_of_listings'],
            'orderby' => 'post__in',
            'suppress_filters' => false,
        );
        $posts = get_posts( $args );

        return $posts;
    }

}
