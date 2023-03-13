<?php

class WPBDP__Manual_Upgrade__18_0__Featured_Levels {

    function __construct() {
        // Check if we actually need to perform the migration.
        global $wpdb;
        if ( 0 === absint( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->postmeta} WHERE meta_key = %s AND meta_value != %s", '_wpbdp[sticky]', 'normal' ) ) ) ) {
            delete_option( 'wpbdp-migrate-18_0-featured-pending' );
            return;
        }

        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
        add_action( 'admin_menu', array( &$this, 'add_upgrade_page' ) );
        add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
    }

    function add_upgrade_page() {
        add_submenu_page(
            'options.php',
            __( 'Business Directory - Featured Levels Migration', 'business-directory-plugin' ),
            __( 'Business Directory - Featured Levels Migration', 'business-directory-plugin' ),
            'administrator',
            'wpbdp_migration_18_0_featured_levels',
            array( &$this, 'migration_page' )
        );
    }

    function _fee_form() {
        $form = wpbdp_render_page( WPBDP_PATH . 'templates/admin/fees-form.tpl.php', array( 'fee' => new WPBDP__Fee_Plan() ) );
        return $form;
    }

    function _validate_config( $levels ) {
		$posted = wpbdp_get_var( array( 'param' => 'level', 'default' => false ), 'post' );

        if ( ! $posted ) {
            return false;
        }

        $config = array();

        foreach ( $levels as $level_id => $level_data ) {
            if ( ! isset( $posted[ $level_id ] ) ) {
                return false;
            }

            $strategy    = wpbdp_getv( $posted[ $level_id ], 'strategy', false );
            $move_to     = absint( wpbdp_getv( $posted[ $level_id ], 'move_to', 0 ) );
            $new_details = wpbdp_getv( $posted[ $level_id ], 'details', false );

            switch ( $strategy ) {
                case 'remove':
                    $config[ $level_id ] = array( 'strategy' => 'remove' );
                    break;
                case 'move':
                    $plan = wpbdp_get_fee_plan( $move_to );

                    if ( ! $plan ) {
                        return false;
                    }

                    $config[ $level_id ] = array(
                        'strategy' => 'move',
                        'fee_id'   => $move_to,
                    );
                    break;
                case 'create':
                    parse_str( $new_details, $fee_details );
                    $fee = stripslashes_deep( $fee_details['fee'] );

                    if ( ! isset( $fee_details['limit_categories'] ) ) {
                        $fee['supported_categories'] = 'all';
                    }

                    if ( ! isset( $fee_details['sticky'] ) ) {
                        $fee['sticky'] = 0;
                    }

                    $config[ $level_id ] = array(
                        'strategy' => 'create',
                        'fee'      => $fee,
                    );
                    break;
                default:
                    return false;
            }
        }

        return $config;
    }

    function _update_db( $config ) {
        global $wpdb;

        if ( ! $config ) {
            // Delete all sticky info.
            // $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s", '_wpbdp[sticky]' );
            // $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s", '_wpbdp[sticky_level]' );
            return;
        }

        $featured_fee_translation = array();

        foreach ( $config as $level_id => $level_config ) {
            switch ( $level_config['strategy'] ) {
                case 'remove':
                    $featured_fee_translation[ $level_id ] = 0;
                    break;
                case 'move':
                    $featured_fee_translation[ $level_id ] = $level_config['fee_id'];

                    $fee = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpbdp_plans WHERE id = %d", $level_config['fee_id'] ) );

                    $wpdb->query(
                        $wpdb->prepare(
                            "UPDATE {$wpdb->prefix}wpbdp_listings SET fee_id = %d, fee_price = %s, fee_days = %d, fee_images = %d, is_sticky = %d WHERE listing_id IN ( SELECT pm.post_id FROM {$wpdb->postmeta} pm WHERE pm.meta_key = %s AND pm.meta_value = %s )",
                            $fee->id,
                            $fee->amount,
                            $fee->days,
                            $fee->images,
                            $fee->sticky,
                            '_wpbdp[sticky_level]',
                            $level_id
                        )
                    );

                    break;
                case 'create':
                    $fee = new WPBDP__Fee_Plan( $level_config['fee'] );

                    if ( $fee->save() ) {
                        $featured_fee_translation[ $level_id ] = $fee->id;

                        $wpdb->query(
                            $wpdb->prepare(
                                "UPDATE {$wpdb->prefix}wpbdp_listings SET fee_id = %d, fee_price = %s, fee_days = %d, fee_images = %d, is_sticky = %d WHERE listing_id IN ( SELECT pm.post_id FROM {$wpdb->postmeta} pm WHERE pm.meta_key = %s AND pm.meta_value = %s )",
                                $fee->id,
                                $fee->amount,
                                $fee->days,
                                $fee->images,
                                $fee->sticky,
                                '_wpbdp[sticky_level]',
                                $level_id
                            )
                        );
                    } else {
                        $featured_fee_translation[ $level_id ] = 0;
                    }

                    break;
            }
        }

        // $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s", '_wpbdp[sticky]' ) );
        // $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s", '_wpbdp[sticky_level]' ) );

        delete_option( 'wpbdp-migrate-18_0-featured-pending' );
    }

