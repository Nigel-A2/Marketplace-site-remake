(function($){

	/**
	 * Custom lightbox for builder popups.
	 *
	 * @class FLLightbox
	 * @since 1.0
	 */
	FLLightbox = function( settings )
	{
		this._init( settings );
	};

	/**
	 * Closes the lightbox of a child element that
	 * is passed to this method.
	 *
	 * @since 1.0
	 * @static
	 * @method closeParent
	 * @param {Object} child An HTML element or jQuery reference to an element.
	 */
	FLLightbox.closeParent = function( child )
	{
		var instanceId = $( child ).closest( '.fl-lightbox-wrap' ).attr( 'data-instance-id' );

		if ( ! _.isUndefined( instanceId ) ) {
			FLLightbox._instances[ instanceId ].close();
		}
	};

	/**
	 * Returns the classname for the resize control in lightbox headers.
	 *
	 * @since 2.0
	 * @static
	 * @method getResizableControlClass
	 * @return {String}
	 */
	FLLightbox.getResizableControlClass = function()
	{
		var resizable = $( '.fl-lightbox-resizable' ).eq( 0 ),
			className = 'far fa-window-maximize';

		if ( resizable.length && resizable.hasClass( 'fl-lightbox-width-full' ) ) {
			className = 'far fa-window-minimize';
		}

		return className;
	};

	/**
	 * Unbinds events for all lightbox instances.
	 *
	 * @since 2.0
	 * @static
	 * @method unbindAll
	 */
	FLLightbox.unbindAll = function()
	{
		var id;

		for ( id in FLLightbox._instances ) {
			FLLightbox._instances[ id ]._unbind();
		}
	};

	/**
	 * Binds events for all lightbox instances.
	 *
	 * @since 2.0
	 * @static
	 * @method bindAll
	 */
	FLLightbox.bindAll = function()
	{
		var id;

		for ( id in FLLightbox._instances ) {
			FLLightbox._instances[ id ]._bind();
		}
	};

	/**
	 * Close all lightbox instances.
	 *
	 * @since 2.0
	 * @static
	 * @method closeAll
	 */
	FLLightbox.closeAll = function()
	{
		var id;

		for ( id in FLLightbox._instances ) {
			FLLightbox._instances[ id ].close();
		}
	};

	/**
	 * An object that stores a reference to each
	 * lightbox instance that is created.
	 *
	 * @since 1.0
	 * @static
	 * @access private
	 * @property {Object} _instances
	 */
	FLLightbox._instances = {};

	/**
	 * Prototype for new instances.
	 *
	 * @since 1.0
	 * @property {Object} prototype
	 */
	FLLightbox.prototype = {

		/**
		 * A unique ID for this instance that's used to store
		 * it in the static _instances object.
		 *
		 * @since 1.0
		 * @access private
		 * @property {String} _id
		 */
		_id: null,

		/**
		 * A jQuery reference to the main wrapper div.
		 *
		 * @since 1.0
		 * @access private
		 * @property {Object} _node
		 */
		_node: null,

		/**
		 * Flag for whether the lightbox is visible or not.
		 *
		 * @since 1.0
		 * @access private
		 * @property {Boolean} _visible
		 */
		_visible: false,

		/**
		 * Whether closing the lightbox is allowed or not.
		 *
		 * @since 2.0
		 * @access private
		 * @property {Boolean} _allowClosing
		 */
		_allowClosing: true,

		/**
		 * A timeout used to throttle the resize event.
		 *
		 * @since 1.0
		 * @access private
		 * @property {Object} _resizeTimer
		 */
		_resizeTimer: null,

		/**
		 * Default config object.
		 *
		 * @since 1.0
		 * @access private
		 * @property {Object}  _defaults
		 * @property {String}  _defaults.className 		- A custom classname to add to the wrapper div.
		 * @property {Boolean} _defaults.destroyOnClose - Flag for whether the instance should be destroyed when closed.
		 * @property {Boolean} _defaults.resizable 		- Flag for Whether this instance should be resizable or not.
		 */
		_defaults: {
			className: '',
			destroyOnClose: false,
			resizable: false
		},

		/**
		 * Opens the lightbox. You can pass new content to this method.
		 * If no content is passed, the previous content will be shown.
		 *
		 * @since 1.0
		 * @method open
		 * @param {String} content HTML content to add to the lightbox.
		 */
		open: function(content)
		{
			var lightbox = this._node.find( '.fl-lightbox' ),
				isPinned = ( lightbox.closest( '.fl-builder-ui-pinned' ).length ),
				settings = this._getPositionSettings();

			if ( ! isPinned && settings && this._defaults.resizable ) {
				lightbox.css( settings );
			}

			this._bind();
			this._node.show();
			this._visible = true;

			if(typeof content !== 'undefined') {
				this.setContent(content);
			}
			else {
				this._resize();
			}

			this.trigger('open');

			FLBuilder.triggerHook('didShowLightbox', this);
		},

		/**
		 * Closes the lightbox.
		 *
		 * @since 1.0
		 * @method close
		 */
		close: function()
		{
			var parent = this._node.data('parent');

			if ( ! this._allowClosing ) {
				return;
			}

			this.trigger('beforeCloseLightbox');
			this._unbind();
			this._node.hide();
			this._visible = false;
			this.trigger('close');

			FLBuilder.triggerHook('didHideLightbox');

			if ( this._defaults.resizable && _.isUndefined( parent ) ) {
				FLBuilder.triggerHook('didHideAllLightboxes');
			}

			if(this._defaults.destroyOnClose) {
				this.destroy();
			}
		},

		/**
		 * Disables closing the lightbox.
		 *
		 * @since 2.0
		 * @method disableClose
		 */
		disableClose: function()
		{
			this._allowClosing = false;
		},

		/**
		 * Enables closing the lightbox.
		 *
		 * @since 2.0
		 * @method enableClose
		 */
		enableClose: function()
		{
			this._allowClosing = true;
		},

		/**
		 * Adds HTML content to the lightbox replacing any
		 * previously added content.
		 *
		 * @since 1.0
		 * @method setContent
		 * @param {String} content HTML content to add to the lightbox.
		 */
		setContent: function(content)
		{
			this._node.find('.fl-lightbox-content').html(content);
			this._resize();
			if( $( '.fl-builder-content-panel-button' ).length == 0 ) {
				$('.fl-builder-panel-drag-handle').show();
			}
		},

		/**
		 * Uses the jQuery empty function to remove lightbox
		 * content and any related events.
		 *
		 * @since 1.0
		 * @method empty
		 */
		empty: function()
		{
			this._node.find('.fl-lightbox-content').empty();
		},

		/**
		 * Bind an event to the lightbox.
		 *
		 * @since 1.0
		 * @method on
		 * @param {String} event The type of event to bind.
		 * @param {Function} callback A callback to fire when the event is triggered.
		 */
		on: function(event, callback)
		{
			this._node.on(event, callback);
		},

		/**
		 * Unbind an event from the lightbox.
		 *
		 * @since 1.0
		 * @method off
		 * @param {String} event The type of event to unbind.
		 * @param {Function} callback
		 */
		off: function(event, callback)
		{
			this._node.off(event, callback);
		},

		/**
		 * Trigger an event on the lightbox.
		 *
		 * @since 1.0
		 * @method trigger
		 * @param {String} event The type of event to trigger.
		 * @param {Object} params Additional parameters to pass to the event.
		 */
		trigger: function(event, params)
		{
			this._node.trigger(event, params);
		},

		/**
		 * Destroy the lightbox by removing all elements, events
		 * and object references.
		 *
		 * @since 1.0
		 * @method destroy
		 */
		destroy: function()
		{
			this._node.empty();
			this._node.remove();

			FLLightbox._instances[this._id] = 'undefined';
			try{ delete FLLightbox._instances[this._id]; } catch(e){}
		},

		/**
		 * Initialize this lightbox instance.
		 *
		 * @since 1.0
		 * @access private
		 * @method _init
		 * @param {Object} settings A setting object for this instance.
		 */
		_init: function(settings)
		{
			var i    = 0,
				prop = null;

			for(prop in FLLightbox._instances) {
				i++;
			}

			this._defaults = $.extend({}, this._defaults, settings);
			this._id = new Date().getTime() + i;
			FLLightbox._instances[this._id] = this;
			this._render();
			this._resizable();
		},

		/**
		 * Renders the main wrapper.
		 *
		 * @since 1.0
		 * @access private
		 * @method _render
		 */
		_render: function()
		{
			this._node = $( '<div class="fl-lightbox-wrap" data-instance-id="'+ this._id +'"><div class="fl-lightbox-mask"></div><div class="fl-lightbox"><div class="fl-lightbox-content-wrap"><div class="fl-lightbox-content"></div></div></div></div>' );
			this._node.addClass( this._defaults.className );
			$( 'body' ).append( this._node );
		},

		/**
		 * Binds events for this instance.
		 *
		 * @since 1.0
		 * @access private
		 * @method _bind
		 */
		_bind: function()
		{
			$( window ).on( 'resize.fl-lightbox-' + this._id, this._delayedResize.bind( this ) );
		},

		/**
		 * Unbinds events for this instance.
		 *
		 * @since 1.0
		 * @access private
		 * @method _unbind
		 */
		_unbind: function()
		{
			$( window ).off( 'resize.fl-lightbox-' + this._id );
		},

		/**
		 * Enable resizing for the lightbox.
		 *
		 * @since 2.0
		 * @method _resizable
		 */
		_resizable: function()
		{
			var body	  = $( 'body' ),
				mask      = this._node.find( '.fl-lightbox-mask' ),
				lightbox  = this._node.find( '.fl-lightbox' ),
				resizable = $( '.fl-lightbox-resizable' ).eq( 0 );

			if ( this._defaults.resizable ) {

				mask.hide();
				lightbox.addClass( 'fl-lightbox-resizable' );
				lightbox.on( 'click', '.fl-lightbox-resize-toggle', this._resizeClicked.bind( this ) );

				lightbox.draggable( {
					cursor		: 'move',
					handle		: '.fl-lightbox-header',
				} ).resizable( {
					handles		: 'all',
					minHeight	: 500,
					minWidth	: 380,
					start		: this._resizeStart.bind( this ),
					stop		: this._resizeStop.bind( this )
				} );

				if ( resizable.length && resizable.hasClass( 'fl-lightbox-width-full' ) ) { // Setup nested
					lightbox.addClass( 'fl-lightbox-width-full' );
					lightbox.draggable( 'disable' );
				} else { // Setup the main parent lightbox
					this._restorePosition();
				}
			}
			else {
				mask.show();
			}

			this._resize();
		},

		/**
		 * Resizes the lightbox after a delay.
		 *
		 * @since 1.0
		 * @access private
		 * @method _delayedResize
		 */
		_delayedResize: function()
		{
			clearTimeout( this._resizeTimer );

			this._resizeTimer = setTimeout( this._resize.bind( this ), 250 );
		},

		/**
		 * Resizes the lightbox.
		 *
		 * @since 1.0
		 * @access private
		 * @method _resize
		 */
		_resize: function()
		{
			var lightbox    = this._node.find( '.fl-lightbox' ),
				boxTop      = parseInt( this._node.css( 'padding-top' ) ),
				boxBottom   = parseInt( this._node.css( 'padding-bottom' ) ),
				boxLeft     = parseInt( this._node.css( 'padding-left' ) ),
				boxRight    = parseInt( this._node.css( 'padding-right' ) ),
				boxHeight   = lightbox.height(),
				boxWidth    = lightbox.width(),
				win         = $( window ),
				winHeight   = win.height() - boxTop - boxBottom,
				winWidth    = win.width() - boxLeft - boxRight,
				top         = '0px';

			if ( ! this._defaults.resizable ) {

				if ( winHeight > boxHeight ) {
					top = ( ( winHeight - boxHeight - 46 ) / 2 ) + 'px';
				}

				lightbox.attr( 'style', '' ).css( 'margin', top + ' auto 0' );
			}
			else {

				if ( boxWidth < 600 ) {
					lightbox.addClass( 'fl-lightbox-width-slim' );
				} else {
					lightbox.removeClass( 'fl-lightbox-width-slim' );
				}

				if ( boxWidth < 450 ) {
					lightbox.addClass( 'fl-lightbox-width-micro' );
				} else {
					lightbox.removeClass( 'fl-lightbox-width-micro' );
				}

				this._resizeEditors();
			}

			this.trigger( 'resized' );
		},

		/**
		 * Callback for when a user lightbox resize starts.
		 *
		 * @since 2.0
		 * @access private
		 * @method _resizeStart
		 */
		_resizeStart: function()
		{
			$( 'body' ).addClass( 'fl-builder-resizable-is-resizing' );
			$( '.fl-builder-lightbox:visible' ).append( '<div class="fl-builder-resizable-iframe-fix"></div>' );

			FLBuilder._destroyOverlayEvents();
			FLBuilder._removeAllOverlays();
		},

		/**
		 * Callback for when a user lightbox resize stops.
		 *
		 * @since 2.0
		 * @access private
		 * @method _resizeStop
		 */
		_resizeStop: function( e, ui )
		{
			var lightbox = $( '.fl-lightbox-resizable:visible' );

			if ( parseInt( lightbox.css( 'top' ) ) < 0 ) {
				lightbox.css( 'top', '0' );
			}

			this._savePosition();

			$( 'body' ).removeClass( 'fl-builder-resizable-is-resizing' );
			$( '.fl-builder-resizable-iframe-fix' ).remove();

			FLBuilder._bindOverlayEvents();
		},

		/**
		 * Resize to full or back to standard when the resize icon is clicked.
		 *
		 * @since 2.0
		 * @access private
		 * @method _expandLightbox
		 */
		_resizeClicked: function()
		{
			var lightboxes = $( '.fl-lightbox-resizable' ),
				controls   = lightboxes.find( '.fl-lightbox-resize-toggle' ),
				lightbox   = this._node.find( '.fl-lightbox' );

			if ( lightbox.hasClass( 'fl-lightbox-width-full' ) ) {
				this._resizeExitFull();
			} else {
				this._resizeEnterFull();
			}

			this._resize();
		},

		/**
		 * Resize to the full size lightbox.
		 *
		 * @since 2.0
		 * @access private
		 * @method _resizeEnterFull
		 */
		_resizeEnterFull: function()
		{
			var lightboxes = $( '.fl-lightbox-resizable' ),
				controls   = lightboxes.find( '.fl-lightbox-resize-toggle' ),
				lightbox   = this._node.find( '.fl-lightbox' );

			controls.removeClass( 'fa-window-maximize' ).addClass( 'fa-window-minimize' );
			lightboxes.addClass( 'fl-lightbox-width-full' );
			lightboxes.draggable( 'disable' );
			lightboxes.resizable( 'disable' );
		},

		/**
		 * Resize to the standard size lightbox.
		 *
		 * @since 2.0
		 * @access private
		 * @method _resizeEnterFull
		 */
		_resizeExitFull: function()
		{
			var lightboxes = $( '.fl-lightbox-resizable' ),
				controls   = lightboxes.find( '.fl-lightbox-resize-toggle' ),
				lightbox   = this._node.find( '.fl-lightbox' );

			controls.removeClass( 'fa-window-minimize' ).addClass( 'fa-window-maximize' );
			lightboxes.removeClass( 'fl-lightbox-width-full' );
			lightboxes.draggable( 'enable' );
			lightboxes.resizable( 'enable' );
		},

		/**
		 * Resizes text and code editor fields.
		 *
		 * @since 2.0
		 * @method _resizeEditors
		 */
		_resizeEditors: function()
		{
			$( '.fl-lightbox-resizable' ).each( function() {

				var	lightbox 	 = $( this ),
					fieldsHeight = lightbox.find( '.fl-builder-settings-fields' ).height(),
					editors		 = lightbox.find( '.mce-edit-area > iframe, textarea.wp-editor-area, .ace_editor' ),
					editor 		 = null;

				if ( fieldsHeight < 350 ) {
					fieldsHeight = 350;
				}

				editors.each( function() {

					editor = $( this );

					if ( editor.hasClass( 'ace_editor' ) ) {
						editor.height( fieldsHeight - 60 );
						editor.closest( '.fl-field' ).data( 'editor' ).resize();
					} else if ( editor.closest( '.mce-container-body' ).find( '.mce-toolbar-grp .mce-toolbar.mce-last' ).is( ':visible' ) ) {
						editor.height( fieldsHeight - 175 );
					} else {
						editor.height( fieldsHeight - 150 );
					}
				} );
			} );
		},

		/**
		 * Save the lightbox position for the current user.
		 *
		 * @since 2.0
		 * @access private
		 * @method _savePosition
		 */
		_savePosition: function()
		{
			var lightbox = this._node.find( '.fl-lightbox' ),
				data     = {
					width  	: lightbox.width(),
					height 	: lightbox.height(),
					top  	: parseInt( lightbox.css( 'top' ) ) < 0 ? '0px' : lightbox.css( 'top' ),
					left  	: lightbox.css( 'left' )
				};

			if ( lightbox.closest( '.fl-builder-ui-pinned' ).length ) {
				return;
			}

			FLBuilderConfig.userSettings.lightbox = data;

			FLBuilder.ajax( {
				action : 'save_lightbox_position',
				data   : data
			} );
		},

		/**
		 * Restores the lightbox position for the current user.
		 *
		 * @since 2.0
		 * @access private
		 * @method _restorePosition
		 */
		_restorePosition: function()
		{
			var lightbox = this._node.find( '.fl-lightbox' ),
				settings = this._getPositionSettings();
			if ( settings ) {
				lightbox.css( settings );
			} else {
				lightbox.css( {
					top  : 25,
					left : FLBuilderConfig.isRtl ? '-' + 25 : 25
				} );
			}
		},

		/**
		 * Get the user settings for the lightbox position.
		 *
		 * Resize the height to 500px if the lightbox height is
		 * taller than the window and the window is taller than
		 * 546px (500px for lightbox min-height and 46px for the
		 * builder bar height).
		 *
		 * @since 2.0
		 * @access private
		 * @method _getPositionSettings
		 * @return {Object|Boolean}
		 */
		_getPositionSettings: function()
		{
			var settings = FLBuilderConfig.userSettings.lightbox;

			if ( ! settings ) {
				return false;
			}
			var winHeight = window.innerHeight,
				height = parseInt( settings.height ),
				top    = parseInt( settings.top ),
				wleft  = parseInt( settings.left ),
				wtop   = parseInt( settings.top ),
				width  = parseInt( settings.width );

			// settings are off the screen to the right
			if( (wleft + width + 100) > screen.width ) {
				settings.left = screen.width - width - 250;
			}

			// settings are off the screen to the left
			if ( wleft < 0 ) {
				settings.left = 50;
			}
			if ( ( height > winHeight && winHeight > 546 ) || top + height > winHeight ) {
				if ( height > winHeight ) {
					settings.height = winHeight - 50;
				}
				settings.top = 0;
			}

			return settings;
		},
	};

})(jQuery);
