<?php
echo wpbdp_admin_header(
	array(
		'title'   => __( 'Directory Reset to Default', 'business-directory-plugin' ),
		'id'      => 'admin-settings',
		'buttons' => array(
			'wpbdp_settings' => array(
				'label' => __( 'Cancel', 'business-directory-plugin' ),
				'url'   => admin_url( 'admin.php?page=wpbdp_settings' ),
			),
		),
	)
);
?>

<div class="wpbdp-note warning">
    <?php _e( 'Use this option if you want to go back to the original factory settings for BD.', 'business-directory-plugin' ); ?>
    <b><?php _e( 'Please note that all of your existing settings will be lost.', 'business-directory-plugin' ); ?></b>
    <br/>
    <?php _e( 'Your existing listings will NOT be deleted doing this.', 'business-directory-plugin' ); ?>
</div>

<form action="" method="POST">
    <input type="hidden" name="wpbdp-action" value="reset-default-settings" />
    <?php wp_nonce_field( 'reset defaults' ); ?>
	<?php echo submit_button( __( 'Reset Defaults', 'business-directory-plugin' ), 'delete button-primary' ); ?>
</form>

<?php
	echo wpbdp_admin_footer();
?>
