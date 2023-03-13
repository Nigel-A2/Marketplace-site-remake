<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}
?>
<div class="wpbdp-wrap" id="wpbdp-addons-page">
	<?php
	wpbdp_admin_header(
		array(
			'title'   => esc_html__( 'Directory Modules', 'business-directory-plugin' ),
			'sidebar' => false,
			'echo'    => true,
		)
	);

	$modules = wpbdp()->modules->get_modules();
	if ( count( $modules ) > 1 && ! isset( $modules['premium'] ) ) {
		// Has a module, but not Premium.
		WPBDP_Admin_Education::show_tip( 'install-premium' );
	}

	?>
	<div class="wrap">

	<div id="the-list" class="wpbdp-addons">
		<?php foreach ( $addons as $slug => $addon ) {
			if ( strpos( $addon['title'], 'Theme' ) ) {
				// Skip themes for now since they have another page.
				continue;
			}

			$plugin_key = str_replace( 'business-directory-', '', substr( $addon['plugin'], 0, strpos( $addon['plugin'], '/' ) ) );
			$module = isset( $modules[ $plugin_key ] ) ? $modules[ $plugin_key ] : false;
			?>
			<div class="wpbdp-card plugin-card-<?php echo esc_attr( $slug ); ?> wpbdp-no-thumb wpbdp-addon-<?php echo esc_attr( $addon['status']['type'] ); ?>">
				<?php if ( strtotime( $addon['released'] ) > strtotime( '-90 days' ) ) : ?>
					<div class="wpbdp-ribbon">
						<span><?php esc_attr_e( 'New', 'business-directory-plugin' ); ?></span>
					</div>
				<?php endif; ?>
				<div class="wpbdp-grid">
					<span class="wpbdp2 wpbdp-card-module-icon wpbdp-admin-module-icon ">
						<img src="<?php echo esc_attr( $addon['icons']['1x'] ); ?>" alt="" />
					</span>
					<div class="<?php echo esc_attr( $addon['status']['type'] === 'active' ? 'wpbdp10' : 'wpbdp7' ); ?>">
						<h2 class="wpbdp-plugin-card-title">
							<?php echo esc_html( str_replace( ' Module', '', $addon['display_name'] ) ); ?>
						</h2>
						<p class="wpbdp-addon-status">
							<?php echo esc_html( $addon['status']['label'] ); ?>
						</p>
					</div>
					<div class="wpbdp-right <?php echo esc_attr( $addon['status']['type'] === 'active' ? 'wpbdp-hidden' : 'wpbdp3' ); ?>">
						<?php
						$passing = array(
							'addon'         => $addon,
							'license_type'  => ! empty( $license_type ) ? $license_type : false,
							'plan_required' => 'plan_required',
							'upgrade_link'  => $pricing,
						);
						WPBDP_Show_Modules::show_conditional_action_button( $passing );
						?>
					</div>
				</div>

				<div class="wpbdp-plugin-card-details">
					<?php echo esc_html( $addon['excerpt'] ); ?>
					<?php $show_docs = isset( $addon['docs'] ) && ! empty( $addon['docs'] ) && $addon['installed']; ?>
					<?php if ( $show_docs ) { ?>
						<div class="wpbdp-plugin-card-docs">
							<a href="<?php echo esc_url( $addon['docs'] ); ?>" target="_blank" aria-label="<?php esc_attr_e( 'View Docs', 'business-directory-plugin' ); ?>">
								<?php esc_html_e( 'View Docs', 'business-directory-plugin' ); ?>
							</a>

							<?php if ( $module && ! empty( $module->settings_url ) ) { ?>
							<a href="<?php echo esc_url( admin_url( $module->settings_url ) ); ?>" class="alignright">
								<?php esc_html_e( 'Settings', 'business-directory-plugin' ); ?>
							</a>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php } ?>
	</div>
</div>
</div>
