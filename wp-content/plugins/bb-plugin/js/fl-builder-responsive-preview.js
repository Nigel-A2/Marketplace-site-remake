( function( $ ) {

	/**
	 * Helper for handling responsive preview in an iframe.
	 *
	 * @since 2.0.6
	 * @class FLBuilderResponsivePreview
	 */
	FLBuilderResponsivePreview = {

		/**
		 * Enters responsive preview mode.
		 *
		 * @since 2.0.6
		 * @method enter
		 */
		enter: function() {
			this.render();
		},

		/**
		 * Exits responsive preview mode.
		 *
		 * @since 2.0.6
		 * @method exit
		 */
		exit: function() {
			this.destroy();
		},

		/**
		 * Switch to a different device preview size.
		 *
		 * @since 2.0.6
		 * @param {String} mode
		 * @method switchTo
		 */
		switchTo: function( mode ) {
			var settings = FLBuilderConfig.global,
				frame	 = $( '#fl-builder-preview-frame' ),
				width 	 = '100%';

			if ( 'responsive' == mode ) {
				width = ( '1' !== settings.responsive_preview && settings.responsive_breakpoint >= 360 ) ? 360 : settings.responsive_breakpoint;
				frame.width( width );
			} else if ( 'medium' == mode ) {
				width = ( '1' !== settings.responsive_preview && settings.medium_breakpoint >= 769 ) ? 769 : settings.medium_breakpoint;
				frame.width( width );
			}

			frame.width( width );
		},

		/**
		 * Renders the iframe for previewing the layout.
		 *
		 * @since 2.0.6
		 * @method render
		 */
		render: function() {
			var body	= $( 'body' ),
				src 	= FLBuilderConfig.previewUrl,
				last	= $( '#fl-builder-preview-mask, #fl-builder-preview-frame' ),
				mask	= $( '<div id="fl-builder-preview-mask"></div>' ),
				frame 	= $( '<iframe id="fl-builder-preview-frame" src="' + src + '"></iframe>' );

			last.remove();
			body.append( mask );
			body.append( frame );
			body.css( 'overflow', 'hidden' );
		},

		_showSize: function(mode) {
				var show_size = $('.fl-builder--preview-actions .size' ),
				medium = ( '1' === FLBuilderConfig.global.responsive_preview ) ? FLBuilderConfig.global.medium_breakpoint : 769,
				responsive = ( '1' === FLBuilderConfig.global.responsive_preview ) ? FLBuilderConfig.global.responsive_breakpoint : 360,
				size_text = '';

			if ( 'responsive' === mode ) {
					size_text = FLBuilderStrings.mobile + ' ' + responsive + 'px';
			} else if ( 'medium' === mode ) {
				size_text = FLBuilderStrings.medium + ' ' + medium + 'px';
			}

			show_size.html('').html(size_text)
		},

		/**
		 * Removes the iframe for previewing the layout.
		 *
		 * @since 2.0.6
		 * @method destroy
		 */
		destroy: function() {
			$( '#fl-builder-preview-mask, #fl-builder-preview-frame' ).remove();
			$( 'body' ).css( 'overflow', 'visible' );
			$('.fl-builder--preview-actions .size' ).html('');
		},
	}
} )( jQuery );
