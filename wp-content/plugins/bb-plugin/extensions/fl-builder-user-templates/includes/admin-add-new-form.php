<div class="wrap">

	<h1><?php _e( 'Add New', 'fl-builder' ); ?></h1>

	<p><?php _e( 'Add new builder content using the form below.', 'fl-builder' ); ?></p>

	<form class="fl-new-template-form" name="fl-new-template-form" method="POST">

		<table class="widefat">

			<tr>
				<th>
					<label for="fl-template[title]"><?php _e( 'Title', 'fl-builder' ); ?></label>
				</th>
				<td>
					<input class="fl-template-title regular-text" type="text" name="fl-template[title]" required />
				</td>
			</tr>

			<tr>
				<th>
					<label for="fl-template[type]"><?php _e( 'Type', 'fl-builder' ); ?></label>
				</th>
				<td>
					<select class="fl-template-type" name="fl-template[type]" required>
						<option value=""><?php _e( 'Choose...', 'fl-builder' ); ?></option>
						<?php foreach ( $types as $type ) : ?>
						<option value="<?php echo $type['key']; ?>" <?php selected( $selected_type, $type['key'] ); ?>><?php echo $type['label']; ?></option>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>

			<tr class="fl-template-module-row">
				<th>
					<label for="fl-template[module]"><?php _e( 'Module', 'fl-builder' ); ?></label>
				</th>
				<td>
					<select class="fl-template-module" name="fl-template[module]" required>
						<option value=""><?php _e( 'Choose...', 'fl-builder' ); ?></option>
						<?php foreach ( $modules as $title => $group ) : ?>
							<?php
							if ( __( 'WordPress Widgets', 'fl-builder' ) == $title ) {
								continue;
							}
							?>
						<optgroup label="<?php echo $title; ?>">
							<?php foreach ( $group as $module ) : ?>
							<option value="<?php echo $module->slug; ?>"><?php echo $module->name; ?></option>
							<?php endforeach; ?>
						</optgroup>
						<?php endforeach; ?>
					</select>
				</td>
			</tr>

			<tr class="fl-template-global-row">
				<th>
					<label for="fl-template[global]"><?php _e( 'Global', 'fl-builder' ); ?></label>
					<i class="dashicons dashicons-editor-help" title="<?php esc_html_e( 'Global rows, columns and modules can be added to multiple pages and edited in one place.', 'fl-builder' ); ?>"></i>
				</th>
				<td>
					<label>
						<input class="fl-template-global" type="checkbox" name="fl-template[global]" value="1" />
						<?php _e( 'Make this saved row or module global?', 'fl-builder' ); ?>
					</label>
				</td>
			</tr>

			<?php do_action( 'fl_builder_user_templates_admin_add_form' ); ?>

		</table>

		<p class="submit">
			<input type="submit" class="fl-template-add button button-primary button-large" value="<?php _e( 'Add', 'fl-builder' ); ?>">
		</p>

		<?php wp_nonce_field( 'fl-add-template-nonce', 'fl-add-template' ); ?>

	</form>
</div>
