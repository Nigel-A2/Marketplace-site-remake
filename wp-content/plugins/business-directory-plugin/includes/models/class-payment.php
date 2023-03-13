<?php
/**
 * @package WPBDP/Includes
 */

/**
 * Class WPBDP_Payment
 */
class WPBDP_Payment extends WPBDP__DB__Model {

    public static $serialized = array( 'payment_items', 'payer_data', 'data' );

    private $old_status = '';

	private $listing = array();

    protected function get_defaults() {
        return array(
            'parent_id'     => 0,
            'payment_items' => array(),
            'payer_data'    => array(),
            'gateway_data'  => array(),
            'status'        => 'pending',
            'currency_code' => WPBDP_Currency_Helper::get_currency_code(),
            'amount'        => 0.0,
            'data'          => array(),
			'test'          => false,
        );
    }

    protected function prepare_row() {
        $row = parent::prepare_row();

		$this->save_created_at( $row );

        // Remove unnecessary columns.
        // FIXME: In the future we should not use WPBDP__DB__Model at all. See #2945.
        // FIXME: We also need to remove at least `created_on`, `processed_on` and `processed_by` which are not used anywhere.
        unset( $row['created_on'] );
        unset( $row['processed_on'] );
        unset( $row['processed_by'] );

        return $row;
    }

	/**
	 * Created_at isn't getting set by the parent during updates.
	 *
	 * @since 6.2.5
	 */
	protected function save_created_at( &$row ) {
		if ( isset( $row['created_at'] ) || empty( $this->_attrs['created_at'] ) ) {
			return;
		}

		$created_at = $this->_attrs['created_at'];
		$formatted  = date( 'Y-m-d H:i:s', strtotime( $created_at ) );
		if ( $created_at === $formatted ) {
			// Only save if the format is correct.
			$row['created_at'] = $formatted;
		}
	}

    protected function before_save( $new = false ) {
        if ( ! $this->payment_key ) {
            $this->payment_key = strtolower( sha1( $this->listing_id . date( 'Y-m-d H:i:s' ) . ( defined( 'AUTH_KEY' ) ? AUTH_KEY : '' ) . uniqid( 'wpbdp', true ) ) );
        }

        $this->amount = 0.0;

        foreach ( $this->payment_items as $item ) {
            $this->amount += floatval( $item['amount'] );
        }

        if ( 0.0 == $this->amount ) {
            if ( ! $this->has_item_type( 'discount_code' ) ) {
                $this->status = 'completed';
            }
        }

        if ( $new ) {
            $this->maybe_set_test_mode();
        }

		WPBDP_Utils::cache_delete_group( 'wpbdp_payments' );
    }

    protected function after_save( $new = false ) {
        if ( $new ) {
            wpbdp_insert_log(
                array(
					'log_type'      => 'listing.payment',
					'object_id'     => $this->listing_id,
					'rel_object_id' => $this->id,
                )
            );
        }

        if ( ! $this->old_status || ! $this->status ) {
            return;
        }

        if ( $this->old_status != $this->status ) {
            wpbdp_insert_log(
                array(
					'log_type'  => 'payment.status_change',
					'actor'     => is_admin() ? 'user:' . get_current_user_id() : 'system',
					'object_id' => $this->id,
					'message'   => sprintf( _x( 'Payment status changed from "%1$s" to "%2$s".', 'payment', 'business-directory-plugin' ), $this->old_status, $this->status ),
                )
            );
			// phpcs:ignore WordPress.NamingConventions.ValidHookName
            do_action_ref_array( 'WPBDP_Payment::status_change', array( &$this, $this->old_status, $this->status ) );
            do_action( "wpbdp_payment_{$this->status}", $this );
        }

        $this->old_status = $this->status;
    }

    protected function after_delete() {
        global $wpdb;
        $wpdb->delete(
            $wpdb->prefix . 'wpbdp_logs',
            array(
				'object_type' => 'payment',
				'object_id'   => $this->id,
            )
        );

		WPBDP_Utils::cache_delete_group( 'wpbdp_payments' );
    }

    protected function set_attr( $name, $value ) {
        if ( in_array( $name, self::$serialized, true ) ) {
            $value = is_array( $value ) ? $value : array();
        }

        if ( 'status' == $name ) {
            $this->old_status = $this->status;
        }

        return parent::set_attr( $name, $value );
    }

