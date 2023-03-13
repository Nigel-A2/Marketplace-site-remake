(function($) {

	$(function() {

		<?php if ( 'lightbox' == $settings->click_action ) : ?>
		if (typeof $.fn.magnificPopup !== 'undefined') {
			$('.fl-node-<?php echo $id; ?> .fl-mosaicflow-content, .fl-node-<?php echo $id; ?> .fl-gallery').magnificPopup({
				delegate: '.fl-photo-content > a',
				closeBtnInside: false,
				type: 'image',
				gallery: {
					enabled: true,
					navigateByImgClick: true,
				},
				'image': {
					titleSrc: function(item) {
						<?php if ( 'below' == $settings->show_captions ) : ?>
							return item.el.parent().next('.fl-photo-caption').text();
						<?php elseif ( 'hover' == $settings->show_captions ) : ?>
							return item.el.next('.fl-photo-caption').text();
						<?php endif; ?>
					}
				},
				callbacks: {
					open: function(){
						<?php if ( 'collage' == $settings->layout ) : ?>
						if ( this.items.length > 0 ) {
							var parent,
								item,
								newIndex = 0,
								newItems = [],
								currItem = this.currItem,
								newCurrItemIndex = -1;

							$(this.items).each(function(i, data){
								item = $(this);
								if ( 'undefined' !== typeof this.el ) {
									item = this.el;
								}
								parent = item.parents('.fl-mosaicflow-item');

								newIndex = $(parent).attr('id').split('-').pop();
								newIndex = newIndex > 0 ? newIndex - 1 : 0;
								newItems[newIndex] = this;

								if ( currItem.src === this.src ){
									newCurrItemIndex = newIndex;
								}
							});

							this.items = newItems;

							if ( newCurrItemIndex >= 0 ){
								this.goTo( newCurrItemIndex );
							}
						}
						<?php endif; ?>
					}
				}
			});
		}
		<?php endif; ?>

		<?php if ( 'collage' == $settings->layout ) : ?>
		$('.fl-node-<?php echo $id; ?> .fl-mosaicflow-content').one( 'mosaicflow-filled', function(){
			var hash = window.location.hash.replace( '#', '' );
			if ( hash != '' ) {
				FLBuilderLayout._scrollToElement( $( '#' + hash ) );
			}
			if ( 'undefined' != typeof Waypoint ) {
				Waypoint.refreshAll();
			}
		}).mosaicflow({
			itemSelector: '.fl-mosaicflow-item',
			columnClass: 'fl-mosaicflow-col',
			minItemWidth: <?php echo $settings->photo_size; ?>
		});
		<?php else : ?>
		$( '.fl-node-<?php echo $id; ?> .fl-gallery' ).imagesLoaded( function(){
			$('.fl-node-<?php echo $id; ?> .fl-gallery-item').wookmark({
				align: 'center',
				autoResize: true,
				container: $('.fl-node-<?php echo $id; ?> .fl-gallery'),
				offset: <?php echo $settings->photo_spacing; ?>,
				itemWidth: 150,
				verticalOffset: <?php echo ( 'below' == $settings->show_captions ) ? '35' : '0'; ?>
			});
		});
		<?php endif; ?>
	});

	jQuery(document).ready(function(){
		setTimeout(function(){
			jQuery('.fl-node-<?php echo $id; ?> .fl-mosaicflow-content').trigger('resize');
		},50);
	});

})(jQuery);
