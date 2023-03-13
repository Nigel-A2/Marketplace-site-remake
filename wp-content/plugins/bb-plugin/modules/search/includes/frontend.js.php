(function($) {

	$(function() {
		new FLBuilderSearchForm({
			id: '<?php echo $id; ?>',
			layout: '<?php echo $settings->layout; ?>',
			btnAction: '<?php echo $settings->btn_action; ?>',
			result: '<?php echo $settings->result; ?>',
			showCloseBtn: <?php echo 'show' == $settings->fs_close_button ? 'true' : 'false'; ?>,
		});
	});

})(jQuery);
