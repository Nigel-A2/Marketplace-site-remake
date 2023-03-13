<?php
/**
 * Fees/Payment API
 *
 * @package BDP/Includes/Views/Checkout
 */

require_once WPBDP_PATH . 'includes/models/class-payment.php';
require_once WPBDP_INC . 'abstracts/class-payment-gateway.php';
require_once WPBDP_PATH . 'includes/class-fees-api.php';


if ( ! class_exists( 'WPBDP_PaymentsAPI' ) ) {
    /**
     * Class WPBDP_PaymentsAPI
     */
class WPBDP_PaymentsAPI {

    public function __construct() {
        add_filter( 'wpbdp_listing_post_status', array( &$this, 'check_listing_payment_status' ), 10, 2 );

        add_action( 'wpbdp_checkout_form_top', array( $this, '_return_fee_list_button' ), -2, 1 );
        add_action( 'wpbdp_checkout_before_action', array( $this, 'maybe_fee_select_redirect' ) );
    }

    public function cancel_subscription( $listing, $subscription ) {
        $payment = $subscription->get_parent_payment();

        if ( ! $payment ) {
            $message = __( "We couldn't find a payment associated with the given subscription.", 'business-directory-plugin' );
            throw new Exception( $message );
        }

        $gateway = $GLOBALS['wpbdp']->payment_gateways->get( $payment->gateway );

        if ( ! $gateway ) {
            $message = __( 'The payment gateway "<payment-gateway>" is not available.', 'business-directory-plugin' );
            $message = str_replace( '<payment-gateway>', $gateway, $message );
            throw new Exception( $message );
        }

        $gateway->cancel_subscription( $listing, $subscription );
    }

    /**
     * @since 5.0
     */
    public function render_receipt( $payment ) {
        $current_user = wp_get_current_user();
        ob_start();
        do_action( 'wpbdp_before_render_receipt', $payment );
?>

<div id="wpbdp-payment-receipt" class="wpbdp-payment-receipt">

    <div class="wpbdp-payment-receipt-header">
        <h4><?php printf( _x( 'Payment #%s', 'payments', 'business-directory-plugin' ), $payment->id ); ?></h4>
        <span class="wpbdp-payment-receipt-date"><?php echo date( 'Y-m-d H:i', strtotime( $payment->created_at ) ); ?></span>

        <span class="wpbdp-tag wpbdp-payment-status wpbdp-payment-status-<?php echo $payment->status; ?>"><?php echo WPBDP_Payment::get_status_label( $payment->status ); ?></span>
    </div>
    <div class="wpbdp-payment-receipt-details">
        <dl>
            <?php if ( $payment->gateway && $payment->gateway_tx_id ) { ?>
            <dt><?php esc_html_e( 'Gateway Transaction ID:', 'business-directory-plugin' ); ?></dt>
            <dd><?php echo esc_html( $payment->gateway . ' ' . $payment->gateway_tx_id ); ?></dd>
			<?php } ?>
			<?php
			$bill_to  = '';
			$bill_to .= ( $payment->payer_first_name || $payment->payer_last_name ) ? $payment->payer_first_name . ' ' . $payment->payer_last_name : $current_user->display_name;
			$bill_to .= $payment->payer_data ? '<br />' . implode( '<br />', $payment->get_payer_address() ) : '';
			$bill_to .= '<br />';
			$bill_to .= $payment->payer_email ? $payment->payer_email : $current_user->user_email;
			if ( ! empty( str_replace( '<br />', '', $bill_to ) ) ) {
				?>
				<dt><?php esc_html_e( 'Bill To:', 'business-directory-plugin' ); ?></dt>
				<dd><?php echo $bill_to; ?></dd>
				<?php
			}
			?>
        </dl>
    </div>

    <?php echo $this->render_invoice( $payment ); ?>

</div>
<a href="#" class="wpbdp-payment-receipt-print button wpbdp-button" ><?php esc_html_e( 'Print Receipt', 'business-directory-plugin' ); ?></a>

<?php
        do_action( 'wpbdp_after_render_receipt', $payment );
        return ob_get_clean();
    }

    /**
     * Renders an invoice table for a given payment.
	 *
     * @param WPBDP_Payment $payment
     * @return string HTML output.
     * @since 3.4
     */
    public function render_invoice( &$payment ) {
        $html  = '';
        $html .= '<div class="wpbdp-checkout-invoice">';
        $html .= wpbdp_render( 'payment/payment_items', array( 'payment' => $payment ), false );
        $html .= '</div>';

        return $html;
    }

	/**
	 * @since 3.5.8
	 */
	public function abandonment_status( $status, $listing_id ) {
		_deprecated_function( __METHOD__, '5.19' );
		return $status;
	}

	/**
	 * @since 3.5.8
	 */
	public function abandonment_admin_views( $views, $post_statuses ) {
		_deprecated_function( __METHOD__, '5.19' );
		return $views;
	}

	/**
	 * @since 3.5.8
	 */
	public function abandonment_admin_filter( $pieces, $filter = '' ) {
		_deprecated_function( __METHOD__, '5.19' );
		return $pieces;
	}

    /**
     * @since 3.5.8
	 * @deprecated 6.1
     */
    public function notify_abandoned_payments() {
		_deprecated_function( __METHOD__, '6.1' );
    }

	function _return_fee_list_button( $payment ) {
        if ( 'renewal' !== $payment->payment_type ) {
            return;
        }

		echo '<input type="submit" name="return-to-fee-select" value="' . esc_attr__( 'Return to plan selection', 'business-directory-plugin' ) . '" style="margin-bottom: 1.5em;" />';
    }

    function maybe_fee_select_redirect( $checkout ) {
        if ( 'renewal' !== $checkout->payment->payment_type ) {
            return;
        }

        if ( empty( $_POST['return-to-fee-select'] ) ) {
            return;
        }

        $url = esc_url_raw(
            add_query_arg(
                array(
                    'return-to-fee-select' => 1,
                ),
                wpbdp_url( 'renew_listing', $checkout->payment->listing_id )
            )
        );

        wp_redirect( $url );
    }

    public function check_listing_payment_status( $status, $listing ) {
        if ( 'publish' !== $status || is_admin() ) {
            return $status;
        }

        $payment = $listing->get_latest_payment();

        if ( ! $payment || 'initial' !== $payment->payment_type || 'completed' === $payment->status ) {
            return $status;
        }

        return 'pending';
    }

}

}