    public function get_listing() {
		if ( empty( $this->listing ) ) {
			$this->listing = WPBDP_Listing::get( $this->listing_id );
		}
		return $this->listing;
    }

    public function get_summary() {
        $summary = '';

        switch ( $this->payment_type ) {
			case 'initial':
				$summary = sprintf( _x( 'Initial payment ("%s")', 'payment', 'business-directory-plugin' ), $this->get_listing()->get_title() );
                break;
			case 'renewal':
				$summary = sprintf( _x( 'Renewal payment ("%s")', 'payment', 'business-directory-plugin' ), $this->get_listing()->get_title() );
                break;
			default:
                break;
        }

        if ( ! $summary ) {
            $first_item = reset( $this->payment_items );
            $summary    = $first_item['description'];
        }

        if ( 'admin-submit' == $this->context ) {
            $summary = sprintf( _x( '%s. Admin Posted.', 'payment summary', 'business-directory-plugin' ), $summary );
        } elseif ( 'csv-import' == $this->context ) {
            $summary = sprintf( _x( '%s. Imported Listing.', 'payment summary', 'business-directory-plugin' ), $summary );
        }

        return $summary;
    }

    public function get_created_at_date() {
        $date = date_parse( $this->created_at );

		return array(
			'year'  => $date['year'],
			'month' => $date['month'],
			'day'   => $date['day'],
		);
    }

    public function get_created_at_time() {
        $date = date_parse( $this->created_at );

		return array(
			'hour'   => $date['hour'],
			'minute' => $date['minute'],
		);
    }

    public function get_payer_details() {
        $data               = array();
        $data['email']      = $this->payer_email;
        $data['first_name'] = $this->payer_first_name;
        $data['last_name']  = $this->payer_last_name;
        $data['country']    = '';
        $data['state']      = '';
        $data['city']       = '';
        $data['address']    = '';
        $data['address_2']  = '';
        $data['zip']        = '';

        foreach ( (array) $this->payer_data as $k => $v ) {
            $data[ $k ] = $v;
        }
		$this->fill_from_listing( $data );

        return $data;
    }

	/**
	 * If the payer is empty, get info from the listing.
	 *
	 * @since 5.11
	 */
	private function fill_from_listing( &$data ) {
		$this->get_listing();
		if ( empty( $this->listing ) ) {
			return;
		}

		$map = array(
			'email'   => array( 'email', 'business_contact_email' ),
			'country' => array( 'country' ),
			'state'   => array( 'state' ),
			'city'    => array( 'city' ),
			'zip'     => array( 'zip_code', 'zip' ),
		);

		foreach ( $map as $key => $fields ) {
			foreach ( $fields as $field ) {
				if ( empty( $data[ $key ] ) ) {
					$data[ $key ] = $this->listing->get_field_value( $field );
				}
			}
		}
	}

    public function get_payer_address() {
        $address = array();

        foreach ( array( 'address', 'address_2', 'city', 'state', 'zip', 'country' ) as $k ) {
            if ( ! empty( $this->payer_data[ $k ] ) ) {
                $address[ $k ] = $this->payer_data[ $k ];
            }
        }

        return $address;
    }

    public function has_item_type( $item_type ) {
        $item_types = wp_list_pluck( $this->payment_items, 'type' );
        return in_array( $item_type, $item_types, true );
    }

    public function find_item( $item_type ) {
        foreach ( $this->payment_items as $item ) {
            if ( $item_type == $item['type'] ) {
                return $item;
            }
        }

        return null;
    }

    public function process_as_admin() {
        // $this->payment_items[0]['description'] .= ' ' . _x( '(admin, no charge)', 'submit listing', 'business-directory-plugin' );
        // $this->payment_items[0]['amount'] = 0.0;
        $this->status  = 'completed';
        $this->context = 'admin-submit';
        $this->save();

        wpbdp_insert_log(
            array(
				'log_type'  => 'payment.note',
				'object_id' => $this->id,
				'actor'     => is_admin() ? 'user:' . get_current_user_id() : 'system',
				'message'   => _x( 'Listing submitted by admin. Payment skipped.', 'submit listing', 'business-directory-plugin' ),
            )
        );
    }

    public function is_completed() {
        return 'completed' == $this->status;
    }

