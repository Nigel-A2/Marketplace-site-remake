<?php
/**
 * Submit Listing Done Template
 *
 * @package WPBDP/Templates
 */

?>
<h3><?php echo esc_html_x( 'Submission Received', 'templates', 'business-directory-plugin' ); ?></h3>

<?php if ( ! $editing ) : ?>
    <p><?php echo esc_html_x( 'Your listing has been submitted.', 'templates', 'business-directory-plugin' ); ?></p>
    <?php if ( $payment && $payment->amount > 0.0 ) : ?>
        <p>
        <?php echo esc_html( wpbdp_get_option( 'payment-message' ) ); ?>
        </p>
        <div id="wpbdp-checkout-confirmation-receipt">
            <?php
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo wpbdp()->payments->render_receipt( $payment );
            ?>
        </div>
    <?php endif; ?>
<?php else : ?>
    <p><?php echo esc_html_x( 'Your listing changes were saved.', 'templates', 'business-directory-plugin' ); ?></p>
<?php endif; ?>

    <p>
        <?php if ( 'publish' === get_post_status( $listing->get_id() ) ) : ?>
            <a href="<?php echo esc_url( get_permalink( $listing->get_id() ) ); ?>"><?php echo esc_html_x( 'Go to your listing', 'templates', 'business-directory-plugin' ); ?></a> |
        <?php elseif ( ! current_user_can( 'administrator' ) ) : ?>
            <?php echo esc_html_x( 'Your listing requires admin approval. You\'ll be notified once your listing is approved.', 'templates', 'business-directory-plugin' ); ?>
    </p>
    <p>
        <?php endif; ?>
        <a href="<?php echo esc_url( wpbdp_get_page_link( 'main' ) ); ?>"><?php esc_html_e( 'Return to directory', 'business-directory-plugin' ); ?></a>
    </p>
