<?php

class WPBDP__Views__All_Listings extends WPBDP__View {

    public function get_title() {
        return __( 'View All Listings', 'business-directory-plugin' );
    }

    public function dispatch() {
        $args_ = isset( $this->query_args ) ? $this->query_args : array();

        $paged = get_query_var( 'page' ) ? get_query_var( 'page' ) : ( get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1 );
        $args = array(
            'post_type' => WPBDP_POST_TYPE,
            'posts_per_page' => wpbdp_get_option( 'listings-per-page' ) > 0 ? wpbdp_get_option( 'listings-per-page' ) : -1,
            'post_status' => 'publish',
			'paged'              => intval( $paged ),
			'orderby'            => wpbdp_get_option( 'listings-order-by', 'title' ),
			'order'              => wpbdp_get_option( 'listings-sort', 'ASC' ),
            'wpbdp_main_query' => true,
            'wpbdp_in_shortcode' => true,
        );

        if ( isset( $args_['numberposts'] ) )
            $args['posts_per_page'] = $args_['numberposts'];

        if ( isset( $args_['items_per_page'] ) )
            $args['posts_per_page'] = $args_['items_per_page'];

        if ( ! empty( $args_['author'] ) )
            $args['author'] = $args_['author'];

        $args = array_merge( $args, $args_ );

        $q = new WP_Query( $args );

        // Try to trick pagination to remove it when processing a shortcode.
        if ( ! empty( $this->in_shortcode ) && empty( $this->pagination ) ) {
            $q->max_num_pages = 1;
        }
        wpbdp_push_query( $q );
		$should_have_menu = isset( $this->in_shortcode ) ? ! $this->in_shortcode : empty( $args['tax_query'] );
		$show_menu = isset( $this->menu ) ? $this->menu : $should_have_menu;

        $template_args = array( '_id' => $show_menu ? 'all_listings' : 'listings',
                                '_wrapper' => $show_menu ? 'page' : '',
                                '_bar'     => $show_menu,
                                'query' => $q );

		if ( ! function_exists( 'wp_pagenavi' ) && is_front_page() ) {
            global $paged;
            $paged = $q->query['paged'];
        }

        $html = wpbdp_x_render( 'listings', $template_args );
        wp_reset_postdata();
		wpbdp_pop_query();

        return $html;
    }

}
