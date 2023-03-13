<?php

$subscribe_form_fields = apply_filters( 'fl_builder_subscribe_form_fields', array(
	'form_name'   => 'fl-subscribe-form-name',
	'form_email'  => 'fl-subscribe-form-email',
	'name_error'  => 'name-error',
	'email_error' => 'email-error',
), $settings );

?>
<div class="fl-subscribe-form fl-subscribe-form-<?php echo $settings->layout; ?> fl-subscribe-form-name-<?php echo $settings->show_name; ?> fl-form fl-clearfix" <?php if ( isset( $module->template_id ) ) { echo 'data-template-id="' . $module->template_id . '" data-template-node-id="' . $module->template_node_id . '"';} ?>><?php // @codingStandardsIgnoreLine ?>

	<?php if ( 'show' == $settings->show_name ) : ?>
	<div class="fl-form-field">
		<label for="<?php echo esc_attr( $subscribe_form_fields['form_name'] ); ?>" class="hidden"><?php echo esc_attr( $settings->name_field_text ); ?></label>
		<input type="text" aria-describedby="name-error" name="fl-subscribe-form-name" placeholder="<?php echo esc_attr( $settings->name_field_text ); ?>" id="<?php echo esc_attr( $subscribe_form_fields['form_name'] ); ?>" class="fl-subscribe-form-name" />
		<div class="fl-form-error-message" id="<?php echo esc_attr( $subscribe_form_fields['name_error'] ); ?>"><?php _e( 'Please enter your name.', 'fl-builder' ); ?></div>
	</div>
	<?php endif; ?>

	<div class="fl-form-field">
		<label for="<?php echo esc_attr( $subscribe_form_fields['form_email'] ); ?>" class="hidden"><?php echo esc_attr( $settings->email_field_text ); ?></label>
		<input type="email" aria-describedby="email-error" name="fl-subscribe-form-email" placeholder="<?php echo esc_attr( $settings->email_field_text ); ?>" id="<?php echo esc_attr( $subscribe_form_fields['form_email'] ); ?>"  class="fl-subscribe-form-email"  />
		<div class="fl-form-error-message" id="<?php echo esc_attr( $subscribe_form_fields['email_error'] ); ?>"><?php _e( 'Please enter a valid email address.', 'fl-builder' ); ?></div>
	</div>

	<?php if ( 'stacked' == $settings->layout ) : ?>
		<?php if ( 'show' == $settings->terms_checkbox ) : ?>
			<div class="fl-form-field fl-terms-checkbox">
				<?php if ( isset( $settings->terms_text ) && ! empty( $settings->terms_text ) ) : ?>
					<div class="fl-terms-checkbox-text"><?php echo $settings->terms_text; ?></div>
				<?php endif; ?>
				<div class="fl-terms-checkbox-wrap">
					<input type="checkbox" id="fl-terms-checkbox-<?php echo $id; ?>" name="fl-terms-checkbox" value="1" />
					<label for="fl-terms-checkbox-<?php echo $id; ?>"><?php echo $settings->terms_checkbox_text; ?></label>
				</div>
				<div class="fl-form-error-message"><?php _e( 'You must accept the Terms and Conditions.', 'fl-builder' ); ?></div>
			</div>
		<?php endif; ?>

		<?php if ( 'show' == $settings->show_recaptcha && ( isset( $settings->recaptcha_site_key ) && ! empty( $settings->recaptcha_site_key ) ) ) : ?>
		<div class="fl-form-field fl-form-recaptcha">
			<div class="fl-form-error-message"><?php _e( 'Please check the captcha to verify you are not a robot.', 'fl-builder' ); ?></div>
			<div id="<?php echo $id; ?>-fl-grecaptcha" class="fl-grecaptcha"<?php $module->recaptcha_data_attributes(); ?>></div>
		</div>
		<?php endif; ?>
	<?php endif; ?>

	<div class="fl-form-button" data-wait-text="<?php esc_attr_e( 'Please Wait...', 'fl-builder' ); ?>">
	<?php FLBuilder::render_module_html( 'button', $module->get_button_settings() ); ?>
	</div>

	<?php if ( 'inline' == $settings->layout ) : ?>
		<?php if ( 'show' == $settings->terms_checkbox ) : ?>
			<div class="fl-form-field fl-terms-checkbox">
				<?php if ( isset( $settings->terms_text ) && ! empty( $settings->terms_text ) ) : ?>
					<div class="fl-terms-checkbox-text"><?php echo $settings->terms_text; ?></div>
				<?php endif; ?>
				<div class="fl-terms-checkbox-wrap">
					<input type="checkbox" id="fl-terms-checkbox-<?php echo $id; ?>" name="fl-terms-checkbox" value="1" />
					<label for="fl-terms-checkbox-<?php echo $id; ?>"><?php echo $settings->terms_checkbox_text; ?></label>
				</div>
				<div class="fl-form-error-message"><?php _e( 'You must accept the Terms and Conditions.', 'fl-builder' ); ?></div>
			</div>
		<?php endif; ?>

		<?php if ( 'show' == $settings->show_recaptcha && ( isset( $settings->recaptcha_site_key ) && ! empty( $settings->recaptcha_site_key ) ) ) : ?>
		<div class="fl-form-field fl-form-recaptcha">
			<div class="fl-form-error-message"><?php _e( 'Please check the captcha to verify you are not a robot.', 'fl-builder' ); ?></div>
			<div id="<?php echo $id; ?>-fl-grecaptcha" class="fl-grecaptcha"<?php $module->recaptcha_data_attributes(); ?>></div>
		</div>
		<?php endif; ?>
	<?php endif; ?>

	<div class="fl-form-success-message"><?php echo $settings->success_message; ?></div>

	<div class="fl-form-error-message"><?php _e( 'Something went wrong. Please check your entries and try again.', 'fl-builder' ); ?></div>

</div>
