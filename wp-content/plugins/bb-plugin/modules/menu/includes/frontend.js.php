<?php

	// set defaults
	$type              = isset( $settings->menu_layout ) ? $settings->menu_layout : 'horizontal';
	$mobile            = isset( $settings->mobile_toggle ) ? $settings->mobile_toggle : 'expanded';
	$below_row         = 'below' == $settings->mobile_full_width ? 'true' : 'false';
	$flyout_menu       = $module->is_responsive_menu_flyout() ? 'true' : 'false';
	$mobile_breakpoint = isset( $settings->mobile_breakpoint ) ? $settings->mobile_breakpoint : 'mobile';
	$post_id           = FLBuilderModel::get_post_id();
	$mobile_stacked    = isset( $settings->mobile_stacked ) && 'no' === $settings->mobile_stacked ? 'false' : 'true';
?>

(function($) {

	$(function() {

		new FLBuilderMenu({
			id: '<?php echo $id; ?>',
			type: '<?php echo $type; ?>',
			mobile: '<?php echo $mobile; ?>',
			mobileBelowRow: <?php echo $below_row; ?>,
			mobileFlyout: <?php echo $flyout_menu; ?>,
			breakPoints: {
				medium: <?php echo FLBuilderUtils::sanitize_number( $global_settings->medium_breakpoint ); ?>,
				small: <?php echo FLBuilderUtils::sanitize_number( $global_settings->responsive_breakpoint ); ?>
			},
			mobileBreakpoint: '<?php echo $mobile_breakpoint; ?>',
			postId : '<?php echo $post_id; ?>',
			mobileStacked: <?php echo $mobile_stacked; ?>,
		});

	});

})(jQuery);
