( function( $ ) {

	FLBuilder.registerModuleHelper( 'post-grid', {

		resizeTimeout: null,

		init: function() {
			var form = $( '.fl-builder-settings' ),
				resizeFields = form.find( '#fl-field-border, #fl-field-title_typography, #fl-field-info_typography, #fl-field-content_typography' ),
				buttonBgColor = form.find( 'input[name=more_btn_bg_color]' ),
				icon = form.find('input[name=icon]'),
				layout = form.find( 'select[name=layout]' ),
				postType = form.find( 'select[name=post_type]' ),
				showContent = form.find( 'select[name=show_content]' );

			layout.on( 'change', this._layoutChanged.bind( this ) );
			postType.on( 'change', this._toggleEventsSection.bind( this ) );
			showContent.on( 'change', this._showContentChanged.bind(this) );
			resizeFields.find( 'input' ).on( 'input', this._resizeLayout.bind( this ) );
			resizeFields.find( 'select' ).on( 'change', this._resizeLayout.bind( this ) );
			buttonBgColor.on( 'change', this._previewButtonBackground );
			icon.on( 'change', this._flipSettings );
			this._flipSettings();
			this._toggleEventsSection();
		},

		/**
		 * Layout Field Change event handler.
		 * @since 2.4.2
		 */
		_layoutChanged: function() {
			this._showContentChanged();
		},

		/**
		 * Toggle 'The Calendar Events' section.
		 * @since TDB
		 */
		_toggleEventsSection: function () {
			var form = $( '.fl-builder-settings' ),
				tecEventsSection = form.find('#fl-builder-settings-section-events'),
				tecEventsButtonSection = form.find('#fl-builder-settings-section-events_button'),
				dataSource = form.find('#fl-field-data_source select').val(),
				selectedPostType = form.find( 'select[name=post_type]' ).val();
			
			if ( tecEventsSection.length <= 0 || tecEventsButtonSection.length <= 0 || 'custom_query' !== dataSource ) {
				return;
			}
			
			if ( 'tribe_events' === selectedPostType ) {
				tecEventsSection.show();
				tecEventsButtonSection.show();
			} else {
				tecEventsSection.hide();
				tecEventsButtonSection.hide();
			}
			
		},

		/**
		 * Show Content Field Change event handler.
		 * @since 2.4.2
		 */
		_showContentChanged: function() {
			var form = $('.fl-builder-settings'),
				showContent = form.find('select[name=show_content]').val();

			this._switchContentFields( '0' === showContent );
		},

		/**
		 * Decide what to do with the Content Type and the Content Length fields
		 * depending on the layout.
		 * @since 2.4.2
		 */
		_switchContentFields: function( hide ) {
			var form = $('.fl-builder-settings'),
				layout = form.find('select[name=layout]').val(),
				contentType = form.find('select[name=content_type]').val(), 
			    contentTypeField = form.find('#fl-field-content_type'),
				contentLengthField = form.find('#fl-field-content_length');
			
			// Hide both fields when Content == '0' (Hide).
			if ( hide ) {
				contentTypeField.hide();
				contentLengthField.hide();
				return;
			}

			if( 'columns' === layout || 'grid' === layout )  {
				contentTypeField.hide();
				contentLengthField.show();
				return;
			}

			if ( 'feed' === layout ) {
				contentTypeField.show();
				if ( 'full' === contentType ) {
					contentLengthField.hide();
				} else {
					contentLengthField.show();
				}
			}
		},

		_flipSettings: function() {
			var form  = $( '.fl-builder-settings' ),
					icon = form.find( 'input[name=icon]' );
			if ( -1 !== icon.val().indexOf( 'fad fa') ) {
				$('#fl-field-duo_color1').show();
				$('#fl-field-duo_color2').show();
				$('#fl-field-icon_color').hide();
				$('#fl-field-hover_color').hide()
			} else {
				$('#fl-field-duo_color1').hide();
				$('#fl-field-duo_color2').hide();
				$('#fl-field-icon_color').show();
				$('#fl-field-hover_color').show()
			}
		},

		_resizeLayout: function( e ) {
			clearTimeout( this.resizeTimeout );
			this.resizeTimeout = setTimeout( this._doResizeLayout.bind( this ), 250 );
		},

		_doResizeLayout: function( e ) {
			var form = $( '.fl-builder-settings' ),
				layout = form.find( 'select[name=layout]' ).val(),
				preview = FLBuilder.preview;

			if ( 'grid' !== layout || ! preview ) {
				return;
			}

			var masonry = preview.elements.node.find( '.fl-post-grid.masonry' ).data( 'masonry' );

			if ( masonry && masonry.layout ) {
				masonry.layout();
			}
		},

		_previewButtonBackground: function( e ) {
			var preview	= FLBuilder.preview,
				selector = preview.classes.node + ' a.fl-button, ' + preview.classes.node + ' a.fl-button:visited',
				form = $( '.fl-builder-settings:visible' ),
				style = form.find( 'select[name=more_btn_style]' ).val(),
				bgColor = form.find( 'input[name=more_btn_bg_color]' ).val();

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
	} );

} )( jQuery );
