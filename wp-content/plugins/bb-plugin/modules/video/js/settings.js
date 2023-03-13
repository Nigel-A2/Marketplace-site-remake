( function( $ ) {

	FLBuilder.registerModuleHelper( 'video', {

		submit: function()
		{

			var form      = $( '.fl-builder-settings' ),
				enabled     = form.find( 'select[name=schema_enabled]' ).val(),
				name        = form.find( 'input[name=name]' ).val(),
				description = form.find( 'input[name=description]' ).val(),
				thumbnail   = form.find( 'input[name=thumbnail]' ).val(),
				update      = form.find( 'input[name=up_date]' ).val(),
				attachment  = form.find( 'select[name=poster_src]' ),
				size        = attachment.find(':selected').attr('data-size') || false;

			if ( size ) {
				$('<input name="poster_size" type="hidden" value="' + size + '">').insertAfter( 'input[name=poster]')
			}

			if( 'no' === enabled ) {
				return true;
			}

			if ( 0 === name.length ) {
				FLBuilder.alert( FLBuilderStrings.schemaAllRequiredMessage );
				return false;
			}
			else if ( 0 === description.length ) {
				FLBuilder.alert( FLBuilderStrings.schemaAllRequiredMessage );
				return false;
			}
			else if ( 0 === thumbnail.length ) {

				FLBuilder.alert( FLBuilderStrings.schemaAllRequiredMessage );

				return false;
			}
			else if( 0 === update.length ) {
				FLBuilder.alert( FLBuilderStrings.schemaAllRequiredMessage );
				return false;
			}

			return true;
		}
	});
})(jQuery);
