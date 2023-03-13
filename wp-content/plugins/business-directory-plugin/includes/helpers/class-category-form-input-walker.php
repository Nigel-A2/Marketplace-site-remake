<?php

// Custom category walker (used when rendering category fields using radios or checkboxes)
class WPBDP_CategoryFormInputWalker extends Walker {
    var $tree_type = 'category';
    var $db_fields = array( 'parent' => 'parent', 'id' => 'term_id' );

    private $input_type;
    private $selected;
    private $field;

	public function __construct( $input_type = 'radio', $selected = null, &$field = null ) {
        $this->input_type = $input_type;
        $this->selected = $selected;
        $this->field = $field;
    }

    public function start_el( &$output, $category, $depth = 0, $args = array(), $id = 0 ) {
		$html_id = 'wpbdp-field-' . $category->term_id . '-' . $this->field->get_id();

        switch ( $this->input_type ) {
            case 'checkbox':
                $output .= '<div class="wpbdp-form-field-checkbox-item wpbdmcheckboxclass">';
				$output .= sprintf(
					'<label for="%7$s"><input id="%7$s" type="checkbox" class="%s" name="%s" value="%s" %s style="margin-left: %dpx;" /> %s</label>',
					$this->field->is_required() ? 'required' : '',
					'listingfields[' . $this->field->get_id() . '][]',
					$category->term_id,
					in_array( $category->term_id, is_array( $this->selected ) ? $this->selected : array( $this->selected ) ) ? 'checked="checked"' : '',
					$depth * 10,
					esc_attr( $category->name ),
					$html_id
				);
                $output .= '</div>';
                break;
            case 'radio':
            default:
				$class   = $this->field->is_required() ? 'inradio required' : 'inradio';
				$output .= '<div class="wpbdm-form-field-radio-item">';
				$output .= '<label for="' . esc_attr( $html_id ) . '">';
				$output .= '<input id="' . esc_attr( $html_id ) . '" type="radio"';
				$output .= ' name="listingfields[' . esc_attr( $this->field->get_id() ) . ']"';
				$output .= ' class="' . esc_attr( $class ) . '" value="' . esc_attr( $category->term_id ) . '" ';
				$output .= checked( $this->selected, $category->term_id, false );
				$output .= ' style="margin-left: ' . ( $depth * 10 ) . 'px;" />';
				$output .= ' ' . esc_html( $category->name );
				$output .= '</label>';
				$output .= '</div>';
                break;
        }

    }
}
