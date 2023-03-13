<?php

class WPBDP_FieldTypes_Twitter extends WPBDP_Form_Field_Type {

    public function __construct() {
		parent::__construct( _x( 'Social Site (Twitter handle)', 'form-fields api', 'business-directory-plugin' ) );
    }

    public function get_id() {
        return 'social-twitter';
    }

    public function setup_field( &$field ) {
        $field->add_display_flag( 'social' );
    }

    /**
     * @since 5.0.5
     */
    public function before_field_update( $field ) {
        // Twitter field does not support validators (except 'required').
        $validators = array();

        if ( $field->has_validator( 'required' ) ) {
            $validators[] = 'required';
        }

        if ( $field->has_validator( 'required-in-search' ) ) {
            $validators[] = 'required-in-search';
        }

        $field->set_validators( $validators );
    }


	public function render_field_inner( &$field, $value, $context, &$extra = null, $field_settings = array() ) {
        // twitter fields are rendered as normal textfields
        global $wpbdp;
        return $wpbdp->formfields->get_field_type( 'textfield' )->render_field_inner( $field, $value, $context, $extra, $field_settings );
    }

    public function get_supported_associations() {
        return array( 'meta' );
    }

    public function get_field_value( &$field, $post_id ) {
        $value = parent::get_field_value( $field, $post_id );

        $value = str_ireplace( array('http://twitter.com/', 'https://twitter.com/', 'http://www.twitter.com/', 'https://www.twitter.com/'), '', $value );
        $value = rtrim( $value, '/' );
        $value = ltrim( $value, ' @' );

        return $value;
    }

    public function get_field_html_value( &$field, $post_id ) {
        $value = $field->value( $post_id );

        if ( ! $value )
            return '';

        $html  = '';
        $html .= '<div class="social-field twitter twitter-handle">';
        $html .= sprintf(
            '<a href="https://twitter.com/%s" class="twitter-follow-button" data-show-count="%s" data-lang="%s">Follow @%s</a>',
            $value,
            ! empty( $field->data( 'show_count' ) ) ? 'true' : 'false',
            substr( get_bloginfo( 'language' ), 0, 2 ),
            $value
        );
        $html .= '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';
        $html .= '</div>';

        return $html;
    }

    /**
     * @since 5.5.10
     */
	public function render_field_settings( &$field = null, $association = null ) {
        $settings = array();

        $settings['show_count'][] = _x( 'Show followers count?', 'form-fields admin', 'business-directory-plugin' );
        $settings['show_count'][] = '<input type="checkbox" value="1" name="field[show_count]" ' . ( $field && $field->data( 'show_count' ) ? ' checked="checked"' : '' ) . ' />';

        return self::render_admin_settings( $settings );
    }

    /**
     * @since 5.5.10
     */
    public function process_field_settings( &$field ) {
        $field->set_data( 'show_count', isset( $_POST['field']['show_count'] ) ? (bool) intval( $_POST['field']['show_count'] ) : false );
    }

}

