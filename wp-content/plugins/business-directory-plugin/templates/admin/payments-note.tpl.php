<?php
if ( wpbdp_starts_with( $note->actor, 'user' ) ) {
    $author = get_user_meta( (int) str_replace( 'user:', '', $note->actor ), 'nickname', true );
} else {
    $author = $note->actor;
}

$date = date_i18n( get_option( 'date_format' ) . ' ' . get_option( 'time_format' ), strtotime( $note->created_at ) );
?>
<div class="wpbdp-payment-note" data-id="<?php echo $note->id; ?>">

	<?php if ( $note->log_type === 'payment.note' ) : ?>
    <a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin-ajax.php?action=wpbdp_admin_ajax&handler=payments__delete_note&note=' . $note->id . '&payment_id=' . $payment_id ), 'wpbdp_ajax' ) ); ?>" class="wpbdp-admin-delete-link wpbdp-admin-confirm" data-confirm="<?php esc_attr_e( 'Are you sure you want to do this?', 'business-directory-plugin' ); ?>">
		<?php esc_html_e( 'Delete', 'business-directory-plugin' ); ?>
	</a>
    <?php endif; ?>
    <div class="wpbdp-payment-note-meta">
        <span class="wpbdp-payment-note-meta-user"><?php echo $author; ?></span>
        <span class="sep"> - </span>
        <span class="wpbdp-payment-note-meta-date"><?php echo $date; ?></span>
    </div>

    <div class="wpbdp-payment-note-text">
        <?php echo esc_html( $note->message ); ?>
    </div>

</div>

