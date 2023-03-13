<?php

class WPBDP__Migrations__5_0 extends WPBDP__Migration {

    /**
     * This upgrade routine takes care of the term splitting feature that is going to be introduced in WP 4.2.
	 *
     * @since 3.6.4
     */
    public function migrate() {
        global $wp_version;

        if ( ! function_exists( 'wp_get_split_term' ) )
            return;

        $terms = $this->gather_pre_split_term_ids();
        foreach ( $terms as $term_id )
            $this->process_term_split( $term_id );
    }

    /**
     * @since 3.6.4
     */
    private function gather_pre_split_term_ids() {
        global $wpdb;

        $res = array();

        // Fees.
        $fees = $wpdb->get_col( "SELECT categories FROM {$wpdb->prefix}wpbdp_fees" );
        foreach ( $fees as $f ) {
            $data = unserialize( $f );

            if ( isset( $data['all'] ) && $data['all'] )
                continue;

            if ( ! empty( $data['categories'] ) )
                $res = array_merge( $res, $data['categories'] );

        }

        // Listing fees.
        if ( $fee_ids = $wpdb->get_col( "SELECT DISTINCT category_id FROM {$wpdb->prefix}wpbdp_listing_fees" ) ) {
            $res = array_merge( $res, $fee_ids );
        }

        // Payments.
        $payments_terms = $wpdb->get_col(
                $wpdb->prepare( "SELECT DISTINCT rel_id_1 FROM {$wpdb->prefix}wpbdp_payments_items WHERE ( item_type = %s OR item_type = %s )",
                                'fee',
                                'recurring_fee' )
        );
        $res = array_merge( $res, $payments_terms );

        // Category images.
        $imgs = get_option( 'wpbdp[category_images]', false );
        if ( $imgs && is_array( $imgs ) ) {
			if ( ! empty( $imgs['images'] ) ) {
				$res = array_merge( $res, array_keys( $imgs['images'] ) );
			}

            if ( ! empty( $imgs['temp'] ) )
                $res = array_merge( $res, array_keys( $imgs['temp'] ) );
        }

        return array_map( 'intval', array_unique( $res ) );
    }

    /**
     * Use this function to update BD references of a pre-split term ID to use the new term ID.
	 *
     * @since 3.6.4
     */
    public function process_term_split( $old_id = 0 ) {
        global $wpdb;

        if ( ! $old_id )
            return;

        $new_id = wp_get_split_term( $old_id, WPBDP_CATEGORY_TAX );
        if ( ! $new_id )
            return;

        // Fees.
        $fees = $wpdb->get_results( "SELECT id, categories FROM {$wpdb->prefix}wpbdp_fees" );
        foreach ( $fees as &$f ) {
            $categories = unserialize( $f->categories );

            if ( ( isset( $categories['all'] ) && $categories['all'] ) || empty( $categories['categories'] ) )
                continue;

            $index = array_search( $old_id, $categories['categories'] );

            if ( $index === false )
                continue;

            $categories['categories'][ $index ] = $new_id;
            $wpdb->update( $wpdb->prefix . 'wpbdp_fees',
                           array( 'categories' => serialize( $categories ) ),
                           array( 'id' => $f->id ) );
        }

        // Listing fees.
        $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}wpbdp_listing_fees SET category_id = %d WHERE category_id = %d",
                                      $new_id,
                                      $old_id ) );

        // Payments.
        $wpdb->query(
            $wpdb->prepare( "UPDATE {$wpdb->prefix}wpbdp_payments_items SET rel_id_1 = %d WHERE ( rel_id_1 = %d AND ( item_type = %s OR item_type = %s ) )",
                            $new_id,
                            $old_id,
                            'fee',
                            'recurring_fee' )
        );

        // Category images.
        $imgs = get_option( 'wpbdp[category_images]', false );
        if ( empty( $imgs ) || ! is_array( $imgs ) )
            return;

        if ( ! empty( $imgs['images'] ) && isset( $imgs['images'][ $old_id ] ) ) {
            $imgs['images'][ $new_id ] = $imgs['images'][ $old_id ];
            unset( $imgs['images'][ $old_id ] );
        }

        if ( ! empty( $imgs['temp'] ) && isset( $imgs['temp'][ $old_id ] ) ) {
            $imgs['temp'][ $new_id ] = $imgs['temp'][ $old_id ];
            unset( $imgs['temp'][ $old_id ] );
        }

        update_option( 'wpbdp[category_images]', $imgs );
    }

}
