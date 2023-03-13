<h3><?php _ex( 'Manage Recurring Payments', 'manage recurring', 'business-directory-plugin' ); ?></h3>

<table id="wpbdp-manage-recurring">
    <thead>
        <th class="listing-title"><?php _ex( 'Listing', 'manage recurring', 'business-directory-plugin' ); ?></th>
        <th class="subscription-details"><?php _ex( 'Subscription / Plan', 'manage subscriptions', 'business-directory-plugin' ); ?></th>
    </thead>
    <tbody>
    <?php foreach ( $listings as $listing ) : ?>
    <tr>
        <td class="listing-title">
			<b><?php
			if ( $listing->is_published() ) :
				printf(
					'<a href="%s">%s</a>',
					esc_url( $listing->get_permalink() ),
					$listing->get_title()
				);
			else :
				echo $listing->get_title();
			endif;
			?></b>
        </td>
        <td class="subscription-details">
            <?php
                $fee = $listing->get_fee_plan();

                $subscription_amount = wpbdp_currency_format( $fee->fee_price );
                $subscription_days = '<i>' . esc_html( $fee->fee_days ) . '</i>';
                $subscription_expiration_date = '<i>' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $fee->expiration_date ) ) ) . '</i>';

                /* translators: %1$s: amount, %2$s: number of days, %3$s: expiration date */
                $subscription_details = __( '%1$s each %2$s days. Next renewal is on %3$s.', 'business-directory-plugin' );
                $subscription_details = sprintf( $subscription_details, $subscription_amount, $subscription_days, $subscription_expiration_date );

                $cancel_url = add_query_arg(
					array(
						'action'  => 'cancel-subscription',
						'listing' => $listing->get_id(),
						'nonce'   => wp_create_nonce( 'cancel-subscription-' . $listing->get_id() ),
					)
				);
            ?>
            <b><?php echo $fee->fee_label; ?>:</b><br />
            <?php echo $subscription_details; ?><br />
            <a href="<?php echo esc_url( $cancel_url ); ?>" class="cancel-subscription"><?php _ex( 'Cancel recurring payment', 'manage recurring', 'business-directory-plugin' ); ?></a>
        </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
</table>
