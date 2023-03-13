<?php
class WPBDP__Listing_Subscription {

    private $parent_payment_id = 0;
    private $listing_id = 0;
    private $subscription_id = '';


    public function __construct( $listing_id = 0, $subscription_id = '' ) {
        $listing_id = absint( $listing_id );
        $subscription_id = trim( $subscription_id );

        if ( $listing_id ) {
            $listing = wpbdp_get_listing( $listing_id );

            if ( $listing && ! $listing->is_recurring() ) {
                throw new Exception( 'Listing is not recurring!' );
            }

            if ( $listing ) {
                $this->fill_data_from_db( 'listing_id', $listing_id );
                return;
            }
        }

        if ( ! $this->fill_data_from_db( 'subscription_id', $subscription_id ) ) {
            throw new Exception( 'Subscription does not exist!' );
        }
    }

    private function fill_data_from_db( $key, $val ) {
        global $wpdb;

        $row = $wpdb->get_row( $wpdb->prepare( "SELECT listing_id, subscription_id, subscription_data FROM {$wpdb->prefix}wpbdp_listings WHERE {$key} = %s", $val ) );

        if ( ! $row ) {
            return false;
        }

        $susc_id = $row->subscription_id;
        $susc_data = $row->subscription_data ? unserialize( $row->subscription_data ) : array();

        $this->listing_id = absint( $row->listing_id );
        $this->subscription_id = $susc_id;
        $this->parent_payment_id = ! empty( $susc_data['parent_payment_id'] ) ? absint( $susc_data['parent_payment_id'] ) : 0;

        $this->data = $susc_data;

        return true;
    }

    public function get_parent_payment() {
        if ( $this->parent_payment_id ) {
            return wpbdp_get_payment( $this->parent_payment_id );
        }

        return null;
    }

    public function get_payments() {
        return ! empty( $this->data['payments'] ) ? $this->data['payments'] : array();
    }

    public function set_subscription_id( $subscription_id ) {
        if ( empty( $subscription_id ) ) {
            return;
        }

        $this->subscription_id = $subscription_id;
        $this->save();
    }

    public function get_subscription_id() {
        return $this->subscription_id;
    }

    public function record_payment( $args_or_payment = array() ) {
        $parent_payment = $this->get_parent_payment();
        $payments = $this->get_payments();

        if ( is_array( $args_or_payment ) ) {
            $args = wp_parse_args(
                $args_or_payment,
                array(
                    'amount'        => '0.0',
                    'gateway_tx_id' => '',
                    'gateway'       => '',
					// TODO: accept 'created_at' and 'mode' (live/test).
                )
            );

            if ( ! empty( $args['gateway_tx_id'] ) ) {
                $p_id = $args['gateway_tx_id'];
                $p_gateway = ( empty( $args['gateway'] ) && $parent_payment ) ? $parent_payment->gateway : $args['gateway'];
                $payment = WPBDP_Payment::objects()->get( array( 'gateway_tx_id' => $p_id, 'gateway' => $p_gateway ) ); // Just in case the payment is already in the database.
            }

            if ( ! $payment ) {
                $payment = new WPBDP_Payment( $args_or_payment );
            }
        } else {
            $payment = $args_or_payment;
        }

        if ( $payment->id && in_array( $payment->id, $payments ) )
            return;

        if ( $parent_payment ) {
            $payment->parent_id = $parent_payment->id;
            $payment->listing_id = $parent_payment->listing_id;
            $payment->payment_type = 'renewal';
            $payment->payer_email = $parent_payment->payer_email;
            $payment->payer_first_name = $parent_payment->payer_first_name;
            $payment->payer_last_name = $parent_payment->payer_last_name;
            $payment->payer_data = $parent_payment->payer_data;
            $payment->currency_code = $parent_payment->currency_code;
            $payment->status = 'completed';
            $payment->gateway = ( ! $payment->gateway ) ? $parent_payment->gateway : $payment->gateway;

            if ( $item = $parent_payment->find_item( 'recurring_plan' ) ) {
                $payment->payment_items[] = $item;
            }
        }

        if ( ! $payment->id ) {
            // Save silently (no hooks fired).
            $payment->save( false, false );
        }

        // This is the first payment.
        if ( ! $payments ) {
            $this->parent_payment_id = $payment->id;
        }

        $payments[] = $payment->id;
        $this->data['payments'] = $payments;
        $this->save();
    }

    public function renew() {
        $listing = wpbdp_get_listing( $this->listing_id );
        $listing->update_plan();
        $listing->set_status( 'complete' );
        $listing->set_post_status( 'publish' );

        do_action( 'wpbdp_listing_renewed', $listing );
    }

    public function cancel() {
        global $wpdb;

        $wpdb->update(
            "{$wpdb->prefix}wpbdp_listings",
            array(
                'is_recurring' => '0',
                'subscription_id' => '',
                'subscription_data' => ''
            ),
            array( 'listing_id' => $this->listing_id )
        );
		WPBDP_Utils::cache_delete_group( 'wpbdp_listings' );
        do_action( 'wpbdp_listing_subscription_canceled', $this->listing_id );
    }

    private function save() {
        global $wpdb;

        $data = array(
            'parent_payment_id' => $this->parent_payment_id,
            'payments' => ! empty( $this->data['payments'] ) ? $this->data['payments'] : array()
        );

        $row = array(
            'subscription_id' => $this->subscription_id,
            'subscription_data' => serialize( $data )
        );
		WPBDP_Utils::cache_delete_group( 'wpbdp_listings' );
        return $wpdb->update( $wpdb->prefix . 'wpbdp_listings', $row, array( 'listing_id' => $this->listing_id ) );
    }
}
