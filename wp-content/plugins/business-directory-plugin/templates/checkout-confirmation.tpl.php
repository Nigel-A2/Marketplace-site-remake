<?php
switch ( $payment->status ) :
case 'completed':
    echo wpbdp_get_option( 'payment-message' );
    break;
case 'on-hold':
    echo wpbdp_render_msg( _x( 'Your payment is on hold. Please contact the admin if you need further details.', 'checkout', 'business-directory-plugin' ) );
    break;
case 'failed':
    echo wpbdp_render_msg( _x( 'Your payment was rejected. Please contact the admin for further details.', 'checkout', 'business-directory-plugin' ), 'error' );
    break;
case 'canceled':
    echo wpbdp_render_msg( sprintf( _x( 'The payment (#%s) was canceled at your request.', 'checkout', 'business-directory-plugin' ), $payment->id ) );
    break;
case 'pending':
    echo '<p>';
    _ex( 'Your payment is awaiting verification by the gateway.', 'checkout', 'business-directory-plugin' );
    echo '</p>';
    echo wpbdp_render_msg( _x( 'Verification usually takes some minutes. This page will automatically refresh if there\'s an update.', 'checkout', 'business-directory-plugin' ) );
    break;
default:
    wp_die();
endswitch
?>

<?php if ( 'canceled' != $payment->status ) : ?>
<div id="wpbdp-checkout-confirmation-receipt">
    <?php echo wpbdp()->payments->render_receipt( $payment ); ?>
</div>
<?php endif; ?>

<?php if ( 'pending' == $payment->status ) : ?>
<script>
setTimeout(function() {
    location.reload();
}, 5000 );
</script>
<?php endif; ?>
