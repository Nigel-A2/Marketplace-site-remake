.fl-node-<?php echo $id; ?> .fl-separator {
	border-top-width: <?php echo $settings->height; ?>px;
	border-top-style: <?php echo $settings->style; ?>;
	border-top-color: <?php echo FLBuilderColor::hex_or_rgb( $settings->color ); ?>;
	max-width: <?php echo $settings->width . $settings->width_unit; ?>;
	margin: <?php echo $settings->align; ?>;
}

<?php if ( $global_settings->responsive_enabled ) { ?>
	@media (max-width: <?php echo $global_settings->medium_breakpoint; ?>px) {
		.fl-node-<?php echo $id; ?> .fl-separator {
			<?php if ( ! empty( $settings->height_medium ) ) { ?>
				border-top-width: <?php echo $settings->height_medium; ?>px;
			<?php } ?>
			<?php if ( ! empty( $settings->width_medium ) ) { ?>
				max-width: <?php echo $settings->width_medium . $settings->width_medium_unit; ?>;
			<?php } ?>
			<?php if ( ! empty( $settings->align_medium ) ) { ?>
				margin: <?php echo $settings->align_medium; ?>;
			<?php } ?>
		}
	}
	@media (max-width: <?php echo $global_settings->responsive_breakpoint; ?>px) {
		.fl-node-<?php echo $id; ?> .fl-separator {
			<?php if ( ! empty( $settings->height_responsive ) ) { ?>
				border-top-width: <?php echo $settings->height_responsive; ?>px;
			<?php } ?>
			<?php if ( ! empty( $settings->width_responsive ) ) { ?>
				max-width: <?php echo $settings->width_responsive . $settings->width_responsive_unit; ?>;
			<?php } ?>
			<?php if ( ! empty( $settings->align_responsive ) ) { ?>
				margin: <?php echo $settings->align_responsive; ?>;
			<?php } ?>
		}
	}
<?php } ?>
