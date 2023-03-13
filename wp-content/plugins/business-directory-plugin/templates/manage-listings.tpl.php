<div id="wpbdp-manage-listings-page" class="wpbdp-manage-listings-page businessdirectory-manage-listings businessdirectory wpbdp-page">
	<?php if ( $query->have_posts() ) : ?>
        <p><?php esc_html_e( 'Your current listings are shown below. To edit a listing click the edit button. To delete a listing click the delete button.', 'business-directory-plugin' ); ?></p>
        <?php
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
        echo wpbdp_x_part( 'listings' );
        ?>
	<?php else : ?>
        <p><?php esc_html_e( 'You do not currently have any listings in the directory.', 'business-directory-plugin' ); ?></p>
        <?php
        echo sprintf(
            '<a href="%s">%s</a>.',
            esc_url( wpbdp_get_page_link( 'main' ) ),
            esc_html__( 'Return to directory', 'business-directory-plugin' )
        );
        ?>
    <?php endif; ?>
</div>
