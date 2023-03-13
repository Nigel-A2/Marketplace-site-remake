<?php

require_once WPBDP_PATH . 'includes/utils.php';

class WPBDP__Migrations__18_0 extends WPBDP__Migration {

    public function migrate() {
        global $wpdb;

        // Remove orphans of everything first to make things easier for us.
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wpbdp_listing_fees WHERE listing_id NOT IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s)", WPBDP_POST_TYPE ) );
        $wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}wpbdp_payments WHERE listing_id NOT IN (SELECT ID FROM {$wpdb->posts} WHERE post_type = %s)", WPBDP_POST_TYPE ) );
        $wpdb->query( "DELETE FROM {$wpdb->prefix}wpbdp_payments_items WHERE payment_id NOT IN (SELECT id FROM {$wpdb->prefix}wpbdp_payments)" );
        $wpdb->query( $wpdb->prepare( "UPDATE {$wpdb->prefix}wpbdp_payments SET status = %s WHERE status = %s", 'failed', 'rejected' ) );

        $this->request_manual_upgrade( '_upgrade_to_18_migrate_fees' );
    }

    public function _upgrade_to_18_migrate_fees() {
        $status_msg = '';
        $done = false;

        $subroutines = array(
            '_migrate_licenses',
            '_migrate_email_notices',
            '_migrate_fee_plans',
            '_migrate_payment_items',
            '_migrate_listings',
            '_set_featured_migration_flag'
        );

        foreach ( $subroutines as $sr ) {
            $done = call_user_func_array( array( $this, $sr ), array( &$status_msg ) );

            if ( ! $done )
                break;
        }

        return array( 'ok' => true, 'done' => $done, 'status' => $status_msg );
    }

    /**
     * Sets an option that tells BD that a Featured levels migration is pending.
     * This process can be performed manually by the admin at any time later after this manual upgrade.
     */
    public function _set_featured_migration_flag( &$msg ) {
        update_option( 'wpbdp-migrate-18_0-featured-pending', true, false );
        return true;
    }

    public function _migrate_licenses( &$msg ) {
        global $wpdb;

        $settings = get_option( 'wpbdp_settings', array() );
        $licenses = get_option( 'wpbdp_licenses', array() );

        $module_keys = $wpdb->get_results( "SELECT option_name, option_value FROM {$wpdb->options} WHERE option_name LIKE 'wpbdp-license-key-%'", ARRAY_A );
        $theme_keys  = get_option( 'wpbdp-themes-licenses', array() );

        foreach ( $module_keys as $item ) {
            $module_id = str_replace( 'wpbdp-license-key-', '', $item['option_name'] );
            $key       = $item['option_value'];
            $status    = get_option( 'wpbdp-license-status-' . $module_id, 'invalid' );

            $settings[ 'license-key-module-' . $module_id ] = $key;
            $licenses[ 'module-' . $module_id ] = array(
                'license_key'  => $key,
                'status'       => $status,
                'expires'      => '',
                'last_checked' => 0
            );
        }

        foreach ( $theme_keys as $theme_id => $license_data ) {
            $settings[ 'license-key-theme-' . $theme_id ] = $license_data['license'];
            $licenses[ 'theme-' . $theme_id ] = array(
                'license_key'  => $license_data['license'],
                'status'       => $license_data['status'],
                'expires'      => '',
                'last_checked' => $license_data['updated']
            );
        }

        update_option( 'wpbdp_settings', $settings );
        update_option( 'wpbdp_licenses', $licenses );

        $msg = _x( 'Migrating license information to new format...', 'installer', 'business-directory-plugin' );
        return true;
    }

    public function _migrate_email_notices( &$msg ) {
        require_once WPBDP_INC . 'admin/settings/class-settings-bootstrap.php';
        $defaults = WPBDP__Settings__Bootstrap::get_default_expiration_notices();

        $notices = array();

		$email = get_option( 'wpbdp-listing-renewal-message', false );
		if ( $email ) {
			$notices[] = array(
				'event' => 'expiration',
				'relative_time' => '0 days',
				'listings' => 'non-recurring',
				'subject' => $email['subject'],
				'body' => $email['body']
			);
		} else {
			$notices[] = $defaults[1];
		}

        if ( $t = get_option( 'wpbdp-renewal-email-threshold', false ) ) {
            if ( $email = get_option( 'wpbdp-renewal-pending-message' ) ) {
                $notices[] = array(
                    'event'         => 'expiration',
                    'relative_time' => '+' . $t . ' days',
                    'listings'      => 'non-recurring',
                    'subject'       => $email['subject'],
                    'body'          => $email['body']
                );
            } else {
                $email = $defaults[0];
                $email['relative_time'] = '+' . $t . ' days';
                $notices[] = $email;
            }

            if ( get_option( 'wpbdp-send-autorenewal-expiration-notice', false ) ) {
                if ( $email = get_option( 'wpbdp-listing-autorenewal-notice', false ) ) {
                    $notices[] = array(
                        'event'         => 'expiration',
                        'relative_time' => '+' . $t . ' days',
                        'listings'      => 'recurring',
                        'subject'       => $email['subject'],
                        'body'          => $email['body']
                    );
                } else {
                    $email = $defaults[3];
                    $email['relative_time'] = '+' . $t . ' days';
                    $notices[] = $email;
                }
            }
        }

        if ( $t = get_option( 'wpbdp-renewal-reminder-threshold', false ) ) {
            if ( $email = get_option( 'wpbdp-renewal-reminder-message' ) ) {
                $notices[] = array(
                    'event'         => 'expiration',
                    'relative_time' => '-' . $t . ' days',
                    'listings'      => 'both',
                    'subject'       => $email['subject'],
                    'body'          => $email['body']
                );
            } else {
                $email = $defaults[2];
                $email['relative_time'] = '-' . $t . ' days';
                $notices[] = $email;
            }
        }

		$email = get_option( 'wpbdp-listing-autorenewal-message' );
		if ( $email ) {
			$notices[] = array(
				'event'         => 'renewal',
				'listings'      => 'recurring',
				'subject'       => $email['subject'],
				'body'          => $email['body']
			);
		} else {
			$notices[] = $defaults[4];
		}

        // Clamp relative times to what we can handle.
        foreach ( $notices as &$notice ) {
            if ( empty( $notice['relative_time'] ) || '0 days' == $notice['relative_time'] ) {
                continue;
            }

            $mod = substr( $notice['relative_time'], 0, 1 );
            $days = absint( trim( str_replace( array( '+', '-', 'days' ), '', $notice['relative_time'] ) ) );

            if ( $days > 45 ) {
                $notice['relative_time'] = $mod . '2 months';
            } elseif ( $days > 23 ) {
                $notice['relative_time'] = $mod . '1 months';
            } else if ( $days > 11 ) {
                $notice['relative_time'] = $mod . '2 weeks';
            } else if ( $days > 5) {
                $notice['relative_time'] = $mod . '1 weeks';
            } else {
                $notice['relative_time'] = $mod . $days . ' days';
            }
        }

        update_option( 'wpbdp-expiration-notices', $notices );

        $msg = _x( 'Migrating email notices to new format...', 'installer', 'business-directory-plugin' );
        return true;
    }

    /**
     * Updates (if needed) current fees to add information for the new columns:
     * supported_categories, pricing_model.
     */
    public function _migrate_fee_plans( &$msg ) {
        global $wpdb;

        $msg = _x( 'Migrating plans...', 'installer', 'business-directory-plugin' );

        if ( ! wpbdp_table_exists( $wpdb->prefix . 'wpbdp_fees' ) ) {
            return true;
        }

        foreach ( $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}wpbdp_fees" ) as $fee ) {
            $old_categories = isset( $fee->categories ) ? unserialize( $fee->categories ) : array();

            if ( ! is_array( $old_categories ) || empty( $old_categories ) || ( isset( $old_categories['all'] ) && $old_categories['all'] ) ) {
                $categories = 'all';
            } else {
                $categories = implode( ',', array_map( 'absint', $old_categories['categories'] ) );
            }

            $row = array(
                'id' => $fee->id,
                'label' => $fee->label,
                'amount' => $fee->amount,
                'days' => absint( $fee->days ),
                'images' => ! empty( $fee->images ) ? absint( $fee->images ) : 0,
                'sticky' => ! empty( $fee->sticky ),
                'pricing_model' => 'flat',
                'supported_categories' => $categories ? $categories : 'all',
                'weight' => ! empty( $fee->weight ) ? $fee->weight : 0,
                'enabled' => ! empty( $fee->enabled ),
                'description' => ! empty( $fee->description ) ? $fee->description : '',
                'tag' => ! empty( $fee->tag ) ? $fee->tag : '',
                'recurring' => ( 0 != $fee->days && $fee->amount > 0.0 && get_option( 'wpbdp-listing-renewal-auto' ) && get_option( 'wpbdp-listing-renewal-auto-dontask' ) ) ? 1 : 0
            );

            // Check if plan already exists.
            $exists  = (bool) $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}wpbdp_plans WHERE id = %d", $fee->id ) );
            $success = true;

            global $wpdb;
            $wpdb->show_errors();
            if ( $exists ) {
                $success = ( false !== $wpdb->update( $wpdb->prefix . 'wpbdp_plans', $row, array( 'id' => $fee->id ) ) );
            } else {
                $success = ( false !== $wpdb->insert( $wpdb->prefix . 'wpbdp_plans', $row ) );
            }

            if ( ! $success ) {
				$msg = sprintf( __( 'Could not migrate plan "%1$s" (%2$d)', 'business-directory-plugin' ), $fee->label, $fee->id );
                return false;
            }
        }

        return true;
    }

    /**
     * Removes rows from payments_items and adds the items to the new payment_items column in the payments table.
     */
    public function _migrate_payment_items( &$msg ) {
        global $wpdb;

        $count = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->prefix}wpbdp_payments WHERE payment_items IS NULL OR payment_items = %s", '' ) );
        $batch_size = 20;

        if ( ! $count )
            return true;

		foreach ( $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpbdp_payments WHERE payment_items IS NULL OR payment_items = %s ORDER BY id ASC LIMIT {$batch_size}", '' ) ) as $payment ) {
            $payment_id = $payment->id;

            if ( isset( $payment->tag ) ) {
                $payment_type = $payment->tag;
            } else {
                $payment_type = null;
            }

            $items = array();

            foreach ( $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpbdp_payments_items WHERE payment_id = %d", $payment_id ) ) as $item ) {
                $new_item = array();

                switch ( $item->item_type ) {
                case 'fee':
                    $new_item['type'] = 'plan';
                    break;
                case 'recurring_fee':
                    $new_item['type'] = 'recurring_plan';
                    break;
                default:
                    $new_item['type'] = $item->item_type;
                    break;
                }

                $new_item['description'] = $item->description;
                $new_item['amount'] = $item->amount;

                if ( $data = unserialize( $item->data ) ) {
                    if ( ! is_array( $data ) )
                        $new_item['deprecated_data'] = $data;

                    foreach ( $data as $key => $val ) {
                        if ( ! isset( $new_item[ $key ] ) )
                            $new_item[ $key ] = $val;
                    }
                }

                if ( ! empty( $new_item['is_renewal'] ) )
                    $new_item['type'] = 'plan_renewal';

                $new_item['rel_id_1'] = $item->rel_id_1;
                $new_item['rel_id_2'] = $item->rel_id_2;

                $items[] = $new_item;
            }

            if ( ! $payment_type ) {
                // TODO: Try to find out the payment type from the items.
            }

            if ( false === $wpdb->update( $wpdb->prefix . 'wpbdp_payments', array( 'payment_items' => serialize( $items ), 'payment_type' => $payment_type ), array( 'id' => $payment_id ) ) ) {
                $msg = sprintf( _x( '! Could not migrate payment #%d', 'installer', 'business-directory-plugin' ), $payment_id );
                return false;
            }
        }

        $msg = sprintf( _x( 'Updating payment items format: %d items remaining...', 'installer', 'business-directory-plugin' ), max( $count - $batch_size, 0 ) );
        return false;
    }

    /**
     * Makes sure that ALL listings have an entry in listings. The fee is extracted from available information:
     * - the (now deprecated) listing fees table
     * - pending payments (recurring taking precedence over regular ones).
     * If nothing useful is found, the default free fee is assigned.
     */
    public function _migrate_listings( &$msg ) {
        global $wpdb;

        if ( ! wpbdp_table_exists( $wpdb->prefix . 'wpbdp_listing_fees' ) ) {
            return true;
        }

        $batch_size = 20;

        $count = absint( $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} p WHERE p.post_type = %s AND p.ID NOT IN (SELECT lp.listing_id FROM {$wpdb->prefix}wpbdp_listings lp) ORDER BY ID ASC LIMIT {$batch_size}", WPBDP_POST_TYPE ) ) );
        $listings = $wpdb->get_col( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} p WHERE p.post_type = %s AND p.ID NOT IN (SELECT lp.listing_id FROM {$wpdb->prefix}wpbdp_listings lp) ORDER BY ID ASC LIMIT {$batch_size}", WPBDP_POST_TYPE ) );

        if ( ! $count )
            return true;

        foreach ( $listings as $listing_id ) {
            $this->set_listing_categories( $listing_id ); // Set listing categories.

            // Obtain new plan.
            $new_plan = $this->plan_from_fees( $listing_id );

            if ( ! $new_plan )
                $new_plan = $this->plan_from_payments( $listing_id );

            // This shouldn't happen but... just in case.
            if ( ! $new_plan ) {
                $free_plan = wpbdp_get_fee_plan( 'free' );
                $new_plan = array(
                    'listing_id' => $listing_id,
                    'fee_id' => $free_plan->id,
                    'fee_price' => 0.0,
                    'fee_days' => ! empty( $free_plan->days ) ? $free_plan->days : 365,
                    'fee_images' => ! empty( $free_plan->images ) ? absint( $free_plan->images ) : 0,
                    'is_sticky' => ! empty( $free_plan->sticky ),
                );

                if ( $expiration = $free_plan->calculate_expiration_time() )
                    $new_plan['expiration_date'] = $expiration;
            }

            $wpdb->delete( $wpdb->prefix . 'wpbdp_listings', array( 'listing_id' => $listing_id ) );
            $wpdb->insert( $wpdb->prefix . 'wpbdp_listings', $new_plan );

            $l = WPBDP_Listing::get( $listing_id );
            $l->get_status( true );
        }

        $msg = sprintf( _x( 'Migrating listing information: %d items remaining...', 'installer', 'business-directory-plugin' ), max( $count - $batch_size, 0 ) );
        return false;
    }

    private function set_listing_categories( $listing_id ) {
        global $wpdb;

        $cat_ids = array();

        // From current fees.
        $cat_ids = array_merge( $cat_ids, $wpdb->get_col( $wpdb->prepare( "SELECT category_id FROM {$wpdb->prefix}wpbdp_listing_fees WHERE listing_id = %d", $listing_id ) ) );

        // From pending payments.
        $pending = $wpdb->get_col( $wpdb->prepare( "SELECT payment_items FROM {$wpdb->prefix}wpbdp_payments WHERE listing_id = %d AND status = %s", $listing_id, 'pending' ) );
        if ( $pending ) {
            $pending = array_map( 'unserialize', $pending );
            $pending = call_user_func_array( 'array_merge', $pending );

            foreach ( $pending as $item ) {
                if ( ! in_array( $item['type'], array( 'plan', 'plan_renewal', 'recurring_plan' ), true ) )
                    continue;

                if ( ! empty( $item['rel_id_1'] ) )
                    $cat_ids[] = $item['rel_id_1'];
            }
        }

        $cat_ids = array_map( 'intval', $cat_ids );

        if ( $cat_ids )
            wp_set_object_terms( $listing_id, $cat_ids, WPBDP_CATEGORY_TAX, true );
    }

    private function plan_from_fees( $listing_id ) {
        global $wpdb;

        $key_translations = array(
            'expiration_date' => 'expires_on',
            'is_recurring' => 'recurring',
            'subscription_id' => 'recurring_id',
            'is_sticky' => 'sticky'
        );

        $choices = array();
        foreach ( array( 'fee_id', 'fee_days', 'fee_images', 'fee_price', 'is_sticky', 'expiration_date' ) as $key ) {
            $choices[ $key ] = array();
        }

        $fees = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpbdp_listing_fees WHERE listing_id = %d", $listing_id ) );

        if ( ! $fees )
            return false;

        foreach ( $fees as $fee ) {
            if ( $fee->recurring ) {
				$x = wpbdp_get_fee_plan( $fee->fee_id );
				if ( $x ) {
					$price = $x->amount;
				} else {
					$price = 0.0;
				}

                return array(
                    'listing_id' => $listing_id,
                    'fee_id' => $fee->fee_id,
                    'fee_price' => $price,
                    'fee_days' => $fee->fee_days,
                    'fee_images' => $fee->fee_images,
                    'expiration_date' => $fee->expires_on,
                    'is_recurring' => 1,
                    'is_sticky' => $fee->sticky,
                    'subscription_id' => $fee->recurring_id
                );
            }

            foreach ( array_keys( $choices ) as $key ) {
                $oldkey = isset( $key_translations[ $key ] ) ? $key_translations[ $key ] : $key;

                if ( 'fee_price' == $key ) {
					$x = wpbdp_get_fee_plan( $fee->fee_id );
					if ( $x ) {
						$fee->fee_price = $x->amount;
					} else {
						$fee->fee_price = 0.0;
					}
                }

                if ( 'expiration_date' == $key && ! $fee->expires_on )
                    $fee->expires_on = -1;

                $choices[ $key ][] = $fee->{$oldkey};
            }
        }

        $res['listing_id'] = $listing_id;
        $res['fee_id'] = $choices['fee_id'][0]; // Use the first fee id.
        $res['fee_days'] = in_array( -1, $choices['fee_days'] ) ? 0 : max( $choices['fee_days'] );

        foreach ( array( 'fee_images', 'fee_price', 'is_sticky' ) as $key )
            $res[ $key ] = max( $choices[ $key ] );

        if ( ! in_array( -1, $choices['expiration_date'] ) ) {
            $res['expiration_date'] = date( 'Y-m-d H:i:s', max( array_map( 'strtotime', $choices['expiration_date'] ) ) );
        }

        $res['is_recurring'] = 0;
        $res['subscription_id'] = '';
        $res['is_sticky'] = ! empty( $res['is_sticky'] );

        return $res;
    }

    private function plan_from_payments( $listing_id ) {
        global $wpdb;

        $fee = null;
        $pending_payments = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}wpbdp_payments WHERE listing_id = %d AND status = %s", $listing_id, 'pending' ) );

        if ( ! $pending_payments )
            return false;

        foreach ( $pending_payments as $payment ) {
            $items = unserialize( $payment->payment_items );

            foreach ( $items as $item_ ) {
                $item = (object) $item_;

                if ( $item->type == 'recurring_plan' ) {
                    $fee = array(
                        'fee_id' => isset( $item->fee_id ) ? $item->fee_id : ( isset( $item->rel_id_2 ) ? $item->rel_id_2 : 0 ),
                        'fee_days' => ! empty( $item->fee_days ) ? $item->fee_days : 0,
                        'fee_images' => ! empty( $item->fee_images ) ? $item->fee_images : 0,
                        'fee_price' => $item->amount,
                        'start_date' => $payment->created_on,
                        'is_recurring' => 1
                    );
                }

                if ( is_null( $fee ) && in_array( $item->type, array( 'plan', 'plan_renewal' ), true ) ) {
                    $fee = array(
                        'fee_id' => isset( $item->fee_id ) ? $item->fee_id : ( isset( $item->rel_id_2 ) ? $item->rel_id_2 : 0 ),
                        'fee_days' => ! empty( $item->fee_days ) ? $item->fee_days : 0,
                        'fee_images' => ! empty( $item->fee_images ) ? $item->fee_images : 0,
                        'fee_price' => $item->amount,
                        'start_date' => $payment->created_on,
                        'is_recurring' => 0
                    );
                }
            }
        }

		$_ = wpbdp_get_fee_plan( $fee['fee_id'] );
        if ( $_ ) {
            $fee['is_sticky'] = $_->sticky;

            if ( 0 == $fee['fee_days'] )
                $fee['fee_days'] = absint( $_->days );
        }

        if ( 0 != $fee['fee_days'] )
            $fee['expiration_date'] = date( 'Y-m-d H:i:s', strtotime( sprintf( '+%d days', $fee['fee_days'] ), strtotime( $fee['start_date'] ) ) );

        $fee['listing_id'] = $listing_id;
        unset( $fee['start_date'] );

        return $fee;
    }

}
