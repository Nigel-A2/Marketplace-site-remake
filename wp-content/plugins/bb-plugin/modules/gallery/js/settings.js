( function( $ ) {

	FLBuilder.registerModuleHelper( 'gallery', {

		init: function() {
			var form   	= $( '.fl-builder-settings' ),
				spacing = form.find( 'input[name=photo_spacing]' );

			spacing.on( 'input', this._previewSpacing );
		},

		_previewSpacing: function( e ) {
			var preview	= FLBuilder.preview,
				form    = $( '.fl-builder-settings' ),
				layout 	= form.find( 'select[name=layout]' ).val(),
				spacing = form.find( 'input[name=photo_spacing]' ).val();

			if ( 'collage' === layout ) {
				spacing = '' === spacing ? 0 : spacing;
				preview.updateCSSRule( preview.classes.node + ' .fl-mosaicflow', 'margin-left', '-' + spacing + 'px' );
				preview.updateCSSRule( preview.classes.node + ' .fl-mosaicflow-item', 'margin', '0 0 ' + spacing + 'px ' + spacing + 'px' );
			} else {
				preview.delayPreview( e );
			}
		},
	} );

} )( jQuery );
