<?php

class WPBDP__Migrations__7_0 extends WPBDP__Migration {

    public function migrate() {
        global $wpdb;

        $fields = $wpdb->get_results( $wpdb->prepare( "SELECT id, field_type FROM {$wpdb->prefix}wpbdp_form_fields WHERE field_type IN (%s, %s, %s, %s) AND association = %s",
                                                      'select', 'multiselect', 'checkbox', 'radio', 'meta' ) );

        foreach ( $fields as $f ) {
            $listing_values = $wpdb->get_results( $wpdb->prepare( "SELECT meta_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = %s",
                                                                  '_wpbdp[fields][' . $f->id . ']' ) );

            foreach ( $listing_values as $lv ) {
                $v = maybe_unserialize( $lv->meta_value );

                if ( in_array( $f->field_type, array( 'select', 'radio' ), true ) ) {
                    if ( is_array( $v ) )
                        $v = array_pop( $v );
                } else {
                    if ( is_array( $v ) )
                        $v = implode( "\t", $v );
                }

                $wpdb->update( $wpdb->postmeta, array( 'meta_value' => $v ), array( 'meta_id' => $lv->meta_id ) );
            }
        }
    }

}
