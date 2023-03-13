<div id="fl-tools-form" class="fl-settings-form">

	<h3 class="fl-settings-form-header"><?php _e( 'Cache', 'fl-builder' ); ?></h3>

	<form id="cache-form" action="<?php FLBuilderAdminSettings::render_form_action( 'tools' ); ?>" method="post">
		<div class="fl-settings-form-content">
			<p><?php _e( 'A CSS and JavaScript file is dynamically generated and cached each time you create a new layout. Sometimes the cache needs to be refreshed when you migrate your site to another server or update to the latest version. If you are running into any issues, please try clearing the cache by clicking the button below.', 'fl-builder' ); ?></p>
			<?php if ( is_network_admin() ) : ?>
			<p><strong><?php _e( 'NOTE:', 'fl-builder' ); ?></strong> <?php _e( 'This applies to all sites on the network.', 'fl-builder' ); ?></p>
			<?php elseif ( ! is_network_admin() && is_multisite() ) : ?>
			<p><strong><?php _e( 'NOTE:', 'fl-builder' ); ?></strong> <?php _e( 'This only applies to this site. Please visit the Network Admin Settings to clear the cache for all sites on the network.', 'fl-builder' ); ?></p>
			<?php endif; ?>

		</div>
		<p class="submit">
			<input type="submit" name="update" class="button-primary" value="<?php esc_attr_e( 'Clear Cache', 'fl-builder' ); ?>" />
			<?php wp_nonce_field( 'cache', 'fl-cache-nonce' ); ?>
		</p>
	</form>
	<hr />

	<?php
	if ( version_compare( PHP_VERSION, '5.3.0', '>' ) && class_exists( '\FLCacheClear\Plugin' ) ) {
		include FL_BUILDER_CACHE_HELPER_DIR . 'includes/admin-settings-cache-plugins.php';
	}

	$debug = get_transient( 'fl_debug_mode' );
	if ( $debug ) {
		$expire_opt = get_option( '_transient_timeout_fl_debug_mode' );
		$datetime1  = new DateTime( 'now' );
		$datetime2  = new DateTime( gmdate( 'Y-m-d H:i:s', $expire_opt ) );
		$interval   = $datetime1->diff( $datetime2 );
	}
	?>
	<?php $header = ( $debug ) ? __( 'Debug Mode Enabled', 'fl-builder' ) : __( 'Debug Mode Disabled', 'fl-builder' ); ?>
	<h3 class="fl-settings-form-header"><?php echo $header; ?></h3>

	<form id="debug-form" action="<?php FLBuilderAdminSettings::render_form_action( 'tools' ); ?>" method="post">
		<div class="fl-settings-form-content">
			<?php if ( ! $debug ) : ?>
			<p><?php _e( 'Enable debug mode to generate a unique support URL.', 'fl-builder' ); ?></p>
		<?php else : ?>
			<p><?php _e( 'Copy this unique URL and send it to support as directed.', 'fl-builder' ); ?></p>
		<?php endif; ?>
			<?php
			if ( $debug ) :
				$url = add_query_arg( array(
					'fldebug' => $debug,
				), site_url() );
				?>
				<p><?php printf( '<code>%s</code>', $url ); ?></p>
				<p><?php printf( 'Link will expire in <strong>%s</strong>', $interval->format( '%d days %h hours %i minutes' ) ); ?></p>
			<?php endif; ?>
		</div>
		<p class="submit">
			<input type="submit" name="update" class="button-primary" value="<?php echo ( $debug ) ? esc_attr__( 'Disable Debug Mode', 'fl-builder' ) : esc_attr__( 'Enable Debug Mode', 'fl-builder' ); ?>" />
			<?php wp_nonce_field( 'debug', 'fl-debug-nonce' ); ?>
		</p>
	</form>

	<?php if ( get_transient( 'fl_debug_mode' ) || ( defined( 'FL_ENABLE_META_CSS_EDIT' ) && FL_ENABLE_META_CSS_EDIT ) ) : ?>

		<?php
		$data = get_option( '_fl_builder_settings' );
		if ( ! isset( $data->css ) ) {
			$css = '';
		} else {
			$css = $data->css;
		}
		if ( ! isset( $data->js ) ) {
			$js = '';
		} else {
			$js = $data->js;
		}
		?>

		<form id="css-js-form" action="<?php FLBuilderAdminSettings::render_form_action( 'tools' ); ?>" method="post">

		<h3 class="fl-settings-form-header"><?php _e( 'Global CSS', 'fl-builder' ); ?></h3>

		<p><textarea style="width:100%" rows=10 name="css"><?php echo esc_attr( $css ); ?></textarea></p>

		<h3 class="fl-settings-form-header"><?php _e( 'Global JS', 'fl-builder' ); ?></h3>

		<p><textarea style="width:100%" rows=10 name="js"><?php echo esc_attr( $js ); ?></textarea></p>

		<input type="submit" name="update-css-js" class="button-primary" value="<?php echo esc_attr__( 'Update Global CSS/JS', 'fl-builder' ); ?>" />
		<?php wp_nonce_field( 'debug', 'fl-css-js-nonce' ); ?>
	</form>

	<?php endif; ?>

	<?php
	$alpha       = get_option( 'fl_alpha_updates', false );
	$beta        = get_option( 'fl_beta_updates', false );
	$header      = __( 'Prerelease Updates', 'fl-builder' );
	$enable_txt  = __( 'Enable', 'fl-builder' );
	$alpha_txt   = __( 'Alpha', 'fl-builder' );
	$beta_txt    = __( 'Beta', 'fl-builder' );
	$updates_txt = __( 'updates', 'fl-builder' );
	?>
	<?php if ( true !== FL_BUILDER_LITE ) : ?>
	<hr />
	<h3 class="fl-settings-form-header"><?php echo $header; ?></h3>

	<form id="beta-form" action="<?php FLBuilderAdminSettings::render_form_action( 'tools' ); ?>" method="post">
		<div class="fl-settings-form-content">
			<p><?php _e( 'Enabling the prerelease channel will enable updates for all Beaver Builder products.', 'fl-builder' ); ?></p>
			<p><input class='beta-checkbox' name='beta-checkbox' type='checkbox' value='1' <?php checked( $beta, 1 ); ?> /> <?php printf( '%s <strong>%s</strong> %s.', $enable_txt, $beta_txt, $updates_txt ); ?></p>
			<?php if ( $beta ) : ?>
			<p><input class='alpha-checkbox' name='alpha-checkbox' type='checkbox' value='1' <?php checked( $alpha, 1 ); ?> /> <?php printf( '%s <strong>%s</strong> %s.', $enable_txt, $alpha_txt, $updates_txt ); ?></p>
		<?php endif; ?>
		<p>
		<?php // translators: %s: Link to Docs ?>
		<?php printf( 'Please be sure to read our %s.', sprintf( "<a target='_blank' href='https://docs.wpbeaverbuilder.com/general/alpha-and-beta-releases/'>%s</a>", __( 'Prerelease Documentation', 'fl-builder' ) ) ); ?>
		</p>
		</div>
		<p class="submit">
			<input type="submit" name="update" class="button-primary" value="<?php esc_attr_e( 'Save Prerelease Settings', 'fl-builder' ); ?>" />
			<?php wp_nonce_field( 'beta', 'fl-beta-nonce' ); ?>
		</p>
	</form>
