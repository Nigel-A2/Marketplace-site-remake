<div id="wpbdp-search-form-wrapper">

<form action="<?php echo esc_url( wpbdp_url( 'search' ) ); ?>" id="wpbdp-search-form" method="get">
    <input type="hidden" name="dosrch" value="1" />
    <input type="hidden" name="q" value="" />

    <?php if ( ! wpbdp_rewrite_on() ) : ?>
    <input type="hidden" name="page_id" value="<?php echo wpbdp_get_page_id(); ?>" />
    <?php endif; ?>
    <input type="hidden" name="wpbdp_view" value="search" />

    <?php if ( ! empty( $return_url ) ) : ?>
    <input type="hidden" name="return_url" value="<?php echo esc_attr( esc_url( $return_url ) ); ?>" />
    <?php endif; ?>

    <?php if ( $validation_errors ) : ?>
        <?php foreach ( $validation_errors as $err ) : ?>
            <?php echo wpbdp_render_msg( $err, 'error' ); ?>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php echo $fields; ?>
	<?php do_action( 'wpbdp_after_search_fields' ); ?>

    <p>
		<input type="submit" class="wpbdp-submit wpbdp-button submit" value="<?php esc_attr_e( 'Search', 'business-directory-plugin' ); ?>" />
		<a href="<?php echo esc_url( wpbdp_get_page_link( 'search' ) ); ?>" class="reset"><?php esc_html_e( 'Clear', 'business-directory-plugin' ); ?></a>
    </p>
</form>

</div>
