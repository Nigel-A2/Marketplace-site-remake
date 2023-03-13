<?php

class WPBDP__Migrations__3_2 extends WPBDP__Migration {

    /*
     * This update converts all form fields to a new, more flexible format that uses a new API introduced in BD 2.3.
     */
    public function migrate() {
        global $wpdb;

        $validators_trans = array(
            'EmailValidator' => 'email',
            'URLValidator' => 'url',
            'IntegerNumberValidator' => 'integer_number',
            'DecimalNumberValidator' => 'decimal_number',
            'DateValidator' => 'date_'
        );

        $old_fields = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpbdp_form_fields" );

        foreach ( $old_fields as &$f ) {
            $newfield = array();
            $newfield['field_type'] = strtolower( $f->type );

            if ( empty( $newfield['field_type'] ) )
                $newfield['field_type'] = 'textfield';

            $newfield['display_flags'] = array();
            $newfield['field_data'] = array();
            $newfield['validators'] = array();

            // display options
			$f_display_options = array_merge( array( 'show_in_excerpt' => true, 'show_in_listing' => true, 'show_in_search' => true ), $f->display_options ? (array) unserialize( $f->display_options ) : array() );
            if ( isset( $f_display_options['hide_field'] ) && $f_display_options['hide_field'] ) {
                // do nothing
            } else {
                if ( $f_display_options['show_in_excerpt'] ) $newfield['display_flags'][] = 'excerpt';
                if ( $f_display_options['show_in_listing'] ) $newfield['display_flags'][] = 'listing';
                if ( $f_display_options['show_in_search'] ) $newfield['display_flags'][] = 'search';
            }

            // validators
            if ( $f->validator && isset( $validators_trans[ $f->validator ] ) ) $newfield['validators'] = array( $validators_trans[ $f->validator ] );
            if ( $f->is_required ) $newfield['validators'][] = 'required';

            // options for multivalued fields
            $f_data = $f->field_data ? unserialize( $f->field_data ) : null;
            $f_data = is_array( $f_data ) ? $f_data : array();

            if ( isset( $f_data['options'] ) && is_array( $f_data['options'] ) ) $newfield['field_data']['options'] = $f_data['options'];
            if ( isset( $f_data['open_in_new_window'] ) && $f_data['open_in_new_window'] ) $newfield['field_data']['open_in_new_window'] = true;

			if ( $newfield['field_type'] === 'textfield' && in_array( 'url', $newfield['validators'] ) ) {
                $newfield['field_type'] = 'url';
			}

            $newfield['display_flags'] = implode( ',', $newfield['display_flags'] );
            $newfield['validators'] = implode( ',', $newfield['validators'] );
            $newfield['field_data'] = serialize( $newfield['field_data'] );

            $wpdb->update( "{$wpdb->prefix}wpbdp_form_fields", $newfield, array( 'id' => $f->id ) );
        }

        $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_form_fields DROP COLUMN validator;" );
        $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_form_fields DROP COLUMN display_options;" );
        $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_form_fields DROP COLUMN is_required;" );
        $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_form_fields DROP COLUMN type;" );

		add_action( 'admin_notices', array( $this, 'disable_regions_in_3_2_upgrade' ) );
    }

    public function disable_regions_in_3_2_upgrade() {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';

        if ( class_exists( 'WPBDP_RegionsPlugin' ) && version_compare( WPBDP_RegionsPlugin::VERSION, '1.1', '<' ) ) {
            deactivate_plugins( 'business-directory-regions/business-directory-regions.php', true );
            echo sprintf( '<div class="error"><p>%s</p></div>',
                          _x( '<b>Business Directory Plugin - Regions Module</b> was disabled because it is incompatible with the current version of Business Directory. Please update the Regions module.', 'installer', 'business-directory-plugin' )
                        );
        }
    }

}
