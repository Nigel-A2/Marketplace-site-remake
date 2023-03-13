<?php

class WPBDP__Migrations__3_9 extends WPBDP__Migration {

    public function migrate() {
        // TODO: make sure this works when passing through manual 3.7 upgrade.
        global $wpdb;

        if ( $wpdb->get_col( $wpdb->prepare( "SHOW COLUMNS FROM {$wpdb->prefix}wpbdp_submit_state LIKE %s", 'created' ) ) )
            $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_submit_state DROP COLUMN created" );

        if ( $wpdb->get_col( $wpdb->prepare( "SHOW COLUMNS FROM {$wpdb->prefix}wpbdp_submit_state LIKE %s", 'updated' ) ) ) {
            $wpdb->query( "UPDATE {$wpdb->prefix}wpbdp_submit_state SET updated_on = updated" );
            $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_submit_state DROP COLUMN updated" );
        }
    }
}
