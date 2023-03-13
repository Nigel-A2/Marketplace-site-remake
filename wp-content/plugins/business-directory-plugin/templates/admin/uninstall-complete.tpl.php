<?php
wpbdp_admin_header(
    array(
        'title' => __( 'Uninstall', 'business-directory-plugin' ),
        'echo' => true,
    )
);
?>

<?php wpbdp_admin_notices(); ?>

<p><?php esc_html_e( 'Uninstall completed.', 'business-directory-plugin' ); ?></p>
<p>
    <a href="<?php echo esc_url( admin_url() ); ?>">
        <?php esc_html_e( 'Return to Dashboard.', 'business-directory-plugin' ); ?>
    </a>
</p>

<?php wpbdp_admin_footer( 'echo' ); ?>
