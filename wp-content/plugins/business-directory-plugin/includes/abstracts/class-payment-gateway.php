<?php
/**
 * Abstract Gateway
 *
 * Hanldes generic payment gateway functionality which is extended by idividual payment gateways.
 */
abstract class WPBDP__Payment_Gateway {

    public function __construct() {
    }

    abstract public function get_id();
    abstract public function get_title();

    public function get_logo() {
        return $this->get_title();
    }

    public function enqueue_scripts() {
    }

    public function is_enabled( $no_errors = true ) {
        $setting_on = wpbdp_get_option( $this->get_id() );
        if ( ! $no_errors )
            return $setting_on;

        return $setting_on && ! $this->validate_settings();
    }

    public function in_test_mode() {
        return wpbdp_get_option( 'payments-test-mode' );
    }

    public function get_option( $key ) {
        return wpbdp_get_option( $this->get_id() . '-' . $key );
    }

    abstract public function get_integration_method();

    public function supports( $feature ) {
        return false;
    }

    public function supports_currency( $currency ) {
        return false;
    }

    public function get_settings() {
        return array();
    }

    public function get_settings_text() {
        return '';
    }

    public function validate_settings() {
        return array();
    }

    public function process_payment( $payment ) {
        return false;
    }

    public function process_postback() {
    }

    public function refund( $payment, $data = array() ) {
    }

    public function render_form( $payment, $errors = array() ) {
        $vars = array();

        $vars['data'] = $_POST;

        foreach ( $payment->get_payer_details() as $k => $v ) {
            if ( empty( $_POST[ 'payer_' . $k ] ) && ! empty( $v ) ) {
                $vars['data'][ 'payer_' . $k ] = $v;
            }
        }

        $vars['gateway'] = $this;
        $vars['errors'] = $errors;
        $vars['payment'] = $payment;

		if ( $this->skip_payment_form( $payment ) ) {
            $vars['show_cc_section'] = false;
            $vars['show_details_section'] = false;
        }

        return wpbdp_x_render( 'checkout-billing-form', $vars );
    }

    public function validate_form( $payment ) {
        $errors = array();

        $required = array( 'payer_email', 'payer_first_name' );
		if ( ! $this->skip_payment_form( $payment ) ) {
            $required = array_merge( $required, array( 'card_number', 'cvc', 'card_name', 'exp_month', 'exp_year', 'payer_address', 'payer_city', 'payer_zip', 'payer_country' ) );
        }

        foreach ( $required as $req_field ) {
            $field_value = wpbdp_get_var( array( 'param' => $req_field ), 'post' );

            if ( ! $field_value ) {
			    $errors[ $req_field ] = sprintf(
				    /* translators: %s: field name */
                    __( 'This field is required (%s).', 'business-directory-plugin' ),
                    $req_field
                );
            }
        }

        return $errors;
    }

	/**
	 * @since 5.13.2
	 */
	private function skip_payment_form( $payment ) {
		return 'form' === $this->get_integration_method() || $payment->amount === '0.00';
	}

    public function save_billing_data( $payment ) {
        $form = $_POST;

        foreach ( array( 'payer_email', 'payer_first_name', 'payer_last_name' ) as $k ) {
            if ( ! empty( $form[ $k ] ) )
                $payment->{$k} = $form[ $k ];
        }

        if ( ! empty( $form['payer_address'] ) )
            $payment->payer_data['address'] = $form['payer_address'];

        if ( ! empty( $form['payer_address_2'] ) )
            $payment->payer_data['address_2'] = $form['payer_address_2'];

        if ( ! empty( $form['payer_city'] ) )
            $payment->payer_data['city'] = $form['payer_city'];

        if ( ! empty( $form['payer_state'] ) )
            $payment->payer_data['state'] = $form['payer_state'];

        if ( ! empty( $form['payer_zip'] ) )
            $payment->payer_data['zip'] = $form['payer_zip'];

        if ( ! empty( $form['payer_country'] ) )
            $payment->payer_data['country'] = $form['payer_country'];

        $payment->save();
    }

    public function get_listener_url() {
        return add_query_arg( 'wpbdp-listener', $this->get_id(), home_url( 'index.php' ) );
    }

	/**
	 * Override this in the individual gateway class.
	 *
	 * @since 5.11
	 */
	public function get_payment_link( $payment ) {
		return '';
	}

    public function cancel_subscription( $listing, $subscription ) {
        $message = __( "There was an unexpected error trying to cancel your subscription. Please contact the website's administrator mentioning this problem. The administrator should be able to cancel your subscription contacting the payment processor directly.", 'business-directory-plugin' );
        throw new Exception( $message );
    }
}
