<?php
/**
 * Manage Listings rendering template
 *
 * @package BDP/templates/Manage Listings
 */

?>
<div id="wpbdp-manage-listings-page" class="wpbdp-manage-listings-page businessdirectory-manage-listings businessdirectory wpbdp-grid wpbdp-page">
    <?php
	if ( $query->have_posts() ) :
		?>
        <p><?php esc_html_e( 'Your current listings are shown below.', 'business-directory-plugin' ); ?></p>
        <?php
		/** @phpstan-ignore-next-line */
		while ( $query->have_posts() ) {
            $query->the_post();
            // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
            echo WPBDP_Listing_Display_Helper::excerpt();
		}

		/** @phpstan-ignore-next-line */
		wpbdp_x_part(
			'parts/pagination',
			array(
				'query' => $query,
			)
		);
	else :
		?>
		<p><?php esc_html_e( 'You do not currently have any listings in the directory.', 'business-directory-plugin' ); ?></p>
		<?php
		echo sprintf(
			'<a href="%s">%s</a>.',
			esc_attr( wpbdp_get_page_link( 'main' ) ),
			esc_html__( 'Return to directory', 'business-directory-plugin' )
		);
	endif;
	?>
</div>
