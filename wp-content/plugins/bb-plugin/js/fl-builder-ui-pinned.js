( function( $ ) {

	/**
	 * Helper for pinning the builder UI to the
	 * sides of the browser window.
	 *
	 * @since 2.0
	 * @class PinnedUI
	 */
	var PinnedUI = {

		minWidth: 320,

		maxWidth: 600,

		minHeight: 400,

		/**
		 * @since 2.0
		 * @method init
		 */
		init: function() {
			this.initPanel();
			this.pinOrUnpin();
			this.bind();
		},

		/**
		 * @since 2.0
		 * @method bind
		 */
		bind: function() {
			var win  = $( window ),
				body = $( 'body' );

			win.on( 'resize', _.throttle( this.windowResize.bind( this ), 250 ) );

			body.on( 'click', '.fl-builder-ui-pinned-collapse', this.collapse );
			body.on( 'click', '.fl-builder--content-library-panel .fl-builder--tabs', this.closeLightboxOnPanelClick );

			FLBuilder.addHook( 'didShowLightbox', this.pinLightboxOnOpen.bind( this ) );
			FLBuilder.addHook( 'didHideAllLightboxes', this.pinnedLightboxClosed.bind( this ) );
			FLBuilder.addHook( 'endEditingSession', this.hide.bind( this ) );
			FLBuilder.addHook( 'didHideEditingUI', this.hide.bind( this ) );
			FLBuilder.addHook( 'publishButtonClicked', this.hide.bind( this ) );
			FLBuilder.addHook( 'restartEditingSession', this.show.bind( this ) );
			FLBuilder.addHook( 'didShowEditingUI', this.show.bind( this ) );
			FLBuilder.addHook( 'didShowLightbox', this.uncollapse.bind(this) );
			FLBuilder.addHook( 'willShowContentPanel', this.uncollapse.bind(this) );
			FLBuilder.addHook( 'willShowContentPanel', this.closeLightboxOnPanelClick.bind(this) );
		},

		/**
		 * Checks to see if the UI is currently pinned or not.
		 *
		 * @since 2.0
		 * @method isPinned
		 * @return {Boolean}
		 */
		isPinned: function() {
			return $( '.fl-builder--content-library-panel' ).hasClass( 'fl-builder-ui-pinned' );
		},

		/**
		 * Pins the UI.
		 *
		 * @since 2.0
		 * @method pin
		 * @param {String} position
		 * @param {Boolean} savePosition
		 */
		pin: function( position, savePosition ) {
			this.pinPanel( position );
			this.pinLightboxes();

			if ( savePosition ) {
				this.savePosition();
			}

			FLBuilder._resizeLayout();
			FLBuilder.triggerHook( 'didPinContentPanel' );
		},

		/**
		 * Unpins the UI.
		 *
		 * @since 2.0
		 * @method unpin
		 * @param {Boolean} savePosition
		 */
		unpin: function( savePosition ) {
			this.unpinLightboxes();
			this.unpinPanel();

			if ( savePosition ) {
				this.savePosition();
			}

			FLBuilder._resizeLayout();
			FLBuilder.triggerHook( 'didUnpinContentPanel' );
		},

		/**
		 * Pins or unpins the UI based on the window size.
		 *
		 * @since 2.0
		 * @method pinOrUnpin
		 */
		pinOrUnpin: function()
		{
			var panel  = $( '.fl-builder--content-library-panel' ),
				pinned = this.isPinned();

			if ( panel.hasClass( 'fl-builder-ui-pinned-hidden' ) ) {
				return;
			} else if ( window.innerWidth <= this.maxWidth ) {
				if ( pinned ) {
					this.unpin( false );
				}
				this.disableDragAndResize();
			} else {
				if ( ! pinned ) {
					this.restorePosition();
				}
				this.enableDragAndResize();
			}
		},

		/**
		 * Shows the pinned UI if it has been hidden.
		 *
		 * @since 2.0
		 * @method show
		 */
		show: function()
		{
			var panel = $( '.fl-builder--content-library-panel' );

			if ( panel.hasClass( 'fl-builder-ui-pinned-hidden' ) ) {
				panel.removeClass( 'fl-builder-ui-pinned-hidden' );
				panel.show();
				this.restorePosition();
			}
		},

		/**
		 * Hides pinned lightboxes without unpinning them.
		 *
		 * @since 2.0
		 * @method hide
		 */
		hide: function()
		{
			var body  = $( 'body' ),
				panel = $( '.fl-builder--content-library-panel' );

			if ( this.isPinned() ) {
				this.uncollapse();
				panel.addClass( 'fl-builder-ui-pinned-hidden' );
				panel.hide();
				body.css( 'margin', '' );
				FLBuilder._resizeLayout();
			}
		},

		/**
		 * Collapse all pinned UI elements.
		 *
		 * @since 2.0
		 * @method collapse
		 */
		collapse: function()
		{
			var button   = $( this ).find('i:visible'),
				body     = $( 'body' ),
				toggle   = button.data( 'toggle' ),
				position = button.data( 'position' ),
				panel 	 = $( '.fl-builder--content-library-panel' ),
				width    = panel.outerWidth();

			if ( 'hide' === toggle ) {
				panel.css( position, '-' + width + 'px' );
				body.css( 'margin-' + position, '' );
				body.addClass( 'fl-builder-ui-pinned-is-collapsed' );
			} else {
				panel.css( position, '0px' );
				body.css( 'margin-' + position, width + 'px' );
				body.removeClass( 'fl-builder-ui-pinned-is-collapsed' );
			}
		},

		/**
		 * Uncollapse all pinned UI elements.
		 *
		 * @since 2.0
		 * @method uncollapse
		 */
		uncollapse: function()
		{
			if ( this.isCollapsed() ) {
				$( '.fl-builder-ui-pinned-collapse:visible' ).trigger( 'click' );
			}
		},

		/**
		 * Return whether or not the panel is currently collapsed
		 *
		 * @since 2.0
		 * @method isCollapsed
		 */
		isCollapsed: function() {
			return $('body').hasClass('fl-builder-ui-pinned-is-collapsed');
		},

		/**
		 * Initializes pinning for the main content panel.
		 *
		 * @since 2.0
		 * @method initPanel
		 */
		initPanel: function() {
			var panel = $( '.fl-builder--content-library-panel' ),
				button = $( '.fl-builder-content-panel-button' ),
				panelHandle = button.length == 0 ? '.fl-builder--tabs, .fl-lightbox-header' : '.fl-builder--tabs';

			panel.draggable( {
				cursor		: 'move',
				handle		: panelHandle,
				cancel		: '.fl-builder--tabs button',
				scroll		: false,
				drag		: this.drag.bind( this ),
				stop		: this.dragStop.bind( this ),
				start		: this.dragStart.bind( this ),
			} ).resizable( {
				handles		: 'e, w',
				minHeight	: this.minHeight,
				minWidth	: this.minWidth,
				maxWidth	: this.maxWidth,
				start		: this.resizeStart.bind( this ),
				stop		: this.resizeStop.bind( this )
			} );

			panel.addClass( 'fl-builder-ui-pinned-container' );
			panel.find( '.ui-resizable-e, .ui-resizable-w' ).hide();
		},

		/**
		 * Pins the main content panel.
		 *
		 * @since 2.0
		 * @method pinPanel
		 * @param {String} position
		 */
		pinPanel: function( position ) {
			var panel 	= $( '.fl-builder--content-library-panel' ),
				width   = panel.outerWidth(),
				body  	= $( 'body' ),
				preview = $( '.fl-responsive-preview, .fl-responsive-preview-mask' ),
				content = $( FLBuilder._contentClass ).parentsUntil( 'body' ).last();

			body.addClass( 'fl-builder-ui-is-pinned fl-builder-ui-is-pinned-' + position );
			body.addClass( 'fl-builder-content-panel-is-showing' );
			body.css( 'margin-' + position, width + 'px' );
			preview.css( 'margin-' + position, width + 'px' );
			content.addClass( 'fl-builder-ui-pinned-content-transform' );
			panel.addClass( 'fl-builder-ui-pinned fl-builder-ui-pinned-' + position );
			panel.find( '.ui-resizable-' + ( 'left' === position ? 'e' : 'w' ) ).show();
			panel.on( 'resize', _.throttle( this.resize.bind( this ), 250 ) );
			panel.attr( 'style', '' );
			FLBuilder.ContentPanel.isShowing = true;
			if( $( '.fl-builder-content-panel-button' ).length == 0 ) {
				$('.fl-builder-panel-drag-handle').show();
			}
		},

        /**
		 * Unpins the main content panel.
         *
         * @since 2.0
         * @method unpinPanel
         */
		unpinPanel: function() {
			var panel   = $( '.fl-builder--content-library-panel' ),
				tab 	= panel.find( '.fl-builder--panel-content .is-showing' ).data( 'tab' ),
				body    = $( 'body' ),
				preview = $( '.fl-responsive-preview, .fl-responsive-preview-mask' ),
				content = $( FLBuilder._contentClass ).parentsUntil( 'body' ).last();

			body.css( 'margin-left', '' );
			body.css( 'margin-right', '' );
			body.removeClass( 'fl-builder-ui-is-pinned' );
			body.removeClass( 'fl-builder-ui-is-pinned-left' );
			body.removeClass( 'fl-builder-ui-is-pinned-right' );
			preview.css( 'margin-left', '' );
			preview.css( 'margin-right', '' );
			content.removeClass( 'fl-lightbox-content-transform' );
			panel.removeClass( 'fl-builder-ui-pinned' );
			panel.removeClass( 'fl-builder-ui-pinned-left' );
			panel.removeClass( 'fl-builder-ui-pinned-right' );
			panel.find( '.ui-resizable-handle' ).hide();
			panel.off( 'resize' );
			panel.attr( 'style', '' );
			panel.find( '.fl-builder--tabs [data-tab=' + tab + ']' ).addClass( 'is-showing' );
        },

		/**
		 * Pins all open lightboxes.
		 *
		 * @since 2.0
		 * @method pinLightboxes
		 */
		pinLightboxes: function() {
			var self = this;

			$( '.fl-lightbox-resizable' ).each( function() {
				self.pinLightbox( $( this ) );
			} );

			FLBuilder._reinitEditorFields();
		},

		/**
		 * Pins a single lightbox.
		 *
		 * @since 2.0
		 * @method pinLightbox
		 * @param {Object} lightbox
		 */
		pinLightbox: function( lightbox ) {
			var panel   = $( '.fl-builder--content-library-panel' ),
				wrapper = lightbox.closest( '.fl-lightbox-wrap' );

			if ( ! wrapper.closest( '.fl-builder-ui-pinned' ).length ) {
				panel.append( wrapper );
				lightbox.attr( 'style', '' );
				lightbox.draggable( 'disable' );
				lightbox.resizable( 'disable' );
			}

			if ( lightbox.is( ':visible' ) ) {
				panel.find( '.fl-builder--tabs .is-showing' ).removeClass( 'is-showing' );
			}
		},

		/**
		 * Pins a lightbox when it opens if it's not already pinned.
		 *
		 * @since 2.0
		 * @method pinLightboxOnOpen
		 * @param {Object} e
		 * @param {FLLightbox} boxObject
		 */
		pinLightboxOnOpen: function( e, boxObject ) {
			var lightbox = boxObject._node.find( '.fl-lightbox-resizable' );

			if ( ! lightbox.length ) {
				return;
			}

			if ( ! lightbox.hasClass( 'fl-builder-ui-pinning-initialized' ) ) {
				lightbox.draggable( 'option', 'start', this.dragStart.bind( this ) );
				lightbox.draggable( 'option', 'drag', this.drag.bind( this ) );
				lightbox.draggable( 'option', 'stop', this.dragStop.bind( this ) );
				lightbox.addClass( 'fl-builder-ui-pinning-initialized' );
			}

			if ( this.isPinned() ) {
				this.pinLightbox( lightbox );
			}

			FLBuilder.addHook( 'responsive-editing-switched', this.resize );
		},

		/**
		 * Handles a pinned lightbox closing.
		 *
		 * @since 2.0
		 * @method pinnedLightboxClosed
		 */
		pinnedLightboxClosed: function() {
			var panel = $( '.fl-builder--content-library-panel' )
				tab   = null;

			if ( this.isPinned() ) {
				tab = panel.find( '.fl-builder--panel-content .is-showing' ).data( 'tab' );
				panel.find( '.fl-builder--tabs [data-tab=' + tab + ']' ).addClass( 'is-showing' );
			}

			$( '.fl-lightbox' ).removeClass( 'fl-lightbox-prevent-animation' );
		},

		/**
		 * Unpins all pinned lightboxes.
		 *
		 * @since 2.0
		 * @method unpinLightboxes
		 */
		unpinLightboxes: function() {
			var body  = $( 'body' ),
				panel = $( '.fl-builder--content-library-panel' );

			panel.find( '.fl-lightbox-wrap' ).each( function() {
				var wrapper  = $( this ),
					lightbox = wrapper.find( '.fl-lightbox' ),
					top		 = 0,
					left	 = 0,
					right	 = 0;

				lightbox.draggable( 'enable' );
				lightbox.resizable( 'enable' );
				lightbox.find( '.ui-resizable-handle' ).show();
				body.append( wrapper );

				if ( lightbox.is( ':visible' ) ) {
					top = parseInt( panel.css( 'top' ) ) - parseInt( wrapper.css( 'top' ) ) - parseInt( wrapper.css( 'padding-top' ) );
					left = parseInt( panel.css( 'left' ) ) - parseInt( wrapper.css( 'padding-left' ) );
					right = parseInt( panel.css( 'right' ) ) - parseInt( wrapper.css( 'padding-right' ) );

					lightbox.css( 'top', ( top < 0 ? 0 : top ) + 'px' );
					lightbox.css( ( FLBuilderConfig.isRtl ? 'right' : 'left' ), ( FLBuilderConfig.isRtl ? right : left ) + 'px' );
					lightbox.addClass( 'fl-lightbox-prevent-animation' );
					body.removeClass( 'fl-builder-content-panel-is-showing' );
					FLBuilder.ContentPanel.isShowing = false;
				} else {
					lightbox.css( {
						top  : '25px',
						left : '25px',
					} );
				}
			} );

			FLBuilder._reinitEditorFields();
		},

		/**
		 * Closes lightboxes when a panel tab is clicked.
		 *
		 * @since 2.0
		 * @method closeLightboxOnPanelClick
		 */
		closeLightboxOnPanelClick: function() {
			FLBuilder._triggerSettingsSave( false, true );
		},

		/**
		 * Unpins if pinned when the window is resized down to a
		 * small device size.
		 *
		 * @since 2.0
		 * @method windowResize
		 */
		windowResize: function()
		{
			this.pinOrUnpin();
		},

		/**
		 * Callback for when content panel resize starts.
		 *
		 * @since 2.0
		 * @method resizeStart
		 */
		resizeStart: function()
		{
			$( 'body' ).addClass( 'fl-builder-resizable-is-resizing' );

			FLBuilder._destroyOverlayEvents();
			FLBuilder._removeAllOverlays();
		},

		/**
		 * Callback for when content panel resizes.
		 *
		 * @since 2.0
		 * @method resize
		 */
		resize: function()
		{
			var body   	  = $( 'body' ),
				preview   = $( '.fl-responsive-preview, .fl-responsive-preview-mask' ),
				panel 	  = $( '.fl-builder--content-library-panel' ),
				width     = panel.outerWidth();

			if ( ! panel.is( ':visible' ) ) {
				body.css( 'margin', '' );
			} else if ( panel.hasClass( 'fl-builder-ui-pinned-left' ) ) {
				body.css( 'margin-left', width + 'px' );
				preview.css( 'margin-left', width + 'px' );
			} else if ( panel.hasClass( 'fl-builder-ui-pinned-right' ) ) {
				body.css( 'margin-right', width + 'px' );
				preview.css( 'margin-right', width + 'px' );
			}
		},

		/**
		 * Callback for when content panel resize stops.
		 *
		 * @since 2.0
		 * @method resizeStop
		 */
		resizeStop: function()
		{
			$( 'body' ).removeClass( 'fl-builder-resizable-is-resizing' );

			FLBuilder._bindOverlayEvents();
			FLBuilder._resizeLayout();
			this.savePosition();
		},

		/**
		 * Callback for when content panel drag starts.
		 *
		 * @since 2.0
		 * @method dragStart
		 */
		dragStart: function( e, ui )
		{
			var body 	= $( 'body' ),
				target  = $( e.target ),
				actions = $( '.fl-builder-bar-actions' );

			if ( ! $( '.fl-lightbox-resizable:visible' ).length ) {
				actions.addClass( 'fl-builder-content-panel-pin-zone' );
			}

			body.addClass( 'fl-builder-draggable-is-dragging' );
			body.append( '<div class="fl-builder-ui-pin-zone fl-builder-ui-pin-zone-left"></div>' );
			body.append( '<div class="fl-builder-ui-pin-zone fl-builder-ui-pin-zone-right"></div>' );
			FLBuilder._destroyOverlayEvents();
		},

		/**
		 * Callback for when content panel is dragged.
		 *
		 * @since 2.0
		 * @method drag
		 */
		drag: function( e, ui )
		{
			var body   	  = $( 'body' ),
				preview   = $( '.fl-responsive-preview' ),
				win 	  = $( window ),
				winWidth  = preview.length ? preview.width() : win.width(),
				scrollTop = win.scrollTop(),
				panel     = $( '.fl-builder--content-library-panel' ),
				offsetTop = panel.offset().top,
				actions   = $( '.fl-builder-bar-actions' ),
				target    = $( e.target );

			if ( target.hasClass( 'fl-builder--content-library-panel' ) ) {
				if ( e.clientX < winWidth - 75 && offsetTop - scrollTop < 46 ) {
					actions.addClass( 'fl-builder-content-panel-pin-zone-hover' );
				} else {
					actions.removeClass( 'fl-builder-content-panel-pin-zone-hover' );
				}
			}

			if ( target.hasClass( 'fl-builder-ui-pinned' ) ) {
				this.unpinPanel();
			} else if ( e.clientX < 75 ) {
				body.addClass( 'fl-builder-ui-show-pin-zone fl-builder-ui-show-pin-zone-left' );
			} else if ( e.clientX > winWidth - 75 ) {
				body.addClass( 'fl-builder-ui-show-pin-zone fl-builder-ui-show-pin-zone-right' );
			} else {
				body.removeClass( 'fl-builder-ui-show-pin-zone' );
				body.removeClass( 'fl-builder-ui-show-pin-zone-left' );
				body.removeClass( 'fl-builder-ui-show-pin-zone-right' );
			}
		},

		/**
		 * Callback for when content panel drag stops.
		 *
		 * @since 2.0
		 * @method dragStop
		 */
		dragStop: function( e, ui )
		{
			var win      = $( window ),
				body     = $( 'body' ),
				actions  = $( '.fl-builder-bar-actions' ),
				zones    = $( '.fl-builder-ui-pin-zone' ),
				panel    = $( '.fl-builder--content-library-panel' ),
				lightbox = $( '.fl-lightbox-resizable:visible' ),
				target   = $( e.target );

			body.removeClass( 'fl-builder-draggable-is-dragging' );
			actions.removeClass( 'fl-builder-content-panel-pin-zone' );
			actions.removeClass( 'fl-builder-content-panel-pin-zone-hover' );
			zones.remove();

			if ( lightbox.length && parseInt( lightbox.css( 'top' ) ) < 0 ) {
				lightbox.css( 'top', '0' );
			}

			if ( body.hasClass( 'fl-builder-ui-show-pin-zone' ) ) {
				if ( body.hasClass( 'fl-builder-ui-show-pin-zone-left' ) ) {
					this.pin( 'left', true );
				} else {
					this.pin( 'right', true );
				}
				body.removeClass( 'fl-builder-ui-show-pin-zone' );
				body.removeClass( 'fl-builder-ui-show-pin-zone-left' );
				body.removeClass( 'fl-builder-ui-show-pin-zone-right' );
			} else if( panel.find( '.fl-lightbox' ).length ) {
				this.unpin( true );
				if ( 'module' === FLBuilderConfig.userTemplateType || FLBuilderConfig.simpleUi ) {
					panel.hide();
				}
			} else {
				panel.attr( 'style', '' );
				this.savePosition();
			}

			FLBuilder._bindOverlayEvents();
		},

		/**
		 * Disables draggable and resizable.
		 *
		 * @since 2.0
		 * @method disableDragAndResize
		 */
		disableDragAndResize: function() {
			var panel 		= $( '.fl-builder--content-library-panel' ),
				lightboxes 	= $( '.fl-lightbox-resizable' );

			panel.draggable( 'disable' );
			panel.resizable( 'disable' );

			lightboxes.draggable( 'disable' );
			lightboxes.resizable( 'disable' );
		},

		/**
		 * Enables draggable and resizable.
		 *
		 * @since 2.0
		 * @method enableDragAndResize
		 */
		enableDragAndResize: function() {
			var panel 		= $( '.fl-builder--content-library-panel' ),
				lightboxes 	= $( '.fl-lightbox-resizable:not(.fl-lightbox-width-full)' );

			panel.draggable( 'enable' );
			panel.resizable( 'enable' );

			if ( ! this.isPinned() ) {
				lightboxes.draggable( 'enable' );
				lightboxes.resizable( 'enable' );
			}
		},

		/**
		 * Save the position data for the pinned UI.
		 *
		 * @since 2.0
		 * @method savePosition
		 */
		savePosition: function()
		{
			var panel 	 = $( '.fl-builder--content-library-panel' ),
				lightbox = $( '.fl-lightbox-resizable:visible' ),
				data  	 = {
					pinned: {
						width  	 : panel.outerWidth(),
						position : null
					}
				};

			if ( panel.hasClass( 'fl-builder-ui-pinned-left' ) ) {
				data.pinned.position = 'left';
			} else if ( panel.hasClass( 'fl-builder-ui-pinned-right' ) ) {
				data.pinned.position = 'right';
			} else if ( lightbox.length ) {
				data.lightbox = {
					width  	: lightbox.width(),
					height 	: lightbox.height(),
					top  	: parseInt( lightbox.css( 'top' ) ) < 0 ? '0px' : lightbox.css( 'top' ),
					left  	: lightbox.css( 'left' )
				};
			}

			FLBuilderConfig.userSettings.pinned = data.pinned;

			if ( data.lightbox ) {
				FLBuilderConfig.userSettings.lightbox = data.lightbox;
			}

			FLBuilder.ajax( {
				action : 'save_pinned_ui_position',
				data   : data
			} );
		},

		/**
		 * Restores the pinned UI position for the current user.
		 *
		 * @since 2.0
		 * @method restorePosition
		 */
		restorePosition: function()
		{
			var panel    = $( '.fl-builder--content-library-panel' ),
				settings = FLBuilderConfig.userSettings.pinned;

			if ( settings && settings.position ) {
				panel.css( 'width', settings.width + 'px' );
				this.pin( settings.position, false );
				panel.css( 'width', settings.width + 'px' );
			}
		},
	};

	$( function() {
		PinnedUI.init();
	} );

} )( jQuery );
