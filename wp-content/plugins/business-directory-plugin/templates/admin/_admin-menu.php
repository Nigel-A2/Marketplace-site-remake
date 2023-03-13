<div class="wpbdp-no-padding wpbdp-menu-area">
	<?php
	WPBDP_Admin_Pages::show_title(
		array(
			'title' => $title,
		)
	);

	require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
	require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';
	$wp_filesystem = new WP_Filesystem_Direct( null );
	?>
	<ul class="wpbdp-nav-items">
		<?php
		foreach ( $tabs as $tab_id => $tab ) :
			if ( strpos( $tab_id, 'wpbdp' ) === 0 ) {
				$link = admin_url( 'admin.php?page=' . $tab_id );
			} elseif ( strpos( $tab_id, '?' ) ) {
				$link = admin_url( $tab_id );
			} else {
				$link = add_query_arg(
					array(
						'page' => 'wpbdp_settings',
						'tab'  => $tab_id,
					)
				);
			}

			?>
			<li class="wpbdp-nav-item">
				<a class="<?php echo $active_tab === $tab_id ? 'active ' : ''; ?><?php echo sanitize_html_class( apply_filters( 'wpbdp_settings_tab_css', '', $tab_id ) ); ?>" href="<?php echo esc_url( $link ); ?>" title="<?php echo esc_html( $tab['title'] ); ?>">
					<span class="wpbdp-nav-item-icon <?php echo esc_attr( ( strpos( $tab['icon'], ' ' ) === false ? 'wpbdp-admin-icon-' : '' ) . $tab['icon'] ); ?>">
						<?php
						if ( ! empty( $tab['icon_url'] ) ) {
							$img = '';
							if ( ini_get( 'allow_url_fopen' ) ) {
								echo $img = $wp_filesystem->get_contents( $tab['icon_url'] );
							}
							if ( ! $img ) {
								echo '<img src="' . esc_url( $tab['icon_url'] ) . '" alt="' . esc_attr( $tab['icon'] ) . '" />';
							}
						} elseif ( strpos( $tab['icon'], ' ' ) === false ) {
							echo $wp_filesystem->get_contents( esc_attr( $tab['icon'] ) );
						}
						?>
					</span>
					<span class="wpbdp-nav-item-name"><?php echo esc_html( $tab['title'] ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="wpbdp-nav-toggle hide-if-no-js">
		<div class="wpbdp-grid">
			<div class="wpbdp2 wpbdp-nav-item-icon">
				<img src="<?php echo esc_url( WPBDP_ASSETS_URL . 'images/icons/caret-left.svg' ); ?>" class="wpbdp-icon-maximized" width="24" height="24"/>
				<img src="<?php echo esc_url( WPBDP_ASSETS_URL . 'images/icons/caret-right.svg' ); ?>" class="wpbdp-icon-minimized" width="24" height="24"/>
			</div>
			<div class="wpbdp10 wpbdp-nav-item-name">
				<?php esc_html_e( 'Minimize Navigation', 'business-directory-plugin' ); ?>
			</div>
		</div>
	</div>
</div>
