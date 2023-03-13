<?php

class WPBDP_FieldTypes_MultiSelect extends WPBDP_FieldTypes_Select {

    public function __construct() {
		parent::__construct( _x( 'Multiple select list', 'form-fields api', 'business-directory-plugin' ) );
        $this->set_multiple( true );
    }

    public function get_name() {
        return _x( 'Multiselect List', 'form-fields api', 'business-directory-plugin' );
    }

    public function get_id() {
        return 'multiselect';
    }

    public function get_supported_associations() {
        return array( 'category', 'tags', 'meta' );
    }

	protected function get_field_settings( $field = null, $association = null ) {
        $settings = parent::get_field_settings( $field, $association );

		if ( $association === 'category' ) {
			// Don't show height option for JS-generated multiselect.
			return $settings;
		}

        $label = _x( 'Number of options visible without scrolling', 'form-fields-admin', 'business-directory-plugin' );
        $description = _x( 'The height of the list will be adjusted to accommodate the specified number of options.', 'form-fields-admin', 'business-directory-plugin' );

        $content = '<span class="description">' . $description . '</span><br />';
		$content .= '<input name="field[x_size]" type="number" value="%d">';

        $settings['size'] = array( $label, sprintf( $content, $field ? $field->data( 'size', 4 ) : 4 ) );

        return $settings;
    }

    public function process_field_settings( &$field ) {
        if ( ! array_key_exists( 'x_size', $_POST['field'] ) ) {
            return;
        }

        $size = absint( sanitize_text_field( $_POST['field']['x_size'] ) );
        $field->set_data( 'size', $size );

        return parent::process_field_settings( $field );
    }
}

