<?php
wpbdp_admin_header(
	array(
		'title'   => __( 'Upload Directory Theme', 'business-directory-plugin' ),
		'id'      => 'themes-install',
		'echo'    => true,
		'sidebar' => false
	)
);
wpbdp_admin_notices();
?>

<p class="howto">
<?php
// translators: %s is the link for Business Directory Premium Themes.
printf(
	esc_html__( 'This is a theme or skin from %s and is NOT a regular WordPress theme.', 'business-directory-plugin' ),
	'<a href="https://businessdirectoryplugin.com/premium-themes/">https://businessdirectoryplugin.com/</a>'
);
?>
</p>

<form action="" method="post" enctype="multipart/form-data">
    <input type="hidden" name="wpbdp-action" value="upload-theme" />
    <?php wp_nonce_field( 'upload theme zip' ); ?>

    <table class="form-table">
        <tbody>
            <tr>
                <th>
                    <?php esc_html_e( 'Business Directory Theme archive (ZIP file)', 'business-directory-plugin' ); ?>
                </th>
                <td>
                    <input type="file" name="themezip" />
                </td>
            </tr>
        </tbody>
    </table>

    <?php submit_button( esc_html__( 'Upload', 'business-directory-plugin' ), 'primary', 'begin-theme-upload' ); ?>
</form>

<?php wpbdp_admin_footer( 'echo' ); ?>
