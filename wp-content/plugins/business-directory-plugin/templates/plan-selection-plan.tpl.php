<?php
/**
 * Plan Selection Template displayed when submitting a listing
 *
 * @package BDP/Templates
 */

$field_name   = isset( $field_name ) ? $field_name : '';
$display_only = isset( $display_only ) ? $display_only : false;
$disabled     = isset( $disabled ) ? $disabled : false;
$selected     = isset( $selected ) ? $selected : 0;

$description = $plan->description ? apply_filters( 'wpbdp_plan_description_for_display', $plan->description, $plan ) : '';
$description = apply_filters( 'wpbdp_fee_selection_fee_description', $description, $plan );
?>
    <div class="wpbdp-plan wpbdp-plan-<?php echo esc_attr( $plan->id ); ?> wpbdp-plan-info-box wpbdp-clearfix <?php echo $display_only ? 'display-only ' : ''; ?><?php echo $disabled ? 'wpbdp-plan-disabled' : ''; ?>"
         data-id="<?php echo esc_attr( $plan->id ); ?>"
         data-disabled="<?php echo absint( $disabled ? 1 : 0 ); ?>"
         data-recurring="<?php echo absint( $plan->recurring ? 1 : 0 ); ?>"
         data-free-text="<?php echo esc_attr( wpbdp_currency_format( 0.0 ) ); ?>"
         data-categories="<?php echo esc_attr( implode( ',', (array) $plan->supported_categories ) ); ?>"
         data-pricing-model="<?php echo esc_attr( $plan->pricing_model ); ?>"
         data-amount="<?php echo esc_attr( $plan->amount ); ?>"
         data-amount-format="<?php echo esc_attr( wpbdp_currency_format( 'placeholder' ) ); ?>"
         data-pricing-details="<?php echo esc_attr( wp_json_encode( $plan->pricing_details ) ); ?>" >
        <div class="wpbdp-plan-details">
        <div class="wpbdp-plan-label"><?php echo esc_html( apply_filters( 'wpbdp_category_fee_selection_label', $plan->label, $plan ) ); ?></div>

			<?php if ( $description ) : ?>
            <div class="wpbdp-plan-description"><?php echo wp_kses_post( wpautop( $description ) ); ?></div>
            <?php endif; ?>

            <ul class="wpbdp-plan-feature-list">
		        <li class="wpbdp-plan-duration">
					<?php if ( $plan->days > 0 ) : ?>
		            <span class="wpbdp-plan-duration-amount">
		                <?php echo esc_html( $plan->days ); ?>
		            </span>
		            <span class="wpbdp-plan-duration-period"><?php esc_html_e( 'days', 'business-directory-plugin' ); ?></span>
						<?php if ( $plan->recurring ) : ?>
		                <span class="wpbdp-plan-is-recurring">(<?php esc_html_e( 'Recurring', 'business-directory-plugin' ); ?>)</span>
		                <?php endif; ?>
					<?php else : ?>
		            <span class="wpbdp-plan-duration-never-expires">
		                <?php esc_html_e( 'Never Expires', 'business-directory-plugin' ); ?>
		            </span>
		            <?php endif; ?>
		        </li>
				<?php foreach ( $plan->get_feature_list() as $feature ) : ?>
                <li><?php echo esc_html( $feature ); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <div class="wpbdp-plan-price">
			<span class="wpbdp-plan-price-amount"><?php echo esc_html( wpbdp_currency_format( $plan->calculate_amount( $categories ) ) ); ?></span>

			<?php if ( ! $display_only ) : ?>
				<input type="radio"
					id="wpbdp-plan-select-radio-<?php echo esc_attr( $plan->id ); ?>"
					name="<?php echo esc_attr( $field_name ); ?>"
					value="<?php echo esc_attr( $plan->id ); ?>"
					<?php disabled( $disabled, true ); ?>
					<?php echo $disabled ? '' : checked( absint( $plan->id ), absint( $selected ), false ); ?> />
				<label class="button wpbdp-button" for="wpbdp-plan-select-radio-<?php echo esc_attr( $plan->id ); ?>">
					<span> </span>
				</label>
			<?php elseif ( empty( $editing ) && isset( $plans_count ) && 1 === $plans_count ) : ?>
				<input type="hidden" name="<?php echo esc_attr( $field_name ); ?>" value="<?php echo esc_attr( $plan->id ); ?>">
			<?php endif; ?>
        </div>

		<?php if ( $disabled ) : ?>
        <div class="wpbdp-msg wpbdp-plan-disabled-msg wpbdp-full">
            <?php esc_html_e( 'This plan can\'t be used for admin submits. For a recurring plan to work, end users need to pay for it using a supported gateway.', 'business-directory-plugin' ); ?>
        </div>
        <?php endif; ?>
        <?php if ( ! empty( $plan->extra_data['private'] ) ) : ?>
            <div class="wpbdp-plan-private-msg wpbdp-full">
                (<?php esc_html_e( 'Private plan', 'business-directory-plugin' ); ?>)
            </div>
        <?php endif; ?>

    </div>
