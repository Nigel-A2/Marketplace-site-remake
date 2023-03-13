<?php
/**
 * Listing Buttons template
 *
 * @package BDP/Templates/parts/Listing Buttons
 */

$buttons = '';

if ( 'single' === $view || 'excerpt' === $view ) :
	if ( wpbdp_user_can( 'edit', $listing_id ) ) :
		$buttons .= sprintf(
			'<a class="wpbdp-button button edit-listing" href="%s" rel="nofollow">%s</a>',
			wpbdp_url( 'edit_listing', $listing_id ),
			_x( 'Edit', 'templates', 'business-directory-plugin' )
		);
	endif;

	if ( wpbdp_get_option( 'enable-listing-flagging' ) && wpbdp_user_can( 'flagging', $listing_id ) ) :
		$buttons .= sprintf(
			'<a class="wpbdp-button button report-listing" href="%s" rel="nofollow">%s</a>',
			esc_url( wpbdp_url( 'flag_listing', $listing_id ) ),
			apply_filters( 'wpbdp_listing_flagging_button_text', _x( 'Flag Listing', 'templates', 'business-directory-plugin' ) )
		);
	endif;

	if ( wpbdp_user_can( 'delete', $listing_id ) ) :
		$buttons .= sprintf(
			'<a class="wpbdp-button button delete-listing" href="%s" rel="nofollow">%s</a>',
			wpbdp_url( 'delete_listing', $listing_id ),
			esc_html__( 'Delete', 'business-directory-plugin' )
		);
    endif;
endif;

if ( 'single' === $view ) :
	if ( wpbdp_get_option( 'show-directory-button' ) ) :
		ob_start();
		wpbdp_get_return_link();
		$buttons .= ob_get_clean();
	endif;
endif;

$buttons = apply_filters( 'wpbdp-listing-buttons', $buttons, $listing_id );
if ( ! $buttons ) {
	return;
}
?>
<div class="listing-actions cf">
	<?php
	// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	echo $buttons;
	?>
</div>
