<?php
    $buttons = array(
		'addfield'    => array(
			'label' => __( 'Add New Form Field', 'business-directory-plugin' ),
			'url'   => wp_nonce_url( admin_url( 'admin.php?page=wpbdp_admin_formfields&action=addfield' ), 'editfield' ),
		),
		'previewform' => array(
			'label' => __( 'Preview Form', 'business-directory-plugin' ),
			'url'   => admin_url( 'admin.php?page=wpbdp_admin_formfields&action=previewform' ),
		),
		'updatetags'  => array(
			'label' => __( 'Manage Theme Tags', 'business-directory-plugin' ),
			'url'   => admin_url( 'admin.php?page=wpbdp_admin_formfields&action=updatetags' ),
		),
    );

	WPBDP_Admin_Pages::show_tabs(
		array(
			'id'      => 'formfields',
			'sub'     => __( 'Form Fields', 'business-directory-plugin' ),
			'buttons' => $buttons,
		)
	);
?>
<span class="howto wpbdp-settings-subtab-description wpbdp-setting-description">
	<?php
	esc_html_e(
		'Create new fields, edit existing fields, change the field order and visibility.',
		'business-directory-plugin'
	);

	echo ' ';
	printf(
		/* translators: %1$s open link, %2$s close link */
		esc_html__( 'Please see the %1$sForm Fields documentation%2$s for more details.', 'business-directory-plugin' ),
		'<a href="https://businessdirectoryplugin.com/knowledge-base/manage-form-fields/" target="_blank" rel="noopener">',
		'</a>'
	);
    ?>
</span>

<?php $table->views(); ?>
<?php $table->display(); ?>

<?php echo wpbdp_admin_footer(); ?>
