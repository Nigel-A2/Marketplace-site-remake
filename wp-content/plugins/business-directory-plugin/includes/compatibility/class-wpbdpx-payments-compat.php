<?php
/**
 * Compatibility for pre-5.0 payments.
 *
 * @package BDP/Includes/Compatibility
 */

/**
 * Class WPBDP__WPBDPX_Payments_Compat
 */
class WPBDP__WPBDPX_Payments_Compat {

    private $gateways;

    public function __construct() {
        $this->gateways = wpbdp()->payment_gateways;
    }

    public function dispatch() {
        $action  = trim( wpbdp_get_var( array( 'param' => 'action' ) ) );
		$payment = wpbdp_get_var( array( 'param' => 'payment_id' ) );
		$payment = $payment ? wpbdp_get_payment( intval( $payment ) ) : null;
        $gid     = trim( wpbdp_get_var( array( 'param' => 'gid' ) ) );

        if ( ! in_array( $action, array( 'postback', 'process', 'notify', 'return', 'cancel', 'ins' ) ) || ( ! $payment && ! $gid ) ) {
            return;
        }

        unset( $_GET['action'] );

        if ( $gid ) {
            unset( $_GET['gid'] );
        }

        $gateway_id = $payment ? $payment->gateway : $gid;
        $gateway    = $this->gateways->get( $gateway_id );

        if ( ! $gateway ) {
            return;
        }

        switch ( $gateway ) {
            case '2checkout':
				$order_id = wpbdp_get_var( array( 'param' => 'merchant_order_id' ), 'request' );
				$_POST['wpbdp_payment_id']    = $order_id;
				$_GET['wpbdp_payment_id']     = $order_id;
				$_REQUEST['wpbdp_payment_id'] = $order_id;
                break;
            case 'paypal':
                break;
            case 'stripe':
                break;
        }

        $gateway->process_postback();
        exit;
    }

}

