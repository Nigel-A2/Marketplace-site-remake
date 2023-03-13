<?php

class WPBDP__Migrations__17_0 extends WPBDP__Migration {

    public function migrate() {
        $form_fields = $this->get_list_form_fields();

        foreach ( $form_fields as $form_field ) {
            $options = array();

            foreach ( $form_field->data( 'options' ) as $key => $value ) {
                $new_key = trim( preg_replace( '/[\r\n]/', '', $key ) );
                $options[ $new_key ] = trim( preg_replace( '/[\r\n]/', '', $value ) );
            }

            $form_field->set_data( 'options', $options );
            $form_field->save();
        }

        if ( $this->count_listing_meta_with_invalid_characters( array_keys( $form_fields ) ) ) {
            $this->request_manual_upgrade( 'upgrade_to_16_fix_form_fields_data' );
        }
    }

    private function get_list_form_fields() {
        $form_fields = array();
        $find_params = array(
            'association' => 'meta',
            'field_type' => array( 'select', 'checkbox', 'radio', 'multiselect' )
        );

        foreach ( wpbdp_get_form_fields( $find_params ) as $form_field ) {
			$form_fields[ '_wpbdp[fields][' . $form_field->get_id() . ']' ] = $form_field;
        }

        return $form_fields;
    }

    private function get_listing_meta_with_invalid_characters( $meta_keys ) {
        global $wpdb;

        if ( ! $meta_keys ) {
            return array();
        }

        $sql = "SELECT post_id, meta_id, meta_key, meta_value FROM {$wpdb->postmeta} ";
		$sql .= "WHERE meta_key IN (%s) AND meta_value REGEXP '^ |\\t | \\t| $|[\\r\\n]' ";
		$sql .= 'LIMIT 50';

        $sql = sprintf( $sql, "'" . implode( "', '", $meta_keys ) . "'" );

        return $wpdb->get_results( $sql );
    }

    public function upgrade_to_16_fix_form_fields_data() {
        $form_fields = $this->get_list_form_fields();
        $meta_entries = $this->get_listing_meta_with_invalid_characters( array_keys( $form_fields ) );

        foreach ( $meta_entries as $meta_entry ) {
            $meta_value = maybe_unserialize( $meta_entry->meta_value );

            if ( is_string( $meta_value ) ) {
                $meta_value = explode( "\t", $meta_entry->meta_value );
            }

            $sanitized_value = array();

            foreach ( (array) $meta_value as $key => $value ) {
                $sanitized_value[ $key ] = trim( preg_replace( '/[\r\n]/', '', $value ) );
            }

            $form_fields[ $meta_entry->meta_key ]->store_value( $meta_entry->post_id, $sanitized_value );
        }

        $records_left = $this->count_listing_meta_with_invalid_characters( array_keys( $form_fields ) );

        $message = _x( 'Cleaning up stored meta data for Checkbox, Radio and Select fields... (%d records left)', 'installer', 'business-directory-plugin' );

        return array(
            'ok' => true,
            'done' => $records_left == 0,
            'status' => sprintf( $message, $records_left ),
        );
    }

    private function count_listing_meta_with_invalid_characters( $meta_keys ) {
        global $wpdb;

        if ( ! $meta_keys ) {
            return 0;
        }

        $sql = "SELECT COUNT(meta_id) FROM {$wpdb->postmeta} WHERE meta_key IN (%s) AND meta_value REGEXP '^ |\\t | \\t| $|[\\r\\n]'";
        $sql = sprintf( $sql, "'" . implode( "', '", $meta_keys ) . "'" );

        return intval( $wpdb->get_var( $sql ) );
    }

}
