<?php
wpbdp_admin_header(
    array(
        'title'   => sprintf(
            __( 'Payment %s', 'business-directory-plugin' ),
            ( $payment && $payment->id ) ? '#' . $payment->id : __( 'Not Found', 'business-directory-plugin' )
        ),
        'id'      => 'payments-details',
        'buttons' => array(
            'wpbdp_admin_payments' => array(
                'label' => __( '← All Payments', 'business-directory-plugin' ),
                'url'   => admin_url( 'admin.php?page=wpbdp_admin_payments' ),
            ),
        ),
        'echo'    => true,
		'sidebar' => false,
    )
);

wpbdp_admin_notices();

if ( ! $payment || ! $payment->id ) {
	?>
	<h2><?php esc_html_e( 'Payment Not Found', 'business-directory-plugin' ); ?></h2>
	<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpbdp_admin_payments' ) ); ?>">
		<?php esc_html_e( '← All Payments', 'business-directory-plugin' ); ?>
	</a>
	<?php
	wpbdp_admin_footer( 'echo' );
	return;
}

// Set some fields to read only for subscriptions.
$hide_text_field = $payment->gateway && $payment->listing->has_subscription() ? 'hidden' : 'text';
?>

<form action="<?php echo esc_url( admin_url( 'admin.php?page=wpbdp_admin_payments&wpbdp-view=payment_update' ) ); ?>" method="post">
    <input type="hidden" name="payment[id]" value="<?php echo esc_attr( $payment->id ); ?>" />
	<?php wp_nonce_field( 'payment-' . $payment->id ); ?>

    <div id="poststuff">
        <div id="post-body" class="metabox-holder columns-2">

            <div id="postbox-container-1" class="postbox-container">

                <div class="meta-box-sortables">
                    <div id="wpbdp-admin-payment-info-box" class="postbox">
                        <h2 class="hndle"><span><?php esc_html_e( 'Overview', 'business-directory-plugin' ); ?></span></h2>
                        <div class="inside">
                            <div class="wpbdp-admin-box with-separators">
                                <div class="wpbdp-admin-box-row">
                                    <label><?php esc_html_e( 'Payment ID', 'business-directory-plugin' ); ?></label>
                                    <?php echo esc_html( $payment->id ); ?>
                                </div>
                                <div class="wpbdp-admin-box-row">
                                    <label><?php esc_html_e( 'Listing', 'business-directory-plugin' ); ?></label>
                                    <a href="<?php echo esc_url( $payment->get_listing()->get_admin_edit_link() ); ?>">
                                        <?php echo esc_html( $payment->get_listing()->get_title() ); ?>
                                    </a>
                                </div>
                                <div class="wpbdp-admin-box-row">
                                    <label><?php esc_html_e( 'Status', 'business-directory-plugin' ); ?></label>

                                    <select name="payment[status]">
									<?php foreach ( WPBDP_Payment::get_stati() as $status_id => $status_label ) : ?>
                                        <option value="<?php echo esc_attr( $status_id ); ?>" <?php selected( $status_id, $payment->status ); ?>><?php echo esc_html( $status_label ); ?></option>
                                    <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="wpbdp-admin-box-row">
                                    <label><?php esc_html_e( 'Date', 'business-directory-plugin' ); ?></label>
									<input type="<?php echo esc_attr( $hide_text_field ); ?>" name="payment[created_at]" value="<?php echo esc_attr( date( 'Y-m-d H:i:s', strtotime( $payment->created_at ) ) ); ?>" />
									<?php
									if ( $hide_text_field === 'hidden' ) {
										echo esc_html(
											date(
												get_option( 'date_format' ) . ' ' . get_option( 'time_format' ),
												strtotime( $payment->created_at )
											)
										);
									}
									?>
                                </div>
                                <div class="wpbdp-admin-box-row">
                                    <label><?php esc_html_e( 'Gateway', 'business-directory-plugin' ); ?></label>
                                    <?php /* translators: Gateway: (Not yet set) */ ?>
                                    <?php echo esc_html( $payment->gateway ? $payment->gateway : __( '(Not yet set)', 'business-directory-plugin' ) ); ?>
                                    <?php if ( $payment->is_test ) : ?>
                                    <span class="wpbdp-payment-test-mode-label"><?php esc_html_e( '- Test Mode', 'business-directory-plugin' ) ?></span>
                                    <?php endif; ?>
                                </div>
								<?php if ( $payment->gateway_tx_id && $payment->get_gateway_link() ) { ?>
									<div class="wpbdp-admin-box-row">
										<label><?php esc_html_e( 'Payment ID', 'business-directory-plugin' ); ?></label>
										<a href="<?php echo esc_url( $payment->get_gateway_link() ); ?>">
											<?php echo esc_html( $payment->gateway_tx_id ); ?>
										</a>
									</div>
								<?php } ?>

                            </div>
                        </div>
                        <div id="major-publishing-actions">
                            <div id="delete-action">
								<a href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?page=wpbdp_admin_payments&wpbdp-view=payment_delete&payment-id=' . $payment->id ), 'payment-' . $payment->id ) ); ?>" class="wpbdp-admin-delete-link wpbdp-admin-confirm"><?php esc_html_e( 'Delete Payment', 'business-directory-plugin' ); ?></a>
                            </div>
                            <input type="submit" class="button button-primary right" value="<?php esc_attr_e( 'Save Payment', 'business-directory-plugin' ); ?>" />
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>

            </div>

            <div id="postbox-container-2" class="postbox-container">
                <div class="meta-box-sortables">
                    <div id="wpbdp-admin-payment-items-box" class="postbox">
                        <h2 class="hndle"><span><?php esc_html_e( 'Details', 'business-directory-plugin' ); ?></span></h2>
                        <div class="inside">
                            <div class="wpbdp-admin-box">
                                <div class="wpbdp-admin-box-row payment-item-header cf">
                                    <span class="payment-item-type"><?php esc_html_e( 'Item Type', 'business-directory-plugin' ); ?></span>
                                    <span class="payment-item-description"><?php esc_html_e( 'Description', 'business-directory-plugin' ); ?></span>
                                    <span class="payment-item-amount"><?php esc_html_e( 'Amount', 'business-directory-plugin' ); ?></span>
                                </div>
                                <?php foreach ( $payment->payment_items as $item ) : ?>
                                <div class="wpbdp-admin-box-row payment-item cf">
                                    <span class="payment-item-type"><?php echo esc_html( $item['type'] ); ?></span>
                                    <span class="payment-item-description"><?php echo esc_html( $item['description'] ); ?></span>
                                    <span class="payment-item-amount"><?php echo esc_html( wpbdp_currency_format( $item['amount'] ) ); ?></span>
                                </div>
                                <?php endforeach; ?>
                                <div class="wpbdp-admin-box-row payment-totals payment-item cf">
                                    <span class="payment-item-type">&nbsp;</span>
                                    <span class="payment-item-description"><?php esc_html_e( 'Total', 'business-directory-plugin' ); ?></span>
                                    <span class="payment-item-amount"><?php echo esc_html( wpbdp_currency_format( $payment->amount ) ); ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                <?php
                $customer = $payment->payer_details;
                ?>
                    <div id="wpbdp-admin-payment-details-box" class="postbox">
                        <h2 class="hndle"><span><?php esc_html_e( 'Customer Details', 'business-directory-plugin' ); ?></span></h2>
                        <div class="inside">
                            <div class="wpbdp-admin-box with-separators">
                                <div class="wpbdp-admin-box-row customer-info-basic cf">
                                    <div class="customer-email">
                                        <label><?php esc_html_e( 'Email', 'business-directory-plugin' ); ?></label>
                                        <input type="text" name="payment[payer_email]" value="<?php echo esc_attr( $customer['email'] ); ?>" />
                                    </div>

                                    <div class="customer-first-name">
                                        <label><?php esc_html_e( 'First Name', 'business-directory-plugin' ); ?></label>
                                        <input type="text" name="payment[payer_first_name]" value="<?php echo esc_attr( $customer['first_name'] ); ?>" />
                                    </div>

                                    <div class="customer-last-name">
                                        <label><?php esc_html_e( 'Last Name', 'business-directory-plugin' ); ?></label>
                                        <input type="text" name="payment[payer_last_name]" value="<?php echo esc_attr( $customer['last_name'] ); ?>" />
                                    </div>
                                </div>
                                <div class="wpbdp-admin-box-row customer-info-address cf">
                                    <div class="customer-address-country">
                                        <label><?php esc_html_e( 'Country', 'business-directory-plugin' ); ?></label>
                                        <input type="text" name="payment[payer_data][country]" value="<?php echo esc_attr( $customer['country'] ); ?>" />
                                    </div>
                                    <div class="customer-address-state">
                                        <label><?php esc_html_e( 'State', 'business-directory-plugin' ); ?></label>
                                        <input type="text" name="payment[payer_data][state]" value="<?php echo esc_attr( $customer['state'] ); ?>" />
                                    </div>
                                    <div class="customer-address-city">
                                        <label><?php esc_html_e( 'City', 'business-directory-plugin' ); ?></label>
                                        <input type="text" name="payment[payer_data][city]" value="<?php echo esc_attr( $customer['city'] ); ?>" />
                                    </div>
                                    <div class="customer-address-zipcode">
                                        <label><?php esc_html_e( 'ZIP Code', 'business-directory-plugin' ); ?></label>
                                        <input type="text" name="payment[payer_data][zip]" value="<?php echo esc_attr( $customer['zip'] ); ?>" />
                                    </div>
                                    <div class="customer-address-line1">
                                        <label><?php esc_html_e( 'Address Line 1', 'business-directory-plugin' ); ?></label>
                                        <input type="text" name="payment[payer_data][address]" value="<?php echo esc_attr( $customer['address'] ); ?>" />
                                    </div>
                                    <div class="customer-address-line2">
                                        <label><?php esc_html_e( 'Address Line 2', 'business-directory-plugin' ); ?></label>
                                        <input type="text" name="payment[payer_data][address_2]" value="<?php echo esc_attr( $customer['address_2'] ); ?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="wpbdp-admin-payment-notes-box" class="postbox">
                        <h2 class="hndle"><span><?php esc_html_e( 'Notes & Log', 'business-directory-plugin' ); ?></span></h2>
                        <div class="inside">
                            <div class="wpbdp-admin-box">
                                <div id="wpbdp-payment-notes">
                                    <div class="no-notes" style="<?php echo ( $payment->payment_notes ) ? 'display: none;' : ''; ?>">
                                        <?php esc_html_e( 'No notes.', 'business-directory-plugin' ); ?>
                                    </div>
                                    <?php
                                    foreach ( $payment->payment_notes as $note ) :
                                        wpbdp_render_page(
                                            WPBDP_PATH . 'templates/admin/payments-note.tpl.php',
                                            array(
                                                'note'       => $note,
                                                'payment_id' => $payment->id,
                                            ),
                                            true
                                        );
                                    endforeach;
                                    ?>
                                </div>

                                <div class="wpbdp-payment-notes-and-log-form">
                                    <textarea name="payment_note" class="large-text"></textarea>
                                    <p>
                                        <button id="wpbdp-payment-notes-add" class="button button-secondary right" data-payment-id="<?php echo esc_attr( $payment->id ); ?>">
                                            <?php esc_html_e( 'Add Note', 'business-directory-plugin' ); ?>
                                        </button>
                                    </p>
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>

</form>

<?php wpbdp_admin_footer( 'echo' ); ?>
