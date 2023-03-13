( function( $ ) {

	FLBuilder.registerModuleHelper( 'icon_group_form', {
		init: function() {
			var form  = $( '.fl-builder-settings' ),
			icon = form.find( 'input[name=icon]' );

			icon.on( 'change', this._flipSettings );

			this._flipSettings();
		},
		_flipSettings: function() {
			var form  = $( '.fl-builder-settings' ),
					icon = form.find( 'input[name=icon]' );
			if ( -1 !== icon.val().indexOf( 'fad fa') ) {
				form.find('#fl-field-duo_color1').show();
				form.find('#fl-field-duo_color2').show();
				form.find('#fl-field-color').hide();
				form.find('#fl-field-hover_color').hide()
			} else {
				form.find('#fl-field-duo_color1').hide();
				form.find('#fl-field-duo_color2').hide();
				form.find('#fl-field-color').show();
				form.find('#fl-field-hover_color').show()
			}
		}
	});

} )( jQuery );
