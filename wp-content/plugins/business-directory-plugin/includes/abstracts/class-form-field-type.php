<?php

/**
 * Base Field Type
 *
 * Handles generice field functionality
 */
class WPBDP_Form_Field_Type {

    private $name = null;

    public function __construct( $name = '' ) {
        if ( ! empty( $name ) )
            $this->name = $name;
    }

    public function get_id() {
        $id = strtolower( get_class( $this ) );
        $id = str_replace( 'wpbdp_fieldtypes_', '', $id );
        $id = str_replace( 'wpbdp_', '', $id );

        return $id;
    }

    public function get_name() {
        if ( empty( $this->name ) ) {
            $name = get_class( $this );
            $name = str_replace( 'WPBDP_FieldTypes_', '', $name );
            $name = str_replace( 'WPBDP_', '', $name );
            $name = str_replace( '_', ' ', $name );
            $name = trim( $name );

            $this->name = $name;
        }

        return $this->name;
    }

    /**
     * Called after a field of this type is constructed.
	 *
     * @param object $field
     */
    public function setup_field( &$field ) {
    }

    /**
     * Called before field validation takes place.
	 *
     * @since 3.6.5
     */
    public function setup_validation( $field, $validator, $value ) {
        return array();
    }

    /**
     * Called before the listing is to be saved.
	 *
     * @since 5.0.5
     */
    public function before_field_update( $field ) {
	}

    /**
     * @since 3.4
     */
    public function get_behavior_flags( &$field ) {
        return array();
    }

    public function get_field_value( &$field, $post_id ) {
        $post = get_post( $post_id );

		if ( ! $post ) {
            return null;
		}

        switch ( $field->get_association() ) {
            case 'title':
                $value = $post->post_title;
                break;
            case 'excerpt':
                $value = $post->post_excerpt;
                break;
            case 'content':
                $value = $post->post_content;
                break;
            case 'category':
                $value = wp_get_object_terms( $post_id, WPBDP_CATEGORY_TAX, array( 'fields' => 'ids' ) );
                break;
            case 'tags':
                $value = wp_get_object_terms( $post_id, WPBDP_TAGS_TAX, array( 'fields' => 'names' ) );

                foreach ( $value as $index => $v ) {
                    $value[ $index ] = htmlspecialchars_decode( $v, ENT_QUOTES );
                }

                break;
            case 'meta':
                $value = get_post_meta( $post_id, '_wpbdp[fields][' . $field->get_id() . ']', true );
                break;
            default:
                $value = null;
                break;
        }

        return $value;
    }

    public function get_field_html_value( &$field, $post_id ) {
        $post = get_post( $post_id );

        switch ( $field->get_association() ) {
            case 'title':
                $value = get_the_title( $post_id );

                if ( 'show_listing' == wpbdp_current_view() ) {
                    break;
                }

				$value = sprintf(
					'<a href="%s" target=%s >%s</a>',
					get_permalink( $post_id ),
					wpbdp_get_option( 'listing-link-in-new-tab' ) ? '"_blank" rel="noopener"' : '"_self"',
					esc_html( $value )
				);
                break;
            case 'excerpt':
				$value = apply_filters( 'get_the_excerpt', wpautop( $post->post_excerpt, true ), $post );
				break;
            case 'content':
                $value = apply_filters( 'the_content', $post->post_content );
                break;
            case 'category':
                $value = get_the_term_list( $post_id, WPBDP_CATEGORY_TAX, '', ', ', '' );
                break;
            case 'tags':
                $value = get_the_term_list( $post_id, WPBDP_TAGS_TAX, '', ', ', '' );
                break;
            case 'meta':
            default:
                $value = $field->value( $post_id );
        }

        return $value;
    }

    public function get_field_plain_value( &$field, $post_id ) {
        return $this->get_field_value( $field, $post_id );
    }

    /**
     * @since 3.4.1
     */
    public function get_field_csv_value( &$field, $post_id ) {
        return $this->get_field_plain_value( $field, $post_id );
    }

    public function is_empty_value( $value ) {
        return empty( $value );
    }

    public function convert_input( &$field, $input ) {
        return $input;
    }

