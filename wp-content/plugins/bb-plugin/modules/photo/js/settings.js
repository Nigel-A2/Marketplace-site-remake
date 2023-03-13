(function($){

	FLBuilder.registerModuleHelper( 'photo', {

		init: function() {
			var form   		= $( '.fl-builder-settings' ),
				source 		= form.find( 'select[name=photo_source]' ),
				attachment 	= form.find( 'select[name=photo_src]' ),
				url 		= form.find( 'input[name=photo_url]' ),
				showCaption = form.find( 'select[name=show_caption]' ),
				caption 	= form.find( 'input[name=caption]' ),
				crop 		= form.find( 'select[name=crop]' );

			this._sourceChanged();
			this._cacheSetup();

			source.on( 'change', this._sourceChanged );
			source.on( 'change', this._previewImage );
			source.on( 'change', this._previewCaption );
			attachment.on( 'change', this._previewImage );
			attachment.on( 'change', this._cacheSetup );
			url.on( 'keyup', this._previewImage );
			showCaption.on( 'change', this._previewCaption );
			caption.on( 'keyup', this._previewCaption );
			crop.on( 'change', this._cropChanged );
		},

		_cacheSetup: function() {
			var form     = $( '.fl-builder-settings' ),
			attachment 	= form.find( 'select[name=photo_src]' );

			size = attachment.find(':selected').attr('data-size') || false;

			if ( size ) {
				FLBuilderConfig.photomodulesize = size;
			}
		},

		submit: function() {
			FLBuilderConfig.photomodulesize = false;
			return true;
		},

		_sourceChanged: function() {
			var form     = $( '.fl-builder-settings' ),
				source 	 = form.find( 'select[name=photo_source]' ).val(),
				linkType = form.find( 'select[name=link_type]' ),
				crop = form.find( 'select[name=crop]' ),
				attachment 	= form.find( 'select[name=photo_src]' ),
				url 		= form.find( 'input[name=photo_url]' );

			linkType.find( 'option[value=page]' ).remove();

			if( source === 'library' ) {
				linkType.append( '<option value="page">' + FLBuilderStrings.photoPage + '</option>' );
			}
		},

		_previewImage: function( e ) {
			var preview		= FLBuilder.preview,
				node		= preview.elements.node,
				content		= node.find( '.fl-photo-content' ),
				img			= null,
				form        = $( '.fl-builder-settings' ),
				source 		= form.find( 'select[name=photo_source]' ).val(),
				attachment 	= form.find( 'select[name=photo_src]' ),
				url 		= form.find( 'input[name=photo_url]' ),
				crop 		= form.find( 'select[name=crop]' ).val();

			if ( '' === crop ) {
				var src = 'library' === source ? attachment.val() : url.val();
				var ext = src.split( '.' ).pop();
				img = node.find( '.fl-photo-img' );
				img.show();
				img.removeAttr( 'height' );
				img.removeAttr( 'width' );
				img.removeAttr( 'srcset' );
				img.removeAttr( 'sizes' );
				img.attr( 'src', src );
				content.removeClass( 'fl-photo-img-jpg fl-photo-img-png fl-photo-img-gif fl-photo-img-svg' );
				content.addClass( 'fl-photo-img-' + ext );
			} else {
				preview.delayPreview( e );
			}
		},

		_previewCaption: function( e ) {
			var attachments = FLBuilderSettingsConfig.attachments,
				preview		= FLBuilder.preview,
				node		= preview.elements.node,
				form    	= $( '.fl-builder-settings' ),
				source 		= form.find( 'select[name=photo_source]' ).val(),
				id 			= form.find( 'input[name=photo]' ).val(),
				show		= form.find( 'select[name=show_caption]' ).val(),
				content		= node.find( '.fl-photo-content' ),
				container   = node.find( '.fl-photo-caption-below' ),
				caption 	= '';

			if ( '0' === show || 'hover' === show ) {
				node.find( '.fl-photo-caption' ).remove();
				return;
			}

			if ( 0 === container.length ) {
				content.append( '<div class="fl-photo-caption fl-photo-caption-below"></div>' );
				container = node.find( '.fl-photo-caption-below' );
			}

			if ( 'library' === source && attachments[ id ] && attachments[ id ].caption ) {
				caption = attachments[ id ].caption;
			} else if ( 'url' === source ) {
				caption = form.find( 'input[name=caption]' ).val();
			}

			container.html( caption );
		},

		_cropChanged: function() {
			var form = $( '.fl-builder-settings' ),
				crop = form.find( 'select[name=crop]' ),
				radius = form.find( '.fl-border-field-radius' );

			if ( 'circle' === crop.val() ) {
				radius.hide();
			} else {
				radius.show();
			}
		},
	} );

} )( jQuery );
