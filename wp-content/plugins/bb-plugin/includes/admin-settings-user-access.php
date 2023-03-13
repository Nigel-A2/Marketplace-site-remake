<?php $raw_settings = FLBuilderUserAccess::get_raw_settings(); ?>
<div id="fl-user-access-form" class="fl-settings-form">

	<h3 class="fl-settings-form-header"><?php _e( 'User Access Settings', 'fl-builder' ); ?></h3>
	<p><?php _e( 'Use these settings to limit which builder features users can access.', 'fl-builder' ); ?></p>

	<form id="editing-form" action="<?php FLBuilderAdminSettings::render_form_action( 'user-access' ); ?>" method="post">
		<div class="fl-settings-form-content">
			<?php foreach ( FLBuilderUserAccess::get_grouped_registered_settings() as $group => $group_data ) : ?>

				<div class="fl-user-access-group">
					<h3><?php echo $group; ?></h3>
					<?php $i = 1; foreach ( $group_data as $cap => $cap_data ) : ?>
						<div class="fl-user-access-setting">
							<h4><?php echo $cap_data['label']; ?><i class="dashicons dashicons-editor-help" title="<?php echo esc_html( $cap_data['description'] ); ?>"></i></h4>
							<?php if ( FLBuilderAdminSettings::multisite_support() && ! is_network_admin() ) : ?>
							<label class="fl-ua-override-ms-label">
								<input class="fl-ua-override-ms-cb" type="checkbox" name="fl_ua_override_ms[<?php echo $cap; ?>]" value="1" <?php echo ( isset( $raw_settings[ $cap ] ) ) ? 'checked' : ''; ?> />
								<?php _e( 'Override network settings?', 'fl-builder' ); ?>
							</label>
							<?php endif; ?>
							<select name="fl_user_access[<?php echo $cap; ?>][]" class="fl-user-access-select" multiple></select>
						</div>
						<?php if ( 0 === $i % 2 || count( $group_data ) == $i ) : ?>
						<div class="clear"></div>
						<?php endif; ?>
						<?php
						$i++;
						endforeach;
					?>
				</div>

			<?php endforeach; ?>
		</div>
		<p class="submit">
			<input type="submit" name="update" class="button-primary" value="<?php esc_attr_e( 'Save User Access Settings', 'fl-builder' ); ?>" />
			<?php wp_nonce_field( 'user-access', 'fl-user-access-nonce' ); ?>
		</p>
	</form>
</div>
