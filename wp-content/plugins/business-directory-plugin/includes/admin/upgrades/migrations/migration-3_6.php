<?php

class WPBDP__Migrations__3_6 extends WPBDP__Migration {

    public function migrate() {
        global $wpdb;

        $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_form_fields MODIFY id bigint(20) AUTO_INCREMENT" );
        $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_fees MODIFY id bigint(20) AUTO_INCREMENT" );
        $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_payments MODIFY id bigint(20) AUTO_INCREMENT" );
        $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_listing_fees MODIFY id bigint(20) AUTO_INCREMENT" );

		update_option( WPBDP__Settings::PREFIX . 'listings-per-page', get_option( 'posts_per_page' ) );
    }

}
