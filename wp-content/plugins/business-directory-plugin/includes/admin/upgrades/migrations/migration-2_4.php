<?php

class WPBDP__Migrations__2_4 extends WPBDP__Migration {

    public function migrate() {
        global $wpdb;
        global $wpbdp;

        $fields = $wpbdp->formfields->get_fields();

        foreach ($fields as &$field) {
            $query = $wpdb->prepare("UPDATE {$wpdb->postmeta} SET meta_key = %s WHERE meta_key = %s AND {$wpdb->postmeta}.post_id IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s)",
                                    '_wpbdp[fields][' . $field->get_id() . ']', $field->get_label(), 'wpbdm-directory');
			$wpdb->query( $query );
        }
    }

}
