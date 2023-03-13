( function( $ ) {

	FLBuilder.registerModuleHelper( 'login-form', {

		init: function()
		{
			$( 'input[name=btn_bg_color]' ).on( 'change', this._previewButtonBackground );
		},

		submit: function()
		{
			return true;
		},

		_previewButtonBackground: function( e ) {
			var preview	= FLBuilder.preview,
				selector = preview.classes.node + ' a.fl-button, ' + preview.classes.node + ' a.fl-button:visited',
				form = $( '.fl-builder-settings:visible' ),
				style = form.find( 'select[name=btn_style]' ).val(),
				bgColor = form.find( 'input[name=btn_bg_color]' ).val();

			if ( 'flat' === style ) {
				if ( '' !== bgColor && bgColor.indexOf( 'rgb' ) < 0 ) {
					bgColor = '#' + bgColor;
				}
				preview.updateCSSRule( selector, 'background-color', bgColor );
				preview.updateCSSRule( selector, 'border-color', bgColor );
			} else {
				preview.delayPreview( e );
			}
		},
	});

})(jQuery);
