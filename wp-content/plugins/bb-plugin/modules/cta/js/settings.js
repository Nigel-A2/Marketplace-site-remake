(function($){

	FLBuilder.registerModuleHelper('cta', {

		init: function() {
			var form = $( '.fl-builder-settings' ),
				layout = form.find( 'select[name=layout]' ),
				buttonBgColor = form.find( 'input[name=btn_bg_color]' ),
				icon = form.find( 'input[name=btn_icon]' )
			icon.on( 'change', this._flipSettings );
			layout.on( 'change', this._layoutChange );
			buttonBgColor.on( 'change', this._previewButtonBackground );
			this._flipSettings()
		},

		_flipSettings: function() {
			var form  = $( '.fl-builder-settings' ),
					icon = form.find( 'input[name=btn_icon]' );
			if ( -1 !== icon.val().indexOf( 'fad fa') ) {
				$('#fl-field-btn_duo_color1').show();
				$('#fl-field-btn_duo_color2').show();
			} else {
				$('#fl-field-btn_duo_color1').hide();
				$('#fl-field-btn_duo_color2').hide();
			}
		},

		_layoutChange: function() {
			var node = FLBuilder.preview.elements.node,
				wrap = node.find( '.fl-cta-wrap' ),
				button = node.find( '.fl-button-wrap' ),
				form = $( '.fl-builder-settings' ),
				layout = form.find( 'select[name=layout]' ).val(),
				alignment = form.find( 'input[name=alignment]' );

			if ( 'inline' === layout ) {
				wrap.removeClass( 'fl-cta-stacked fl-cta-left fl-cta-center fl-cta-right' );
				wrap.addClass( 'fl-cta-inline' );
				button.removeClass( 'fl-button-width-auto' );
				button.addClass( 'fl-button-width-full' );
				FLBuilder.preview.updateCSSRule( FLBuilder.preview.classes.node + ' .fl-cta-wrap', 'text-align', '' );
			} else {
				wrap.removeClass( 'fl-cta-inline' );
				wrap.addClass( 'fl-cta-stacked' );
				button.removeClass( 'fl-button-width-full' );
				button.addClass( 'fl-button-width-auto' );
				alignment.trigger( 'change' );
			}
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
