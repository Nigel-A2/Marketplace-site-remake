<?php

class WPBDP_FieldTypes_Facebook extends WPBDP_Form_Field_Type {

    public function __construct() {
		parent::__construct( _x( 'Social Site (Facebook page)', 'form-fields api', 'business-directory-plugin' ) );
    }

    public function get_id() {
        return 'social-facebook';
    }

    public function setup_field( &$field ) {
        $field->add_display_flag( 'social' );
    }

	public function render_field_inner( &$field, $value, $context, &$extra = null, $field_settings = array() ) {
        // facebook fields are rendered as normal textfields
        global $wpbdp;
        return $wpbdp->formfields->get_field_type( 'textfield' )->render_field_inner( $field, $value, $context, $extra, $field_settings );
    }

    public function get_supported_associations() {
        return array( 'meta' );
    }

    public function get_field_html_value( &$field, $post_id ) {
        $value = $field->value( $post_id );

        if ( ! $value ) {
            return '';
        }

        $html  = '';
        $html .= '<div class="social-field facebook">';
        $html .= '<div id="fb-root"></div>';
        $html .= '<script>(function(d, s, id) {
            var js, fjs = d.getElementsByTagName(s)[0];
            if (d.getElementById(id)) return;
            js = d.createElement(s); js.id = id;
            js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
            fjs.parentNode.insertBefore(js, fjs);
          }(document, \'script\', \'facebook-jssdk\'));</script>';

        // data-layout can be 'box_count', 'standard' or 'button_count'
        // ref: https://developers.facebook.com/docs/reference/plugins/like/
        $html .= sprintf( '<div class="fb-like" data-href="%s" data-send="false" data-width="200" data-layout="button_count" data-show-faces="false"></div>', $value );
        $html .= '</div>';

        return $html;
    }

}

