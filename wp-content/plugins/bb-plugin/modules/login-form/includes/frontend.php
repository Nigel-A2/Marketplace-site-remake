<?php if ( ! is_user_logged_in() || FLBuilderModel::is_builder_active() ) : ?>
<div class="fl-login-form fl-login-form-<?php echo $settings->layout; ?> fl-form fl-clearfix login" <?php if ( isset( $module->template_id ) ) { echo 'data-template-id="' . $module->template_id . '" data-template-node-id="' . $module->template_node_id . '"';} ?>><?php // @codingStandardsIgnoreLine ?>
	<?php wp_nonce_field( 'fl-login-form', 'fl-login-form-nonce' ); ?>
	<div class="fl-form-field">
		<input type="text" name="fl-login-form-name" placeholder="<?php echo esc_attr( $settings->name_field_text ); ?>" aria-label="name" />
		<div class="fl-form-error-message"><?php _e( 'Please enter your username/email.', 'fl-builder' ); ?></div>
	</div>

	<div class="fl-form-field">
		<input type="password" name="fl-login-form-password" placeholder="<?php echo esc_attr( $settings->password_field_text ); ?>" aria-label="password" />
		<div class="fl-form-error-message"><?php _e( 'Please enter your password.', 'fl-builder' ); ?></div>
	</div>

	<?php if ( 'stacked' === $settings->layout ) : ?>
		<?php if ( isset( $settings->forget ) && 'yes' === $settings->forget ) : ?>
		<div class="fl-input-field fl-remember-forget">
			<a href="<?php echo wp_lostpassword_url( get_permalink() ); ?>" title="Lost Password"><?php _e( 'Forgot?', 'fl-builder' ); ?></a>
		</div>
		<?php endif; ?>

		<?php if ( isset( $settings->remember ) && 'yes' === $settings->remember ) : ?>
		<div class="fl-input-field fl-remember-checkbox">
			<label for="fl-login-checkbox-<?php echo $id; ?>">
				<input type="checkbox" name="fl-login-form-remember" value="1" /><span class="fl-remember-checkbox-text"><?php _e( 'Remember me', 'fl-builder' ); ?></span>
			</label>
		</div>
		<?php endif; ?>
		<div class="fl-form-button" data-wait-text="<?php esc_attr_e( 'Please Wait...', 'fl-builder' ); ?>">
		<?php FLBuilder::render_module_html( 'button', $module->get_button_settings( 'btn_' ) ); ?>
		</div>
	<?php else : ?>
		<div class="fl-form-button" data-wait-text="<?php esc_attr_e( 'Please Wait...', 'fl-builder' ); ?>">
		<?php FLBuilder::render_module_html( 'button', $module->get_button_settings( 'btn_' ) ); ?>
		</div>

	<?php endif; ?>

	<div class="fl-form-error-message"><?php _e( 'Something went wrong. Please check your entries and try again.', 'fl-builder' ); ?></div>

</div>
<?php else : ?>
	<div class="fl-login-form fl-login-form-<?php echo $settings->layout; ?> fl-form fl-clearfix logout" <?php if ( isset( $module->template_id ) ) { echo 'data-template-id="' . $module->template_id . '" data-template-node-id="' . $module->template_node_id . '"';} ?>><?php // @codingStandardsIgnoreLine ?>
		<?php wp_nonce_field( 'fl-login-form', 'fl-login-form-nonce' ); ?>
		<div class="fl-form-button log-out" data-wait-text="<?php esc_attr_e( 'Please Wait...', 'fl-builder' ); ?>">
		<?php FLBuilder::render_module_html( 'button', $module->get_button_settings( 'lo_btn_' ) ); ?>

		</div>

	</div>
<?php endif; ?>
