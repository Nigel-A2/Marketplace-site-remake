<?php
/**
 * Template Renew listing resume.
 *
 * @package Templates/Renew Resume
 */

?>

<h2><?php echo esc_html( $listing->get_title() ); ?> - <?php echo esc_html_x( 'Renew Plan Resume', 'renewal', 'business-directory-plugin' ); ?></h2>

<p>
    <?php
	printf(
		esc_html_x( 'You are about to renew the listing %s.', 'renewal', 'business-directory-plugin' ),
		'<a href="' . esc_url( $listing->get_permalink() ) . '">' . esc_html( $listing->get_title() ) . '</a>'
	);
    ?>
    <br />
    <?php echo esc_html_x( 'In order to complete the renewal, please confirm plan selection.', 'renewal', 'business-directory-plugin' ); ?>
</p>

<div class="wpbdp-payment-invoice">
    <?php
    echo $invoice_resume;
    ?>
</div>

<div id="wpbdp-claim-listings-confirm-fees">
    <div class="inner">
        <form action="" method="post">
            <?php wp_nonce_field( 'cancel renewal fee ' . $payment->id ); ?>
            <input type="submit" name="proceed-to-checkout" value="<?php echo esc_html_x( 'Continue to checkout', 'templates', 'business-directory-plugin' ); ?>" />
            <input type="submit" name="return-to-fee-select" value="<?php echo esc_html_x( 'Return to plan selection', 'templates', 'business-directory-plugin' ); ?>" />
        </form>
    </div>
</div>
