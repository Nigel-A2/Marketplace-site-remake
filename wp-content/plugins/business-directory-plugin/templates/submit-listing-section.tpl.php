<div class="wpbdp-submit-listing-section wpbdp-submit-listing-section-<?php echo esc_attr( $section['id'] ); ?> <?php echo esc_attr( implode( ' ', $section['flags'] ) ); ?>" data-section-id="<?php echo esc_attr( $section['id'] ); ?>">
    <div class="wpbdp-submit-listing-section-content <?php echo ! empty( $section['content_css_classes'] ) ? esc_attr( $section['content_css_classes'] ) : ''; ?>">
		<?php if ( $messages ) : ?>
            <div class="wpbdp-submit-listing-section-messages wpbdp-full">
                <?php
                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                echo $messages;
                ?>
            </div>
        <?php endif; ?>

        <?php
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo $section['html'];
        ?>
        <div class="wpbdp-submit-listing-form-actions wpbdp-full">
        <?php if ( ! empty( $section['prev_section'] ) ) : ?>
			<button class="submit-back-button wpbdp-button" data-previous-section="<?php echo esc_attr( $section['prev_section'] ); ?>"><?php esc_html_e( 'Back', 'business-directory-plugin' ); ?></button>
			<?php
		endif;

		if ( ! empty( $section['next_section'] ) ) :
			?>
            <button class="submit-next-button wpbdp-button" data-next-section="<?php echo esc_attr( $section['next_section'] ); ?>"><?php esc_html_e( 'Next', 'business-directory-plugin' ); ?></button>
			<?php
		else :
			if ( $is_admin || ! wpbdp_payments_possible() || $submit->skip_plan_payment ) {
				$label = __( 'Complete Listing', 'business-directory-plugin' );
			} elseif ( $editing ) {
				$label = __( 'Save Changes', 'business-directory-plugin' );
			} else {
				$label = __( 'Continue to Payment', 'business-directory-plugin' );
			}
			?>
			<button type="submit" id="wpbdp-submit-listing-submit-btn" class="wpbdp-button"><?php echo esc_html( $label ); ?></button>
        <?php endif; ?>
        </div>
    </div>
</div>