    /**
     * @since 3.4.1
     */
    public function convert_csv_input( &$field, $input = '', $import_settings = array() ) {
        return $this->convert_input( $field, $input );
    }

    public function store_field_value( &$field, $post_id, $value ) {
		$update_post = array( 'ID' => $post_id );

        switch ( $field->get_association() ) {
            case 'title':
				$update_post['post_title'] = trim( strip_tags( $value ) );
                break;
            case 'excerpt':
				$update_post['post_excerpt'] = $value;
                break;
            case 'content':
				$update_post['post_content'] = $value;
                break;
            case 'category':
                wp_set_post_terms( $post_id, $value, WPBDP_CATEGORY_TAX, false );
                break;
            case 'tags':
                wp_set_post_terms( $post_id, $value, WPBDP_TAGS_TAX, false );
                break;
            default:
				// Everything else is meta.
                update_post_meta( $post_id, '_wpbdp[fields][' . $field->get_id() . ']', $value );
                break;
        }

		if ( count( $update_post ) > 1 ) {
			wp_update_post( $update_post );
		}
    }

    // this function should not try to hide values depending on field, context or value itself.
    public function display_field( &$field, $post_id, $display_context ) {
        return self::standard_display_wrapper( $field, $field->html_value( $post_id, $display_context ) );
    }

	public function render_field_inner( &$field, $value, $render_context, &$extra = null, $field_settings = array() ) {
        return '';
    }

	public function render_field( &$field, $value, $render_context, &$extra = null, $field_settings = array() ) {
        $html = '';

        switch ( $render_context ) {
            case 'search':
                $html .= sprintf(
                    '<div class="wpbdp-search-filter %s %s" %s>',
                    esc_attr( $field->get_field_type()->get_id() ),
                    esc_attr( implode( ' ', $field->get_css_classes( $render_context ) ) ),
                    $this->html_attributes( $field->html_attributes )
                );
                $html .= '<div class="wpbdp-search-field-label">';
                $html .= sprintf(
                    '<label for="%s">%s</label>',
                    'wpbdp-field-' . esc_attr( $field->get_id() ),
                    esc_html( apply_filters( 'wpbdp_render_field_label', $field->get_label(), $field ) ) .
					( $field->has_validator( 'required-in-search' ) ? '<span class="wpbdp-form-field-required-indicator">*</span>' : '' )
                );

                $html .= '</div>';
                $html .= '<div class="field inner">';

                $field_inner = $this->render_field_inner( $field, $value, $render_context, $extra, $field_settings );
                $field_inner = apply_filters_ref_array( 'wpbdp_render_field_inner', array( $field_inner, &$field, $value, $render_context, &$extra ) );

                $html .= $field_inner;
                $html .= '</div>';
                $html .= '</div>';

                break;

			default: // includes submit and edit
                $html_attributes = $this->html_attributes( apply_filters_ref_array( 'wpbdp_render_field_html_attributes', array( $field->html_attributes, &$field, $value, $render_context, &$extra ) ) );

                $html .= sprintf(
                    '<div class="%s" %s>',
                    esc_attr( implode( ' ', $field->get_css_classes( $render_context ) ) ),
                    $html_attributes
                );
                $html .= '<div class="wpbdp-form-field-label">';

                $this->add_error_message( $field, $html );

                $html .= sprintf(
                    '<label for="%s">%s</label>',
                    'wpbdp-field-' . esc_attr( $field->get_id() ),
					wp_kses_post( apply_filters( 'wpbdp_render_field_label', $field->get_label(), $field ) ) .
					( ( $field->has_validator( 'required' ) && 'widget' !== $render_context ) ? '<span class="wpbdp-form-field-required-indicator">*</span>' : '' )
                );

                $html .= '</div>';

                $field_description = trim( apply_filters( 'wpbdp_render_field_description', $field->get_description(), $field ) );
                if ( $field_description && 'widget' !== $render_context ) {
					$html .= '<div class="wpbdp-form-field-description">' . wp_kses_post( $field_description ) . '</div>';
				}

                $html .= '<div class="wpbdp-form-field-html wpbdp-form-field-inner">';

                $field_inner = $this->render_field_inner( $field, $value, $render_context, $extra, $field_settings );
                $field_inner = apply_filters_ref_array( 'wpbdp_render_field_inner', array( $field_inner, &$field, $value, $render_context, &$extra ) );

                $html .= $field_inner;
                $html .= '</div>';
                $html .= '</div>';

                break;
        }

		$this->strip_label_for_hidden( $field_inner, $html );

        return $html;
    }

