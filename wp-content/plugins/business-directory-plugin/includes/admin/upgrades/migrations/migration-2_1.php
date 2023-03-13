<?php

class WPBDP__Migrations__2_1 extends WPBDP__Migration {

    public function migrate() {
        global $wpdb;

        /* This is only to make this routine work for BD 3.0. It's not necessary in other versions. */
        $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_form_fields ADD COLUMN validator VARCHAR(255) NULL;" );
        $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_form_fields ADD COLUMN display_options BLOB NULL;" );
        $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_form_fields ADD COLUMN is_required TINYINT(1) NOT NULL DEFAULT 0;" );
        $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_form_fields ADD COLUMN type VARCHAR(255) NOT NULL;" );

        static $pre_2_1_types = array(null, 'textfield', 'select', 'textarea', 'radio', 'multiselect', 'checkbox');
        static $pre_2_1_validators = array(
            'email' => 'EmailValidator',
            'url' => 'URLValidator',
            'missing' => null, /* not really used */
            'numericwhole' => 'IntegerNumberValidator',
            'numericdeci' => 'DecimalNumberValidator',
            'date' => 'DateValidator'
        );
        static $pre_2_1_associations = array(
            'title' => 'title',
            'description' => 'content',
            'category' => 'category',
            'excerpt' => 'excerpt',
            'meta' => 'meta',
            'tags' => 'tags'
        );

        $field_count = $wpdb->get_var(
			sprintf( "SELECT COUNT(*) FROM {$wpdb->prefix}options WHERE option_name LIKE '%%%s%%'", 'wpbusdirman_postform_field_label' )
		);

		for ( $i = 1; $i <= $field_count; $i++ ) {
			$label = get_option( 'wpbusdirman_postform_field_label_' . $i );
			$type = get_option( 'wpbusdirman_postform_field_type_'. $i );
			$validation = get_option( 'wpbusdirman_postform_field_validation_'. $i );
			$association = get_option( 'wpbusdirman_postform_field_association_'. $i );
			$required = strtolower( get_option( 'wpbusdirman_postform_field_required_'. $i ) );
			$show_in_excerpt = strtolower( get_option( 'wpbusdirman_postform_field_showinexcerpt_'. $i ) );
			$hide_field = strtolower( get_option( 'wpbusdirman_postform_field_hide_'. $i ) );
			$options = get_option( 'wpbusdirman_postform_field_options_'. $i );

            $newfield = array();
            $newfield['label'] = $label;
			$newfield['type']        = wpbdp_getv( $pre_2_1_types, intval( $type ), 'textfield' );
			$newfield['validator']   = wpbdp_getv( $pre_2_1_validators, $validation, null );
			$newfield['association'] = wpbdp_getv( $pre_2_1_associations, $association, 'meta' );
			$newfield['is_required'] = $required === 'yes';
            $newfield['display_options'] = serialize(
                array( 'show_in_excerpt' => $show_in_excerpt == 'yes' ? true : false,
                      'hide_field' => $hide_field == 'yes' ? true : false)
            );
			$newfield['field_data'] = $options ? serialize( array( 'options' => explode( ',', $options ) ) ) : null;

			if ( $wpdb->insert( $wpdb->prefix . 'wpbdp_form_fields', $newfield ) ) {
				delete_option( 'wpbusdirman_postform_field_label_' . $i );
				delete_option( 'wpbusdirman_postform_field_type_' . $i );
				delete_option( 'wpbusdirman_postform_field_validation_' . $i );
				delete_option( 'wpbusdirman_postform_field_association_' . $i );
				delete_option( 'wpbusdirman_postform_field_required_' . $i );
				delete_option( 'wpbusdirman_postform_field_showinexcerpt_' . $i );
				delete_option( 'wpbusdirman_postform_field_hide_' . $i );
				delete_option( 'wpbusdirman_postform_field_options_' . $i );
				delete_option( 'wpbusdirman_postform_field_order_' . $i );
            }

        }
    }

}
