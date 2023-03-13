<?php

class WPBDP__Migrations__2_0 extends WPBDP__Migration {

    public function migrate() {
        global $wpdb;
        global $wpbdp;

        // make directory-related metadata hidden
        $old_meta_keys = array(
            'termlength', 'image', 'listingfeeid', 'sticky', 'thumbnail', 'paymentstatus', 'buyerfirstname', 'buyerlastname',
            'paymentflag', 'payeremail', 'paymentgateway', 'totalallowedimages', 'costoflisting'
        );

        foreach ($old_meta_keys as $meta_key) {
            $query = $wpdb->prepare("UPDATE {$wpdb->postmeta} SET meta_key = %s WHERE meta_key = %s AND {$wpdb->postmeta}.post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s)",
                                    '_wpbdp_' . $meta_key, $meta_key, 'wpbdm-directory');
			$wpdb->query( $query );
        }

		wpbdp_log( 'Made WPBDP directory metadata hidden attributes' );
    }
}
