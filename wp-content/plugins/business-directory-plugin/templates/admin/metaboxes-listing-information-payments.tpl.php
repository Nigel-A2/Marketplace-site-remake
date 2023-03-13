<?php
/**
 * Listing recent payments metabox template
 *
 * @package BDP/Templates/Admin/Metabox listing payments
 */

?>
<!-- {{ Recent payments. -->
<div id="wpbdp-listing-metabox-payments" class="wpbdp-listing-metabox-tab wpbdp-admin-tab-content" tabindex="2">
    <div id="wpbdp-listing-payment-message" style="display: none;"></div>
    <?php if ( $payments ) : ?>
        <div class="wpbdp-payment-items">
            <?php echo _x( 'Click a transaction to see its details (and approve/reject).', 'listing metabox', 'business-directory-plugin' ); ?>
            <?php foreach ( $payments as $payment ) : ?>
            <?php $payment_link = esc_url( admin_url( 'admin.php?page=wpbdp_admin_payments&wpbdp-view=details&payment-id=' . $payment->id ) ); ?>
            <div class="wpbdp-payment-item wpbdp-payment-status-<?php echo $payment->status; ?> cf">
                <div class="wpbdp-payment-item-row">
                    <div class="wpbdp-payment-date">
                        <a href="<?php echo $payment_link; ?>">#<?php echo $payment->id; ?> - <?php echo date_i18n( get_option( 'date_format' ), strtotime( $payment->created_at ) ); ?></a>
                    </div>
                    <div class="wpbdp-payment-status"><span class="tag paymentstatus <?php echo $payment->status; ?>"><?php echo $payment->status; ?></span></div>
                </div>
                <div class="wpbdp-payment-item-row">
                    <div class="wpbdp-payment-summary"><a href="<?php echo $payment_link; ?>" title="<?php echo esc_attr( $payment->summary ); ?>"><?php echo $payment->summary; ?></a></div>
                    <div class="wpbdp-payment-total"><?php echo wpbdp_currency_format( $payment->amount ); ?></div>
                </div>
            </div>
            <?php endforeach; ?>
            <span class="payment-delete-action" style="font-size: 13px; padding: 2px 0 0;">
                <a href="#" class="wpbdp-admin-delete-link" name="delete-payments" data-id="<?php echo $listing->get_id(); ?>">Delete payment history</a>
            </span>
        </div>
    <?php else : ?>
        <?php echo _x( 'No payments available.', 'listing metabox', 'business-directory-plugin' ); ?>
    <?php endif; ?>
</div>
<!-- }} -->
