<?php
/**
 * URL Field Class
 *
 * @package WPBDP/Views/Includes/Fields/URL
 */

/**
 * Class WPBDP_FieldTypes_URL
 */
class WPBDP_FieldTypes_URL extends WPBDP_Form_Field_Type {

    public function __construct() {
        parent::__construct( __( 'URL Field', 'business-directory-plugin' ) );
        add_filter( 'wpbdp_form_field_css_classes', array( $this, 'css_classes' ), 10, 3 );
    }

    public function get_id() {
        return 'url';
    }

    public function get_supported_associations() {
        return array( 'meta' );
    }

	public function render_field_settings( &$field = null, $association = null ) {
        if ( $association != 'meta' )
            return '';

        $settings = array();

        $settings['new-window'][] = esc_html__( 'Open link in a new window?', 'business-directory-plugin' );
        $settings['new-window'][] = '<input type="checkbox" value="1" name="field[x_open_in_new_window]" ' . ( $field && $field->data( 'open_in_new_window' ) ? ' checked="checked"' : '' ) . ' />';

        $settings['nofollow'][] = esc_html__( 'Use rel="nofollow" when displaying the link?', 'business-directory-plugin' );
        $settings['nofollow'][] = '<input type="checkbox" value="1" name="field[x_use_nofollow]" ' . ( $field && $field->data( 'use_nofollow' ) ? ' checked="checked"' : '' ) . ' />';

        return self::render_admin_settings( $settings );
    }

    public function process_field_settings( &$field ) {
        if ( array_key_exists( 'x_open_in_new_window', $_POST['field'] ) ) {
            $open_in_new_window = (bool) intval( $_POST['field']['x_open_in_new_window'] );
            $field->set_data( 'open_in_new_window', $open_in_new_window );
        }

        if ( array_key_exists( 'x_use_nofollow', $_POST['field'] ) ) {
            $use_nofollow = (bool) intval( $_POST['field']['x_use_nofollow'] );
            $field->set_data( 'use_nofollow', $use_nofollow );
        }
    }

    public function setup_field( &$field ) {
        $field->add_validator( 'url' );
    }

    public function get_field_value( &$field, $post_id ) {
        $value = parent::get_field_value( $field, $post_id );

        if ( $value === null )
            return array( '', '' );

        if ( ! is_array( $value ) )
            return array( $value, '' );

        if ( ! isset( $value[1] ) || empty( $value[1] ) )
            $value[1] = '';

        return $value;
    }

    public function get_field_html_value( &$field, $post_id ) {
        $value = $field->value( $post_id );

        if ( empty( $value ) || empty( $value[0] ) ) {
            return '';
		}

		$rel = $field->data( 'use_nofollow' ) ? 'nofollow' : '';
		if ( $field->data( 'open_in_new_window' ) ) {
			$rel .= ' noopener';
		}

		$label = empty( $value[1] ) ? $value[0] : $value[1];

		return sprintf(
			'<a href="%s" rel="%s" target="%s" title="%s">%s</a>',
			esc_url( $value[0] ),
			esc_attr( $rel ),
			esc_attr( $field->data( 'open_in_new_window' ) ? '_blank' : '_self' ),
			esc_attr( $label ),
			esc_html( $label )
		);
    }

    public function get_field_plain_value( &$field, $post_id ) {
        $value = $field->value( $post_id );
        return $value[0];
    }

    public function convert_csv_input( &$field, $input = '', $import_settings = array() ) {
        $input = str_replace( array( '"', '\'' ), '', $input );
        $input = str_replace( ';', ',', $input ); // Support ; as a separator here.
        $parts = explode( ',', $input );

        if ( 1 == count( $parts ) )
            return array( $parts[0], '' );

        return array( $parts[0], $parts[1] );
    }

    public function get_field_csv_value( &$field, $post_id ) {
        $value = parent::get_field_value( $field, $post_id );

        if ( is_array( $value ) && count( $value ) > 1 ) {
            return sprintf( '%s,%s', $value[0], $value[1] );
        }

        return is_array( $value ) ? $value[0] : $value;
    }

    public function convert_input( &$field, $input ) {
        if ( $input === null )
            return array( '', '' );

        $url = trim( sanitize_text_field( is_array( $input ) ? $input[0] : $input ) );
        $text = trim( is_array( $input ) ? sanitize_text_field( $input[1] ) : '' );

		if ( $url && ! parse_url( $url, PHP_URL_SCHEME ) )
            $url = 'http://' . $url;

        return array( $url, $text );
    }

    public function is_empty_value( $value ) {
        return empty( $value[0] );
    }

    public function store_field_value( &$field, $post_id, $value ) {
        if ( ! is_array( $value ) || $value[0] == '' )
            $value = null;

        parent::store_field_value( $field, $post_id, $value );
    }

	public function render_field_inner( &$field, $value, $context, &$extra = null, $field_settings = array() ) {
		if ( empty( $value ) ) {
			// Set an empty array to avoid php warnings.
			$value = array( '', '' );
		}

        if ( $context == 'search' ) {
            global $wpbdp;
            return $wpbdp->formfields->get_field_type( 'textfield' )->render_field_inner( $field, $value[1], $context, $extra, $field_settings );
        }

        $html  = '';
        $html .= '<div class="wpbdp-url-field-col wpbdp-half">';
        $html .= sprintf(
            '<label for="%s"><span class="sublabel">%s</span></label>',
            'wpbdp-field-' . esc_attr( $field->get_id() ) . '-url',
            __( 'URL', 'business-directory-plugin' )
        );
        $html .= sprintf(
			'<input type="text" id="%s" name="%s" value="%s" />',
			'wpbdp-field-' . esc_attr( $field->get_id() ),
			'listingfields[' . esc_attr( $field->get_id() ) . '][0]',
			esc_attr( $value[0] )
		);
        $html .= '</div>';

        $html .= '<div class="wpbdp-url-field-col wpbdp-half">';
        $html .= sprintf(
            '<label for="%s"><span class="sublabel">%s</span></label>',
            'wpbdp-field-' . esc_attr( $field->get_id() ) . '-title',
            esc_html__( 'Link Text (optional)', 'business-directory-plugin' )
        );
		$html .= sprintf(
			'<input type="text" id="%s" name="%s" value="%s" placeholder="" />',
			'wpbdp-field-' . esc_attr( $field->get_id() ) . '-title',
			'listingfields[' . esc_attr( $field->get_id() ) . '][1]',
			esc_attr( $value[1] )
		);
        $html .= '</div>';

		if ( strpos( $context, 'submit' ) !== false ) {
			$html = '<div class="wpbdp-grid">' . $html . '</div>';
		}

        return $html;
    }

    public function css_classes( $css_classes, $field, $render_context ) {
        if ( $field->get_field_type()->get_id() == 'url' ) {
            $css_classes[] = 'wpbdp-clearfix';
        }

        return $css_classes;
    }

}
