(function($) {

	$(function() {

		new FLBuilderLoginForm({
			id: '<?php echo $id; ?>',
			lo_url: '<?php echo esc_url( $settings->lo_success_url ); ?>',
		});
	});

})(jQuery);
