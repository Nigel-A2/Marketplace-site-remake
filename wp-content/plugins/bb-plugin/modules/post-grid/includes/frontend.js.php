(function($) {

	$(function() {

		new FLBuilderPostGrid({
			id: '<?php echo $id; ?>',
			layout: '<?php echo $settings->layout; ?>',
			pagination: '<?php echo $settings->pagination; ?>',
			postSpacing: '<?php echo empty( $settings->post_spacing ) ? 60 : intval( $settings->post_spacing ); ?>',
			postWidth: '<?php echo empty( $settings->post_width ) ? 300 : intval( $settings->post_width ); ?>',
			matchHeight: {
				default	   : '<?php echo $settings->match_height; ?>',
				medium 	   : '<?php echo $settings->match_height_medium; ?>',
				responsive : '<?php echo $settings->match_height_responsive; ?>'
			},
			isRTL: <?php echo is_rtl() ? 'true' : 'false'; ?>
		});

		<?php if ( 'grid' == $settings->layout ) : ?>
			$('.fl-node-<?php echo $id; ?> .fl-post-<?php echo $settings->layout; ?>').masonry('reloadItems');
		<?php endif; ?>
	});

})(jQuery);
