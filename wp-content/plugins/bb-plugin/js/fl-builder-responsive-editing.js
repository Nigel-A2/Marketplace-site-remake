( function( $ ) {

	/**
	 * Helper for handling responsive editing logic.
	 *
	 * @since 1.9
	 * @class FLBuilderResponsiveEditing
	 */
	FLBuilderResponsiveEditing = {

		/**
		 * The current editing mode we're in.
		 *
		 * @since 1.9
		 * @private
		 * @property {String} _mode
		 */
		_mode: 'default',

		/**
		 * Refreshes the media queries for the responsive preview
		 * if necessary.
		 *
		 * @since 1.9
		 * @method refreshPreview
		 * @param {Function} callback
		 */
		refreshPreview: function( callback )
		{
			var width;

			if ( $( '.fl-responsive-preview' ).length && 'default' !== this._mode ) {

				if ( 'responsive' == this._mode ) {
					width = FLBuilderConfig.global.responsive_breakpoint >= 320 ? 320 : FLBuilderConfig.global.responsive_breakpoint;
					FLBuilderSimulateMediaQuery.update( width, callback );
				}
				else if ( 'medium' == this._mode ) {
					width = FLBuilderConfig.global.medium_breakpoint >= 769 ? 769 : FLBuilderConfig.global.medium_breakpoint;
					FLBuilderSimulateMediaQuery.update( width, callback );
				}

				FLBuilder._resizeLayout();

			} else if ( callback ) {
				callback();
			}
		},

		/**
		 * Initializes responsive editing.
		 *
		 * @since 1.9
		 * @access private
		 * @method _init
		 */
		_init: function()
		{
			this._bind();
			this._initMediaQueries();
		},

		/**
		 * Bind events.
		 *
		 * @since 1.9
		 * @access private
		 * @method _bind
		 */
		_bind: function()
		{
			FLBuilder.addHook( 'endEditingSession', this._clearPreview );
			FLBuilder.addHook( 'didEnterRevisionPreview', this._clearPreview );
			FLBuilder.addHook( 'responsiveEditing', this._menuToggleClicked );
			FLBuilder.addHook( 'preview-init', this._switchAllSettingsToCurrentMode );
			FLBuilder.addHook( 'responsive-editing-switched', this._showSize );

			$( 'body' ).on( 'click', '.fl-field-responsive-toggle', this._settingToggleClicked );
			$( 'body' ).on( 'click', '.fl-responsive-preview-message button', this._previewToggleClicked );
		},

		/**
		 * Initializes faux media queries.
		 *
		 * @since 1.10
		 * @access private
		 * @method _initMediaQueries
		 */
		_initMediaQueries: function()
		{
			// Don't simulate media queries for stylesheets that match these paths.
			FLBuilderSimulateMediaQuery.ignore(
				[
					FLBuilderConfig.pluginUrl,
					FLBuilderConfig.relativePluginUrl
				]
			)
			ignorelist = $.map(FLBuilderConfig.responsiveIgnore, function(value, index) {
				return [value];
			});
			FLBuilderSimulateMediaQuery.ignore( ignorelist );

			// Reparse stylesheets that match these paths on each update.
			FLBuilderSimulateMediaQuery.reparse( [
				FLBuilderConfig.postId + '-layout-draft.css',
				FLBuilderConfig.postId + '-layout-draft-partial.css',
				FLBuilderConfig.postId + '-layout-preview.css',
				FLBuilderConfig.postId + '-layout-preview-partial.css',
				FLBuilderConfig.postId + '-inline-css',
				'fl-builder-global-css',
				'fl-builder-layout-css'
			] );
		},

		_showSize: function() {
				var show_size = $('.fl-responsive-preview-message .size' ),
				medium = ( '1' === FLBuilderConfig.global.responsive_preview ) ? FLBuilderConfig.global.medium_breakpoint : 769,
				responsive = ( '1' === FLBuilderConfig.global.responsive_preview ) ? FLBuilderConfig.global.responsive_breakpoint : 360,
				size_text = '';

			if ( $('.fl-responsive-preview').hasClass('fl-preview-responsive') ) {
					size_text = FLBuilderStrings.mobile + ' ' + responsive + 'px';
			} else if ( $('.fl-responsive-preview').hasClass('fl-preview-medium') ) {
				size_text = FLBuilderStrings.medium + ' ' + medium + 'px';
			}

			show_size.html('').html(size_text)
		},

		/**
		 * Switches to either mobile, tablet or desktop editing.
		 *
		 * @since 1.9
		 * @access private
		 * @method _switchTo
		 */
		_switchTo: function( mode, callback )
		{
			var html		= $( 'html' ),
				body        = $( 'body' ),
				content     = $( FLBuilder._contentClass ),
				preview     = $( '.fl-responsive-preview' ),
				mask        = $( '.fl-responsive-preview-mask' ),
				placeholder = $( '.fl-content-placeholder' ),
				width       = null;

			// Save the new mode.
			FLBuilderResponsiveEditing._mode = mode;

			// Setup the preview.
			if ( 'default' == mode ) {

				if ( 0 === placeholder.length ) {
					return;
				}

				html.removeClass( 'fl-responsive-preview-enabled' );
				placeholder.after( content );
				placeholder.remove();
				preview.remove();
				mask.remove();
			}
			else if ( 0 === preview.length ) {
				html.addClass( 'fl-responsive-preview-enabled' );
				content.after( '<div class="fl-content-placeholder"></div>' );
				body.prepend( wp.template( 'fl-responsive-preview' )() );
				$( '.fl-responsive-preview' ).addClass( 'fl-preview-' + mode );
				$( '.fl-responsive-preview-content' ).append( content );
			}
			else {
				preview.removeClass( 'fl-preview-responsive fl-preview-medium' );
				preview.addClass( 'fl-preview-' + mode  );
			}

			// Set the content width and apply media queries.
			if ( 'responsive' == mode ) {
				width = ( '1' !== FLBuilderConfig.global.responsive_preview && FLBuilderConfig.global.responsive_breakpoint >= 360 ) ? 360 : FLBuilderConfig.global.responsive_breakpoint;
				content.width( width );
				FLBuilderSimulateMediaQuery.update( width, callback );
				FLBuilderResponsiveEditing._setMarginPaddingPlaceholders();
			}
			else if ( 'medium' == mode ) {
				width = ( '1' !== FLBuilderConfig.global.responsive_preview && FLBuilderConfig.global.medium_breakpoint >= 769 ) ? 769 : FLBuilderConfig.global.medium_breakpoint;
				content.width( width );
				FLBuilderSimulateMediaQuery.update( width, callback );
				FLBuilderResponsiveEditing._setMarginPaddingPlaceholders();
			}
			else {
				content.width( '' );
				FLBuilderSimulateMediaQuery.update( null, callback );
			}

			// Set the content background color.
			this._setContentBackgroundColor();

			// Resize the layout.
			FLBuilder._resizeLayout();

			// Preview all responsive settings.
			this._previewFields();

			// Broadcast the switch.
			FLBuilder.triggerHook( 'responsive-editing-switched', mode );
		},

		/**
		 * Sets the background color for the builder content
		 * in a responsive preview.
		 *
		 * @since 1.9
		 * @access private
		 * @method _setContentBackgroundColor
		 */
		_setContentBackgroundColor: function()
		{
			var content     = $( FLBuilder._contentClass ),
				preview     = $( '.fl-responsive-preview' ),
				placeholder = $( '.fl-content-placeholder' ),
				parents     = placeholder.parents(),
				parent      = null,
				color       = '#fff',
				i           = 0;

			if ( 0 === preview.length ) {
				content.css( 'background-color', '' );
			}
			else {

				for( ; i < parents.length; i++ ) {

					color = parents.eq( i ).css( 'background-color' );

					if ( color != 'rgba(0, 0, 0, 0)' ) {
						break;
					}
				}

				content.css( 'background-color', color );
			}
		},

		/**
		 * Switches to the given mode and scrolls to an
		 * active node if one is present.
		 *
		 * @since 1.9
		 * @access private
		 * @method _switchToAndScroll
		 */
		_switchToAndScroll: function( mode )
		{
			var nodeId  = $( '.fl-builder-settings' ).data( 'node' ),
				element = undefined === nodeId ? undefined : $( '.fl-node-' + nodeId );

			FLBuilderResponsiveEditing._switchTo( mode, function() {

				if ( undefined !== element && element ) {

						var win     = $( window ),
							content = $( '.fl-responsive-preview-content' );

						if ( content.length ) {
							content.scrollTop( 0 );
							content.scrollTop( element.offset().top - 150 );
						} else {
							$( 'html, body' ).scrollTop( element.offset().top - 100 );
						}
				}

				$('.fl-row-bg-parallax').each(function(){
					var row = $(this),
						content = row.find('> .fl-row-content-wrap'),
						rowImages = {
							'default': row.data('parallax-image'),
							'medium': row.data('parallax-image-medium'),
							'responsive': row.data('parallax-image-responsive'),
						};
						
					if ( undefined !== rowImages[mode] ) {
						content.css('background-image', 'url(' + rowImages[mode] + ')');
					}

				});
				
			} );
		},

		/**
		 * Switches all responsive settings in a settings form
		 * to the given mode.
		 *
		 * @since 1.9
		 * @access private
		 * @method _switchAllSettingsTo
		 * @param {String} mode
		 */
		_switchAllSettingsTo: function( mode )
		{
			var className = 'dashicons-desktop dashicons-tablet dashicons-smartphone';

			$( '.fl-field-responsive-toggle' ).removeClass( className );
			$( '.fl-field-responsive-setting' ).hide();

			if ( 'default' == mode ) {
				className = 'dashicons-desktop';
			}
			else if ( 'medium' == mode ) {
				className = 'dashicons-tablet';
			}
			else {
				className = 'dashicons-smartphone';
			}

			$( '.fl-field-responsive-toggle' ).addClass( className ).data( 'mode', mode );
			$( '.fl-field-responsive-setting-' + mode ).css( 'display', 'inline-block' );
		},

		/**
		 * Switches all responsive settings in a settings form
		 * to the current mode.
		 *
		 * @since 2.2
		 * @access private
		 * @method _switchAllSettingsToCurrentMode
		 */
		_switchAllSettingsToCurrentMode: function()
		{
			var self = FLBuilderResponsiveEditing;

			self._switchAllSettingsTo( self._mode );

			FLBuilder.triggerHook( 'responsive-editing-switched', self._mode );
		},

		/**
		 * Set Placeholders for Padding and Margin
		 *
		 * @since 2.4
		 * @access private
		 * @method _setMarginPaddingPlaceholders
		 */
		_setMarginPaddingPlaceholders: function()
		{
			var paddingDefaultID    = '#fl-field-padding .fl-field-responsive-setting-default',
				paddingDefault      = {
					'values'        : {
						'top'       : $( paddingDefaultID + ' input[ name="padding_top" ]').val(),
						'right'     : $( paddingDefaultID + ' input[ name="padding_right" ]').val(),
						'bottom'    : $( paddingDefaultID + ' input[ name="padding_bottom" ]').val(),
						'left'      : $( paddingDefaultID + ' input[ name="padding_left" ]').val(),
					},
					'placeholders'  : {
						'top'       : $( paddingDefaultID + ' input[ name="padding_top" ]').attr('placeholder'),
						'right'     : $( paddingDefaultID + ' input[ name="padding_right" ]').attr('placeholder'),
						'bottom'    : $( paddingDefaultID + ' input[ name="padding_bottom" ]').attr('placeholder'),
						'left'      : $( paddingDefaultID + ' input[ name="padding_left" ]').attr('placeholder'),
					}
				},
				paddingMediumID     = '#fl-field-padding .fl-field-responsive-setting-medium',
				paddingMedium       = {
					'values'        : {
						'top'       : $( paddingMediumID + ' input[ name="padding_top_medium" ]').val(),
						'right'     : $( paddingMediumID + ' input[ name="padding_right_medium" ]').val(),
						'bottom'    : $( paddingMediumID + ' input[ name="padding_bottom_medium" ]').val(),
						'left'      : $( paddingMediumID + ' input[ name="padding_left_medium" ]').val(),
					},
					'placeholders'  : {
						'top'       : '',
						'right'     : '',
						'bottom'    : '',
						'left'      : '',
					}
				},
				paddingResponsiveID  = '#fl-field-padding .fl-field-responsive-setting-responsive',
				paddingResponsive    = {
					'values'        : {
						'top'       : $( paddingMediumID + ' input[ name="padding_top_responsive" ]').val(),
						'right'     : $( paddingMediumID + ' input[ name="padding_right_responsive" ]').val(),
						'bottom'    : $( paddingMediumID + ' input[ name="padding_bottom_responsive" ]').val(),
						'left'      : $( paddingMediumID + ' input[ name="padding_left_responsive" ]').val(),
					},
					'placeholders'  : {
						'top'       : '',
						'right'     : '',
						'bottom'    : '',
						'left'      : '',
					}
				},
				marginDefaultID     = '#fl-field-margin .fl-field-responsive-setting-default',
				marginDefault       = {
					'values'        : {
						'top'       : $( marginDefaultID + ' input[ name="margin_top" ]').val(),
						'right'     : $( marginDefaultID + ' input[ name="margin_right" ]').val(),
						'bottom'    : $( marginDefaultID + ' input[ name="margin_bottom" ]').val(),
						'left'      : $( marginDefaultID + ' input[ name="margin_left" ]').val(),
					},
					'placeholders'  : {
						'top'       : $( marginDefaultID + ' input[ name="margin_top" ]').attr('placeholder'),
						'right'     : $( marginDefaultID + ' input[ name="margin_right" ]').attr('placeholder'),
						'bottom'    : $( marginDefaultID + ' input[ name="margin_bottom" ]').attr('placeholder'),
						'left'      : $( marginDefaultID + ' input[ name="margin_left" ]').attr('placeholder'),
					}
				},
				marginMediumID      = '#fl-field-margin .fl-field-responsive-setting-medium',
				marginMedium        = {
					'values'        : {
						'top'       : $( marginMediumID + ' input[ name="margin_top_medium" ]').val(),
						'right'     : $( marginMediumID + ' input[ name="margin_right_medium" ]').val(),
						'bottom'    : $( marginMediumID + ' input[ name="margin_bottom_medium" ]').val(),
						'left'      : $( marginMediumID + ' input[ name="margin_left_medium" ]').val(),
					},
					'placeholders'	: {
						'top'       : marginDefault.values.top ? marginDefault.values.top : marginDefault.placeholders.top,
						'right'     : marginDefault.values.right ? marginDefault.values.right : marginDefault.placeholders.right,
						'bottom'    : marginDefault.values.bottom ? marginDefault.values.bottom : marginDefault.placeholders.bottom,
						'left'      : marginDefault.values.left ? marginDefault.values.left : marginDefault.placeholders.left,
					}
				},
				marginResponsiveID  = '#fl-field-margin .fl-field-responsive-setting-responsive',
				marginResponsive    = {
					'values'        : {
						'top'       : $( marginResponsiveID + ' input[ name="margin_top_responsive" ]').val(),
						'right'     : $( marginResponsiveID + ' input[ name="margin_right_responsive" ]').val(),
						'bottom'    : $( marginResponsiveID + ' input[ name="margin_bottom_responsive" ]').val(),
						'left'      : $( marginResponsiveID + ' input[ name="margin_left_responsive" ]').val(),
					},
					'placeholders'  : {
						'top'       : '',
						'right'     : '',
						'bottom'    : '',
						'left'      : '',
					}
				};

			// --- Set Padding Placeholders (Medium)---
			// -- top --
			if ( '' != paddingDefault.values.top ) {
				$( paddingMediumID + ' input[ name="padding_top_medium"] ').attr( 'placeholder', paddingDefault.values.top );
			} else {
				$( paddingMediumID + ' input[ name="padding_top_medium"] ').attr( 'placeholder', paddingDefault.placeholders.top );
			}

			// -- right --
			if ( '' != paddingDefault.values.right ) {
				$( paddingMediumID + ' input[ name="padding_right_medium"] ').attr( 'placeholder', paddingDefault.values.right );
			} else {
				$( paddingMediumID + ' input[ name="padding_right_medium"] ').attr( 'placeholder', paddingDefault.placeholders.right );
			}

			// -- bottom --
			if ( '' != paddingDefault.values.bottom ) {
				$( paddingMediumID + ' input[ name="padding_bottom_medium"] ').attr( 'placeholder', paddingDefault.values.bottom );
			} else {
				$( paddingMediumID + ' input[ name="padding_bottom_medium"] ').attr( 'placeholder', paddingDefault.placeholders.bottom );
			}

			// -- left --
			if ( '' != paddingDefault.values.left ) {
				$( paddingMediumID + ' input[ name="padding_left_medium"] ').attr( 'placeholder', paddingDefault.values.left );
			} else {
				$( paddingMediumID + ' input[ name="padding_left_medium"] ').attr( 'placeholder', paddingDefault.placeholders.left );
			}

			// --- Set Padding Placeholders (Responsive) ---
			// -- top --
			if ( '' != paddingMedium.values.top ) {
				$( paddingResponsiveID + ' input[ name="padding_top_responsive"] ').attr( 'placeholder', paddingMedium.values.top );
			} else if ( '' != paddingDefault.values.top ) {
				$( paddingResponsiveID + ' input[ name="padding_top_responsive"] ').attr( 'placeholder', paddingDefault.values.top );
			} else {
				$( paddingResponsiveID + ' input[ name="padding_top_responsive"] ').attr( 'placeholder', paddingDefault.placeholders.top );
			}

			// -- right --
			if ( '' != paddingMedium.values.right ) {
				$( paddingResponsiveID + ' input[ name="padding_right_responsive"] ').attr( 'placeholder', paddingMedium.values.right );
			} else if ( '' != paddingDefault.values.right ) {
				$( paddingResponsiveID + ' input[ name="padding_right_responsive"] ').attr( 'placeholder', paddingDefault.values.right );
			} else {
				$( paddingResponsiveID + ' input[ name="padding_right_responsive"] ').attr( 'placeholder', paddingDefault.placeholders.right );
			}

			// -- bottom --
			if ( '' != paddingMedium.values.bottom ) {
				$( paddingResponsiveID + ' input[ name="padding_bottom_responsive"] ').attr( 'placeholder', paddingMedium.values.bottom );
			} else if ( '' != paddingDefault.values.bottom ) {
				$( paddingResponsiveID + ' input[ name="padding_bottom_responsive"] ').attr( 'placeholder', paddingDefault.values.bottom );
			} else {
				$( paddingResponsiveID + ' input[ name="padding_bottom_responsive"] ').attr( 'placeholder', paddingDefault.placeholders.bottom );
			}

			// -- left --
			if ( '' != paddingMedium.values.left ) {
				$( paddingResponsiveID + ' input[ name="padding_left_responsive"] ').attr( 'placeholder', paddingMedium.values.left );
			} else if ( '' != paddingDefault.values.left ) {
				$( paddingResponsiveID + ' input[ name="padding_left_responsive"] ').attr( 'placeholder', paddingDefault.values.left );
			} else {
				$( paddingResponsiveID + ' input[ name="padding_left_responsive"] ').attr( 'placeholder', paddingDefault.placeholders.left );
			}

			// --- Set Margin Placeholders (Medium) ---
			// -- top --
			if ( '' != marginDefault.values.top ) {
				$( marginMediumID + ' input[ name="margin_top_medium" ]').attr( 'placeholder', marginDefault.values.top );
			} else {
				$( marginMediumID + ' input[ name="margin_top_medium" ]').attr( 'placeholder', marginDefault.placeholders.top );
			}

			// -- right --
			if ( '' != marginDefault.values.right ) {
				$( marginMediumID + ' input[ name="margin_right_medium" ]').attr( 'placeholder', marginDefault.values.right );
			} else {
				$( marginMediumID + ' input[ name="margin_right_medium" ]').attr( 'placeholder', marginDefault.placeholders.right );
			}

			// -- bottom --
			if ( '' != marginDefault.values.bottom ) {
				$( marginMediumID + ' input[ name="margin_bottom_medium" ]').attr( 'placeholder', marginDefault.values.bottom );
			} else {
				$( marginMediumID + ' input[ name="margin_bottom_medium" ]').attr( 'placeholder', marginDefault.placeholders.bottom );
			}

			// -- left --
			if ( '' != marginDefault.values.left ) {
				$( marginMediumID + ' input[ name="margin_left_medium" ]').attr( 'placeholder', marginDefault.values.left );
			} else {
				$( marginMediumID + ' input[ name="margin_left_medium" ]').attr( 'placeholder', marginDefault.placeholders.left );
			}

			// --- Set Margin Placeholders (Responsive) ---
			// -- top --
			if ( '' != marginMedium.values.top ) {
				$( marginResponsiveID + ' input[ name="margin_top_responsive" ]').attr( 'placeholder', marginMedium.values.top );
			} else if ( '' != marginDefault.values.top ) {
				$( marginResponsiveID + ' input[ name="margin_top_responsive" ]').attr( 'placeholder', marginDefault.values.top );
			} else {
				$( marginResponsiveID + ' input[ name="margin_top_responsive" ]').attr( 'placeholder', marginDefault.placeholders.top );
			}

			// -- right --
			if ( '' != marginMedium.values.right ) {
				$( marginResponsiveID + ' input[ name="margin_right_responsive" ]').attr( 'placeholder', marginMedium.values.right );
			} else if ( '' != marginDefault.values.right ) {
				$( marginResponsiveID + ' input[ name="margin_right_responsive" ]').attr( 'placeholder', marginDefault.values.right );
			} else {
				$( marginResponsiveID + ' input[ name="margin_right_responsive" ]').attr( 'placeholder', marginDefault.placeholders.right );
			}

			// -- bottom --
			if ( '' != marginMedium.values.bottom ) {
				$( marginResponsiveID + ' input[ name="margin_bottom_responsive" ]').attr( 'placeholder', marginMedium.values.bottom );
			} else if ( '' != marginDefault.values.bottom ) {
				$( marginResponsiveID + ' input[ name="margin_bottom_responsive" ]').attr( 'placeholder', marginDefault.values.bottom );
			} else {
				$( marginResponsiveID + ' input[ name="margin_bottom_responsive" ]').attr( 'placeholder', marginDefault.placeholders.bottom );
			}

			// -- left --
			if ( '' != marginMedium.values.left ) {
				$( marginResponsiveID + ' input[ name="margin_left_responsive" ]').attr( 'placeholder', marginMedium.values.left );
			} else if ( '' != marginDefault.values.left ) {
				$( marginResponsiveID + ' input[ name="margin_left_responsive" ]').attr( 'placeholder', marginDefault.values.left );
			} else {
				$( marginResponsiveID + ' input[ name="margin_left_responsive" ]').attr( 'placeholder', marginDefault.placeholders.left );
			}

		},

		/**
		 * Callback for when the responsive toggle of a setting
		 * is clicked.
		 *
		 * @since 1.9
		 * @access private
		 * @method _settingToggleClicked
		 */
		_settingToggleClicked: function()
		{
			var toggle  = $( this ),
				mode    = toggle.data( 'mode' );

			if ( 'default' == mode ) {
				mode  = 'medium';
			}
			else if ( 'medium' == mode ) {
				mode  = 'responsive';
			}
			else {
				mode  = 'default';
			}

			FLBuilderResponsiveEditing._switchAllSettingsTo( mode );
			FLBuilderResponsiveEditing._switchToAndScroll( mode );

			toggle.siblings( '.fl-field-responsive-setting:visible' ).find( 'input' ).focus();
		},

		/**
		 * Callback for when the main menu item is clicked.
		 *
		 * @since 2.2
		 * @access private
		 * @method _menuToggleClicked
		 */
		_menuToggleClicked: function()
		{
			var mode = FLBuilderResponsiveEditing._mode;

			if ( 'default' == mode ) {
				mode = 'medium';
			} else if ( 'medium' == mode ) {
				mode = 'responsive';
			} else {
				mode = 'default';
			}

			FLBuilder.MainMenu.hide();
			FLBuilderResponsiveEditing._switchAllSettingsTo( mode );
			FLBuilderResponsiveEditing._switchToAndScroll( mode );
		},

		/**
		 * Callback for when the switch buttons of the responsive
		 * preview header are clicked.
		 *
		 * @since 2.2
		 * @access private
		 * @method _previewToggleClicked
		 */
		_previewToggleClicked: function()
		{
			var mode = $( this ).data( 'mode' );
			FLBuilderResponsiveEditing._switchAllSettingsTo( mode );
			FLBuilderResponsiveEditing._switchToAndScroll( mode );
		},

		/**
		 * Clears the responsive editing preview and reverts
		 * to the default view.
		 *
		 * @since 1.9
		 * @access private
		 * @method _clearPreview
		 */
		_clearPreview: function()
		{
			FLBuilderResponsiveEditing._switchToAndScroll( 'default' );
		},

		/**
		 * Callback for when the responsive preview changes
		 * to live preview CSS for responsive fields.
		 *
		 * @since 1.9
		 * @access private
		 * @method _previewFields
		 */
		_previewFields: function()
		{
			var mode = FLBuilderResponsiveEditing._mode,
				form = $( '.fl-builder-settings:visible' );

			if ( 0 === form.length || undefined === form.attr( 'data-node' ) ) {
				return;
			}

			FLBuilder.triggerHook( 'responsive-editing-before-preview-fields', mode );

			form.find( '.fl-builder-settings-tab' ).each( function() {

				var tab = $( this );
				tab.css( 'display', 'block' );

				tab.find( '.fl-field-responsive-setting-' + mode + ':visible' ).each( function() {

					var field = $( this ),
						parent = field.closest( '.fl-field' ),
						type = parent.data( 'type' ),
						preview = parent.data( 'preview' ),
						hasConnection = parent.find( '.fl-field-connection-visible' ).length;

					if ( 'refresh' == preview.type ) {
						return;
					}

					if ( hasConnection ) {
						if ( 'photo' === type && 'default' !== mode ) {
							field.find( '.fl-photo-remove' ).trigger( 'click' );
						}
					} else{
						field.find( 'input' ).trigger( 'keyup' );
						field.find( 'select' ).trigger( 'change' );
					}
				} );

				tab.css( 'display', '' );
			} );

			FLBuilder.triggerHook( 'responsive-editing-after-preview-fields', mode );
		},
	};

	$( function() { FLBuilderResponsiveEditing._init() } );

} )( jQuery );
