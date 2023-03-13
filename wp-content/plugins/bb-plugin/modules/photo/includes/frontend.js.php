jQuery(function($) {
	<?php if ( 'lightbox' == $settings->link_type ) : ?>
	if (typeof $.fn.magnificPopup !== 'undefined') {
		$('.fl-node-<?php echo $id; ?> a').magnificPopup({
			type: 'image',
			closeOnContentClick: true,
			closeBtnInside: false,
			tLoading: '',
			preloader: true,
			image: {
					titleSrc: function(item) {
						<?php if ( 'below' == $settings->show_caption || 'hover' == $settings->show_caption ) : ?>
							return $( item.el ).closest( '.fl-photo' ).find( '.fl-photo-caption' ).text();
						<?php endif; ?>
					}
			},
			callbacks: {
				open: function() {
					$('.mfp-preloader').html('<i class="fas fa-spinner fa-spin fa-3x fa-fw"></i>');
				}
			}
		});
	}
	<?php endif; ?>

	<?php if ( ! isset( $settings->title_hover ) || ( isset( $settings->title_hover ) && 'no' === $settings->title_hover ) ) : ?>
	$(function() {
		$( '.fl-node-<?php echo $id; ?> .fl-photo-img' )
			.on( 'mouseenter', function( e ) {
				$( this ).data( 'title', $( this ).attr( 'title' ) ).removeAttr( 'title' );
			} )
			.on( 'mouseleave', function( e ){
				$( this ).attr( 'title', $( this ).data( 'title' ) ).data( 'title', null );
			} );
	});
	<?php endif; ?>
});
