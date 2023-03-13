<?php

class WPBDP__Migrations__3_1 extends WPBDP__Migration {

    public function migrate() {
        global $wpdb;

		$wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->posts} SET post_type = %s WHERE post_type = %s", WPBDP_POST_TYPE, 'wpbdm-directory' ) );

		if ( function_exists( 'flush_rewrite_rules' ) ) {
			flush_rewrite_rules( false );
		}
    }

}
