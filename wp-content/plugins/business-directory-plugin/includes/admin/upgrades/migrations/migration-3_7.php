<?php

class WPBDP__Migrations__3_7 extends WPBDP__Migration {

    public function migrate() {
        global $wpdb;

        // Try to disable incompatible modules.
        include_once ABSPATH . 'wp-admin/includes/plugin.php';

        if ( is_plugin_active( 'business-directory-regions/business-directory-regions.php' ) ) {
            deactivate_plugins( 'business-directory-regions/business-directory-regions.php' );
        }

        // Remove invalid listing fees (quick).
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wpbdp_listing_fees WHERE listing_id NOT IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s)", WPBDP_POST_TYPE ) );
        $wpdb->query( "DELETE FROM {$wpdb->prefix}wpbdp_listing_fees WHERE category_id NOT IN (SELECT term_id FROM {$wpdb->terms})" );

        if ( wpbdp_column_exists( "{$wpdb->prefix}wpbdp_listing_fees", 'charged' ) ) {
            $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_listing_fees DROP charged" );
        }

        if ( wpbdp_column_exists( "{$wpdb->prefix}wpbdp_listing_fees", 'updated_on' ) ) {
            $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_listing_fees DROP updated_on" );
        }

        // Update notify-admin email option.
		if ( get_option( WPBDP__Settings::PREFIX . 'notify-admin', false ) ) {
			update_option( WPBDP__Settings::PREFIX . 'admin-notifications', array( 'new-listing') );
		}

