<?php
WPBDP_Admin_Pages::show_tabs(
	array(
		'id'      => 'admin-fees',
		'sub'     => __( 'Plans', 'business-directory-plugin' ),
		'buttons' => array(
			__( 'Add New Plan', 'business-directory-plugin' ) => esc_url( admin_url( 'admin.php?page=wpbdp-admin-fees&wpbdp-view=add-fee' ) )
		),
	)
);
?>

    <?php if ( 'active' == $table->get_current_view() || 'all' == $table->get_current_view() ) : ?>
        <div class="fees-order">
            <form>
            <input type="hidden" name="action" value="wpbdp-admin-fees-set-order" />
            <?php wp_nonce_field( 'change fees order' ); ?>
            <select name="fee_order[method]">
            <?php foreach ( $order_options as $k => $l ) : ?>
            <option value="<?php echo esc_attr( $k ); ?>" <?php echo $k == $current_order['method'] ? 'selected="selected"' : ''; ?> ><?php echo esc_html( $l ); ?></option>
            <?php endforeach; ?>
            </select>

            <select name="fee_order[order]" style="<?php echo ( 'custom' == $current_order['method'] ) ? 'display: none;' : ''; ?>">
            <?php
            foreach ( array(
				'asc'  => __( '↑ Ascending', 'business-directory-plugin' ),
				'desc' => __( '↓ Descending', 'business-directory-plugin' ),
			) as $o => $l ) :
				?>
                <option value="<?php echo esc_attr( $o ); ?>" <?php echo $o == $current_order['order'] ? 'selected="selected"' : ''; ?> ><?php echo esc_html( $l ); ?></option>
            <?php endforeach; ?>
            </select>

			<a class="button-secondary fee-order-submit">
				<?php esc_html_e( 'Save front-end order', 'business-directory-plugin' ); ?>
			</a>

            <?php if ( 'custom' == $current_order['method'] ) : ?>
            <span><?php esc_html_e( 'Drag and drop to re-order plans.', 'business-directory-plugin' ); ?></span>
            <?php endif; ?>

            </form>
        </div>

        <br class="clear" />
	<?php endif; ?>

	<?php $table->views(); ?>
	<?php $table->display(); ?>

	<div class="purchase-gateways cf">
		<h3>
			<?php
			if ( ! wpbdp_payments_possible() ) {
				esc_html_e( 'Set up a payment gateway to charge a fee for listings', 'business-directory-plugin' );
			} else {
				esc_html_e( 'Add a payment gateway to increase conversion rates', 'business-directory-plugin' );
			}
			?>
		</h3>
		<div class="wpbdp-fee-gateway-list wpbdp-grid">
        <?php
		foreach ( $gateways as $mod_info ) :
			$cols = floor( 12 / count( $gateways ) );
			?>
		<div class="gateway wpbdp<?php echo absint( $cols ); ?>">
			<a class="gateway-title" href="<?php echo esc_url( $mod_info['link'] ); ?>" target="_blank" rel="noopener">
				<img src="<?php echo esc_url( WPBDP_ASSETS_URL ); ?>images/modules/<?php echo esc_attr( $mod_info[1] ); ?>.svg" class="gateway-logo" />
			</a>
			<div class="gateway-description">
				<?php
				echo sprintf(
					// translators: %s: payment gateway name */
					esc_html__( 'Add %s as a payment option.', 'business-directory-plugin' ),
					esc_html( $mod_info[2] )
				);
				?>
			</div>
			<p class="gateway-footer">
				<a href="<?php echo esc_url( $mod_info['link'] ); ?>" target="_blank" rel="noopener" class="button-primary">
					<?php echo esc_html( $mod_info['cta'] ); ?>
				</a>
			</p>
		</div>
        <?php endforeach; ?>
		</div>
	</div>

<?php WPBDP_Admin_Pages::show_tabs_footer( array( 'sub' => true ) ); ?>
