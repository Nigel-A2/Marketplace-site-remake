<?php
/**
 * @since 4.0
 */
class WPBDP_Field_Display_List implements IteratorAggregate {

    private $display = '';
    private $listing_id = '';
    private $frozen = false;

    private $items = array();
    private $displayed_fields = array();
    private $names_to_ids = array();


    public function __construct( $listing_id, $display, $fields = array() ) {
        $this->listing_id = $listing_id;
        $this->display = $display;

        foreach ( $fields as &$f ) {
            $this->append( $f );
        }
    }

    public function append( &$f ) {
        if ( $this->frozen )
            return;

        if ( $f instanceof _WPBDP_Lightweight_Field_Display_Item ) {
            $this->items[ $f->id ] = $f;
            $this->names_to_ids[ $f->field->get_short_name() ] = $f->id;
            $this->names_to_ids[ 't_' . $f->field->get_tag() ] = $f->id;

            if ( $f->field->display_in( $this->display ) )
                $this->displayed_fields[] = $f->id;

            return;
        }

        $field_id = $f->get_id();

        if ( isset( $this->items[ $field_id ] ) )
            return;

        if ( ! $f->display_in( $this->display ) )
            return;

        // if( $f->display_in( $this->display ) )
        //     $this->displayed_fields[] = $field_id;

        $this->displayed_fields[] = $field_id;
        $this->items[ $field_id ] = new _WPBDP_Lightweight_Field_Display_Item( $f, $this->listing_id, $this->display );
        $this->names_to_ids[ $f->get_short_name() ] = $field_id;
        $this->names_to_ids[ 't_' . $f->get_tag() ] = $field_id;
    }

    public function freeze() {
        $this->frozen = true;
    }

    public function not( $filter ) {
        return $this->filter( '-' . $filter );
    }

    public function filter( $filter ) {
		$neg = ( '-' === substr( $filter, 0, 1 ) );
        $filter = ( $neg ? substr( $filter, 1 ) : $filter );

        $filtered = array();

		$social_filter  = ( $filter === 'social' );
		$is_association = false;

		if ( ! $social_filter && $filter !== 'excerpt' ) {
			$api            = WPBDP_FormFields::instance();
			$post_fields    = $api->get_associations();
			$is_association = isset( $post_fields[ $filter ] );
		}

        foreach ( $this->items as &$f ) {
			$display = ! $neg;
			if ( $is_association ) {
				$mapping = $f->field->get_association();
				$display = $mapping === $filter;
			} else {
				$display = $f->field->display_in( $filter );
			}

			if ( $neg !== $display ) {
				$filtered[] = $f;
			}
        }

        $res = new self( $this->listing_id, $this->display, $filtered );
        $res->freeze();

        return $res;
    }

    public function exclude( $fields_ ) {
        $exclude = is_array( $fields_ ) ? $fields_ : explode( ',', $fields_ );
        $filtered = array();

        if ( ! $exclude )
            return $this;

        foreach ( $this->items as $f ) {
            if ( in_array( 'id_' . $f->id, $exclude ) || in_array( 't_' . $f->field->get_tag(), $exclude, true ) || in_array( $f->field->get_short_name(), $exclude, true ) )
                continue;

            $filtered[] = $f;
        }

        $res = new self( $this->listing_id, $this->display, $filtered );
        $res->freeze();
        return $res;
    }

	#[\ReturnTypeWillChange]
    public function getIterator() {
        return new ArrayIterator( $this->items_for_display() );
    }

    public function items_for_display() {
        $valid_ids = $this->displayed_fields;
        $fields = array();

        if ( ! $valid_ids )
            return array();

        foreach ( $this->items as $i ) {
            if ( ! in_array( $i->id, $valid_ids ) )
                continue;

            $fields[] = $i;
        }

        return $fields;
    }

    public function __isset( $key ) {
        if ( 'html' == $key ) {
            return true;
        }

        if ( '_h_' == substr( $key, 0, 3 ) ) {
            return method_exists( $this, 'helper__' . substr( $key, 3 ) );
        }

        if ( 'id' == substr( $key, 0, 2 ) ) {
            $field_id = absint( substr( $key, 2 ) );
        } else {
            $field_id = 0;
        }

        if ( ! $field_id ) {
            $field_id = isset( $this->names_to_ids[ $key ] ) ? $this->names_to_ids[ $key ] : 0;
        }

        if ( $field_id && isset( $this->items[ $field_id ] ) ) {
            return true;
        }

        return false;
    }

