( function( $ ) {

	FLBuilder.registerModuleHelper( 'search', {

		init: function()
		{
			var form = $( '.fl-builder-settings:visible' ),
				icon = form.find( 'input[name=btn_icon]' ),
				btnAlign = form.find( 'input[name=btn_align]' );

			$( 'input[name=btn_bg_color]' ).on( 'change', this._previewButtonBackground );
			$( 'select[name=layout]' ).on( 'change', $.proxy( this._toggleBtnSettings, this ) );
			btnAlign.on( 'change', this._previewButtonAlign );
			icon.on( 'change', this._flipSettings );
			this._toggleBtnSettings();
			this._flipSettings();
		},

		_previewButtonBackground: function( e )
		{
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

		_toggleBtnSettings: function()
		{
			var form       = $( '.fl-builder-settings:visible' ),
			    layout     = form.find( 'select[name=layout]' ).val(),
				btnAction  = form.find( 'select[name=btn_action]' ),
				btnToggles = btnAction.data('toggle'),
				fields     = [];

			if( 'undefined' === typeof btnToggles ) {
				return;
			}

			for( var i in btnToggles ){
				if( 'undefined' === typeof btnToggles[i].fields ) {
					return;
				}

				fields = btnToggles[i].fields.map(function(val, i){
					return '#fl-field-' + val;
				});

				if( 'button' === layout && i === btnAction.val() ){
					$( fields.join(', ') ).show();
				}
				else {
					$( fields.join(', ') ).hide();
				}
			}
		},

		_previewButtonAlign: function( e )
		{
			var preview	 = FLBuilder.preview,
				selector = preview.classes.node + ' .fl-search-form-wrap, ' + preview.classes.node + ' .fl-search-form-fields',
				property = 'justify-content',
				form     = $( '.fl-builder-settings:visible' ),
				layout   = form.find( 'select[name=layout]' ).val(),
				bgAlign  = form.find( 'input[name=btn_align]' ).val();

			if ( 'stacked' == layout ) {
				selector = preview.classes.node + ' .fl-button-wrap';
				property = 'text-align';
			}

			preview.updateCSSRule( selector, property, bgAlign );
		},

		_flipSettings: function() {
			var form  = $( '.fl-builder-settings' ),
				icon  = form.find( 'input[name=btn_icon]' );
			if ( -1 !== icon.val().indexOf( 'fad fa') ) {
				$('#fl-field-btn_duo_color1').show();
				$('#fl-field-btn_duo_color2').show();
				$('#fl-builder-settings-section-button_icon_color').hide();
			} else {
				$('#fl-field-btn_duo_color1').hide();
				$('#fl-field-btn_duo_color2').hide();
				$('#fl-builder-settings-section-button_icon_color').show();
			}
		},

	});

})(jQuery);
