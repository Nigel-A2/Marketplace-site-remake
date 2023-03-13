<?php

for ( $i = 0; $i < count( $settings->items ); $i++ ) :
	if ( ! is_object( $settings->items[ $i ] ) ) {
		continue;
	}

	if ( isset( $settings->items[ $i ]->click_action ) && 'lightbox' == $settings->items[ $i ]->click_action ) :
		$alt_node = "fl-node-$id-$i";
		?>
		(function($){
			$('<?php echo ".$alt_node.fl-button-lightbox"; ?>').magnificPopup({
				<?php if ( 'video' == $settings->items[ $i ]->lightbox_content_type ) : ?>
				type: 'iframe',
				mainClass: 'fl-button-lightbox-wrap',
				<?php endif; ?>

				<?php if ( 'html' == $settings->items[ $i ]->lightbox_content_type ) : ?>
				type: 'inline',
				items: {
					src: $( '<?php echo ".$alt_node.fl-button-lightbox-content"; ?>' )[0]
				},
				callbacks: {
					open: function() {
						var divWrap = $( $(this.content)[0] ).find('> div');
						divWrap.css('display', 'block');

						// Triggers select change in we have multiple forms in a page
						if ( divWrap.find('form select').length > 0 ) {
							divWrap.find('form select').trigger('change');
						}
					}
				},
				<?php endif; ?>
				closeBtnInside: true,
				tLoading: '<i class="fas fa-spinner fa-spin fa-3x fa-fw"></i>'
			});
		})(jQuery);
		<?php
	endif;
endfor;