    public function is_pending() {
        return 'pending' == $this->status;
    }

	/**
	 * The link to view the payment at the gateway.
	 *
	 * @since 5.11
	 *
	 * @return string
	 */
	public function get_gateway_link() {
		$gateway = wpbdp()->payment_gateways->get( $this->gateway );
		$link    = '';
		if ( $gateway ) {
			$link = $gateway->get_payment_link( $this );
		}
		return $link;
	}

    public function get_admin_url() {
        return admin_url( 'admin.php?page=wpbdp_admin_payments&wpbdp-view=details&payment-id=' . $this->id );
    }

    public function get_checkout_url( $force_http = false ) {
        $url = wpbdp_url( 'checkout', $this->payment_key );

        if ( ! $force_http && ! is_ssl() ) {
            $url = set_url_scheme( $url, 'https' );
        }

        return $url;
    }

    public function get_return_url() {
        $params = array(
            'action'   => 'return',
            '_wpnonce' => wp_create_nonce( 'wpbdp-checkout-' . $this->id ),
        );

		if ( $this->gateway ) {
			// Set the correct gateway if leaving to complete the payment.
			$params['gateway'] = $this->gateway;
		}

        return add_query_arg( $params, $this->get_checkout_url() );
    }

    public function get_cancel_url() {
        // XXX: Is 'cancel-payment' really used?
        return add_query_arg( 'cancel-payment', '1', $this->get_checkout_url() );
    }

    public function get_payment_notes() {
        if ( ! $this->id ) {
            return array();
        }

        return wpbdp_get_logs(
            array(
				'object_id'   => $this->id,
				'object_type' => 'payment',
            )
        );
    }

    public function log( $msg ) {
        return wpbdp_insert_log(
            array(
				'object_id'   => $this->id,
				'object_type' => 'payment',
				'log_type'    => 'payment.note',
				'message'     => $msg,
            )
        );
    }

    public function set_payment_method( $method ) {
        $this->gateway = $method;
        $this->save();
    }

    /**
     * Check if payment gateway is in test mode and set the payment as testing if so
     *
     * @return void
     */
    public function maybe_set_test_mode() {
        $this->is_test = (bool) wpbdp_get_option( 'payments-test-mode' );
    }

    public function has_been_processed() {
        return ! empty( $this->processed_by );
    }

    /**
     * Returns the list of supported payment statuses. By default, this is the list of statuses and their meaning:
     * - Pending: Payment generated, but not paid.
     * - Failed: Payment failed/was declined.
     * - Completed: Payment was received successfuly and order is complete.
     * - Canceled: Payment was canceled either by the user or the admin.
     * - Refunded: Payment was refunded by admin.
     * - On-hold: Not really used, but might be useful for manual payment gateways in the future.
     *
     * @return array Array of status => label items.
     */
    public static function get_stati() {
        $stati              = array();
        $stati['pending']   = _x( 'Pending', 'payment', 'business-directory-plugin' );
        $stati['failed']    = _x( 'Failed', 'payment', 'business-directory-plugin' );
        $stati['completed'] = _x( 'Completed', 'payment', 'business-directory-plugin' );
        $stati['canceled']  = _x( 'Canceled', 'payment', 'business-directory-plugin' );
        $stati['on-hold']   = _x( 'On Hold', 'payment', 'business-directory-plugin' );
        $stati['refunded']  = _x( 'Refunded', 'payment', 'business-directory-plugin' );

        return $stati;
    }

    public static function get_status_label( $status ) {
        $stati = self::get_stati();
        return $stati[ $status ];
    }

    /**
     * @override
     */
    public static function objects() {
        return parent::_objects( get_class() );
    }

	/**
	 * Check if the payment is recurring or the amount is greater than 0.
	 * This is used to show the payment options when price can be 0 or if is recurring.
	 *
	 * @since 5.15
	 *
	 * @return bool
	 */
	public function show_payment_options() {
		return ( $this->has_item_type( 'recurring_plan' ) || $this->amount > 0 );
	}

	/**
	 * @deprecated 6.1
	 */
	public function is_canceled() {
		_deprecated_function( __METHOD__, '6.1' );
		return $this->status === 'canceled';
	}

	/**
	 * @deprecated 6.1
	 */
	public function is_rejected() {
		_deprecated_function( __METHOD__, '6.1' );
		return false;
	}
}