<?php endif; ?>

<?php
if ( FLBuilderUsage::show_settings() ) {
	$usage = get_site_option( 'fl_builder_usage_enabled', false );

	$endpoint = is_network_admin() ? 'settings.php?page=fl-builder-multisite-settings#tools' : 'options-general.php?page=fl-builder-settings#tools';
	$url      = wp_nonce_url( network_admin_url( $endpoint ), 'stats_enable' );
	if ( '1' == $usage ) {
		$text = __( 'Disable', 'fl-builder' );
		$url  = add_query_arg( array(
			'fl_usage' => 0,
		), $url );
	} else {
		$text = __( 'Enable', 'fl-builder' );
		$url  = add_query_arg( array(
			'fl_usage' => 1,
		), $url );
	}
	$btn = sprintf( '<a class="button button-primary" href="%s">%s</a>', $url, $text );
	?>
	<hr />
	<h3 class="fl-settings-form-header"><?php echo __( 'Send Usage Data', 'fl-builder' ); ?></h3>

	<p><?php _e( 'If enabled we will send anonymous usage stats to help improve the plugin.', 'fl-builder' ); ?></p>

	<p><?php echo $btn; ?></p>

	<?php echo FLBuilderUsage::data_demo(); ?>
	<?php } ?>

	<?php if ( is_network_admin() || ! self::multisite_support() ) : ?>

	<hr />

	<h3 class="fl-settings-form-header"><?php _e( 'Uninstall', 'fl-builder' ); ?></h3>

	<div class="fl-settings-form-content">
		<p><?php _e( 'Clicking the button below will uninstall the page builder plugin and delete all of the data associated with it. You can uninstall or deactivate the page builder from the plugins page instead if you do not wish to delete the data.', 'fl-builder' ); ?></p>
		<p><strong><?php _e( 'NOTE:', 'fl-builder' ); ?></strong> <?php _e( 'The builder does not delete the post meta <code>_fl_builder_data</code>, <code>_fl_builder_draft</code> and <code>_fl_builder_enabled</code> in case you want to reinstall it later. If you do, the builder will rebuild all of its data using those meta values.', 'fl-builder' ); ?></p>
		<?php if ( is_multisite() ) : ?>
		<p><strong><?php _e( 'NOTE:', 'fl-builder' ); ?></strong> <?php _e( 'This applies to all sites on the network.', 'fl-builder' ); ?></p>
		<?php endif; ?>
		<form id="uninstall-form" action="<?php FLBuilderAdminSettings::render_form_action( 'tools' ); ?>" method="post">
			<p class="submit">
				<input type="submit" name="uninstall-submit" class="button button-primary" value="<?php _e( 'Uninstall', 'fl-builder' ); ?>">
				<?php wp_nonce_field( 'uninstall', 'fl-uninstall' ); ?>
			</p>
		</form>
	</div>

	<?php endif; ?>

</div>
