<?php

class WPBDP__Migrations__3_5 extends WPBDP__Migration {

    public function migrate() {
        global $wpdb;
        $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = %s WHERE taxonomy = %s", WPBDP_CATEGORY_TAX, 'wpbdm-category' ) );
        $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->term_taxonomy} SET taxonomy = %s WHERE taxonomy = %s", WPBDP_TAGS_TAX, 'wpbdm-tags' ) );
    }

}
