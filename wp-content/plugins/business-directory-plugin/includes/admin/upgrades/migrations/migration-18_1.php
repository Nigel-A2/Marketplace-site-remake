<?php

class WPBDP__Migrations__18_1 extends WPBDP__Migration {

    public function migrate() {
        global $wpdb;

        // wpbdp_payments: move from 'created_on' column to 'created_at'.
        if ( wpbdp_column_exists( "{$wpdb->prefix}wpbdp_payments", 'created_on' ) ) {
            $wpdb->query( "UPDATE {$wpdb->prefix}wpbdp_payments SET created_at = FROM_UNIXTIME(UNIX_TIMESTAMP(created_on))" );
        }
    }

}