	/**
	 * If the input is a hidden field, don't show the field label.
	 *
	 * @since 5.10
	 */
	protected function strip_label_for_hidden( $field_inner, &$html ) {
		if ( strpos( $field_inner, '<input type="hidden"' ) === 0 && substr_count( $field_inner, '<input ' ) === 1 ) {
			$html = $field_inner;
		}
	}

	/**
	 * Include the error message and icon for validation errors.
	 */
	protected function add_error_message( $field, &$html ) {
		$field_validation_errors = $field->get_validation_errors();
		if ( empty( $field_validation_errors ) ) {
			return;
		}

		$html .= '<div class="wpbdp-form-field-validation-error-wrapper">';
		$html .= '<div class="wpbdp-form-field-validation-errors wpbdp-clearfix">';

		wpbdp_sanitize_value( 'esc_html', $field_validation_errors );
		$html .= implode( '<br />', $field_validation_errors );

		$html .= '</div></div>';
	}

    /**
     * @since 4.1.7
     */
    public function get_schema_org( $field, $post_id ) {
        $schema = array();

        switch ( $field->get_tag() ) {
        case 'title':
            $schema['name'] = $field->plain_value( $post_id );
            break;
        case 'category':
            break;
        case 'excerpt':
            $schema['description'] = $field->plain_value( $post_id );
            break;
        case 'address':
            $schema['address'] = array( 'streetAddress' => $field->plain_value( $post_id ) );
            break;
        case 'city':
            $schema['address'] = array( 'addressLocality' => $field->plain_value( $post_id ) );
            break;
        case 'state':
            $schema['address'] = array( 'addressRegion' => $field->plain_value( $post_id ) );
            break;
        case 'zip':
            $schema['address'] = array( 'postalCode' => $field->plain_value( $post_id ) );
            break;
        case 'fax':
            $schema['faxNumber'] = $field->plain_value( $post_id );
            break;
        case 'phone':
            $schema['telephone'] = $field->plain_value( $post_id );
            break;
        case 'website':
            break;
        }

        return apply_filters( 'wpbdp_field_schema_org', $schema, $field, $post_id );
    }

	/**
	 * Called after a field of this type is deleted.
	 *
	 * @param object $field the deleted WPBDP_Form_Field object.
	 */
    public function cleanup( &$field ) {
        if ( $field->get_association() == 'meta' ) {
            global $wpdb;
            $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s", '_wpbdp[fields][' . $field->get_id() . ']' ) );
        }
    }

    /**
     * Returns an array of valid associations for this field type.
	 *
     * @return array
     */
    public function get_supported_associations() {
        return array( 'title', 'excerpt', 'content', 'category', 'tags', 'meta' );
    }

    /**
     * Renders the field-specific settings area for fields of this type.
     * It is recommended to use `render_admin_settings` here to keep an uniform look.
     * `$_POST` values can be used here to populate things when needed.
	 *
     * @param object $field might be NULL if field is new or the field that is being edited.
     * @param string $association field association.
     * @return string the HTML output.
     */
	public function render_field_settings( &$field = null, $association = null ) {
        return '';
    }

    /**
     * Called when saving fields of this type.
     * It should be used by field types to store any field type specific configuration.
	 *
     * @param object $field the field being saved.
     * @return mixed WP_Error in case of error, anything else for success.
     */
    public function process_field_settings( &$field ) {
    }

    /**
     * @since 5.0
     */
    public function configure_search( &$field, $query, &$search ) {
        return false;
    }