    function migration_page() {
        if ( ! get_option( 'wpbdp-migrate-18_0-featured-pending', false ) ) {
            return;
        }

        global $wpdb;

        $levels = array();

        if ( $wpdb->get_row( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->prefix . 'wpbdp_x_featured_levels' ) ) ) {
            $db_levels = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpbdp_x_featured_levels" );

            foreach ( $db_levels as $db_level ) {
                $levels[ $db_level->id ] = (array) $db_level;
            }
        }

        unset( $levels['normal'] );
        if ( ! isset( $levels['sticky'] ) ) {
            $levels['sticky'] = array(
                'name'        => _x( 'Featured Listing', 'listings-api', 'business-directory-plugin' ),
                'description' => wpbdp_get_option( 'featured-description' ),
                'cost'        => floatval( wpbdp_get_option( 'featured-price' ) ),
            );
        }
		echo wpbdp_admin_header(
			array(
				'title'   => __( 'Business Directory - Featured Levels Migration', 'business-directory-plugin' ),
				'id'      => 'manual-upgrade',
				'sidebar' => false,
			)
		);

        // Validate (in case data was POSTed).
        if ( $config = $this->_validate_config( $levels ) ) {
            $this->_update_db( $config );
            echo _x( 'Featured Levels migration is complete.', 'migrate-18', 'business-directory-plugin' );
            echo '<br /><br />';
            echo '<a href="' . esc_url( admin_url( 'edit.php?post_type=' . WPBDP_POST_TYPE ) ) . '" class="button button-secondary">' . _x( '← Return to Directory dashboard', 'upgrade-18', 'business-directory-plugin' ) . '</a>';
            echo wpbdp_admin_footer();

            return;
        }

        echo '<div class="wpbdp-manual-upgrade-wrapper">';

        echo '<div id="wpbdp-manual-upgrade-18_0-config">';

        echo '<div id="add-fee-form" data-title="' . _x( 'Configure Plan', 'upgrade-18', 'business-directory-plugin' ) . '">';
        echo $this->_fee_form();
        echo '</div>';

        _ex( 'Business Directory <b>version 5.0</b> is changing how Featured Levels plugin works. We are leaving restricted features for plans, but removing the confusing notion of a "featured level" that was limited to sticky listings.', 'migrate-18', 'business-directory-plugin' );
        echo '<br />';
        _ex( 'We need to migrate your existing "featured levels" to plans for use by the upgrade. YOUR DATA WILL NOT BE LOST HERE! Our new setup will make it easier to configure and manage your listings with restricted feature access. If you are unsure about what to do here, <support-link>contact support</support-link> and <cancel-link>cancel migration</cancel-link>.', 'migrate-18', 'business-directory-plugin' );
        echo '<br /><br />';
        _ex( 'Before we do the migration, we need to ask a few simple questions to move your data from the old "featured level" to the new "restricted feature plan" that is right for you.', 'migrate-18', 'business-directory-plugin' );

        // Compute listing counts.
        foreach ( array_keys( $levels ) as $level_id ) {
            $levels[ $level_id ]['count'] = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p JOIN {$wpdb->postmeta} pm ON pm.post_id = p.ID WHERE p.post_type = %s AND pm.meta_key = %s AND pm.meta_value = %s", WPBDP_POST_TYPE, '_wpbdp[sticky_level]', $level_id ) );
        }

        // Gather possible fee options for migration.
        $fee_options = '';
        foreach ( $wpdb->get_results( "SELECT id, label FROM {$wpdb->prefix}wpbdp_plans" ) as $r ) {
            $fee_options .= '<option value="' . $r->id . '">' . $r->label . '</option>';
        }

        echo '<form action="" method="post">';
        echo '<table id="fee-decisions">';
        echo '<thead>';
        echo '<tr>';
        echo '<th class="level-name">' . _x( 'Featured Level', 'upgrade-18', 'business-directory-plugin' ) . '</th>';
        echo '<th>' . _x( 'What to do with it?', 'upgrade-18', 'business-directory-plugin' ) . '</th>';
        echo '</tr>';
        echo '</thead>';
        echo '<tbody>';