        $this->request_manual_upgrade( 'upgrade_to_3_7_migrate_payments' );
    }

    public function upgrade_to_3_7_migrate_payments() {
        global $wpdb;

        $status_msg = _x( 'Migrating payments information.', 'installer', 'business-directory-plugin' );

        // Remove/update listing fees.
        if ( ! $wpdb->get_col( $wpdb->prepare( "SHOW COLUMNS FROM {$wpdb->prefix}wpbdp_listing_fees LIKE %s", 'migrated' ) ) ) {
            $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_listing_fees ADD migrated tinyint(1) DEFAULT 0" );
        }

        if ( ! $wpdb->get_col( $wpdb->prepare( "SHOW COLUMNS FROM {$wpdb->prefix}wpbdp_listing_fees LIKE %s", 'fee_days' ) ) ) {
            $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_listing_fees ADD fee_days smallint unsigned NOT NULL" );
        }

        if ( ! $wpdb->get_col( $wpdb->prepare( "SHOW COLUMNS FROM {$wpdb->prefix}wpbdp_listing_fees LIKE %s", 'fee_images' ) ) ) {
            $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_listing_fees ADD fee_images smallint unsigned NOT NULL DEFAULT 0" );
        }

        if ( ! $wpdb->get_col( $wpdb->prepare( "SHOW COLUMNS FROM {$wpdb->prefix}wpbdp_listing_fees LIKE %s", 'fee_id' ) ) ) {
            $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_listing_fees ADD fee_id bigint(20) NULL" );
        }

        $n_fees = intval( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wpbdp_listing_fees" ) );
        $n_fees_migrated = intval( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}wpbdp_listing_fees WHERE migrated = %d", 1 ) ) );
        $fees_done = ( $n_fees_migrated == $n_fees ) ? true : false;

        if ( ! $fees_done ) {
			$status_msg = sprintf( _x( 'Cleaning up plan information... %1$d/%2$d', 'installer', 'business-directory-plugin' ), $n_fees_migrated, $n_fees );

            $fees = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpbdp_listing_fees WHERE migrated = %d ORDER BY id ASC LIMIT 50", 0 ), ARRAY_A );

            foreach ( $fees as &$f ) {
                // Delete fee if category does not exist.
                if ( ! term_exists( intval( $f['category_id'] ), WPBDP_CATEGORY_TAX ) ) {
                    $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wpbdp_listing_fees WHERE id = %d", $f['id'] ) );
                } else {
                    // Delete duplicated listing fees.
                    $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wpbdp_listing_fees WHERE id < %d AND category_id = %d AND listing_id = %d",
                                                  $f['id'],
                                                  $f['category_id'],
                                                  $f['listing_id'] ) );

                    $f['fee'] = (array) unserialize( $f['fee'] );
                    $f['fee_days'] = abs( intval( $f['fee']['days'] ) );
                    $f['fee_images'] = abs( intval( $f['fee']['images'] ) );
                    $f['fee_id'] = intval( $f['fee']['id'] );
                    $f['fee'] = '';
                    $f['migrated'] = 1;

                    unset( $f['fee'] );

                    if ( ! $f['expires_on'] )
                        unset( $f['expires_on'] );

                    $wpdb->update( $wpdb->prefix . 'wpbdp_listing_fees', $f, array( 'id' => $f['id'] ) );
                }
            }
        }

        // Migrate transactions.
        $transactions_done = false;

        if ( $fees_done ) {
            if ( ! $wpdb->get_col( $wpdb->prepare( "SHOW COLUMNS FROM {$wpdb->prefix}wpbdp_payments LIKE %s", 'migrated' ) ) )
                $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_payments ADD migrated tinyint(1) DEFAULT 0" );

            $n_transactions = intval( $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}wpbdp_payments" ) );
            $n_transactions_migrated = intval( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}wpbdp_payments WHERE migrated = %d", 1 ) ) );
            $transactions_done = ( $n_transactions_migrated == $n_transactions ) ? true : false;

            if ( $transactions_done ) {
                if ( wpbdp_column_exists( "{$wpdb->prefix}wpbdp_payments", 'payment_type' ) ) {
                    $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_payments DROP payment_type" );
                }
                if ( wpbdp_column_exists( "{$wpdb->prefix}wpbdp_payments", 'migrated' ) ) {
                    $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_payments DROP migrated" );
                }
                if ( wpbdp_column_exists( "{$wpdb->prefix}wpbdp_listing_fees", 'fee' ) ) {
                    $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_listing_fees DROP fee" );
                }
                if ( wpbdp_column_exists( "{$wpdb->prefix}wpbdp_listing_fees", 'migrated' ) ) {
                    $wpdb->query( "ALTER TABLE {$wpdb->prefix}wpbdp_listing_fees DROP migrated" );
                }
                $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->postmeta} WHERE meta_key = %s", '_wpbdp[payment_status]' ) );
            } else {
				$status_msg = sprintf( _x( 'Migrating previous transactions to new Payments API... %1$d/%2$d', 'installer', 'business-directory-plugin' ), $n_transactions_migrated, $n_transactions );

                $transactions = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpbdp_payments WHERE migrated = %d ORDER BY id ASC LIMIT 50", 0 ), ARRAY_A );

                foreach ( $transactions as &$t ) {
                    $t['status'] = 'approved' == $t['status'] ? 'completed' : ( 'pending' == $t['status'] ? 'pending' : 'rejected' );
                    $t['currency_code'] = get_option( 'wpbdp-currency' );
                    $t['migrated'] = 1;

                    if ( ! isset( $t['processed_on'] ) || empty( $t['processed_on'] ) )
                        unset( $t['processed_on'] );

                    if ( ! isset( $t['created_on'] ) || empty( $t['created_on'] ) )
                        unset( $t['created_on'] );

                    if ( ! isset( $t['listing_id'] ) || empty( $t['listing_id'] ) )
                        $t['listing_id'] = 0;

                    if ( ! isset( $t['amount'] ) || empty( $t['amount'] ) )
                        $t['amount'] = '0.0';

                    // TODO: delete duplicated pending transactions (i.e. two renewals for the same category & listing ID that are 'pending').

                    switch ( $t['payment_type'] ) {
                        case 'initial':
                            $wpdb->insert( $wpdb->prefix . 'wpbdp_payments_items',
                                           array( 'payment_id' => $t['id'],
                                                  'amount' => $t['amount'],
                                                  'item_type' => 'charge',
                                                  'description' => _x( 'Initial listing payment (BD < 3.4)', 'installer', 'business-directory-plugin' )
                                                ) );
                            $wpdb->update( $wpdb->prefix . 'wpbdp_payments', $t, array( 'id' => $t['id'] ) );

                            break;

                        case 'edit':
                            $wpdb->insert( $wpdb->prefix . 'wpbdp_payments_items',
                                           array( 'payment_id' => $t['id'],
                                                  'amount' => $t['amount'],
                                                  'item_type' => 'charge',
                                                  'description' => _x( 'Listing edit payment (BD < 3.4)', 'installer', 'business-directory-plugin' )
                                                ) );
                            $wpdb->update( $wpdb->prefix . 'wpbdp_payments', $t, array( 'id' => $t['id'] ) );

                            break;

                        case 'renewal':
                            $data = unserialize( $t['extra_data'] );
                            $fee_info = $wpdb->get_row( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpbdp_listing_fees WHERE id = %d", $data['renewal_id'] ) );

                            if ( ! $fee_info || ! term_exists( intval( $fee_info->category_id ), WPBDP_CATEGORY_TAX ) ) {
                                $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wpbdp_payments WHERE id = %d", $t['id'] ) );
                                continue;
                            }

                            $fee_info->fee = unserialize( $fee_info->fee );

                            $item = array();
                            $item['payment_id'] = $t['id'];
                            $item['amount'] = $t['amount'];
                            $item['item_type'] = 'fee';
							$item['description'] = sprintf(
								__( 'Renewal fee "%1$s" for category "%2$s"', 'business-directory-plugin' ),
								$fee_info->fee['label'],
								wpbdp_get_term_name( $fee_info->category_id )
							);
                            $item['data'] = serialize( array( 'fee' => $fee_info->fee ) );
                            $item['rel_id_1'] = $fee_info->category_id;
                            $item['rel_id_2'] = $fee_info->fee['id'];

                            $wpdb->insert( $wpdb->prefix . 'wpbdp_payments_items', $item );
                            $wpdb->update( $wpdb->prefix . 'wpbdp_payments', $t, array( 'id' => $t['id'] ) );

                            $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wpbdp_listing_fees WHERE id = %d", $data['renewal_id'] ) );

                            break;

                        case 'upgrade-to-sticky':
                            $wpdb->insert( $wpdb->prefix . 'wpbdp_payments_items',
                                           array( 'payment_id' => $t['id'],
                                                  'amount' => $t['amount'],
                                                  'item_type' => 'upgrade',
                                                  'description' => _x( 'Listing upgrade to featured', 'installer', 'business-directory-plugin' )
                                                ) );
                            $wpdb->update( $wpdb->prefix . 'wpbdp_payments', $t, array( 'id' => $t['id'] ) );

                            break;

                        default:
                            $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wpbdp_payments WHERE id = %d", $t['id'] ) );
                            break;
                    }

                }
            }
        }

        $res = array( 'ok' => true,
                      'done' => $transactions_done,
                      'status' => $status_msg );

        return $res;
    }

}