    public function __get( $key ) {
        $field_id = 0;

        if ( 'html' == $key ) {
            $html  = '';
            $html .= implode( '', wp_list_pluck( $this->items_for_display(), 'html' ) );

            // FIXME: move this to a compat layer.
            if ( 'listing' == $this->display ) {
                $html = apply_filters( 'wpbdp_single_listing_fields', $html, $this->listing_id );
            }

            return $html;
        }

        if ( '_h_' == substr( $key, 0, 3 ) )
            return method_exists( $this, 'helper__' . substr( $key, 3 ) ) ? call_user_func( array( $this, 'helper__' . substr( $key, 3 ) ) ) : '';

        if ( 'id' == substr( $key, 0, 2 ) )
            $field_id = absint( substr( $key, 2 ) );

        if ( ! $field_id )
            $field_id = isset( $this->names_to_ids[ $key ] ) ? $this->names_to_ids[ $key ] : 0;

        if ( $field_id && isset( $this->items[ $field_id ] ) )
            return $this->items[ $field_id ];

        wpbdp_debug( 'Invalid field key: ' . $key );
        return new WPBDP_NoopObject(); // FIXME: templates shouldn't rely on a field existing.
    }

    //
    // Helpers. {{
    //

    public function helper__address() {
        $address  = trim( $this->t_address->value );
        $address2 = trim( $this->t_address2->value );
        $city     = trim( $this->t_city->value );
        $state    = trim( $this->t_state->value );
        $country  = trim( $this->t_country->value );
        $zip      = trim( is_array( $this->t_zip->value ) ? $this->t_zip->value['zip'] : $this->t_zip->value );

        $first_line = $address;

        $second_line = $address2;

        $third_line = $city;
		$third_line .= ( $city && $state ) ? ', ' . $state : $state;
		$third_line .= $zip ? ' ' . $zip : '';

        return implode(
            '<br />',
            array_filter(
                array( $first_line, $second_line, $third_line, $country ),
                function( $line ) {
                    return ! empty( $line );
                }
            )
        );
    }

    public function helper__address_nobr() {
        return str_replace( '<br />', ', ', $this->helper__address() );
    }

	/**
	 * Helper function to get the address label.
	 *
	 * @since 5.15.3
	 *
	 * @return string
	 */
	public function helper__address_label() {
		$field = $this->t_address->field;
		$atts = array(
			'class' => 'address-label',
		);

		if ( ! $field ) {
			return WPBDP_Form_Field_Type::field_label_display_wrapper( __( 'Address', 'business-directory-plugin' ), $atts );
		}

		if ( $field->has_display_flag( 'nolabel' ) ) {
			return '';
		}

		$atts['field'] = $field;

		return WPBDP_Form_Field_Type::field_label_display_wrapper( $this->t_address->label, $atts );
	}

    public function helper__author() {
        $listing = wpbdp_get_listing( $this->listing_id );
        $author  = $listing->get_author_meta( 'display_name' );

        return $author ? $author : $listing->get_author_meta( 'user_login' );
    }

    public function helper__created_date() {
        return wpbdp_date( get_the_date( 'U', $this->listing_id ) );
    }

    public function helper__modified_date() {
        return wpbdp_get_listing( $this->listing_id )->get_modified_date();
    }

    //
    // }}
    //
}

/**
 * @since 4.0
 */
class _WPBDP_Lightweight_Field_Display_Item {

    public $field = null;
    private $listing_id = 0;
    private $display = '';

    private $html_ = null;
    private $value_ = null;
    private $raw_ = null;

    public function __construct( $field, $listing_id, $display ) {
        $this->field = $field;
        $this->listing_id = $listing_id;
        $this->display = $display;
    }

    public function __isset( $key ) {
        $supported_keys = array(
            'html', 'value', 'raw', 'id', 'label', 'tag', 'field'
        );

        return in_array( $key, $supported_keys, true );
    }

    public function __get( $key ) {
        $k = "${key}_";

        if ( isset( $this->{$k} ) )
            return $this->{$k};

        $v = null;

        switch ( $key ) {
            case 'html':
                $v = $this->field->display( $this->listing_id, $this->display );
                break;
            case 'value':
                $v = $this->field->html_value( $this->listing_id );
                break;
            case 'raw':
                $v = $this->field->value( $this->listing_id );
                break;
            case 'id':
                return $this->field->get_id();

            case 'label':
                return $this->field->get_label();

            case 'tag':
                return $this->field->get_tag();

            case 'field':
                return $this->field;
        }

        $this->{$k} = $v;
        return $v;
    }

}


