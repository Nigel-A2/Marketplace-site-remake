(function($){

	FLBuilder.registerModuleHelper('button', {

		init: function() {
			var form = $( '.fl-builder-settings:visible' ),
				bgColor = form.find( 'input[name=bg_color]' ),
				text = form.find( 'input[name=text]' ),
				icon = form.find( 'input[name=icon]' ),
				iconPosition = form.find( 'select[name=icon_position]' ),
				iconAnimation = form.find( 'select[name=icon_animation]' );

			bgColor.on( 'change', this._previewBackground );
			text.on( 'keyup', this._previewIcon );
			icon.on( 'change', this._previewIcon );
			iconPosition.on( 'change', this._previewIcon );
			iconAnimation.on( 'change', this._previewIcon );
			icon.on( 'change', this._flipSettings );
			this._flipSettings()
		},

		_flipSettings: function() {
			var form  = $( '.fl-builder-settings' ),
					icon = form.find( 'input[name=icon]' );
			if ( -1 !== icon.val().indexOf( 'fad fa') ) {
				$('#fl-field-duo_color1').show();
				$('#fl-field-duo_color2').show();
				$('#fl-builder-settings-section-icons').show()
			} else {
				$('#fl-field-duo_color1').hide();
				$('#fl-field-duo_color2').hide();
				$('#fl-builder-settings-section-icons').hide()
			}
		},

		_previewBackground: function( e ) {
			var preview	= FLBuilder.preview,
				selector = preview.classes.node + ' a.fl-button, ' + preview.classes.node + ' a.fl-button:visited',
				form = $( '.fl-builder-settings:visible' ),
				style = form.find( 'select[name=style]' ).val(),
				bgColor = form.find( 'input[name=bg_color]' ).val();

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

		_previewIcon: function() {
			var node = FLBuilder.preview.elements.node,
				wrap = node.find( '.fl-button-wrap' ),
				link = node.find( 'a.fl-button' ),
				form = $( '.fl-builder-settings:visible' ),
				text = form.find( 'input[name=text]' ).val(),
				icon = form.find( 'input[name=icon]' ).val(),
				position = form.find( 'select[name=icon_position]' ).val(),
				animation = form.find( 'select[name=icon_animation]' ).val();

			node.find( '.fl-button-icon' ).remove();
			wrap.removeClass( 'fl-button-has-icon' );

			if ( '' !== icon ) {
				wrap.addClass( 'fl-button-has-icon' );

				if ( 'before' === position ) {
					link.prepend( '<i class="fl-button-icon fl-button-icon-before ' + icon + '"></i>' );
				} else if ( 'after' === position ) {
					link.append( '<i class="fl-button-icon fl-button-icon-after ' + icon + '"></i>' );
				}

				if ( 'enable' === animation ) {
					link.find( '.fl-button-icon' ).hide();
				}

				if ( '' === text ) {
					link.find( '.fl-button-icon' ).css( 'margin', '0' );
				}
			}
		},
	});

})(jQuery);
