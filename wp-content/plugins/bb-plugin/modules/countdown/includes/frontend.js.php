<?php

	// set defaults
	$time = $module->get_time();
	$type = $settings->layout;

?>

(function($) {

	$(function() {

		new FLBuilderCountdown({
			id: '<?php echo $id; ?>',
			time: '<?php echo $time; ?>',
			type: '<?php echo $type; ?>',
		});

	});

})(jQuery);
