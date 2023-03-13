<?php

class WPBDP__Migrations__6_0 extends WPBDP__Migration {

    public function migrate() {
        global $wpdb;

        $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_payments MODIFY created_on TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP" );
        $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}wpbdp_payments SET processed_on = NULL WHERE processed_on = %s", '0000-00-00 00:00:00' ) );
    }
}

