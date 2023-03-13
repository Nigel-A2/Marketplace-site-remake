( function( $ ) {

	FLBuilder.registerModuleHelper( 'icon', {

		init: function() {
			var form  = $( '.fl-builder-settings' ),
				icon = form.find( 'input[name=icon]' ),
				size = form.find( '#fl-field-size input[type=number]' ),
				text = form.find( '[data-name="text"] textarea.wp-editor-area' ),
				editorId = text.attr( 'id' );

			this._flipSettings();

			icon.on( 'change', this._previewIcon );
			icon.on( 'change', this._flipSettings );
			text.on( 'keyup', this._previewText );

			if ( 'undefined' !== typeof tinyMCE ) {
				var editor = tinyMCE.get( editorId );
				editor.on( 'change', this._previewText );
				editor.on( 'keyup', this._previewText );
			}
		},

		_previewIcon: function() {
			var ele = FLBuilder.preview.elements.node.find( '.fl-icon i' ),
				form  = $( '.fl-builder-settings' ),
				icon = form.find( 'input[name=icon]' );

			ele.attr( 'class', icon.val() );
		},

		_flipSettings: function() {
			var form  = $( '.fl-builder-settings' ),
					icon = form.find( 'input[name=icon]' );
			if ( -1 !== icon.val().indexOf( 'fad fa') ) {
				$('#fl-field-duo_color1').show();
				$('#fl-field-duo_color2').show();
				$('#fl-field-color').hide();
				$('#fl-field-hover_color').hide()
			} else {
				$('#fl-field-duo_color1').hide();
				$('#fl-field-duo_color2').hide();
				$('#fl-field-color').show();
				$('#fl-field-hover_color').show()
			}
		},

		_previewText: function() {
			var ele = FLBuilder.preview.elements.node.find( '.fl-icon-text' ),
				form = $( '.fl-builder-settings' ),
				text = form.find( '[data-name="text"] textarea.wp-editor-area' ),
				editorId = text.attr( 'id' ),
				editor = 'undefined' !== typeof tinyMCE ? tinyMCE.get( editorId ) : null,
				value = '';

			if ( editor && 'none' === text.css( 'display' ) ) {
				value = editor.getContent();
			} else {
				value = text.val();
			}

			if ( '' === value ) {
				ele.addClass( 'fl-icon-text-empty' );
			} else {
				ele.removeClass( 'fl-icon-text-empty' );
			}
		},
	});

} )( jQuery );
