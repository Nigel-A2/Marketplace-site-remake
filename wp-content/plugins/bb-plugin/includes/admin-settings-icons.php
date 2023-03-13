<div id="fl-icons-form" class="fl-settings-form">

	<h3 class="fl-settings-form-header"><?php _e( 'Icon Settings', 'fl-builder' ); ?></h3>

	<?php
	$fa5_pro_enabled = get_option( '_fl_builder_enable_fa_pro', false );
	$legacy          = apply_filters( 'fl_enable_fa5_pro', false );
	$kit_checked     = ( $fa5_pro_enabled ) ? 'checked="checked"' : '';

	if ( FLBuilderAdminSettings::multisite_support() && ! is_network_admin() ) {

		global $blog_id;

		if ( BLOG_ID_CURRENT_SITE == $blog_id ) {
			?>
			<p><?php _e( 'Icons for the main site must be managed in the network admin.', 'fl-builder' ); ?></p>
			</div>
			<?php
			return;
		}
	}

	?>

	<form id="icons-form" action="<?php FLBuilderAdminSettings::render_form_action( 'icons' ); ?>" method="post">

		<?php if ( FLBuilderAdminSettings::multisite_support() && ! is_network_admin() ) : ?>
		<label>
			<input class="fl-override-ms-cb" type="checkbox" name="fl-override-ms" value="1" <?php echo ( get_option( '_fl_builder_enabled_icons' ) ) ? 'checked="checked"' : ''; ?> />
			<?php _e( 'Override network settings?', 'fl-builder' ); ?>
		</label>
		<?php endif; ?>

		<div class="fl-settings-form-content">
			<?php /* translators: %s: docs link */ ?>
			<p><?php printf( __( 'Enable or disable icon sets using the options below or upload a custom icon set. Instructions on how to generate your own custom icon sets can be read %s.', 'fl-builder' ), sprintf( '<a href="https://docs.wpbeaverbuilder.com/beaver-builder/styles/icons/create-and-import-a-custom-icon-set/" target="_blank">%s</a>', _x( 'here', 'Link text', 'fl-builder' ) ) ); ?></p>
			<p><?php _e( 'If an icon is being used in a supported module its CSS will be enqueued. Deselecting sets here only removes the set from the settings UI.', 'fl-builder' ); ?></p>
			<?php

			$enabled_icons = FLBuilderModel::get_enabled_icons();
			$icon_sets     = FLBuilderIcons::get_sets_for_current_site();

			foreach ( $icon_sets as $key => $set ) {
				$checked = in_array( $key, $enabled_icons ) ? ' checked' : '';
				?>
				<p>
					<label>
						<input type="checkbox" name="fl-enabled-icons[]" value="<?php echo $key; ?>" <?php echo $checked; ?>>
						<?php echo ' ' . $set['name']; ?>
						<?php if ( 'core' != $set['type'] ) : ?>
						<a href="javascript:void(0);" class="fl-delete-icon-set" data-set="<?php echo $key; ?>"><?php _ex( 'Delete', 'Plugin setup page: Delete icon set.', 'fl-builder' ); ?></a>
						<?php endif; ?>
					</label>
				</p>
				<?php
			}

			?>
			<hr />
			<?php if ( ! FLBuilderFontAwesome::is_installed() ) : ?>
			<p>
				<?php if ( $legacy ) : ?>
					<?php _e( 'Font Awesome PRO already enabled via fl_enable_fa5_pro filter.', 'fl-builder' ); ?>
				<?php else : ?>
				<input type="checkbox" name="fl-enable-fa-pro" <?php echo $kit_checked; ?> /> <?php _e( 'Enable Font Awesome PRO icons.', 'fl-builder' ); ?>
			<?php endif; ?>
			</p>
				<?php if ( $fa5_pro_enabled || $legacy ) : ?>
				<p>
				<input style="width:300px;" placeholder="https://kit.fontawesome.com/nnnnnn.js" type="text" name="fl-fa-pro-kit" value="<?php echo esc_attr( get_option( '_fl_builder_kit_fa_pro' ) ); ?>" />
				<br /><?php _e( 'For KIT support enter the kit URL here otherwise the Pro CDN will be used.', 'fl-builder' ); ?>
				<br /><br /><strong><?php _e( 'Note: KIT must be set to Webfont and not SVG.', 'fl-builder' ); ?></strong>
				</p>
				<p>
					<?php _e( 'If you do not see a colored star icon below there may be an issue with your KIT URL, or you have not added your sites domain to the CDN settings at fontawesome.com', 'fl-builder' ); ?>
				</p>
				<p><i class="fad fa-star fa-3x" style="--fa-primary-color:yellow;--fa-secondary-color:orange;--fa-secondary-opacity:1" ></i></p>
				<?php endif; ?>
				<hr />
			<?php else : ?>
				<?php $data = FLBuilderFontAwesome::get_fa_data(); ?>
				<h4>Font Awesome Integration</h4>
				<ul>
					<?php
					foreach ( $data as $k => $item ) {
						printf( '<li><strong>%s</strong>: %s</li>', $item['name'], $item['value'] );
					}
					?>
				</ul>
			<?php endif; ?>
		</div>
		<p class="submit">
			<input type="button" name="fl-upload-icon" class="button" value="<?php esc_attr_e( 'Upload Icon Set', 'fl-builder' ); ?>" />
			<input type="submit" name="fl-save-icons" class="button-primary" value="<?php esc_attr_e( 'Save Icon Settings', 'fl-builder' ); ?>" />
			<input type="hidden" name="fl-new-icon-set" value="" />
			<input type="hidden" name="fl-delete-icon-set" value="" />
			<?php wp_nonce_field( 'icons', 'fl-icons-nonce' ); ?>
		</p>
	</form>
</div>
