<!-- {{  Plan info. -->
<?php
/**
 * Listing information plan metabox
 *
 * @package Admin Templates/listing information plan
 */

echo wp_nonce_field( 'update listing plan', 'wpbdp-admin-listing-plan-nonce', false, false );
?>
<div id="wpbdp-listing-metabox-plan-info" class="wpbdp-listing-metabox-tab wpbdp-admin-tab-content" tabindex="1">
	<div class="misc-pub-section">
		<?php esc_html_e( 'Listing status', 'business-directory-plugin' ); ?>:
		<b>
			<?php
			$status = apply_filters( 'wpbdp_admin_listing_display_status', array( $listing->get_status(), $listing->get_status_label() ), $listing );
			if ( 'incomplete' === $status[0] ) :
				esc_html_e( 'N/A', 'business-directory-plugin' );
			else :
				echo esc_html( $status[1] );
			endif;
			?>
		</b>
	</div>
	<div class="misc-pub-section">
		<?php esc_html_e( 'Last renew date', 'business-directory-plugin' ); ?>:
		<b>
			<?php
			$renewal_date = $listing->get_renewal_date();
			if ( $renewal_date ) :
				echo esc_html( $renewal_date );
			else :
				esc_html_e( 'N/A', 'business-directory-plugin' );
			endif;
			?>
		</b>
	</div>

    <h4><?php _ex( 'Plan Details', 'listing metabox', 'business-directory-plugin' ); ?></h4>
    <dl>
		<dt><?php esc_html_e( 'Plan', 'business-directory-plugin' ); ?></dt>
        <dd>
            <span class="display-value <?php echo $current_plan ? '' : 'wpbdp-hidden'; ?>" id="wpbdp-listing-plan-prop-label">
				<?php if ( $current_plan ) : ?>
                    <a href="<?php echo esc_url( admin_url( 'admin.php?page=wpbdp-admin-fees&wpbdp-view=edit-fee&id=' . $current_plan->fee_id ) ); ?>"><?php echo esc_html( $current_plan->fee_label ); ?></a>
				<?php else : ?>
                    -
                <?php endif; ?>
            </span>
            <a href="#" class="edit-value-toggle <?php echo $current_plan ? '' : 'wpbdp-hidden'; ?>">
                <?php _ex( 'Change plan', 'listing metabox', 'business-directory-plugin' ); ?>
            </a>
            <div class="value-editor" <?php echo $current_plan ? '' : 'style="display:block"'; ?>>
				<input type="hidden" name="listing_plan[fee_id]" value="<?php echo esc_attr( $current_plan ? $current_plan->fee_id : '' ); ?>" />
                <select name="" id="wpbdp-listing-plan-select">
                <?php foreach ( $plans as $p ) : ?>
                    <?php
                    $plan_info = array(
                        'id'              => $p->id,
                        'label'           => $p->label,
                        'amount'          => $p->amount ? wpbdp_currency_format( $p->amount ) : '',
                        'days'            => $p->days,
                        'images'          => $p->images,
                        'sticky'          => $p->sticky,
                        'recurring'       => $p->recurring,
                        'expiration_date' => $p->calculate_expiration_time( $listing->get_expiration_time() ),
                    );
                    ?>
					<option value="<?php echo esc_attr( $p->id ); ?>" <?php selected( $p->id, $current_plan ? $current_plan->fee_id : 0 ); ?> data-plan-info="<?php echo esc_attr( json_encode( $plan_info ) ); ?>">
						<?php echo esc_html( $p->label ); ?>
					</option>
                <?php endforeach; ?>
                </select>

				<p>
					<a href="#" class="update-value button"><?php esc_html_e( 'OK', 'business-directory-plugin' ); ?></a>
					<a href="#" class="cancel-edit button-cancel"><?php esc_html_e( 'Cancel', 'business-directory-plugin' ); ?></a>
				</p>
        </div>
        </dd>
        <dt><?php esc_html_e( 'Amount', 'business-directory-plugin' ); ?></dt>
        <dd>
            <span class="display-value" id="wpbdp-listing-plan-prop-amount">
                <?php echo $current_plan ? wpbdp_currency_format( $current_plan->fee_price ) : '-'; ?>
            </span>
        </dd>
		<dt><?php esc_html_e( 'Expires on', 'business-directory-plugin' ); ?></dt>
        <dd>
            <span class="display-value" id="wpbdp-listing-plan-prop-expiration">
                <?php echo ( $current_plan && $current_plan->expiration_date ) ? wpbdp_date( strtotime( $current_plan->expiration_date ) ) : ( $listing->get_fee_plan() ? 'Never' : '-' ); ?>
            </span>
			<?php if ( ! $listing->has_subscription() ) : ?>
				<a href="#" class="edit-value-toggle"><?php esc_html_e( 'Edit', 'business-directory-plugin' ); ?></a>
			<?php endif; ?>
            <div class="value-editor">
				<input type="text" name="listing_plan[expiration_date]" value="<?php echo esc_attr( ( $current_plan && $current_plan->expiration_date ) ? $current_plan->expiration_date : '' ); ?>" placeholder="<?php esc_attr_e( 'Never', 'business-directory-plugin' ); ?>" style="max-width:150px" />
				<?php if ( ! $listing->has_subscription() ) : ?>
					<p>
						<a href="#" class="update-value button"><?php esc_html_e( 'OK', 'business-directory-plugin' ); ?></a>
						<a href="#" class="cancel-edit button-cancel"><?php esc_html_e( 'Cancel', 'business-directory-plugin' ); ?></a>
					</p>
				<?php endif; ?>
            </div>
        </dd>
        <dt><?php _ex( '# of images', 'listing metabox', 'business-directory-plugin' ); ?></dt>
        <dd>
            <span class="display-value" id="wpbdp-listing-plan-prop-images">
                <?php echo $current_plan ? $current_plan->fee_images : '-'; ?>
            </span>
			<a href="#" class="edit-value-toggle"><?php esc_html_e( 'Edit', 'business-directory-plugin' ); ?></a>
            <div class="value-editor">
                <input type="text" name="listing_plan[fee_images]" value="<?php echo $current_plan ? $current_plan->fee_images : 0; ?>" size="2" />

				<a href="#" class="update-value button"><?php esc_html_e( 'OK', 'business-directory-plugin' ); ?></a>
				<a href="#" class="cancel-edit button-cancel"><?php esc_html_e( 'Cancel', 'business-directory-plugin' ); ?></a>
            </div>
        </dd>
		<dt><?php esc_html_e( 'Featured', 'business-directory-plugin' ); ?></dt>
        <dd>
            <span class="display-value" id="wpbdp-listing-plan-prop-is_sticky">
                <?php echo esc_html( $current_plan && $current_plan->is_sticky ? __( 'Yes', 'business-directory-plugin' ) : __( 'No', 'business-directory-plugin' ) ); ?>
            </span>
<!-- Removed the ability to set a listing as "Featured" in "info" metabox for 5.1.6 according to instructions on issue #3413 -->
        </dd>
		<dt><?php esc_html_e( 'Recurring', 'business-directory-plugin' ); ?></dt>
        <dd>
            <span class="display-value" id="wpbdp-listing-plan-prop-is_recurring">
                <?php echo esc_html( $current_plan && $current_plan->is_recurring ? __( 'Yes', 'business-directory-plugin' ) : __( 'No', 'business-directory-plugin' ) ); ?>
            </span>
        </dd>
    </dl>

    <ul class="wpbdp-listing-metabox-renewal-actions">
        <li>
            <a href="#" class="button button-small" onclick="window.prompt('<?php _ex( 'Renewal url (copy & paste)', 'admin infometabox', 'business-directory-plugin' ); ?>', '<?php echo esc_url_raw( $listing->get_renewal_url() ); ?>'); return false;"><?php _ex( 'Get renewal URL', 'admin infometabox', 'business-directory-plugin' ); ?></a>
            <a class="button button-small" href="
            <?php
            echo esc_url(
                add_query_arg(
                    array(
                        'wpbdmaction' => 'send-renewal-email',
                        'listing_id'  => $listing->get_id(),
                    ),
                    get_edit_post_link( $listing->get_id() )
                )
            );
            ?>
            ">
                <?php _ex( 'Send renewal e-mail', 'admin infometabox', 'business-directory-plugin' ); ?>
            </a>
        </li>
        <?php if ( 'pending_renewal' == $listing->get_status() || ( $current_plan && $current_plan->expired ) ) : ?>
        <li>
			<a href="<?php echo esc_url( add_query_arg( 'wpbdmaction', 'renewlisting', get_edit_post_link( $listing->get_id() ) ) ); ?>" class="button-primary button button-small">
				<?php esc_html_e( 'Renew listing', 'business-directory-plugin' ); ?>
			</a>
        </li>
        <?php endif; ?>
    </ul>
</div>
<!-- }} -->
