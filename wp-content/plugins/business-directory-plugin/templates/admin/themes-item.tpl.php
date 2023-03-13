<?php
if ( $theme->active ) {
	$status = __( 'Active', 'business-directory-plugin' );
} elseif ( ! $theme->can_be_activated && ! empty( $theme->license_status ) ) {
	$status = ucfirst( $theme->license_status );
	$status = $theme->license_status === 'invalid' ? __( 'Missing License', 'business-directory-plugin' ) : $status;
} else {
	$status = __( 'Inactive', 'business-directory-plugin' );
}
?>
<div class="wpbdp-card plugin-card-<?php echo esc_attr( $theme->id ); ?> wpbdp-no-thumb  wpbdp-theme <?php echo esc_attr( $theme->id ); ?> <?php echo ( $theme->active ? 'wpbdp-addon-active active' : '' ); ?> <?php do_action( 'wpbdp-admin-themes-item-css', $theme ); ?> ">
	<h2 class="wpbdp-plugin-card-title">
		<?php echo esc_html( $theme->name ); ?>
	</h2>

	<div class="wpbdp-theme-details-wrapper">
		<?php if ( $theme->can_be_activated && $is_outdated ) : ?>
			<div class="wpbdp-theme-update-info update-message notice inline notice-warning notice-alt" data-l10n-updating="<?php esc_attr_e( 'Updating theme...', 'business-directory-plugin' ); ?>" data-l10n-updated="<?php esc_attr_e( 'Theme updated.', 'business-directory-plugin' ); ?>">
				<div class="update-message">
					<?php
					echo sprintf(
						// translators: %1$s is opening <a> tag, %2$s is closing </a> tag
						esc_html__( 'New version available. %1$sUpdate now.%2$s', 'business-directory-plugin' ),
						'<a href="#" data-theme-id="' . esc_attr( $theme->id ) . '" data-nonce="' . esc_attr( wp_create_nonce( 'update theme ' . $theme->id ) ) . '" class="update-link">',
						'</a>'
					);
					?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( $theme->thumbnail ) : ?>
			<a href="<?php echo esc_url( $theme->thumbnail ); ?>" title="<?php echo esc_attr( $theme->name ); ?>" class="thickbox" rel="wpbdp-theme-<?php echo esc_attr( $theme->id ); ?>-gallery"><img src="<?php echo esc_url( $theme->thumbnail ); ?>" class="wpbdp-theme-thumbnail" /></a>
		<?php else : ?>
		<div class="wpbdp-theme-thumbnail">
			<p><?php echo esc_html( $theme->description ); ?></p>
		</div>
		<?php endif; ?>

	</div>

	<div class="wpbdp-grid wpbdp-card-footer">
		<div class="wpbdp6">
			<p class="addon-status">
				<?php echo esc_html( $status ) . esc_html( ' v' . $theme->version ); ?>
			</p>
		</div>
		<div class="wpbdp6 wpbdp-right wpbdp-theme-actions">
			<?php if ( ! $theme->active && ! in_array( $theme->id, array( 'default', 'no_theme' ), true ) ) : ?>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpbdp-themes&action=delete-theme&theme_id=' . esc_attr( $theme->id ) ) ); ?>" class="delete-theme-link delete-theme">
					<?php esc_html_e( 'Delete', 'business-directory-plugin' ); ?>
				</a>
			<?php endif; ?>
			<?php if ( $theme->can_be_activated ) : ?>
				<form action="" method="post">
					<input type="hidden" name="wpbdp-action" value="set-active-theme" />
					<input type="hidden" name="theme_id" value="<?php echo esc_attr( $theme->id ); ?>" />
					<?php wp_nonce_field( 'activate theme ' . $theme->id ); ?>
					<input type="submit" class="button choose-theme button-primary" value="<?php esc_attr_e( 'Activate', 'business-directory-plugin' ); ?>" />
				</form>
			<?php endif; ?>
		</div>
	</div>

	<?php do_action( 'wpbdp-admin-themes-extra', $theme ); ?>
</div>
