(function($) {

	// Clear the controls in case they were already created.
	$('.fl-node-<?php echo $id; ?> .fl-slider-next').empty();
	$('.fl-node-<?php echo $id; ?> .fl-slider-prev').empty();

	// Create the slider.
	$('.fl-node-<?php echo $id; ?> .fl-testimonials').bxSlider({
		autoStart : <?php echo $settings->auto_play; ?>,
		auto : true,
		adaptiveHeight: true,
		pause : <?php echo $settings->pause * 1000; ?>,
		mode : '<?php echo $settings->transition; ?>',
		autoDirection: '<?php echo $settings->direction; ?>',
		speed : <?php echo $settings->speed * 1000; ?>,
		pager : <?php echo ( 'wide' == $settings->layout ) ? $settings->dots : 0; ?>,
		nextSelector : '.fl-node-<?php echo $id; ?> .fl-slider-next',
		prevSelector : '.fl-node-<?php echo $id; ?> .fl-slider-prev',
		nextText: '<i class="fas fa-chevron-circle-right"></i>',
		prevText: '<i class="fas fa-chevron-circle-left"></i>',
		controls : <?php echo ( 'compact' == $settings->layout ) ? $settings->arrows : 0; ?>,
		onSliderLoad: function(currentIndex) {
			$('.fl-node-<?php echo $id; ?> .fl-testimonials').addClass('fl-testimonials-loaded');
			$('.fl-node-<?php echo $id; ?> .fl-slider-next a').attr('aria-label', '<?php _e( 'Next testimonial.', 'fl-builder' ); ?>' );
			$('.fl-node-<?php echo $id; ?> .fl-slider-prev a').attr('aria-label', '<?php _e( 'Previous testimonial.', 'fl-builder' ); ?>' );
		},
		onSliderResize: function(currentIndex){
			this.working = false;
			this.reloadSlider();
		},
		onSlideBefore: function(ele, oldIndex, newIndex) {
			$('.fl-node-<?php echo $id; ?> .fl-slider-next a').addClass('disabled');
			$('.fl-node-<?php echo $id; ?> .fl-slider-prev a').addClass('disabled');
			$('.fl-node-<?php echo $id; ?> .bx-controls .bx-pager-link').addClass('disabled');
		},
		onSlideAfter: function( ele, oldIndex, newIndex ) {
			$('.fl-node-<?php echo $id; ?> .fl-slider-next a').removeClass('disabled'); 
			$('.fl-node-<?php echo $id; ?> .fl-slider-prev a').removeClass('disabled'); 
			$('.fl-node-<?php echo $id; ?> .bx-controls .bx-pager-link').removeClass('disabled');
		},
		onSlideNext: function(ele, oldIndex, newIndex) {
			$('.fl-node-<?php echo $id; ?> .fl-slider-next').attr( 'aria-pressed', 'true' );
			$('.fl-node-<?php echo $id; ?> .fl-slider-prev').attr( 'aria-pressed', 'false' );
		},
		onSlidePrev: function(ele, oldIndex, newIndex) {
			$('.fl-node-<?php echo $id; ?> .fl-slider-next').attr( 'aria-pressed', 'false' );
			$('.fl-node-<?php echo $id; ?> .fl-slider-prev').attr( 'aria-pressed', 'true' );
		}
	});

})(jQuery);
