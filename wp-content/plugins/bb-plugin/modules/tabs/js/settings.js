(function($){

	FLBuilder.registerModuleHelper('tabs', {

		init: function() {
			var form        = $( '.fl-builder-settings' ),
				layout 		= form.find( 'select[name=layout]' ),
				borderWidth = form.find( 'input[name=border_width]' );

			layout.on( 'change', this._layoutChange );
			borderWidth.on( 'input', this._borderWidthChange );
		},

		_layoutChange: function() {
			var wrap	= FLBuilder.preview.elements.node.find( '.fl-tabs' ),
				form    = $( '.fl-builder-settings' ),
				layout 	= form.find( 'select[name=layout]' ).val();

			if ( 'horizontal' === layout ) {
				wrap.addClass( 'fl-tabs-horizontal' );
				wrap.removeClass( 'fl-tabs-vertical' );
			} else {
				wrap.addClass( 'fl-tabs-vertical' );
				wrap.removeClass( 'fl-tabs-horizontal' );
			}
		},

		_borderWidthChange: function() {
			var preview		= FLBuilder.preview,
				form        = $( '.fl-builder-settings' ),
				layout 		= form.find( 'select[name=layout]' ).val(),
				borderWidth = form.find( 'input[name=border_width]' ).val(),
				selector	= preview.classes.node + ' .fl-tabs-labels .fl-tabs-label.fl-tab-active::after';

			if ( 'horizontal' === layout ) {
				preview.updateCSSRule( selector, {
					bottom: -borderWidth + 'px',
					height: borderWidth + 'px',
				} );
			} else {
				preview.updateCSSRule( selector, {
					right: -borderWidth + 'px',
					width: borderWidth + 'px',
				} );
			}
		}
	});

})(jQuery);