    /* Utils. */
	public static function standard_display_wrapper( $labelorfield, $content = null, $extra_classes = '', $args = array() ) {
        $css_classes = '';
        $css_classes .= 'wpbdp-field-display wpbdp-field wpbdp-field-value field-display field-value ';

        if ( is_object( $labelorfield ) ) {
            if ( $labelorfield->has_display_flag( 'social' ) )
                return $content;

            $css_classes .= 'wpbdp-field-' . self::normalize_name( $labelorfield->get_label() ) . ' ';
            $css_classes .= 'wpbdp-field-' . $labelorfield->get_association() . ' ';
            $css_classes .= 'wpbdp-field-type-' . $labelorfield->get_field_type_id() . ' ';
            $css_classes .= 'wpbdp-field-association-' . $labelorfield->get_association() . ' ';
            $label = $labelorfield->has_display_flag( 'nolabel' ) ? null : $labelorfield->get_label();
        } else {
            $css_classes .= 'wpbdp-field-' . self::normalize_name( $labelorfield ) . ' ';
            $label = $labelorfield;
        }

        $html  = '';
        $tag_attrs = isset( $args['tag_attrs'] ) ? self::html_attributes( $args['tag_attrs'] ) : '';
		$atts = array();
		if ( is_object( $labelorfield ) ) {
			$atts['field'] = $labelorfield;
		}
		$extra_classes = apply_filters( 'wpbdp_display_field_wrapper_classes', $extra_classes, $atts );
		$html .= '<div class="' . esc_attr( $css_classes . ' ' . $extra_classes ) . '" ' . $tag_attrs . '>';

		if ( $label ) {
			$html .= self::field_label_display_wrapper( $label, $atts );
		}

		if ( $content ) {
			$html .= '<div class="value">' . $content . '</div>';
		}

        $html .= '</div>';

        return $html;
    }

	/**
	 * Field label display wrapper.
	 * Used to render the field label.
	 *
	 * @param string $label The field label.
	 * @param array $atts includes $atts['field'] - The field object of the label.
	 *
	 * @since 5.15.3
	 *
	 * @return string
	 */
	public static function field_label_display_wrapper( $label, $atts = array() ) {
		$class = 'field-label';
		if ( isset( $atts['class'] ) ) {
			$class .= ' ' . $atts['class'];
		}
		$field = isset( $atts['field'] ) ? $atts['field'] : '';
		return '<span class="' . esc_attr( $class ) . '">' .
			apply_filters( 'wpbdp_display_field_label', esc_html( $label ), $field ) .
			'</span> ';
	}

	public static function render_admin_settings( $admin_settings = array() ) {
		if ( ! $admin_settings ) {
			return '';
		}

        $html  = '';
        $html .= '<table class="form-table">';

        foreach ( $admin_settings as $s ) {
            $label = is_array( $s ) ? $s[0] : '';
            $content = is_array( $s ) ? $s[1] : $s;

            $html .= '<tr>';
            if ( $label ) {
                $html .= '<th scope="row">';
                $html .= '<label>' . $label . '</label>';
                $html .= '</th>';
            }

            $html .= $label ? '<td>' : '<td colspan="2">';
            $html .= $content;
            $html .= '</td>';
            $html .= '</tr>';
        }

        $html .= '</table>';

        return $html;
    }

    public static function html_attributes( $attrs, $exceptions = array( 'class' ) ) {
        return wpbdp_html_attributes( $attrs, $exceptions );
    }

    /**
     * @since 3.5.3
     */
    public static function normalize_name( $name ) {
        $name = wpbdp_buckwalter_arabic_transliteration( $name );
        $name = strtolower( $name );
        $name = remove_accents( $name );
        $name = preg_replace( '/\s+/', '_', $name );
        $name = preg_replace( '/[^a-zA-Z0-9_-]+/', '', $name );

        return $name;
    }

}

/**
 * @deprecated Since 3.4.2. Use {@link WPBDP_Form_Field_Type} instead.
 */
class WPBDP_FormFieldType extends WPBDP_Form_Field_Type {
	public function __construct( $name = '' ) {
		_deprecated_constructor( __CLASS__, '3.4.2', 'WPBDP_Form_Field_Type' );
		parent::__construct( $name );
	}
}
