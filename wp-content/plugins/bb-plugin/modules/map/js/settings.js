(function($){

	FLBuilder.registerModuleHelper( 'map', {

		init: function() {
			var form = $( '.fl-builder-settings' ),
				address = form.find( 'textarea[name=address]' ),
				height = form.find( 'input[name=height_responsive]' );

			address.on( 'input', this._previewAddress );
			height.on( 'input', this._previewResponsiveHeight );
		},

		_previewAddress: function() {
			var form = $( '.fl-builder-settings' ),
				address = form.find( 'textarea[name=address]' ).val(),
				q = '' === address ? 'United Kingdom' : address,
				url = 'https://www.google.com/maps/embed/v1/place?key=AIzaSyD09zQ9PNDNNy9TadMuzRV_UsPUoWKntt8&q=',
				iframe = $( FLBuilder.preview.classes.node + ' iframe' );

			iframe.attr( 'src', url + q );
		},

		_previewResponsiveHeight: function() {
			var form = $( '.fl-builder-settings' ),
				height = form.find( 'input[name=height]' ).val(),
				wrapper = $( FLBuilder.preview.classes.node + ' .fl-map' );

			if ( isNaN( height ) ) {
				wrapper.removeClass( 'fl-map-auto-responsive-disabled' );
			} else {
				wrapper.addClass( 'fl-map-auto-responsive-disabled' );
			}
		},
	});

})(jQuery);
