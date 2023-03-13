<?php

class WPBDP_FieldTypes_Phone_Number extends WPBDP_FieldTypes_TextField {

    public function __construct() {
    }

    public function get_id() {
        return 'phone_number';
    }

    public function get_name() {
        return __( 'Phone Number', 'business-directory-plugin' );
    }

    public function get_supported_associations() {
        return array( 'meta' );
    }

    public function get_field_html_value( &$field, $post_id ) {
        $val = $this->get_field_value( $field, $post_id );

        if ( ! $val )
            return '';

        return '<a href="tel:' . esc_attr( $val ) . '">' . esc_html( $val ) . '</a>';
    }

    public function store_field_value( &$field, $post_id, $value ) {
        $value = preg_replace( '/[^0-9\-\–\+\.\s]+/', '', $value );

        return parent::store_field_value( $field, $post_id, $value );
    }

}

