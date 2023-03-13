<?php
/**
 * Uninstall confirm template
 *
 * @package WPBDP/Templates/Admin
 */

?>

<div id="wpbdp-admin-page-uninstall" class="wpbdp-admin-page-uninstall">
<?php wpbdp_admin_notices(); ?>

<div id="wpbdp-uninstall-messages">
    <div id="wpbdp-uninstall-warning">
        <div class="wpbdp-warning-content">
			<p>
				<span class="dashicons dashicons-warning"></span>
				<?php _ex( 'Uninstalling Business Directory Plugin will do the following:', 'uninstall', 'business-directory-plugin' ); ?>
			</p>

            <ul>
                <li><?php _ex( 'Remove ALL directory listings', 'uninstall', 'business-directory-plugin' ); ?></li>
                <li><?php _ex( 'Remove ALL directory categories', 'uninstall', 'business-directory-plugin' ); ?></li>
                <li><?php _ex( 'Remove ALL directory settings', 'uninstall', 'business-directory-plugin' ); ?></li>
				<li><?php esc_html_e( 'Remove ALL module data (regions, maps, ratings, restrictions)', 'business-directory-plugin' ); ?></li>
                <li><?php _ex( 'Deactivate the plugin from the file system', 'uninstall', 'business-directory-plugin' ); ?></li>
            </ul>

            <p><?php esc_html_e( 'ONLY do this if you want to DELETE ALL OF YOUR DATA.', 'business-directory-plugin' ); ?></p>
        </div>

        <a id="wpbdp-uninstall-proceed-btn" class="button"><?php esc_html_e( 'Yes, I want to uninstall', 'business-directory-plugin' ); ?></a>
    </div>

    <div id="wpbdp-uninstall-reinstall-suggestion">
        <p><?php _ex( 'If you just need to reinstall the plugin, please do the following:', 'uninstall', 'business-directory-plugin' ); ?></p>

        <ul>
			<li>
				<?php
				echo sprintf(
					/* translators: %1$s: open link html, %2$s: close link html */
					esc_html__( 'Go to %1$sPlugins > Installed Plugins%2$s', 'business-directory-plugin' ),
					'<a href="' . esc_url( admin_url( 'plugins.php?plugin_status=active' ) ) . '">',
					'</a>'
				);
				?>
			</li>
            <li>
                <?php
                printf(
                    /* translators: %1$s: open italic html, %2$s: close italic html */
                    esc_html__( 'Click on "Delete" for Business Directory Plugin. %1$sTHIS OPERATION IS SAFE--your data will NOT BE LOST doing this%2$s', 'business-directory-plugin' ),
                    '<i>',
                    '</i>'
                );
                ?>
            </li>
            <li><?php esc_html_e( 'Wait for the delete to finish', 'business-directory-plugin' ); ?></li>
            <li><?php esc_html_e( 'The plugin is now removed, but your data is still present inside of your database.', 'business-directory-plugin' ); ?></li>
            <li>
                <?php
                echo sprintf(
                    /* translators: %1$s: open link html, %2$s: close link html */
                    esc_html__( 'You can reinstall the plugin again under %1$sPlugins > Add New%2$s', 'business-directory-plugin' ),
                    '<a href="' . esc_url( admin_url( 'plugin-install.php' ) ) . '">',
                    '</a>'
                );
                ?>
            </li>
        </ul>

        <a href="<?php echo esc_url( admin_url( 'plugins.php' ) ); ?>" class="button">
			<?php esc_html_e( 'Take me to the Plugins screen', 'business-directory-plugin' ); ?>
		</a>
    </div>

	<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpbdp-debug-info' ) ); ?>">
		<?php esc_html_e( 'Get debug info', 'business-directory-plugin' ); ?>
	</a>
</div>

<?php wpbdp_render_page( WPBDP_PATH . 'templates/admin/uninstall-capture-form.tpl.php', array(), true ); ?>
</div>