        foreach ( $levels as $level_id => $level ) {
            echo '<tr>';
            echo '<td class="level-name">';
            echo '<strong>' . $level['name'] . '</strong><br />';
            echo sprintf( _nx( '%d listing is on this level.', '%d listings are on this level.', $level['count'], 'upgrade-18', 'business-directory-plugin' ), $level['count'] );
            echo '</td>';
            echo '<td>';
            echo '<select data-level-id="' . $level_id . '" class="level-migration" name="level[' . $level_id . '][strategy]">';
            echo '<option class="placeholder" value="">' . _x( 'Select an option', 'upgrade-18', 'business-directory-plugin' ) . '</option>';
            echo '<option data-description="' . esc_attr( _x( 'Remove "sticky" status for listings.', 'upgrade-18', 'business-directory-plugin' ) ) . '" value="remove">' . _x( 'Remove this (old) level, and leave the listing on the old plan.', 'upgrade-18', 'business-directory-plugin' ) . '</option>';

            if ( $fee_options ) {
                echo '<option data-description="' . esc_attr( _x( 'May change "sticky" status depending on plan.', 'upgrade-18', 'business-directory-plugin' ) ) . '" value="move">' . _x( 'Move listings with this level to existing plan.', 'upgrade-18', 'business-directory-plugin' ) . '</option>';
            }

                echo '<option data-description="' . esc_attr( _x( 'Keep "sticky" status of listings.', 'upgrade-18', 'business-directory-plugin' ) ) . '" value="create">' . _x( 'Replace this level with a new plan.', 'upgrade-18', 'business-directory-plugin' ) . '</option>';

            echo '</select>';
            echo '<div class="option-description"></div>';

            if ( $fee_options ) :
                echo '<div class="option-configuration option-move" >';
                echo _x( 'Move to: ', 'migrate-18', 'business-directory-plugin' );
                echo '<select name="level[' . $level_id . '][move_to]">';
                echo $fee_options;
                echo '</select>';
                echo '</div>';
            endif;

            echo '<div class="option-configuration option-create">';
            echo '<input type="hidden" name="level[' . $level_id . '][details]" />';

            echo '<h4>' . _x( 'New plan summary', 'migrate-18', 'business-directory-plugin' ) . '</h4>';
            echo '<table class="new-fee-summary" data-level-id="' . esc_attr( $level_id ) . '">';
            echo '<thead><tr>';
            echo '<th>' . esc_html__( 'Plan Label', 'business-directory-plugin' ) . '</th>';
            echo '<th>' . esc_html__( 'Amount', 'business-directory-plugin' ) . '</th>';
            echo '<th>' . esc_html__( 'Duration', 'business-directory-plugin' ) . '</th>';
            echo '<th>' . esc_html__( 'Images', 'business-directory-plugin' ) . '</th>';
            echo '</tr></thead>';
            echo '<tbody>';
            echo '<tr>';
            echo '<td data-attr="fee_label"></td>';
            echo '<td data-attr="fee_amount"></td>';
            echo '<td data-attr="fee_duration"></td>';
            echo '<td data-attr="fee_images"></td>';
            echo '</tr>';
            echo '</tbody>';
            echo '</table>';

            echo '</div>';
            echo '</td>';
            echo '</tr>';
        }

        echo '</tbody>';
        echo '</table>';

        echo '<p>';
        echo '<input type="submit" value="' . _x( 'Perform migration', 'migrate-18', 'business-directory-plugin' ) . '" class="button button-primary" />';
        echo '</p>';

        echo '</form>';

        // $wpdb->query( $wpdb->prepare("UPDATE {$wpdb->postmeta} SET meta_value = %s WHERE meta_key = %s AND meta_value = %s", $level->downgrade, '_wpbdp[sticky_level]', $level->id) );
        echo '</div>';

        echo '</div>';
        echo wpbdp_admin_footer();
    }

    public function enqueue_scripts() {
        add_thickbox();

        wp_enqueue_style(
            'wpbdp-admin',
            WPBDP_ASSETS_URL . 'css/admin.min.css',
            array(),
            WPBDP_VERSION
        );

        wp_enqueue_style(
            'wpbdp-manual-upgrade-css',
            WPBDP_ASSETS_URL . 'css/admin-manual-upgrade.min.css',
            array(),
            WPBDP_VERSION
        );

        wp_enqueue_script(
            'wpbdp-manual-upgrade',
            WPBDP_ASSETS_URL . 'js/admin-manual-upgrade.min.js',
            array( 'jquery' ),
            WPBDP_VERSION,
			true
        );
    }

    function admin_notices() {
        if ( ! empty( $_GET['page'] ) && 'wpbdp_migration_18_0_featured_levels' == $_GET['page'] ) {
            return;
        }

        echo '<div class="wpbdp-notice error"><p>';
        echo '<strong>';
        echo _x( 'Business Directory Plugin - Featured Levels migration required.', 'migrate-18', 'business-directory-plugin' );
        echo '</strong><br />';
        echo str_replace(
            '<a>',
            '<a href="' . esc_url( admin_url( 'admin.php?page=wpbdp_migration_18_0_featured_levels' ) ) . '">',
            _x( 'Featured levels were removed in 5.0. You need to perform your <a>Featured Levels migration here</a>.', 'migrate-18', 'business-directory-plugin' )
        );
        echo '</p></div>';
    }

}
