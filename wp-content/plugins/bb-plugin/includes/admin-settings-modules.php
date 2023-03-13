<?php
if ( FLBuilder::is_module_disable_enabled() ) {
	$used_modules = array();

	$args = array(
		'post_type'      => FLBuilderModel::get_post_types(),
		'post_status'    => 'publish',
		'meta_key'       => '_fl_builder_enabled',
		'meta_value'     => '1',
		'posts_per_page' => -1,
	);

	$query           = new WP_Query( $args );
	$data['enabled'] = count( $query->posts );

	/**
	* Using the array of pages/posts using builder get a list of all used modules
	*/
	if ( is_array( $query->posts ) && ! empty( $query->posts ) ) {
		foreach ( $query->posts as $post ) {
			$meta = get_post_meta( $post->ID, '_fl_builder_data', true );
			foreach ( (array) $meta as $node_id => $node ) {
				if ( @isset( $node->type ) && 'module' === $node->type ) { // @codingStandardsIgnoreLine
					if ( ! isset( $used_modules[ $node->settings->type ][ $post->post_type ] ) ) {
						$used_modules[ $node->settings->type ][ $post->post_type ] = array();
					}

					if ( ! isset( $used_modules[ $node->settings->type ][ $post->post_type ][ $post->ID ] ) ) {
						$used_modules[ $node->settings->type ][ $post->post_type ][ $post->ID ] = 1;
					} else {
						$used_modules[ $node->settings->type ][ $post->post_type ][ $post->ID ] ++;
					}


					if ( ! isset( $used_modules[ $node->settings->type ][ $post->post_type ]['total'] ) ) {
						$used_modules[ $node->settings->type ][ $post->post_type ]['total'] = 1;
					} else {
						$used_modules[ $node->settings->type ][ $post->post_type ]['total'] ++;
					}
				}
			}
		}
	}
}

?>
<div id="fl-modules-form" class="fl-settings-form">
	<h3 class="fl-settings-form-header"><?php _e( 'Enabled Modules', 'fl-builder' ); ?></h3>

	<form id="modules-form" action="<?php FLBuilderAdminSettings::render_form_action( 'modules' ); ?>" method="post">

		<?php if ( FLBuilderAdminSettings::multisite_support() && ! is_network_admin() ) : ?>
		<label>
			<input class="fl-override-ms-cb" type="checkbox" name="fl-override-ms" value="1" <?php echo ( get_option( '_fl_builder_enabled_modules' ) ) ? 'checked="checked"' : ''; ?> />
			<?php _e( 'Override network settings?', 'fl-builder' ); ?>
		</label>
		<?php endif; ?>

		<div class="fl-settings-form-content">

			<p><?php _e( 'Check or uncheck modules below to enable or disable them.', 'fl-builder' ); ?></p>
			<?php


			$categories      = FLBuilderModel::get_categorized_modules( true );
			$enabled_modules = FLBuilderModel::get_enabled_modules();
			$checked         = in_array( 'all', $enabled_modules ) ? 'checked' : '';

			?>
			<label>
				<input class="fl-module-all-cb" type="checkbox" name="fl-modules[]" value="all" <?php echo $checked; ?> />
				<?php _ex( 'All', 'Plugin setup page: Modules.', 'fl-builder' ); ?>
			</label>
			<?php foreach ( $categories as $title => $modules ) : ?>
			<h3><?php echo $title; ?></h3>
				<?php

				if ( __( 'WordPress Widgets', 'fl-builder' ) == $title ) :

					$checked = in_array( 'widget', $enabled_modules ) ? 'checked' : '';

					?>
				<p>
					<label>
						<input class="fl-module-cb" type="checkbox" name="fl-modules[]" value="widget" <?php echo $checked; ?> />
						<?php echo $title; ?>
					</label>
				</p>
					<?php

					continue;

				endif;
				foreach ( $modules as $module ) :

					$checked = in_array( $module->slug, $enabled_modules ) ? 'checked' : '';

					?>
				<p>
					<label>
						<input class="fl-module-cb" type="checkbox" name="fl-modules[]" value="<?php echo $module->slug; ?>" <?php echo $checked; ?> />
						<?php
						$text = 'Not used';
						if ( isset( $used_modules[ $module->slug ] ) ) {
							$txt = array();
							foreach ( $used_modules[ $module->slug ] as $type => $used ) {
								$type  = str_replace( 'fl-theme-layout', 'Themer Layout', $type );
								$type  = str_replace( 'fl-builder-template', 'Builder Template', $type );
								$txt[] = sprintf( '%s times on %s %ss', $used['total'], count( $used ) - 1, ucfirst( $type ) );
							}
							$text = implode( ', ', $txt );
						}
						?>
						<?php echo ( FLBuilder::is_module_disable_enabled() ) ? sprintf( '%s ( %s )', $module->name, $text ) : $module->name; ?>
					</label>
				</p>
				<?php endforeach; ?>
			<?php endforeach; ?>
		</div>
		<p class="submit">
			<input type="submit" name="update" class="button-primary" value="<?php esc_attr_e( 'Save Module Settings', 'fl-builder' ); ?>" />
			<?php wp_nonce_field( 'modules', 'fl-modules-nonce' ); ?>
		</p>
	</form>
</div>
