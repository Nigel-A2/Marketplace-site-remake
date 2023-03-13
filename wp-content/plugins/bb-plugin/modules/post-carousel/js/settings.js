(function($){

	FLBuilder.registerModuleHelper('post-carousel', {

		init: function()
		{
			var form   = $('.fl-builder-settings'),
				layout = form.find('select[name=layout]'),
				icon = form.find( 'input[name=post_icon]' );

			layout.on('change', this._fixfeatured);
			icon.on( 'change', this._flipSettings );
			this._flipSettings()
		},

		_flipSettings: function() {
			var form  = $( '.fl-builder-settings' ),
					icon = form.find( 'input[name=post_icon]' );
			if ( -1 !== icon.val().indexOf( 'fad fa') ) {
				$('#fl-field-duo_color1').show();
				$('#fl-field-duo_color2').show();
				$('#fl-field-post_icon_color').hide()
			} else {
				$('#fl-field-duo_color1').hide();
				$('#fl-field-duo_color2').hide();
				$('#fl-field-post_icon_color').show();
			}
		},

		_fixfeatured: function()
		{
			var form   = $('.fl-builder-settings'),
				image  = form.find('select[name=show_image]'),
				layout = form.find('select[name=layout]')

				if( 'gallery' === layout.val() ) {
					image.val('1')
					image.hide()
					form.find('label[for=show_image]').hide()
					$('#fl-field-image_size').show()
					$('#fl-field-crop').show()
				} else {
					form.find('label[for=show_image]').show()
					form.find('select[name=show_image]').show()
					$('#fl-field-image_size').show()
					$('#fl-field-crop').show()
				}
		}
	});

})(jQuery);
