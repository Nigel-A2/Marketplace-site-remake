<?php do_action( 'wpbdp_before_submit_listing_page', $listing ); ?>

<div id="wpbdp-submit-listing" class="wpbdp-submit-page wpbdp-page">
    <form action="" method="post" data-ajax-url="<?php echo esc_url( wpbdp_ajax_url() ); ?>" enctype="multipart/form-data">
        <?php wp_nonce_field( 'listing submit' ); ?>
        <input type="hidden" name="listing_id" value="<?php echo esc_attr( $listing->get_id() ); ?>" />
        <input type="hidden" name="editing" value="<?php echo $editing ? '1' : '0'; ?>" />
        <input type="hidden" name="save_listing" value="1" />
        <input type="hidden" name="reset" value="" />
        <input type="hidden" name="current_section" value="<?php echo esc_attr( $submit->current_section ); ?>" />

		<h3>
			<?php
			if ( $editing ) {
				esc_html_e( 'Edit Listing', 'business-directory-plugin' );
			} else {
				esc_html_e( 'Add Listing', 'business-directory-plugin' );
			}
			?>
		</h3>
			<?php
			$submit->render_rootline();

			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $messages['general'];

			foreach ( $sections as $section ) {
				wpbdp_render(
                    'submit-listing-section',
                    array(
						'echo'     => true,
                        'section'  => $section,
                        'listing'  => $listing,
                        'messages' => ( ! empty( $messages[ $section['id'] ] ) ? $messages[ $section['id'] ] : '' ),
                        'is_admin' => $is_admin,
                        'submit'   => $submit,
                        'editing'  => $editing
                    )
                );
			}
			?>
    </form>
</div>
<?php do_action( 'wpbdp_after_submit_listing_page', $listing ); ?>
