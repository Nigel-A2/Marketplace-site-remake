<?php
$button_node_id = "fl-node-$id";

if ( isset( $settings->click_action ) && 'lightbox' == $settings->click_action ) : ?>
(function($){
	$('.<?php echo $button_node_id; ?> .fl-button-lightbox').magnificPopup({
		<?php if ( 'video' == $settings->lightbox_content_type ) : ?>
		type: 'iframe',
		mainClass: 'fl-button-lightbox-wrap',
		<?php endif; ?>

		<?php if ( 'html' == $settings->lightbox_content_type ) : ?>
		type: 'inline',
		items: {
			src: $('.<?php echo $button_node_id; ?> .fl-button-lightbox-content')[0]
		},
		callbacks: {
			open: function() {
				var divWrap = $( $(this.content)[0] ).find('> div');
				divWrap.css('display', 'block');

				// Triggers select change in we have multiple forms in a page
				if ( divWrap.find('form select').length > 0 ) {
					divWrap.find('form select').trigger('change');
				}
			},
		},
		<?php endif; ?>
		closeBtnInside: true,
		tLoading: '<i class="fas fa-spinner fa-spin fa-3x fa-fw"></i>',
	});
})(jQuery);
<?php endif; ?>
