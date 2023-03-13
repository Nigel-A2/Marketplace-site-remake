<div id="wpbdp-renewal-page" class="wpbdp-renewal-page businessdirectory-renewal businessdirectory wpbdp-page">
	<h2><?php esc_html_e( 'Recurring Plan Management', 'business-directory-plugin' ); ?></h2>

    <p><?php _ex( 'Because you are on a recurring plan you don\'t have to renew your listing right now as this will be handled automatically when renewal comes.', 'renew', 'business-directory-plugin' ); ?></p>

	<h4><?php _ex( 'Current Plan Details', 'renewal', 'business-directory-plugin' ); ?></h4>

    <dl class="recurring-fee-details">
        <dt><?php _ex( 'Name:', 'renewal', 'business-directory-plugin' ); ?></dt>
        <dd><?php echo $plan->fee_label; ?></dd>
        <dt><?php _ex( 'Number of images:', 'renewal', 'business-directory-plugin' ); ?></dt>
        <dd><?php echo $plan->fee_images; ?></dd>
        <dt><?php _ex( 'Expiration date:', 'renewal', 'business-directory-plugin' ); ?></dt>
        <dd><?php echo date_i18n( get_option( 'date_format' ), strtotime( $plan->expiration_date ) ); ?></dd>
    </dl>

    <?php
	if ( $show_cancel_subscription_button ) :

		$url = add_query_arg(
			array(
				'wpbdp_view' => 'manage_recurring',
				'action' => 'cancel-subscription',
				'listing' => $listing->get_id(),
				'nonce' => wp_create_nonce( 'cancel-subscription-' . $listing->get_id() ),
			),
			wpbdp_url( 'main' )
		);

        $message = _x( 'However, if you want to cancel your subscription you can do that on <manage-recurring-link>the manage recurring payments page</manage-recurring-link>. When the renewal time comes you\'ll be able to change your settings again.', 'renew', 'business-directory-plugin' );

        $message = str_replace( '<manage-recurring-link>', '<a href="' . esc_url( $url ) . '">', $message );
        $message = str_replace( '</manage-recurring-link>', '</a>', $message );
    ?>
    <p><?php echo $message; ?></p>

    <p><a class="button button-primary" href="<?php echo esc_url( $url ); ?>"><?php _ex( 'Go to Manage Recurring Payments page', 'renew', 'business-directory-plugin' ); ?></a>
    <?php endif; ?>
</div>
