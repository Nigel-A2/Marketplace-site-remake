<?php
wpbdp_admin_header( array( 'echo' => true, 'sidebar' => false ) );
wpbdp_admin_notices();
?>


<?php $table->views(); ?>

<form action="" method="get">
    <p class="search-box">
        <label class="screen-reader-text" for="payment-search-input"><?php esc_html_e( 'Search Payments:', 'business-directory-plugin' ); ?></label>
        <input type="search" id="payment-search-input" name="s" value="<?php echo esc_attr( wpbdp_get_var( array( 'param' => 's' ) ) ); ?>" />
        <input type="submit" id="search_submit" class="button" value="<?php esc_attr_e( 'Search', 'business-directory-plugin' ); ?>" />
    </p>

    <input type="hidden" name="page" value="<?php echo esc_attr( wpbdp_get_var( array( 'param' => 'page' ) ) ); ?>" />
    <input type="hidden" name="status" value="<?php echo esc_attr( wpbdp_get_var( array( 'param' => 'status', 'default' => 'all' ) ) ); ?>" />

<?php $table->display(); ?>

</form>

<?php wpbdp_admin_footer( 'echo' ); ?>
