(function($){

	/**
	 * Helper class for dealing with live previews.
	 *
	 * @class FLBuilderPreview
	 * @since 1.3.3
	 * @param {Object} config
	 */
	FLBuilderPreview = function( config )
	{
		// Set the preview ID.
		this.id = new Date().getTime();

		// Set the type.
		this.type = config.type;

		// Save the current state.
		this._saveState();

		// Initialize the preview.
		if ( config.layout ) {
			FLBuilder._renderLayout( config.layout, function() {
				this._init();
				if ( config.callback ) {
					config.callback();
				}
			}.bind( this ) );
		} else {
			this._init();
		}
	};

	/**
	 * Stores all the fonts and weights of all font fields.
	 * This is used to render the stylesheet with Google Fonts.
	 *
	 * @since 1.6.3
	 * @access private
	 * @property {Array} _fontsList
	 */
	FLBuilderPreview._fontsList = {};

	/**
	 * Returns a formatted selector string for a preview.
	 *
	 * @since 2.1
	 * @method getFormattedSelector
	 * @param {String} selector A CSS selector string.
	 * @return {String}
	 */
	FLBuilderPreview.getFormattedSelector = function( prefix, selector )
	{
		var formatted = '',
			parts 	  = selector.split( ',' ),
			i 	  	  = 0;

		for ( ; i < parts.length; i++ ) {

			if ( parts[ i ].indexOf( '{node}' ) > -1 ) {
				formatted += parts[ i ].replace( '{node}', prefix );
			} else if ( parts[ i ].indexOf( '{node_id}' ) > -1 ) {
				formatted += parts[ i ].replace( /{node_id}/g, this.nodeId );
			} else {
				formatted += prefix + ' ' + parts[ i ];
			}

			if ( i != parts.length - 1 ) {
				formatted += ', ';
			}
		}

		return formatted;
	};

	/**
	 * Prototype for new instances.
	 *
	 * @since 1.3.3
	 * @property {Object} prototype
	 */
	FLBuilderPreview.prototype = {

		/**
		 * A unique ID for this preview.
		 *
		 * @since 1.3.3
		 * @property {String} id
		 */
		id                	: '',

		/**
		 * The type of node that we are previewing.
		 *
		 * @since 1.3.3
		 * @property {String} type
		 */
		type                : '',

		/**
		 * The ID of node that we are previewing.
		 *
		 * @since 1.3.3
		 * @property {String} nodeId
		 */
		nodeId              : null,

		/**
		 * An object with data for each CSS class
		 * in the preview.
		 *
		 * @since 1.3.3
		 * @property {Object} classes
		 */
		classes             : {},

		/**
		 * An object with references to each element
		 * in the preview.
		 *
		 * @since 1.3.3
		 * @property {Object} elements
		 */
		elements            : {},

		/**
		 * An object that contains data for the current
		 * state of a layout before changes are made.
		 *
		 * @since 1.3.3
		 * @property {Object} state
		 */
		state               : null,

		/**
		 * Node settings saved when the preview was initalized.
		 *
		 * @since 1.7
		 * @access private
		 * @property {Object} _savedSettings
		 */
		_savedSettings       : null,

		/**
		 * An instance of FLStyleSheet for the current preview.
		 *
		 * @since 1.3.3
		 * @access private
		 * @property {FLStyleSheet} _styleSheet
		 */
		_styleSheet         : null,

		/**
		 * An instance of FLStyleSheet for the medium device preview.
		 *
		 * @since 1.9
		 * @access private
		 * @property {FLStyleSheet} _styleSheetMedium
		 */
		_styleSheetMedium   : null,

		/**
		 * An instance of FLStyleSheet for the responsive device preview.
		 *
		 * @since 1.9
		 * @access private
		 * @property {FLStyleSheet} _styleSheet
		 */
		_styleSheetResponsive : null,

		/**
		 * A timeout object for delaying the current preview refresh.
		 *
		 * @since 1.3.3
		 * @access private
		 * @property {Object} _timeout
		 */
		_timeout            : null,

		/**
		 * A timeout object for delaying when we show the loading
		 * graphic for refresh previews.
		 *
		 * @since 1.10
		 * @access private
		 * @property {Object} _loaderTimeout
		 */
		_loaderTimeout       : null,

		/**
		 * Stores the last classname for a classname preview.
		 *
		 * @since 1.3.3
		 * @access private
		 * @property {String} _lastClassName
		 */
		_lastClassName      : null,

		/**
		 * A reference to the AJAX object for a preview refresh.
		 *
		 * @since 1.3.3
		 * @access private
		 * @property {Object} _xhr
		 */
		_xhr                : null,

		/**
		 * Initializes a builder preview.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _init
		 */
		_init: function()
		{
			// Node Id
			this.nodeId = $('.fl-builder-settings').data('node');

			// Save settings
			this._saveSettings();

			// Elements and Class Names
			this._initElementsAndClasses();

			// Create the preview stylesheets
			this._createSheets();

			// Responsive previews
			this._initResponsivePreviews();

			// Default field previews
			this._initDefaultFieldPreviews();

			// Init
			switch(this.type) {

				case 'row':
				this._initRow();
				break;

				case 'col':
				this._initColumn();
				break;

				case 'module':
				this._initModule();
				break;
			}

			FLBuilder.triggerHook( 'preview-init', this );
		},

		/**
		 * Saves the current settings to be checked to see if
		 * anything has changed when a preview is canceled.
		 *
		 * @since 1.7
		 * @access private
		 * @method _saveSettings
		 */
		_saveSettings: function()
		{
			var form = $('.fl-builder-settings-lightbox .fl-builder-settings');

			this._savedSettings = FLBuilder._getSettingsForChangedCheck( this.nodeId, form );
		},

		/**
		 * Checks to see if the settings have changed.
		 *
		 * @since 1.7
		 * @access private
		 * @method _settingsHaveChanged
		 * @return bool
		 */
		_settingsHaveChanged: function()
		{
			var form 	 = $('.fl-builder-settings-lightbox .fl-builder-settings'),
				settings = FLBuilder._getSettings( form );

			return JSON.stringify( this._savedSettings ) != JSON.stringify( settings );
		},

		/**
		 * Initializes the classname and element references
		 * for this preview.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _initElementsAndClasses
		 */
		_initElementsAndClasses: function()
		{
			var contentClass;

			// Content Class
			if(this.type == 'row') {
				contentClass = '.fl-row-content-wrap';
			}
			else {
				contentClass = '.fl-' + this.type + '-content';
			}

			// Class Names
			$.extend(this.classes, {
				settings        : '.fl-builder-' + this.type + '-settings',
				settingsHeader  : '.fl-builder-' + this.type + '-settings .fl-lightbox-header',
				node            : FLBuilder._contentClass + ' .fl-node-' + this.nodeId,
				content         : FLBuilder._contentClass + ' .fl-node-' + this.nodeId + ' > ' + contentClass
			});

			// Elements
			$.extend(this.elements, {
				settings        : $(this.classes.settings),
				settingsHeader  : $(this.classes.settingsHeader),
				node            : $(this.classes.node),
				content         : $(this.classes.content)
			});
		},

		/**
		 * Creates the stylesheets for default, medium
		 * and responsive previews.
		 *
		 * @since 1.9
		 * @method _createSheets
		 */
		_createSheets: function()
		{
			this._destroySheets();

			if ( ! this._styleSheet ) {
				this._styleSheet = new FLStyleSheet( {
					id : 'fl-builder-preview',
					className : 'fl-builder-preview-style'
				} );
			}
			if ( ! this._styleSheetMedium ) {
				this._styleSheetMedium = new FLStyleSheet( {
					id : 'fl-builder-preview-medium',
					className : 'fl-builder-preview-style'
				} );
				this._styleSheetMedium.disable();
			}
			if ( ! this._styleSheetResponsive ) {
				this._styleSheetResponsive = new FLStyleSheet( {
					id : 'fl-builder-preview-responsive',
					className : 'fl-builder-preview-style'
				} );
				this._styleSheetResponsive.disable();
			}
		},

		/**
		 * Destroys all preview sheets.
		 *
		 * @since 1.9
		 * @method _destroySheets
		 */
		_destroySheets: function()
		{
			if ( this._styleSheet ) {
				this._styleSheet.destroy();
				this._styleSheet = null;
			}
			if ( this._styleSheetMedium ) {
				this._styleSheetMedium.destroy();
				this._styleSheetMedium = null;
			}
			if ( this._styleSheetResponsive ) {
				this._styleSheetResponsive.destroy();
				this._styleSheetResponsive = null;
			}
		},

		/**
		 * Disables preview styles for the current
		 * responsive editing mode.
		 *
		 * @since 2.2
		 * @method _disableStyles
		 */
		_disableStyles: function()
		{
			var mode = FLBuilderResponsiveEditing._mode,
				config = FLBuilderConfig.global,
				node = this.elements.node;

			if ( 'responsive' === mode ) {
				FLBuilderSimulateMediaQuery.disableStyles( config.responsive_breakpoint );
				this._styleSheetResponsive.disable();
			} else if ( 'medium' === mode ) {
				FLBuilderSimulateMediaQuery.disableStyles( config.medium_breakpoint );
				this._styleSheetMedium.disable();
			} else {
				node.removeClass( function( i, className ) {
					return ( className.match( /fl-node-[^\s]*/g ) || [] ).join( ' ' );
				} );
			}
		},

		/**
		 * Enables preview styles for the current
		 * responsive editing mode.
		 *
		 * @since 2.2
		 * @method _enableStyles
		 */
		_enableStyles: function()
		{
			var mode = FLBuilderResponsiveEditing._mode,
				node = this.elements.node;

			if ( 'responsive' === mode ) {
				FLBuilderSimulateMediaQuery.enableStyles();
				this._styleSheetResponsive.enable();
			} else if ( 'medium' === mode ) {
				FLBuilderSimulateMediaQuery.enableStyles();
				this._styleSheetMedium.enable();
			} else {
				node.addClass( 'fl-node-' + node.data( 'node' ) );
			}
		},

		/**
		 * Attempt to find the default value for a CSS property.
		 *
		 * @since 2.2
		 * @method _getDefaultValue
		 * @param {String} selector
		 * @param {String} property
		 * @return {String}
		 */
		_getDefaultValue: function( selector, property )
		{
			var value = '',
				element = $( selector ),
				node = element.closest( '[data-node]' ),
				ignore = [ 'line-height', 'font-weight' ];

			if ( 'width' === property ) {
				value = 'auto';
			} else if ( -1 === $.inArray( property, ignore ) && node.length ) {
				this._disableStyles();
				value = element.css( property );
				this._enableStyles();
			}

			return value;
		},

		/**
		 * Updates a CSS rule for this preview.
		 *
		 * @since 1.3.3
		 * @method updateCSSRule
		 * @param {String} selector The CSS selector to update.
		 * @param {String} property The CSS property to update.
		 * @param {String} value The CSS value to update.
		 * @param {String|Boolean} responsive If this preview is responsive or not.
		 */
		updateCSSRule: function( selector, property, value, responsive )
		{
			var mode = FLBuilderResponsiveEditing._mode,
				sheetKey = '';

			// Get the default value if needed.
			if ( '' === value || 'null' === value ) {
				value = this._getDefaultValue( selector, property );
			}

			// Update the rule.
			if ( responsive ) {
				if ( 'string' === typeof responsive ) {
					sheetKey = this.toUpperCaseWords( responsive );
				} else {
					sheetKey = 'default' === mode ? '' : this.toUpperCaseWords( mode );
				}
				this[ '_styleSheet' + sheetKey ].updateRule( selector, property, value );
			} else {
				this._styleSheet.updateRule( selector, property, value );
			}
		},

		/**
		 * Runs a delay with a callback.
		 *
		 * @since 1.3.3
		 * @method delay
		 * @param {Number} length How long to wait before running the callback.
		 * @param {Function} callback A function to call when the delay is complete.
		 */
		delay: function(length, callback)
		{
			this._cancelDelay();
			this._timeout = setTimeout(callback, length);
		},

		/**
		 * Cancels a preview refresh delay.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _cancelDelay
		 */
		_cancelDelay: function()
		{
			if(this._timeout !== null) {
				clearTimeout(this._timeout);
			}
		},

		/**
		 * Converts a hex value to an array of RGB values.
		 *
		 * @since 1.3.3
		 * @method hexToRgb
		 * @param {String} hex
		 * @return {Array}
		 */
		hexToRgb: function(hex)
		{
			var bigInt  = parseInt(hex, 16),
				r       = (bigInt >> 16) & 255,
				g       = (bigInt >> 8) & 255,
				b       = bigInt & 255;

			return [r, g, b];
		},

		/**
		 * Returns a hex or rgb formatted value.
		 *
		 * @since 2.0.3
		 * @method hexOrRgb
		 * @param {String} value
		 * @return {String}
		 */
		hexOrRgb: function( value )
		{
			if ( value.indexOf( 'rgb' ) < 0 && value.indexOf( '#' ) < 0 ) {
				value = '#' + value;
			}

			return value;
		},

		/**
		 * Parses a float or returns 0 if we don't have a number.
		 *
		 * @since 1.3.3
		 * @method parseFloat
		 * @param {Number} value
		 * @return {Number}
		 */
		parseFloat: function(value)
		{
			return isNaN(parseFloat(value)) ? 0 : parseFloat(value);
		},

		/* Responsive Previews
		----------------------------------------------------------*/

		/**
		 * Initializes logic for responsive previews.
		 *
		 * @since 1.9
		 * @method _initResponsivePreviews
		 */
		_initResponsivePreviews: function()
		{
			var namespace = '.preview-' + this.id;

			FLBuilder.addHook( 'responsive-editing-switched' + namespace, $.proxy( this._responsiveEditingSwitched, this ) );
			FLBuilder.addHook( 'responsive-editing-before-preview-fields' + namespace, $.proxy( this._responsiveEditingPreviewFields, this ) );
		},

		/**
		 * Destroys responsive preview events.
		 *
		 * @since 1.9
		 * @method _destroyResponsivePreviews
		 */
		_destroyResponsivePreviews: function()
		{
			var namespace = '.preview-' + this.id;

			FLBuilder.removeHook( 'responsive-editing-switched' + namespace );
			FLBuilder.removeHook( 'responsive-editing-before-preview-fields' + namespace );
		},

		/**
		 * Initializes logic for responsive previews.
		 *
		 * @since 1.9
		 * @method _responsiveEditingSwitched
		 */
		_responsiveEditingSwitched: function( e, mode )
		{
			if ( 'default' == mode ) {
				this._styleSheetMedium.disable();
				this._styleSheetResponsive.disable();
			}
			else if ( 'medium' == mode ) {
				this._styleSheetMedium.enable();
				this._styleSheetResponsive.disable();
			}
			else if ( 'responsive' == mode ) {
				this._styleSheetMedium.enable();
				this._styleSheetResponsive.enable();
			}
		},

		/**
		 * Logic that needs to run before field previews are triggered
		 * after responsive editing mode switches.
		 *
		 * @since 2.2
		 * @method _responsiveEditingPreviewFields
		 */
		_responsiveEditingPreviewFields: function( e, mode )
		{
			if ( 'medium' === mode ) {
				if ( 'col' === this.type && this.elements.node[0].style.width ) {
					size = parseFloat( this.elements.node[0].style.width );
					this.elements.size.val( size );
				}
			}
		},

		/**
		 * Deprecated. Use updateCSSRule instead.
		 *
		 * @since 1.9
		 */
		updateResponsiveCSSRule: function( selector, property, value )
		{
			this.updateCSSRule( selector, property, value, true );
		},

		/* States
		----------------------------------------------------------*/

		/**
		 * Saves the current state of a layout.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _saveState
		 */
		_saveState: function()
		{
			var post    = FLBuilderConfig.postId,
				css     = $('link[href*="/cache/' + post + '"]').attr('href'),
				js      = $('script[src*="/cache/' + post + '"]').attr('src'),
				html    = $(FLBuilder._contentClass).html();

			this.state = {
				css     : css,
				js      : js,
				html    : html
			};
		},

		/**
		 * Runs a preview refresh for the current settings lightbox.
		 *
		 * @since 1.3.3
		 * @method preview
		 */
		preview: function()
		{
			var form     = $('.fl-builder-settings-lightbox .fl-builder-settings'),
				nodeId   = form.attr('data-node'),
				settings = FLBuilder._getSettings(form);

			// Show the node as loading.
			FLBuilder._showNodeLoading( nodeId );

			// Abort an existing preview request.
			this._cancelPreview();

			settings      = FLBuilder._inputVarsCheck( settings );

			if ( 'error' === settings  ) {
				return 0;
			}

			// Make a new preview request.
			this._xhr = FLBuilder.ajax({
				action          : 'render_layout',
				node_id         : nodeId,
				node_preview    : settings
			}, $.proxy(this._renderPreview, this));
		},

		/**
		 * Runs a preview refresh with a delay.
		 *
		 * @since 1.3.3
		 * @method delayPreview
		 */
		delayPreview: function(e)
		{
			var heading         = typeof e == 'undefined' ? [] : $(e.target).closest('tr').find('th'),
				widgetHeading   = $('.fl-builder-widget-settings .fl-builder-settings-title'),
				lightboxHeading = $('.fl-builder-settings .fl-lightbox-header'),
				loaderSrc       = FLBuilderLayoutConfig.paths.pluginUrl + 'img/ajax-loader-small.svg',
				loader          = $('<img class="fl-builder-preview-loader" src="' + loaderSrc + '" />');

			this.delay(1000, $.proxy(this.preview, this));

			this._loaderTimeout = setTimeout( function() {

				$('.fl-builder-preview-loader').remove();

				if(heading.length > 0) {
					heading.append(loader);
				}
				else if(widgetHeading.length > 0) {
					widgetHeading.append(loader);
				}
				else if(lightboxHeading.length > 0) {
					lightboxHeading.append(loader);
				}

			}, 1500 );
		},

		/**
		 * Cancels a preview refresh.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _cancelPreview
		 */
		_cancelPreview: function()
		{
			if(this._xhr) {
				this._xhr.abort();
				this._xhr = null;
			}
		},

		/**
		 * Renders the response of a preview refresh.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _renderPreview
		 * @param {String} response The JSON encoded response.
		 */
		_renderPreview: function(response)
		{
			this._xhr = null;

			FLBuilder._renderLayout(response, $.proxy(this._renderPreviewComplete, this));
		},

		/**
		 * Fires when a preview refresh has finished rendering.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _renderPreviewComplete
		 */
		_renderPreviewComplete: function()
		{
			// Refresh the preview styles.
			this._createSheets();

			// Refresh the elements.
			this._initElementsAndClasses();

			// Refresh preview config for element references.
			this._initDefaultFieldPreviews();

			// Clear the loader timeout.
			if(this._loaderTimeout !== null) {
				clearTimeout(this._loaderTimeout);
			}

			// Remove the loading graphic.
			$('.fl-builder-preview-loader').remove();

			// Fire the preview rendered event.
			$( FLBuilder._contentClass ).trigger( 'fl-builder.preview-rendered' );
		},

		/**
		 * Reverts a preview to the state that was saved
		 * before the preview was initialized.
		 *
		 * @since 1.3.3
		 * @method revert
		 */
		revert: function()
		{
			var nodeId = this.nodeId;

			if ( ! this._settingsHaveChanged() ) {
				this.clear();
				return;
			}

			if ( 'col' === this.type ) {
				nodeId = this.elements.node.closest( '.fl-col-group' ).data( 'node' );
			}

			FLBuilder._updateNode( nodeId, function() {
				this.clear();
			}.bind( this ) );
		},

		/**
		 * Cancels a preview refresh.
		 *
		 * @since 1.3.3
		 * @method clear
		 */
		cancel: function()
		{
			this._cancelDelay();
			this._cancelPreview();
		},

		/**
		 * Cancels a preview refresh and removes
		 * any stylesheet changes.
		 *
		 * @since 1.3.3
		 * @method clear
		 */
		clear: function()
		{
			// Canel any preview delays or requests.
			this.cancel();

			// Destroy the preview stylesheet.
			this._destroySheets();

			// Destroy responsive editing previews.
			this._destroyResponsivePreviews();
		},

		/* Node Text Color Settings
		----------------------------------------------------------*/

		/**
		 * Initializes node text color previews.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _initNodeTextColor
		 */
		_initNodeTextColor: function()
		{
			// Elements
			$.extend(this.elements, {
				textColor    : $(this.classes.settings + ' input[name=text_color]'),
				linkColor    : $(this.classes.settings + ' input[name=link_color]'),
				hoverColor 	 : $(this.classes.settings + ' input[name=hover_color]'),
				headingColor : $(this.classes.settings + ' input[name=heading_color]')
			});

			// Events
			this.elements.textColor.on('change', $.proxy(this._textColorChange, this));
			this.elements.linkColor.on('change', $.proxy(this._textColorChange, this));
			this.elements.hoverColor.on('change', $.proxy(this._textColorChange, this));
			this.elements.headingColor.on('change', $.proxy(this._textColorChange, this));
		},

		/**
		 * Fires when the text color field for a node
		 * is changed.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _textColorChange
		 * @param {Object} e An event object.
		 */
		_textColorChange: function(e)
		{
			var textColor    = this.elements.textColor.val(),
				linkColor    = this.elements.linkColor.val(),
				hoverColor   = this.elements.hoverColor.val(),
				headingColor = this.elements.headingColor.val();

			linkColor 	 = linkColor === '' ? textColor : linkColor;
			hoverColor 	 = hoverColor === '' ? textColor : hoverColor;
			headingColor = headingColor === '' ? textColor : headingColor;

			if ( textColor && textColor.indexOf( 'rgb' ) < 0 ) {
				textColor = '#' + textColor;
			}
			if ( linkColor && linkColor.indexOf( 'rgb' ) < 0 ) {
				linkColor = '#' + linkColor;
			}
			if ( hoverColor && hoverColor.indexOf( 'rgb' ) < 0 ) {
				hoverColor = '#' + hoverColor;
			}
			if ( headingColor && headingColor.indexOf( 'rgb' ) < 0 ) {
				headingColor = '#' + headingColor;
			}

			this.delay(50, $.proxy(function(){

				// Update Text color.
				if(textColor === '') {
					this.updateCSSRule(this.classes.node, 'color', '');
				}
				else {
					this.updateCSSRule(this.classes.node, 'color', textColor);
				}

				// Update Link Color
				if ( linkColor === '' ) {
					this.updateCSSRule(this.classes.node + ' a', 'color', '');
				}
				else {
					this.updateCSSRule(this.classes.node + ' a', 'color', linkColor);
				}

				// Hover Color
				if(hoverColor === '') {
					this.updateCSSRule(this.classes.node + ' a:hover', 'color', '');
				}
				else {
					this.updateCSSRule(this.classes.node + ' a:hover', 'color', hoverColor);
				}

				// Heading Color
				if(headingColor === '') {
					this.updateCSSRule(this.classes.node + ' h1', 'color', '');
					this.updateCSSRule(this.classes.node + ' h2', 'color', '');
					this.updateCSSRule(this.classes.node + ' h3', 'color', '');
					this.updateCSSRule(this.classes.node + ' h4', 'color', '');
					this.updateCSSRule(this.classes.node + ' h5', 'color', '');
					this.updateCSSRule(this.classes.node + ' h6', 'color', '');
					this.updateCSSRule(this.classes.node + ' h1 a', 'color', '');
					this.updateCSSRule(this.classes.node + ' h2 a', 'color', '');
					this.updateCSSRule(this.classes.node + ' h3 a', 'color', '');
					this.updateCSSRule(this.classes.node + ' h4 a', 'color', '');
					this.updateCSSRule(this.classes.node + ' h5 a', 'color', '');
					this.updateCSSRule(this.classes.node + ' h6 a', 'color', '');
				}
				else {
					this.updateCSSRule(this.classes.node + ' h1', 'color', headingColor);
					this.updateCSSRule(this.classes.node + ' h2', 'color', headingColor);
					this.updateCSSRule(this.classes.node + ' h3', 'color', headingColor);
					this.updateCSSRule(this.classes.node + ' h4', 'color', headingColor);
					this.updateCSSRule(this.classes.node + ' h5', 'color', headingColor);
					this.updateCSSRule(this.classes.node + ' h6', 'color', headingColor);
					this.updateCSSRule(this.classes.node + ' h1 a', 'color', headingColor);
					this.updateCSSRule(this.classes.node + ' h2 a', 'color', headingColor);
					this.updateCSSRule(this.classes.node + ' h3 a', 'color', headingColor);
					this.updateCSSRule(this.classes.node + ' h4 a', 'color', headingColor);
					this.updateCSSRule(this.classes.node + ' h5 a', 'color', headingColor);
					this.updateCSSRule(this.classes.node + ' h6 a', 'color', headingColor);
				}

			}, this));
		},

		/* Node Bg Settings
		----------------------------------------------------------*/

		/**
		 * Initializes node background previews.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _initNodeBg
		 */
		_initNodeBg: function()
		{
			// Elements
			$.extend(this.elements, {
				bgType                      : $(this.classes.settings + ' select[name=bg_type]'),
				bgColor                     : $(this.classes.settings + ' input[name=bg_color]'),
				bgColorPicker               : $(this.classes.settings + ' .fl-picker-bg_color'),
				bgGradientType              : $(this.classes.settings + ' select.fl-gradient-picker-type-select'),
				bgVideoSource               : $(this.classes.settings + ' select[name=bg_video_source]'),
				bgVideo                     : $(this.classes.settings + ' input[name=bg_video]'),
				bgVideoServiceUrl           : $(this.classes.settings + ' input[name=bg_video_service_url]'),
				bgVideoFallbackSrc          : $(this.classes.settings + ' select[name=bg_video_fallback_src]'),
				bgSlideshowSource           : $(this.classes.settings + ' select[name=ss_source]'),
				bgSlideshowPhotos           : $(this.classes.settings + ' input[name=ss_photos]'),
				bgSlideshowFeedUrl          : $(this.classes.settings + ' input[name=ss_feed_url]'),
				bgSlideshowSpeed            : $(this.classes.settings + ' input[name=ss_speed]'),
				bgSlideshowTrans            : $(this.classes.settings + ' select[name=ss_transition]'),
				bgSlideshowTransSpeed       : $(this.classes.settings + ' input[name=ss_transitionDuration]'),
				bgParallaxImageSrc          : $(this.classes.settings + ' select[name=bg_parallax_image_src]'),
				bgOverlayType               : $(this.classes.settings + ' select[name=bg_overlay_type]'),
				bgOverlayColor              : $(this.classes.settings + ' input[name=bg_overlay_color]'),
				bgOverlayGradient    		: $(this.classes.settings + ' #fl-field-bg_overlay_gradient select'),
			});

			// Events
			this.elements.bgType.on(                	'change', $.proxy(this._bgTypeChange, this));
			this.elements.bgColor.on(               	'change', $.proxy(this._bgColorChange, this));
			this.elements.bgVideoServiceUrl.on(   		'change', $.proxy(this._bgVideoChange, this));
			this.elements.bgSlideshowSource.on(     	'change', $.proxy(this._bgSlideshowChange, this));
			this.elements.bgSlideshowPhotos.on(     	'change', $.proxy(this._bgSlideshowChange, this));
			this.elements.bgSlideshowFeedUrl.on(    	'keyup',  $.proxy(this._bgSlideshowChange, this));
			this.elements.bgSlideshowSpeed.on(      	'keyup',  $.proxy(this._bgSlideshowChange, this));
			this.elements.bgSlideshowTrans.on(      	'change', $.proxy(this._bgSlideshowChange, this));
			this.elements.bgSlideshowTransSpeed.on( 	'keyup',  $.proxy(this._bgSlideshowChange, this));
			this.elements.bgParallaxImageSrc.on(    	'change', $.proxy(this._bgParallaxChange, this));
			this.elements.bgOverlayType.on(         	'change', $.proxy(this._bgOverlayChange, this));
			this.elements.bgOverlayColor.on(        	'change', $.proxy(this._bgOverlayChange, this));
		},

		/**
		 * Fires when the background type field of
		 * a node changes.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _bgTypeChange
		 * @param {Object} e An event object.
		 */
		_bgTypeChange: function(e)
		{
			var val = this.elements.bgType.val(),
				mode = FLBuilderResponsiveEditing._mode;

			// Clear bg styles first.
			this.elements.node.removeClass('fl-row-bg-video');
			this.elements.node.removeClass('fl-row-bg-slideshow');
			this.elements.node.removeClass('fl-row-bg-parallax');
			this.elements.node.find('.fl-bg-video').remove();
			this.elements.node.find('.fl-bg-slideshow').remove();
			this.elements.content.css('background-image', '');

			this.updateCSSRule(this.classes.content, 'background-color', 'transparent');
			this.updateCSSRule(this.classes.content, 'background-image', 'none');
			this.updateCSSRule(this.classes.content, 'background-image', 'none', 'medium');
			this.updateCSSRule(this.classes.content, 'background-image', 'none', 'responsive');

			// None
			if(val == 'none') {
				this._bgOverlayClear();
			}

			// Color
			else if(val == 'color') {
				this.elements.bgColor.trigger('change');
				this._bgOverlayClear();
			}

			// Gradient
			else if(val == 'gradient') {
				this.elements.bgGradientType.trigger('change');
				this._bgOverlayClear();
			}

			// Photo
			else if(val == 'photo') {
				this.elements.bgColor.trigger('change');
				this.elements.settings.find( '[data-device="' + mode + '"] select[name*="bg_"]' ).trigger( 'change' );
			}

			// Video
			else if(val == 'video') {
				this.elements.bgColor.trigger('change');
				this._bgVideoChange();
			}

			// Slideshow
			else if(val == 'slideshow') {
				this.elements.bgColor.trigger('change');
				this._bgSlideshowChange();
			}

			// Parallax
			else if(val == 'parallax') {
				this.elements.bgColor.trigger('change');
				this.elements.bgParallaxImageSrc.trigger('change');
			}
		},

		/**
		 * Fires when the background color field of
		 * a node changes.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _bgColorChange
		 * @param {Object} e An event object.
		 */
		_bgColorChange: function(e)
		{
			var rgb, alpha, value;

			if(this.elements.bgColor.val() === '') {
				this.updateCSSRule(this.classes.content, 'background-color', 'transparent');
			}
			else {
				value = this.hexOrRgb( this.elements.bgColor.val() );

				this.delay(100, $.proxy(function(){
					this.updateCSSRule(this.classes.content, 'background-color', value);
				}, this));
			}
		},

		/**
		 * Fires when the background video field of
		 * a node changes.
		 *
		 * @since 1.9.2
		 * @access private
		 * @method _bgVideoChange
		 * @param {Object} e An event object.
		 */
		_bgVideoChange: function(e)
		{
			var eles        	= this.elements,
				source 			= eles.bgVideoSource.val(),
				video 			= eles.bgVideo.val(),
				videoUrl		= eles.bgVideoServiceUrl.val(),
				youtubePlayer 	= 'https://www.youtube.com/iframe_api',
				vimeoPlayer		= 'https://player.vimeo.com/api/player.js',
				scriptTag  		= $( '<script>' );

			// Only load the required API script library
			if(source == 'video_service' && videoUrl != '') {
				if (/^(?:(?:(?:https?:)?\/\/)?(?:www.)?(?:youtu(?:be.com|.be))\/(?:watch\?v\=|v\/|embed\/)?([\w\-]+))/i.test(videoUrl)
					&& $( 'script[src*="youtube.com"' ).length < 1) {
					scriptTag.attr('src', youtubePlayer);
				}
				else if(/^(http\:\/\/|https\:\/\/)?(www\.)?(vimeo\.com\/)([0-9]+)$/.test(videoUrl)
					&& $( 'script[src*="vimeo.com"' ).length < 1) {
					scriptTag.attr('src', vimeoPlayer);
				}

				scriptTag
					.attr('type', 'text/javascript')
					.appendTo('head');

				this.delay(500, $.proxy(this.preview, this));
			}
			else if(video != '') {
				this.preview();
			}
		},

		/**
		 * Fires when the background slideshow field of
		 * a node changes.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _bgSlideshowChange
		 * @param {Object} e An event object.
		 */
		_bgSlideshowChange: function(e)
		{
			var eles        = this.elements,
				source      = eles.bgSlideshowSource.val(),
				photos      = eles.bgSlideshowPhotos.val(),
				feed        = eles.bgSlideshowFeedUrl.val(),
				speed       = eles.bgSlideshowSpeed.val(),
				transSpeed  = eles.bgSlideshowTransSpeed.val();

			if(source == 'wordpress' && photos === '') {
				return;
			}
			else if(source == 'smugmug' && feed === '') {
				return;
			}
			else if(isNaN(parseInt(speed))) {
				return;
			}
			else if(isNaN(parseInt(transSpeed))) {
				return;
			}

			this.delay(500, $.proxy(this.preview, this));
		},

		/**
		 * Fires when the background parallax field of
		 * a node changes.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _bgParallaxChange
		 * @param {Object} e An event object.
		 */
		_bgParallaxChange: function(e)
		{
			if(this.elements.bgParallaxImageSrc.val()) {

				this.updateCSSRule(this.classes.content, {
					'background-image'      : 'url(' + this.elements.bgParallaxImageSrc.val() + ')',
					'background-repeat'     : 'no-repeat',
					'background-position'   : 'center center',
					'background-attachment' : 'fixed',
					'background-size'       : 'cover'
				});
			}
		},

		/**
		 * Fires when the background overlay field of
		 * a node changes.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _bgOverlayChange
		 * @param {Object} e An event object.
		 */
		_bgOverlayChange: function(e)
		{
			var type = this.elements.bgOverlayType.val(),
				color = this.elements.bgOverlayColor.val(),
				rgb, alpha, value;

			if ( 'color' === type ) {
				if ( color === '' ) {
					this.elements.node.removeClass('fl-row-bg-overlay');
					this.elements.node.removeClass('fl-col-bg-overlay');
					this.updateCSSRule(this.classes.content + '::after', 'background-color', 'transparent');
				} else {
					value = this.hexOrRgb( this.elements.bgOverlayColor.val() );
					this.delay(100, $.proxy(function(){
						this._bgOverlayAddClasses();
						this.updateCSSRule( this.classes.content + '::after', 'background-color', value );
					}, this));
				}
				this.updateCSSRule(this.classes.content + '::after', 'background-image', 'none');
			} else if ( 'gradient' === type ) {
				this._bgOverlayAddClasses();
				this.updateCSSRule(this.classes.content + '::after', 'background-color', 'transparent');
				this.elements.bgOverlayGradient.trigger( 'change' );
			} else {
				this.elements.node.removeClass('fl-row-bg-overlay');
				this.elements.node.removeClass('fl-col-bg-overlay');
				this.updateCSSRule(this.classes.content + '::after', 'background-color', 'transparent');
				this.updateCSSRule(this.classes.content + '::after', 'background-image', 'none');
			}
		},

		/**
		 * Adds the necessary classes for background overlays.
		 *
		 * @since 2.2
		 * @access private
		 * @method _bgOverlayAddClasses
		 */
		_bgOverlayAddClasses: function() {
			if ( this.elements.node.hasClass( 'fl-col' ) ) {
				this.elements.node.addClass( 'fl-col-bg-overlay' );
			} else {
				this.elements.node.addClass( 'fl-row-bg-overlay' );
			}
		},

		/**
		 * Fires when a background overlay color is cleared.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _bgOverlayClear
		 * @param {Object} e An event object.
		 */
		_bgOverlayClear: function(e)
		{
			this.elements.bgOverlayColor.prev('.fl-color-picker-clear').trigger('click');
			this.elements.bgOverlayType.val( 'color' ).trigger( 'change' );
		},

		/* Node Class Name Settings
		----------------------------------------------------------*/

		/**
		 * Initializes node classname previews.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _initNodeClassName
		 */
		_initNodeClassName: function()
		{
			// Elements
			$.extend(this.elements, {
				className : $(this.classes.settings + ' input[name=class]')
			});

			// Events
			this.elements.className.on('keyup', $.proxy(this._classNameChange, this));
			this._lastClassName = this.elements.className.val();
		},

		/**
		 * Fires when the classname of a node changes.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _classNameChange
		 * @param {Object} e An event object.
		 */
		_classNameChange: function(e)
		{
			var className = this.elements.className.val();

			if(this._lastClassName !== null) {
				this.elements.node.removeClass(this._lastClassName);
			}

			this.elements.node.addClass(className);
			this._lastClassName = className;
		},

		/* Node Spacing Settings
		----------------------------------------------------------*/

		/**
		 * Initializes node responsive dimension previews for things
		 * like margins, padding and borders.
		 *
		 * @since 1.9
		 * @access private
		 * @method _initNodeDimensions
		 */
		_initNodeDimensions: function( property )
		{
			var elements      = {},
				dimensions    = [ 'Top', 'Bottom', 'Left', 'Right' ],
				devices       = [ '', 'Medium', 'Responsive' ],
				settingsClass = this.classes.settings,
				elementKey    = '',
				inputName     = '',
				i             = null,
				k             = null;

			for ( i = 0; i < dimensions.length; i++ ) {

				for ( k = 0; k < devices.length; k++ ) {

					elementKey = property + dimensions[ i ] + devices[ k ];
					inputName  = property + '_' + dimensions[ i ].toLowerCase();

					if ( '' != devices[ k ] ) {
						inputName += '_' + devices[ k ].toLowerCase();
					}

					elements[ elementKey ] = $( settingsClass + ' input[name=' + inputName + ']');
				}
			}

			$.extend( this.elements, elements );
		},

		/* Row Settings
		----------------------------------------------------------*/

		/**
		 * Initializes a row preview.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _initRow
		 */
		_initRow: function()
		{
			// Elements
			$.extend(this.elements, {
				width           	: $(this.classes.settings + ' select[name=width]'),
				contentWidth    	: $(this.classes.settings + ' select[name=content_width]'),
				maxContentWidth 	: $(this.classes.settings + ' input[name=max_content_width]'),
				maxContentWidthUnit : $(this.classes.settings + ' select[name=max_content_width_unit]'),
				height          	: $(this.classes.settings + ' select[name=full_height]'),
				minHeight          	: $(this.classes.settings + ' input[name=min_height]'),
				align           	: $(this.classes.settings + ' select[name=content_alignment]')
			});

			// Events
			this.elements.width.on(         		'change', $.proxy(this._rowWidthChange, this));
			this.elements.contentWidth.on(  		'change', $.proxy(this._rowContentWidthChange, this));
			this.elements.maxContentWidth.on(   	'input',  $.proxy(this._rowMaxContentWidthChange, this));
			this.elements.maxContentWidthUnit.on(   'change', $.proxy(this._rowMaxContentWidthChange, this));
			this.elements.height.on(        		'change', $.proxy(this._rowHeightChange, this));
			this.elements.align.on(         		'change', $.proxy(this._rowHeightChange, this));

			// Common Elements
			this._initNodeTextColor();
			this._initNodeBg();
			this._initNodeClassName();
			this._initNodeDimensions( 'border' );
			this._initNodeDimensions( 'margin' );
			this._initNodeDimensions( 'padding' );
		},

		/**
		 * Fires when the width field of a row changes.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _rowWidthChange
		 * @param {Object} e An event object.
		 */
		_rowWidthChange: function(e)
		{
			var settings		= FLBuilderConfig.global,
				row 	 		= this.elements.node,
				content  		= this.elements.content.find('.fl-row-content'),
				maxWidth 		= this.elements.maxContentWidth.val(),
				maxWidthUnit 	= this.elements.maxContentWidthUnit.val();

			row.css( 'max-width', 'none' );
			content.css( 'max-width', 'none' );

			if(this.elements.width.val() == 'full') {
				row.removeClass('fl-row-fixed-width');
				row.addClass('fl-row-full-width');
			}
			else {
				row.removeClass('fl-row-full-width');
				row.addClass('fl-row-fixed-width');
			}

			this._rowMaxContentWidthChange();
		},

		/**
		 * Fires when the content width field of a row changes.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _rowContentWidthChange
		 * @param {Object} e An event object.
		 */
		_rowContentWidthChange: function(e)
		{
			var settings		= FLBuilderConfig.global,
				row 	 		= this.elements.node,
				content  		= this.elements.content.find('.fl-row-content'),
				maxWidth 		= this.elements.maxContentWidth.val(),
				maxWidthUnit 	= this.elements.maxContentWidthUnit.val();

			row.css( 'max-width', 'none' );
			content.css( 'max-width', 'none' );

			if(this.elements.contentWidth.val() == 'full') {
				content.removeClass('fl-row-fixed-width');
				content.addClass('fl-row-full-width');
			}
			else {
				content.removeClass('fl-row-full-width');
				content.addClass('fl-row-fixed-width');
				this._rowMaxContentWidthChange();
			}
		},

		/**
		 * Fires when the content width field of a row changes.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _rowContentWidthChange
		 * @param {Object} e An event object.
		 */
		_rowMaxContentWidthChange: function(e)
		{
			var settings	= FLBuilderConfig.global,
				row     	= this.elements.node,
				content 	= this.elements.content.find('.fl-row-content'),
				width   	= this.elements.maxContentWidth.val(),
				unit		= this.elements.maxContentWidthUnit.val();

			if ( '' == width ) {
				width = settings.row_width + settings.row_width_unit;
			} else {
				width += unit;
			}

			if ( 'fixed' === this.elements.width.val() ) {
				row.css( 'max-width', width );
			}

			content.css( 'max-width', width );
		},

		/**
		 * Fires when the height field of a row changes.
		 *
		 * @since 1.6.3
		 * @access private
		 * @method _rowHeightChange
		 * @param {Object} e An event object.
		 */
		_rowHeightChange: function(e)
		{
			var row = this.elements.node,
				content = this.elements.content;

			row.removeClass('fl-row-align-top');
			row.removeClass('fl-row-align-center');
			row.removeClass('fl-row-align-bottom');
			row.removeClass('fl-row-full-height');
			row.removeClass('fl-row-custom-height');

			if(this.elements.height.val() == 'full') {
				row.addClass('fl-row-full-height');
				row.addClass('fl-row-align-' + this.elements.align.val());
				this.elements.minHeight.val( '' ).trigger( 'input' );
			} else if(this.elements.height.val() == 'custom') {
				row.addClass('fl-row-custom-height');
				row.addClass('fl-row-align-' + this.elements.align.val());
				this.elements.minHeight.trigger( 'input' );
			} else {
				this.elements.minHeight.val( '' ).trigger( 'input' );
			}
		},

		/* Columns Settings
		----------------------------------------------------------*/

		/**
		 * Initializes a column preview.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _initRow
		 */
		_initColumn: function()
		{
			// Elements
			$.extend(this.elements, {
				size         	: $(this.classes.settings + ' input[name=size]'),
				sizeMedium      : $(this.classes.settings + ' input[name=size_medium]'),
				sizeResponsive  : $(this.classes.settings + ' input[name=size_responsive]'),
				columnHeight 	: $(this.classes.settings + ' select[name=equal_height]'),
				columnAlign     : $(this.classes.settings + ' select[name=content_alignment]'),
				responsiveOrder : $(this.classes.settings + ' select[name=responsive_order]')
			});

			// Events
			this.elements.size.on(   		   'input', $.proxy( this._colSizeChange, this ) );
			this.elements.sizeMedium.on(   	   'input', $.proxy( this._colSizeChange, this ) );
			this.elements.sizeResponsive.on(   'input', $.proxy( this._colSizeChange, this ) );
			this.elements.columnHeight.on(     'change', $.proxy( this._colHeightChange, this ) );
			this.elements.columnAlign.on(      'change', $.proxy( this._colHeightChange, this ) );
			this.elements.responsiveOrder.on(  'change', $.proxy( this._colResponsiveOrder, this ) );

			// Common Elements
			this._initNodeTextColor();
			this._initNodeBg();
			this._initNodeClassName();
			this._initNodeDimensions( 'border' );
			this._initNodeDimensions( 'margin' );
			this._initNodeDimensions( 'padding' );
		},

		/**
		 * Fires when the size field of a column changes.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _colSizeChange
		 */
		_colSizeChange: function( e )
		{
			var input			= $( e.target ),
				minWidth        = 8,
				maxWidth        = 100 - minWidth,
				size            = parseFloat( input.val() ),
				group			= this.elements.node.closest( '.fl-col-group' ),
				prev            = this.elements.node.prev('.fl-col'),
				next            = this.elements.node.next('.fl-col'),
				sibling         = next.length === 0 ? prev : next,
				siblings        = this.elements.node.siblings('.fl-col'),
				siblingsWidth   = 0,
				mode 			= FLBuilderResponsiveEditing._mode;

			// Don't resize if we only have one column.
			if(siblings.length === 0) {
				return;
			}

			// Find the fallback size if we don't have a number.
			if ( isNaN( size ) ) {
				if ( 'medium' === mode ) {
					size = this.elements.size.val();
				} else if ( 'responsive' === mode ) {
					if ( this.elements.sizeMedium.val() ) {
						size = this.elements.sizeMedium.val();
					} else {
						size = 'auto';
					}
				}

				if ( 'auto' !== size && isNaN( size ) ) {
					size = minWidth;
				}
			}

			// Default mode logic to keep columns from stacking because of resize.
			if ( 'default' === mode ) {

				// Adjust sizes based on other columns.
				siblings.each(function() {

					if($(this).data('node') == sibling.data('node')) {
						return;
					}

					maxWidth        -= parseFloat($(this)[0].style.width);
					siblingsWidth   += parseFloat($(this)[0].style.width);
				});

				// Make sure the new width isn't too small.
				if(size < minWidth) {
					size = minWidth;
				}

				// Make sure the new width isn't too big.
				if(size > maxWidth) {
					size = maxWidth;
				}

				// Update the width.
				this.elements.node.css('width', size + '%');
				sibling.css('width', (100 - siblingsWidth - size) + '%');

			} else {

				// Don't allow resizing past 100%.
				if ( size > 100 ) {
					size = 100;
					input.val( 100 );
				}

				// Update the width for responsive sizes.
				this.updateCSSRule( this.classes.node, {
					'max-width': ( 'auto' === size ? 100 : size ) + '% !important',
					'width': ( 'auto' === size ? size : size + '%' ) + ' !important',
				}, undefined, true );

				// Float the column only if we have a responsive size.
				if ( 'responsive' === mode ) {
					if ( input.val() ) {
						this.updateCSSRule( this.classes.node, 'float', ( FLBuilderConfig.isRtl ? 'right' : 'left' ), true );
						this.updateCSSRule( this.classes.node, 'clear', 'none', true );
					} else {
						this.updateCSSRule( this.classes.node, 'float', 'none', true );
						this.updateCSSRule( this.classes.node, 'clear', 'both', true );
					}

					if ( input.val() || this._colsHaveCustomResponsiveWidth( siblings ) ) {
						group.addClass( 'fl-col-group-custom-width' );
					} else {
						group.removeClass( 'fl-col-group-custom-width' );
					}
				}
			}
		},

		/**
		 * Checks to see if any columns in a group have
		 * custom responsive widths.
		 *
		 * @since 2.2
		 * @access private
		 * @method _colsHaveCustomResponsiveWidth
		 * @return {Boolean}
		 */
		_colsHaveCustomResponsiveWidth: function( cols )
		{
			var settings = FLBuilderSettingsConfig.nodes,
				hasWidth = false;

			cols.each( function() {
				var id = $( this ).data( 'node' );
				if ( settings[ id ] && settings[ id ].size_responsive ) {
					hasWidth = true;
				}
			} );

			return hasWidth;
		},

		/**
		 * Fires when the equal height field of a column changes.
		 *
		 * @since 1.6.3
		 * @access private
		 * @method _colHeightChange
		 */
		_colHeightChange: function()
		{
			var parent = this.elements.node.parent('.fl-col-group');

			parent.removeClass('fl-col-group-align-top');
			parent.removeClass('fl-col-group-align-center');
			parent.removeClass('fl-col-group-align-bottom');

			if(this.elements.columnHeight.val() == 'yes') {
				parent.addClass('fl-col-group-equal-height');
				parent.addClass('fl-col-group-align-' + this.elements.columnAlign.val());
			}
			else {
				parent.removeClass('fl-col-group-equal-height');
			}
		},

		/**
		 * Fires when the responsive order field of a column changes.
		 *
		 * @since 1.8
		 * @access private
		 * @method _colResponsiveOrder
		 */
		_colResponsiveOrder: function()
		{

			var parent = this.elements.node.parent('.fl-col-group');

			if(this.elements.responsiveOrder.val() == 'reversed') {
				parent.addClass('fl-col-group-responsive-reversed');
			}
			else {
				parent.removeClass('fl-col-group-responsive-reversed');
			}
		},

		/* Module Settings
		----------------------------------------------------------*/

		/**
		 * Initializes a module preview.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _initModule
		 */
		_initModule: function()
		{
			this._initNodeClassName();
			this._initNodeDimensions( 'margin' );
		},

		/* Default Field Previews
		----------------------------------------------------------*/

		/**
		 * Initializes the default preview logic for each
		 * field in a settings form.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _initDefaultFieldPreviews
		 * @param {Object} fields
		 */
		_initDefaultFieldPreviews: function( fields )
		{
			var fields      = ! FLBuilder.isUndefined(fields) ? fields : this.elements.settings.find('.fl-field'),
				field       = null,
				fieldType   = null,
				preview     = null,
				i           = 0;

			if ( FLBuilderConfig.safemode ) {
				return false;
			}

			for( ; i < fields.length; i++) {

				field = fields.eq(i);
				fieldType = field.data( 'type' );
				preview = field.data('preview');

				if(preview.type == 'refresh') {
					this._initFieldRefreshPreview(field);
				}
				if(preview.type == 'text') {
					this._initFieldTextPreview(field);
				}
				if(preview.type == 'css') {
					this._initFieldCSSPreview(field);
				}
				if(preview.type == 'widget') {
					this._initFieldWidgetPreview(field);
				}
				if(preview.type == 'font') {
					this._initFieldFontPreview(field);
				}
				if(preview.type == 'attribute') {
					this._initFieldAttributePreview(field);
				}
				if(preview.type == 'animation') {
					this._initFieldAnimationPreview(preview, field);
				}
				if(preview.type == 'callback') {
					this._initFieldCallbackPreview( preview, field, fieldType, fields );
				}

				this._initFieldUnitSelect(field);
			}
		},

		/**
		 * Setup callback type previews
		 *
		 * @since 2.2
		 * @access private
		 * @method _initFieldCallbackPreview
		 * @param {Object} preview - the preview args from the field configuration
		 * @param {Object} field - reference to the .fl-field DOM element
		 * @return void
		 */
		_initFieldCallbackPreview: function ( preview, field, fieldType, fields ) {
			var callback,
				callback_name = preview['callback'],
				form = $( '.fl-builder-settings:visible' ),
				nodeID = form.data('node'),
				node = $('.fl-builder-content .fl-node-' + nodeID );

			if ( 'undefined' !== typeof FLBuilderPreviewCallbacks[callback_name] ) {
				callback = FLBuilderPreviewCallbacks[callback_name];
			} else if ( 'undefined' !== typeof window[callback_name] ) {
				callback = window[callback_name];
			}

			if ( 'function' === typeof callback ) {
				var args = {
					field: field,
					fields: fields,
					type: fieldType,
					preview: preview,
					form: form,
					nodeID: nodeID,
					node: node,
				};

				// Grab input references
				switch( fieldType ) {
					case 'align':
					case 'button-group':
					case 'text':
					case 'multiple-photos':
					case 'video':
					case 'icon':
					case 'ordering':
						args.input = field.find('input');
						args.getValue = function() {
							return args.input.val();
						}
						break;

					case 'color':
						args.input = field.find('input.fl-color-picker-value');
						args.getValues = function() {
							var value = args.input.val(),
								values = {
									value: value,
									formattedValue: FLBuilderPreview.formatColor( value ),
							};
						}
						break;

					case 'textarea':
					case 'code':
						args.textarea = field.find('textarea');
						args.getValue = function() {
							return args.textarea.val();
						}
						break;

					case 'select':
					case 'photo-sizes':
					case 'post-type':
						args.select = field.find('select');
						args.getValue = function() {
							return args.select.val();
						}
						break;

					case 'photo':
						args.input = field.find('input[type=hidden]');
						args.sizeSelect = field.find('select');
						args.getValues = function() {
							return {
								value: args.input.val(),
								size: args.sizeSelect.val(),
							};
						}
						break;

					case 'unit':
						args.input = field.find('input[type=number]');
						args.unitSelect = field.find( '.fl-field-unit-select' );
						args.getValues = function() {
							var inputVal = args.input.val(),
								unitVal = args.unitSelect.val(),
								values = {
									value: inputVal,
									unit: unitVal,
									formattedValue: inputVal + unitVal
							};
							return values;
						}
						break;

					case 'dimension':
						args.inputs = field.find('input[type=number]');
						args.unitSelect = field.find( '.fl-field-unit-select' );
						args.getValues = function() {
							var values = {
								inputs: [],
								props: {},
								unit: args.unitSelect.val(),
							};

							args.inputs.each( function( i, input ) {
								var input = $( input ),
									val = input.val(),
									prop = input.data('unit');

								values.inputs.push( val );
								values.props[prop] = val;
							} );

							return values;
						}
						break;

					case 'animation':
						args.input = field.find('input');
						args.select = field.find('select');
						args.getValues = function() {
							return {
								delay: args.input.val(),
								style: args.select.val(),
							};
						}
						break;

					case 'link':
						args.input = field.find('.fl-link-field-input-wrap input');
						args.targetInput = field.find('input[name$=_target]');
						args.noFollowInput = field.find('input[name$=_nofollow]');
						args.getValues = function() {
							return {
								url: args.input.val(),
								target: args.targetInput.val(),
								noFollow: args.noFollowInput.val(),
							}
						}
						break;

					case 'shadow':
						args.colorInput = field.find('input.fl-color-picker-value');
						args.inputs = field.find('input[type=number]');
						args.getValues = function() {
							var values = {
								color: args.colorInput.val(),
								x: args.inputs[0].val(),
								y: args.inputs[1].val(),
								blur: args.inputs[2].val(),
								spread: args.inputs[3].val(),
							}
						}
						break;

					case 'gradient':
						// for event setup
						args.inputs = field.find('input');
						args.select = field.find('select');
						// callback helpers
						args.gradientInputs = {};
						args.gradientInputs.type = field.find('select[name$="[type]"]');
						args.gradientInputs.angle = field.find('input[name$="[angle]"]');
						args.gradientInputs.position = field.find('select[name$="[position]"]');

						args.gradientInputs.stops = [];
						field.find('.fl-gradient-picker-colors .fl-gradient-picker-color-row').each( function( i, row ) {
							row = $(row);
							args.gradientInputs.stops.push({
								color: row.find('.fl-gradient-picker-color input'),
								stop: row.find('.fl-gradient-picker-stop input'),
							});
						});

						args.getValues = function() {
							var values = {
								type: args.gradientInputs.type.val(),
								angle: args.gradientInputs.angle.val(),
								position: args.gradientInputs.position.val(),
								stops: [],
							};
							for( var i in args.gradientInputs.stops ) {
								var stop = args.gradientInputs.stops[i];
								values.stops[i] = {
									color: stop.color.val(),
									stop: stop.stop.val(),
								}
							}
							return values;
						}
						break;

					case 'shape-transform':
						args.inputs = field.find('input');
						args.getValues = function() {
							return {
								scaleXSign: args.inputs.eq(0).val(),
								scaleYSign: args.inputs.eq(1).val(),
								skewX: args.inputs.eq(2).val(),
								skewY: args.inputs.eq(3).val(),
								scaleX: args.inputs.eq(4).val(),
								rotate: args.inputs.eq(5).val(),
								scaleY: args.inputs.eq(6).val(), /* hidden field */
							}
						}

						break;
					default:
						args.input = field.find('input');
						args.getValue = function() {
							return args.input.val();
						}
				}

				// Grab reference to responsive toggle
				var toggle = field.find( '.fl-field-responsive-toggle');
				args.responsiveToggle = toggle.length ? toggle : false;

				callback = callback.bind( this, args );

				// Loop over gathered inputs and setup event listeners
				var props = {
					input: 'change keyup input',
					inputs: 'change keyup input',
					targetInput: 'change keyup input',
					noFollowInput: 'change keyup input',
					colorInput: 'change input',
					textarea: 'change keyup input',
					select: 'change',
					sizeSelect: 'change',
					unitSelect: 'change',
				};

				for( var i in props ) {
					if ( 'undefined' !== typeof args[i] ) {
						args[i].on( props[i], callback );
					}
				}
			}
		},

		/* Refresh Preview
		----------------------------------------------------------*/

		/**
		 * Initializes the refresh preview for a field.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _initFieldRefreshPreview
		 * @param {Object} field The field to preview.
		 */
		_initFieldRefreshPreview: function(field)
		{
			var fieldType = field.data('type'),
				preview   = field.data('preview'),
				callback  = $.proxy(this.delayPreview, this);

			switch(fieldType) {

				case 'align':
					field.find( 'input' ).on( 'change', callback );
				break;

				case 'text':
					field.find('input[type=text]').on('keyup', callback);
				break;

				case 'textarea':
					field.find('textarea').on('keyup', callback);
				break;

				case 'select':
					field.find('select').on('change', callback);
				break;

				case 'color':
					field.find('.fl-color-picker-value').on('change', callback);
				break;

				case 'photo':
					field.find('select').on('change', callback);
				break;

				case 'multiple-photos':
					field.find('input').on('change', callback);
				break;

				case 'photo-sizes':
					field.find('select').on('change', callback);
				break;

				case 'video':
					field.find('input').on('change', callback);
				break;

				case 'multiple-audios':
					field.find('input').on('change', callback);
				break;

				case 'icon':
					field.find('input').on('change', callback);
				break;

				case 'form':
					field.on( 'change', 'input', callback);
				break;

				case 'editor':
					this._addTextEditorCallback(field, preview);
				break;

				case 'code':
					field.find('textarea').on('change', callback);
				break;

				case 'post-type':
					field.find('select').on('change', callback);
				break;

				case 'suggest':
					field.find('.as-values').on('change', callback);
					field.find('select').on('change', callback);
				break;

				case 'unit':
				case 'dimension':
					field.find('input[type=number]').on('input', callback);
				break;

				case 'ordering':
					field.find('input[type=hidden]').on('change', callback);
				break;

				default:
					field.on('change', callback);
			}
		},

		/* Text Preview
		----------------------------------------------------------*/

		/**
		 * Initializes a text preview for a field.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _initFieldTextPreview
		 * @param {Object} field The field to preview.
		 */
		_initFieldTextPreview: function(field)
		{
			var fieldType = field.data('type'),
				preview   = field.data('preview'),
				callback  = $.proxy(this._previewText, this, preview);

			switch(fieldType) {

				case 'text':
					field.find('input[type=text]').on('keyup', callback);
				break;

				case 'unit':
					field.find('input[type=number]').on('keyup', callback);
				break;

				case 'textarea':
					field.find('textarea').on('keyup', callback);
				break;

				case 'code':
					field.find('textarea').on('change', callback);
				break;

				case 'editor':
					this._addTextEditorCallback(field, preview);
				break;
			}
		},

		/**
		 * Runs a real time preview for text fields.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _previewText
		 * @param {Object} preview A preview object.
		 * @param {Object} e An event object.
		 */
		_previewText: function(preview, e)
		{
			var selector = this._getPreviewSelector( this.classes.node, preview.selector ),
				element  = $( selector ),
				text     = $('<div>' + $(e.target).val() + '</div>');

			if(element.length > 0) {
				text.find('script').remove();
				element.html(text.html());
			} else {
				this.delayPreview(e);
			}
		},

		/**
		 * Runs a real time preview for text editor fields.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _previewText
		 * @param {Object} preview A preview object.
		 * @param {String} id The ID of the text editor.
		 * @param {Object} e An event object.
		 */
		_previewTextEditor: function(preview, id, e)
		{
			var selector = this._getPreviewSelector( this.classes.node, preview.selector ),
				element  = $( selector ),
				editor   = typeof tinyMCE != 'undefined' ? tinyMCE.get(id) : null,
				textarea = $('#' + id),
				text     = '';

			if(element.length > 0) {

				if(editor && textarea.css('display') == 'none') {
					text = $('<div>' + editor.getContent() + '</div>');
				}
				else {
					if ( 'undefined' == typeof switchEditors || 'undefined' == typeof switchEditors.wpautop ) {
						text = $('<div>' + textarea.val() + '</div>');
					}
					else {
						text = $('<div>' + switchEditors.wpautop( textarea.val() ) + '</div>');
					}
				}

				text.find('script').remove();
				element.html(text.html());
			}
		},

		/**
		 * Callback for text editor previews.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _previewText
		 * @param {Object} field A field object.
		 * @param {Object} preview A preview object.
		 */
		_addTextEditorCallback: function(field, preview)
		{
			var id       = field.find('textarea.wp-editor-area').attr('id'),
				callback = null;

			if(preview.type == 'refresh') {
				callback = $.proxy(this.delayPreview, this);
			}
			else if(preview.type == 'text') {
				callback = $.proxy(this._previewTextEditor, this, preview, id);
			}
			else {
				return;
			}

			$('#' + id).on('keyup', callback);

			if(typeof tinyMCE != 'undefined') {
				editor = tinyMCE.get(id);
				editor.on('change', callback);
				editor.on('keyup', callback);
			}
		},

		/* Font Field Preview
		----------------------------------------------------------*/

		/**
		 * Initializes a font preview for a field.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _initFieldFontPreview
		 * @param {Object} field The field to preview.
		 */
		_initFieldFontPreview: function(field)
		{
			var fieldType = field.data('type'),
				preview   = field.data('preview');

			// store field id
			preview.id = field.attr( 'id' );

			var callback  = $.proxy(this._previewFont, this, preview);

			if( fieldType == 'font' ){
				field.find('.fl-font-field').on('change', 'select', callback);
			}

		},

		/**
		 * Gets the selected font and weight, and make the necessary updates for live preview.
		 *
		 * @since 1.6.3
		 * @access private
		 * @see _getPreviewSelector
		 * @see _buildFontStylesheet
		 * @see updateCSSRule
		 *
		 * @method _previewFont
		 * @param  {Object} preview An object with data about the current field and css selector.
		 * @param  {[type]} e       The current field.
		 */
		_previewFont: function( preview, e ){
			var parent     = $( e.delegateTarget ),
				font       = parent.find( '.fl-font-field-font' ),
				selected   = $( font ).find( ':selected' ),
				fontGroup  = selected.parent().attr( 'label' ),
				weight     = parent.find( '.fl-font-field-weight' ),
				uniqueID   = preview.id + '-' + this.nodeId,
				selector = this._getPreviewSelector( this.classes.node, preview.selector ),
				important = preview.important ? ' !important' : '',
				val = ''

			// If the selected font is a Google Font, build the font stylesheet
			if( fontGroup == 'Google' || fontGroup == 'Recently Used' ){
				this._buildFontStylesheet( uniqueID, font.val(), weight.val() );
			}

			val = font.val();

			// Some google fonts that end with numbers need to be wrapped in quotes.
			var checkNum = new RegExp('[0-9]');
			if( checkNum.test( font.val() ) ){
				val = '"' + font.val() + '"';
			}

			this.updateCSSRule( selector, 'font-family', 'Default' === font.val() ? '' : val + important );
			this.updateCSSRule( selector, 'font-weight', 'default' === weight.val() ? '' : weight.val() + important );
		},

		/**
		 * Gets all fonts store insite FLBuilderPreview._fontsList and renders the respective
		 * link tag with Google Fonts.
		 *
		 * @since 1.6.3
		 * @access private
		 *
		 * @method _buildFontStylesheet
		 * @param  {String} id     The field unique ID.
		 * @param  {String} font   The selected font.
		 * @param  {String} weight The selected weight.
		 */
		_buildFontStylesheet: function( id, font, weight ){
			var url     = FLBuilderConfig.googleFontsUrl,
				href    = '',
				fontObj = {},
				fontArray = {};

			// build the font family / weight object
			fontObj[ font ] = [ weight ];

			// adds to the list of fonts for this font setting
		    FLBuilderPreview._fontsList[ id ] = fontObj;

			// iterate over the keys of the FLBuilderPreview._fontsList object
			Object.keys( FLBuilderPreview._fontsList ).forEach( function( fieldFont ) {

				var field = FLBuilderPreview._fontsList[ fieldFont ];

				// iterate over the font / weight object
				Object.keys( field ).forEach( function( key ) {

					// get the weights of this font
					var weights = field[ key ];
					fontArray[ key ] = fontArray[ key ] || [];

					// remove duplicates from the values array
					weights = weights.filter( function( weight ) {
				        return fontArray[ key ].indexOf( weight ) < 0;
				    });

					fontArray[ key ] = fontArray[ key ].concat( weights );

				});

			});

			$.each( fontArray, function( font, weight ){
				if ( 'Molle' === font ) {
					href += font + ':i|';
				} else {
					href += font + ':' + weight.join() + '|';
				}
			} );

			// remove last character and replace spaces with plus signs
			href = url + href.slice( 0, -1 ).replace( ' ', '+' );

			if( $( '#fl-builder-google-fonts-preview' ).length < 1 ){
				$( '<link>' )
					.attr( 'id', 'fl-builder-google-fonts-preview' )
					.attr( 'type', 'text/css' )
					.attr( 'rel', 'stylesheet' )
					.attr( 'href', href )
					.appendTo('head');
			} else{
				$( '#fl-builder-google-fonts-preview' ).attr( 'href', href );
			}

		},

		/* CSS Preview
		----------------------------------------------------------*/

		/**
		 * Initializes CSS previews for a node.
		 *
		 * @since 1.3.3
		 * @since 1.6.1 Reworked to accept a preview.rules array.
		 * @access private
		 * @method _initFieldCSSPreview
		 * @param {Object} field A field object.
		 */
		_initFieldCSSPreview: function( field )
		{
			var preview = field.data( 'preview' ),
				i 		= null;

			if ( 'undefined' != typeof preview.rules ) {
				for ( i in preview.rules ) {
					this._initFieldCSSPreviewCallback( field, preview.rules[ i ] );
				}
			}
			else {
				this._initFieldCSSPreviewCallback( field, preview );
			}
		},

		/**
		 * Initializes CSS preview callbacks for a field.
		 *
		 * @since 1.6.1
		 * @access private
		 * @method _initFieldCSSPreviewCallback
		 * @param {Object} field A field object.
		 * @param {Object} preview The preview data object.
		 */
		_initFieldCSSPreviewCallback: function( field, preview )
		{
			switch ( field.data( 'type' ) ) {

				case 'align':
					field.find( 'input' ).on( 'change', $.proxy( this._previewCSS, this, preview, field ) );
				break;

				case 'border':
					field.find( 'select' ).on( 'change', $.proxy( this._previewBorderCSS, this, preview, field ) );
					field.find( 'input[type=number]' ).on( 'input', $.proxy( this._previewBorderCSS, this, preview, field ) );
					field.find( 'input[type=hidden]' ).on( 'change', $.proxy( this._previewBorderCSS, this, preview, field ) );
				break;

				case 'color':
					field.find( '.fl-color-picker-value' ).on( 'change', $.proxy( this._previewColorCSS, this, preview, field ) );
				break;

				case 'dimension':
					field.find( 'input[type=number]' ).on( 'input', $.proxy( this._previewDimensionCSS, this, preview, field ) );
				break;

				case 'gradient':
					field.find( 'select' ).on( 'change', $.proxy( this._previewGradientCSS, this, preview, field ) );
					field.find( '.fl-gradient-picker-angle' ).on( 'input', $.proxy( this._previewGradientCSS, this, preview, field ) );
					field.find( '.fl-color-picker-value' ).on( 'change', $.proxy( this._previewGradientCSS, this, preview, field ) );
					field.find( '.fl-gradient-picker-stop' ).on( 'input', $.proxy( this._previewGradientCSS, this, preview, field ) );
				break;

				case 'photo':
					field.find( 'select' ).on( 'change', $.proxy( this._previewCSS, this, preview, field ) );
				break;

				case 'select':
					field.find( 'select' ).on( 'change', $.proxy( this._previewCSS, this, preview, field ) );
				break;

				case 'shadow':
					field.find( 'input' ).on( 'input', $.proxy( this._previewShadowCSS, this, preview, field ) );
					field.find( '.fl-color-picker-value' ).on( 'change', $.proxy( this._previewShadowCSS, this, preview, field ) );
				break;

				case 'text':
					field.find( 'input[type=text]' ).on( 'keyup', $.proxy( this._previewCSS, this, preview, field ) );
				break;

				case 'typography':
					field.find( 'select' ).on( 'change', $.proxy( this._previewTypographyCSS, this, preview, field ) );
					field.find( 'input[type=number]' ).on( 'input', $.proxy( this._previewTypographyCSS, this, preview, field ) );
					field.find( 'input[type=hidden]' ).on( 'change', $.proxy( this._previewTypographyCSS, this, preview, field ) );
				break;

				case 'unit':
					field.find( 'input[type=number]' ).on( 'input', $.proxy( this._previewCSS, this, preview, field ) );
				break;
			}
		},

		/**
		 * Updates the CSS rule for a preview.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _previewCSS
		 * @param {Object} preview A preview object.
		 * @param {Object} field A preview field element.
		 * @param {Object} e An event object.
		 */
		_previewCSS: function( preview, field, e )
		{
			var selector 	= this._getPreviewSelector( this.classes.node, preview.selector ),
				property 	= preview.property,
				unit     	= this._getPreviewCSSUnit( preview, field, e ),
				input    	= $( e.target ),
				value    	= input.val(),
				responsive 	= input.closest( '.fl-field-responsive-setting' ).length ? true : false,
				important 	= preview.important && '' !== value ? ' !important' : '';

			if ( property.indexOf( 'image' ) > -1 && value ) {
				value = 'url(' + value + ')';
			} else if ( '%' === unit && 'opacity' === property ) {
				value = parseInt( value )/100;
			} else if ( '' !== value ) {
				value += unit;
			}

			this.updateCSSRule( selector, property, value + important, responsive );
		},

		/* Border Field CSS Preview
		----------------------------------------------------------*/

		/**
		 * Updates the CSS rule for a border preview.
		 *
		 * @since 2.2
		 * @access private
		 * @method _previewBorderCSS
		 * @param {Object} preview A preview object.
		 * @param {Object} field A field object.
		 * @param {Object} e An event object.
		 */
		_previewBorderCSS: function( preview, field, e )
		{
			var selector = this._getPreviewSelector( this.classes.node, preview.selector ),
				target = $( e.target ),
				field = target.closest( '.fl-field' ),
				wrap = target.closest( '.fl-compound-field-setting' ),
				property = wrap.data( 'property' ),
				value = target.val(),
				unit = wrap.find( '.fl-field-unit-select' ),
				responsive = target.closest( '.fl-field-responsive-setting' ).length ? true : false,
				important = preview.important && '' !== value ? ' !important' : '';

			preview.property = property;

			if ( 'border-color' === property ) {
				this._previewColorCSS( preview, field, e );
			} else if ( 'border-width' === property || 'border-radius' === property ) {
				this._previewDimensionCSS( preview, field, e );
			} else if ( 'box-shadow' === property ) {
				this._previewShadowCSS( preview, wrap, e );
			} else {

				if ( 'border-style' === property ) {
					field.find( '.fl-border-field-width input:visible' ).trigger( 'input' );
				}

				this.updateCSSRule( selector, property, value + important, responsive );
			}
		},

		/* Color Field CSS Preview
		----------------------------------------------------------*/

		/**
		 * Updates the CSS rule for a color preview.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _previewColorCSS
		 * @param {Object} preview A preview object.
		 * @param {Object} field A field object.
		 * @param {Object} e An event object.
		 */
		_previewColorCSS: function(preview, field, e)
		{
			var selector 	= this._getPreviewSelector( this.classes.node, preview.selector ),
				input    	= $(e.target),
				value      	= input.val(),
				responsive 	= input.closest( '.fl-field-responsive-setting' ).length ? true : false,
				important 	= preview.important && '' !== value ? ' !important' : '';

			if ( '' !== value && value.indexOf( 'rgb' ) < 0 ) {
				value = '#' + value;
			}

			this.updateCSSRule( selector, preview.property, value + important, responsive );
		},

		/* Dimension Field CSS Preview
		----------------------------------------------------------*/

		/**
		 * Updates the CSS rule for a dimension field preview.
		 *
		 * @since 2.0.7
		 * @access private
		 * @method _previewDimensionCSS
		 * @param {Object} preview A preview object.
		 * @param {Object} field A preview field element.
		 * @param {Object} e An event object.
		 */
		_previewDimensionCSS: function( preview, field, e )
		{
			var selector = this._getPreviewSelector( this.classes.node, preview.selector ),
				property = preview.property,
				key = field.attr( 'id' ).replace( 'fl-field-', '' ),
				dimension = $( e.target ).data( 'unit' ),
				value = this._getDimensionValue( preview, field, dimension, e ),
				responsive = field.find( '.fl-field-responsive-setting' ).length ? true : false,
				important = preview.important && '' !== value ? ' !important' : '';

			if ( 'border-radius' === property ) {
				property = 'border-' + dimension.replace( '_', '-' ) + '-radius';
			} else if ( 'border-width' === property ) {
				property = 'border-' + dimension + '-width';
			} else {
				property = property + '-' + dimension;
			}

			this.updateCSSRule( selector, property, value + important, responsive );

			if ( 'margin' === key || 'padding' === key || 'border' === key ) {
				if ( this.elements.node.find('.fl-bg-slideshow').length ) {
					FLBuilder._resizeLayout();
				}
			}
		},

		/**
		 * Get a preview dimension value for a property.
		 *
		 * @since 2.2
		 * @access private
		 * @param {Object} preview A preview object.
		 * @param {Object} field A preview field element.
		 * @param {String} dimension The dimension key.
		 * @param {Object} e An event object.
		 * @return {String}
		 */
		_getDimensionValue: function( preview, field, dimension, e )
		{
			var value = $( e.target ).val(),
				unit  = '';

			value = value.toLowerCase().replace( /[^a-z0-9%.\-]/g, '' );

			if ( null !== value && '' !== value && ! isNaN( value ) ) {
				unit = this._getPreviewCSSUnit( preview, field, e );
				value = parseFloat( value ) + ( unit ? unit : 'px' );
			}

			return value;
		},

		/**
		 * Get the value's unit for a CSS preview.
		 *
		 * @since 2.2
		 * @access private
		 * @param {Object} preview A preview object.
		 * @param {Object} field A preview field element.
		 * @param {Object} e An event object.
		 * @return {String}
		 */
		_getPreviewCSSUnit: function( preview, field, e )
		{
			var input 		= $( e.target ),
				mode        = FLBuilderResponsiveEditing._mode,
				compound 	= input.closest( '.fl-compound-field-setting' ).length ? true : false,
				responsive 	= input.closest( '.fl-field-responsive-setting' ).length ? true : false,
				select		= null;

			if ( compound ) {
				select = input.closest( '.fl-compound-field-setting' ).find( '.fl-field-unit-select' );
			} else if ( responsive ) {
				select = input.closest( '.fl-field-responsive-setting' ).find( '.fl-field-unit-select' );
			} else {
				select = field.find( '.fl-field-unit-select' );
			}

			if ( select && select.length ) {
				if ( 'SELECT' === select.prop( 'tagName' ) ) {
					return select.val();
				} else {
					return select.text();
				}
			} else if ( preview.unit ) {
				return preview.unit;
			}

			return '';
		},

		/**
		 * Initializes the custom unit select for a field.
		 *
		 * @since 2.2
		 * @access private
		 * @method _initFieldUnitSelect
		 * @param {Object} field
		 */
		_initFieldUnitSelect: function(field)
		{
			field.find( '.fl-field-unit-select' ).on( 'change', function() {
				var select = $( this ),
					responsive = select.closest( '.fl-field-responsive-setting' ),
					field = select.closest( '.fl-field' );

				if ( responsive.length ) {
					responsive.find( 'input' ).trigger( 'input' );
				} else {
					field.find( 'input' ).trigger( 'input' );
				}
			} );
		},

		/* Gradient Field CSS Preview
		----------------------------------------------------------*/

		/**
		 * Updates the CSS rule for a gradient preview.
		 *
		 * @since 2.2
		 * @access private
		 * @method _previewGradientCSS
		 * @param {Object} preview A preview object.
		 * @param {Object} field A field object.
		 * @param {Object} e An event object.
		 */
		_previewGradientCSS: function( preview, field, e )
		{
			var selector 	= this._getPreviewSelector( this.classes.node, preview.selector ),
				type		= field.find( '.fl-gradient-picker-type-select' ).val(),
				angle		= field.find( '.fl-gradient-picker-angle' ).val(),
				position	= field.find( '.fl-gradient-picker-position' ).val(),
				colors		= field.find( '.fl-color-picker-value' ),
				stops		= field.find( '.fl-gradient-picker-stop input' ),
				values		= [],
				value		= '',
				important 	= '';

			colors.each( function( i ) {
				var color = $( this ).val(),
					stop  = stops.eq( i ).val();

				if ( '' === color ) {
					color = 'rgba(255,255,255,0)';
				}
				if ( color.indexOf( 'rgb' ) < 0 ) {
					color = '#' + color;
				}
				if ( isNaN( stop ) ) {
					stop = 0;
				}

				values.push( color + ' ' + stop + '%' );
			} );

			values = values.join( ', ' );

			if ( 'linear' === type ) {
				if ( isNaN( angle ) ) {
					angle = 0;
				}
				value = 'linear-gradient(' + angle + 'deg, ' + values + ')';
			} else {
				value = 'radial-gradient(at ' + position + ', ' + values + ')';
			}

			important = preview.important && '' !== value ? ' !important' : '';

			this.updateCSSRule( selector, preview.property, value +  important );
		},

		/* Shadow Field CSS Preview
		----------------------------------------------------------*/

		/**
		 * Updates the CSS rule for a shadow preview.
		 *
		 * @since 2.2
		 * @access private
		 * @method _previewShadowCSS
		 * @param {Object} preview A preview object.
		 * @param {Object} field A field object.
		 * @param {Object} e An event object.
		 */
		_previewShadowCSS: function( preview, field, e )
		{
			var selector 	= this._getPreviewSelector( this.classes.node, preview.selector ),
				color		= field.find( '.fl-shadow-field-color input' ).val(),
				horizontal	= field.find( '.fl-shadow-field-horizontal input' ).val(),
				vertical	= field.find( '.fl-shadow-field-vertical input' ).val(),
				blur		= field.find( '.fl-shadow-field-blur input' ).val(),
				spread		= field.find( '.fl-shadow-field-spread input' ).val(),
				hasSpread   = field.find( '.fl-shadow-field-spread input' ).length ? true : false,
				responsive  = $( e.target ).closest( '.fl-field-responsive-setting' ).length ? true : false,
				value		= '',
				important 	= '';

			if ( '' !== color ) {

				if ( '' === horizontal ) {
					horizontal = 0;
				}
				if ( '' === vertical ) {
					vertical = 0;
				}
				if ( '' === blur ) {
					blur = 0;
				}
				if ( '' === spread ) {
					spread = 0;
				}
				if ( color.indexOf( 'rgb' ) < 0 ) {
					color = '#' + color;
				}

				value = horizontal + 'px ';
				value += vertical + 'px ';
				value += blur + 'px ';

				if ( hasSpread ) {
					value += spread + 'px ';
				}

				value += color;
				value += important;
			}

			important = preview.important && '' !== value ? ' !important' : '';

			this.updateCSSRule( selector, preview.property, value, responsive );
		},

		/* Typography Field CSS Preview
		----------------------------------------------------------*/

		/**
		 * Updates the CSS rule for a typography preview.
		 *
		 * @since 2.2
		 * @access private
		 * @method _previewTypographyCSS
		 * @param {Object} preview A preview object.
		 * @param {Object} field A field object.
		 * @param {Object} e An event object.
		 */
		_previewTypographyCSS: function( preview, field, e )
		{
			var selector = this._getPreviewSelector( this.classes.node, preview.selector ),
				target = $( e.target ),
				field = target.closest( '.fl-field' ),
				wrap = target.closest( '.fl-compound-field-setting' ),
				property = wrap.data( 'property' ),
				value = target.val(),
				unit = wrap.find( '.fl-field-unit-select' ),
				responsive = target.closest( '.fl-field-responsive-setting' ).length ? true : false,
				important = preview.important && '' !== value ? ' !important' : '';

			if ( 'font-family' === property ) {
				preview.id = field.attr( 'id' );
				this._previewFont( preview, { delegateTarget: wrap } );
			} else if ( 'text-shadow' === property ) {
				preview.property = 'text-shadow';
				this._previewShadowCSS( preview, wrap, e );
			} else {

				if ( unit.length && '' !== value ) {
					if ( 'vw' === unit.val() ) {
						// calc(14px + 5vw);
						value = 'calc(' + FLBuilderConfig.global.responsive_base_fontsize + 'px + ' + value + 'vw)'
					} else {
						value += 'SELECT' === unit.prop( 'tagName' ) ? unit.val() : 'px';
					}
				}
				this.updateCSSRule( selector, property, value + important, responsive );
			}
		},

		/* Widget Preview
		----------------------------------------------------------*/

		/**
		 * Initializes the attribute preview for a field.
		 *
		 * @since 2.2
		 * @access private
		 * @method _initFieldAttributePreview
		 * @param {Object} field The field to preview.
		 */
		_initFieldAttributePreview: function(field)
		{
			var preview   = field.data('preview'),
				attrName = preview.attribute,
				input = field.find('input'),
				value = field.val(),
				formatValue = window[preview.format_callback];

			var fullSelector = this._getPreviewSelector( this.classes.node, preview.selector ),
				element = $( fullSelector );

			var callback = this._previewAttribute.bind( this, input, element, attrName, formatValue );

			input.on('change', callback );
			input.on('keyup', callback );
			input.on('input', callback );
		},

		/**
		 * Runs a real time preview for attribute fields.
		 *
		 * @since 2.2
		 * @access private
		 * @method _previewAttribute
		 * @param {Object} input A ref to the input control.
		 * @param {Object} element A ref to the selected element within the node.
		 * @param String attrName The name of the attribute to be changed.
		 */
		_previewAttribute: function( input, element, attrName, formatValue ) {
			var value = input.val();
			if ( 'function' === typeof formatValue ) {
				value = formatValue( value );
			}
			for (i = 0; i < element.length; i++) {
				element[i].setAttribute( attrName, value );
			}
		},

		/**
		 * Initializes the preview for a WordPress widget.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _initFieldWidgetPreview
		 * @param {Object} field A field object.
		 */
		_initFieldWidgetPreview: function(field)
		{
			var callback = $.proxy(this.delayPreview, this);

			field.find('input').on('keyup', callback);
			field.find('input[type=checkbox]').on('click', callback);
			field.find('textarea').on('keyup', callback);
			field.find('select').on('change', callback);
		},

		/* Animation Field Preview
		----------------------------------------------------------*/

		/**
		 * Initializes animation previews.
		 *
		 * @since 2.2
		 * @access private
		 * @method _initFieldAnimationPreview
		 */
		_initFieldAnimationPreview: function( preview, field )
		{
			field.find( '.fl-animation-field-style select' ).on( 'change', $.proxy( this._previewAnimationField, this, preview, field ) );
			field.find( '.fl-animation-field-duration input' ).on( 'input', $.proxy( this._previewAnimationField, this, preview, field ) );
		},

		/**
		 * Previews an animation field.
		 *
		 * @since 2.2
		 * @access private
		 * @method _previewAnimationField
		 */
		_previewAnimationField: function( preview, field, e )
		{
			var selector = this._getPreviewSelector( this.classes.node, preview.selector ),
				element = $( selector ),
				animation = field.find( '.fl-animation-field-style select' ),
				duration = field.find( '.fl-animation-field-duration input' ),
				options = animation[0].options;

			element.removeClass( 'fl-animated' );
			element.removeClass( 'fl-animation' );
			element.css( 'animation-duration', '' );

			for ( var i = 0; i < options.length; i++ ) {
				element.removeClass( 'fl-' + options[i].value );
			}

			if ( '' !== animation.val() ) {
				element.addClass( 'fl-animation' );
				element.addClass( 'fl-' + animation.val() );
				element.data( 'animation-delay', 0 );
				element.data( 'animation-duration', duration.val() );
			}

			FLBuilderLayout._doModuleAnimation.apply( element );
		},

		/**
		 * Returns a formatted selector string for a preview.
		 *
		 * @since 1.6.1
		 * @access private
		 * @method _getPreviewSelector
		 * @param {String} selector A CSS selector string.
		 * @return {String}
		 */
		_getPreviewSelector: function( prefix, selector )
		{
			return FLBuilderPreview.getFormattedSelector.call( this, prefix, selector );
		},

		/**
		 * Converts words in a string to upper case.
		 *
		 * @since 2.2
		 * @method toUpperCaseWords
		 * @param {String} string
		 * @return {String}
		 */
		toUpperCaseWords: function( string ) {
			return string.charAt( 0 ).toUpperCase() + string.slice( 1 );
		},
	};

	/**
	 * Format a color value for use with CSS
	 * @since 2.2
	 * @method formatColor
	 * @param {String} value
	 * @return {String}
	 */
	FLBuilderPreview.formatColor = function( value ) {
		if ( '' !== value && ( value.indexOf( 'rgb' ) < 0 && value.indexOf( 'url' ) < 0 ) ) {
			value = '#' + value;
		}
		return value;
	};

	FLBuilderPreviewCallbacks = {

		/**
		 * Setup the shape when selected
		 */
		previewShape: function( args, e ) {
			var position = args.preview.position,
				prefix = args.preview.prefix,
				form = args.form,
				yOrientationInputName = prefix + 'transform[][scaleYSign]';
				yOrientation = form.find('input[name="' + yOrientationInputName + '"]');

			if ( 'bottom' === position ) {
				yOrientation.val('invert');
			} else {
				yOrientation.val('');
			}
			yOrientation.trigger('change');
			// Cause refresh
			this.delayPreview();
		},

		/**
		 * Preview the layer's width, height and Y offset
		 */
		previewShapeLayerSize: function( args, e ) {
			var values = args.getValues(),
				unitValue = values.unit,
				width = values.props.width,
				height = values.props.height,
				top = values.props.top,

				/* static data from field config */
				prefix = args.preview.prefix,
				position = args.preview.position,
				layerSelector = this._getPreviewSelector( this.classes.node, '.fl-builder-' + position + '-edge-layer' ),
				shapeSelector = layerSelector + ' > *',

				/* the align field */
				align = args.form.find('[name="' + prefix + 'align"]'),
				alignValue = align.val(),
				alignParts = alignValue.split(' '),
				yAlign = alignParts[0],
				xAlign = alignParts[1],

				/* calculated props */
				shapeField = args.form.find('[name="' + prefix + 'shape"]'),
				shapeValue = shapeField.val(),
				shapePreset = FLBuilderConfig.presets.shape[shapeValue]
				shapeProps = {};

			// Defaults
			shapeProps.width = '100%';
			shapeProps.left = 'auto';
			shapeProps.right = 'auto';
			shapeProps.height = 'auto';
			shapeProps.top = 'auto';
			shapeProps.bottom = 'auto';

			// Width
			if ( width ) {
				shapeProps.width = width + unitValue;
			 	var offset = ( width / 2 ) + unitValue;

				switch( xAlign ) {
					case 'left':
						shapeProps.left = '0';
						shapeProps.right = 'auto';
						break;
					case 'right':
						shapeProps.left = 'auto';
						shapeProps.right = '0';
						break;
					case 'center':
						shapeProps.left = 'calc( 50% - ' + offset + ')';
						shapeProps.right = 'auto';
						break;
				}
			}
			this.updateCSSRule( shapeSelector, 'width', shapeProps.width );
			this.updateCSSRule( shapeSelector, 'left', shapeProps.left );
			this.updateCSSRule( shapeSelector, 'right', shapeProps.right );

			// Height

			// We need a height for vertical centering to work, but it doesn't have to be explicit.
			var heightOffset;
			if ( height ) {
				heightOffset = ( height / 2 ) + unitValue;
			} else if ( width ) {
				var viewBoxHeight = shapePreset.data.viewBox.width,
					impliedHeight = ( width / viewBoxHeight ) * 100;

				heightOffset = ( impliedHeight / 2 ) + unitValue ;
			} else {
				heightOffset = ''
			}

			if ( height ) {
				shapeProps.height = height + unitValue;
			}

			switch( yAlign ) {
				case 'top':
					shapeProps.top = '0';
					shapeProps.bottom = 'auto';
					break;
				case 'bottom':
					shapeProps.top = 'auto';
					shapeProps.bottom = '0';
					break;
				case 'center':
					shapeProps.top = 'calc( 50% - ' + heightOffset + ')';
					shapeProps.bottom = 'auto';
					break;
			}

			this.updateCSSRule( shapeSelector, 'height', shapeProps.height );
			this.updateCSSRule( shapeSelector, 'top', shapeProps.top );
			this.updateCSSRule( shapeSelector, 'bottom', shapeProps.bottom );

			// Y offset
			if ( '' === top ) {
				this.updateCSSRule( layerSelector, position, '0' );
			} else {
				this.updateCSSRule( layerSelector, position, top + unitValue );
			}

		},

		previewShapeAlign: function( args, e ) {
			// Let width and height preview do the work.
			var prefix = args.preview.prefix,
				widthField = args.form.find('[name="' + prefix + 'size_width"]');

			widthField.trigger('input');
		},

		/**
		 * Process the fill style when toggled
		 *
		 * @param {Object} args - a collection of helper references setup on field init
		 * @param Event e - the event passed by the event listener
		 * @return void
		 */
		previewShapeFillStyle: function( args, e ) {
			var value = args.input.val(),
				preview = args.preview,
				prefix = args.preview.prefix,
				linearGradientId = 'fl-row-' + args.nodeID + '-' + prefix + '-linear-gradient',
				radialGradientId = 'fl-row-' + args.nodeID + '-' + prefix + '-radial-gradient',
				patternId = 'fl-row-' + args.nodeID + '-' + prefix + '-pattern',
				form = args.form;

			if ( 'undefined' !== typeof value ) {
				var selector = this._getPreviewSelector( this.classes.node, preview.selector );

				switch( value ) {
					case 'color':
						var colorValue = form.find('[name=' + prefix + 'fill_color]').val();
						this.updateCSSRule( selector, 'fill', FLBuilderPreview.formatColor( colorValue ) );
						break;
					case 'gradient':
						var gradientField = form.find('#fl-field-' + prefix + 'fill_gradient'),
							gradientType = gradientField.find('select[name$="[type]"]').val();

						var gradientId = 'radial' === gradientType ? radialGradientId : linearGradientId ;

						this.updateCSSRule( selector, 'fill', 'url(#' + gradientId + ')' );
						break;
					case 'pattern':
						var fill = 'url(#' + patternId + ')';
						this.updateCSSRule( selector, 'fill', fill );
				}
			}
		},

		/**
		 * Process the gradient control values
		 *
		 * @param {Object} args - a collection of helper references setup on field init
		 * @param Event e - the event passed by the event listener
		 * @return void
		 */
		previewShapeGradientFill: function( args, e ) {
			var values = args.getValues(),
				node = args.node,
				preview = args.preview,
				layerSelector = '.fl-builder-' + preview.position + '-edge-layer',
				gradientDef = node.find( layerSelector + ' ' + values.type + 'Gradient' ),
				fill = 'url(#' + gradientDef.attr('id') + ')',
				shapeSelector = this._getPreviewSelector( this.classes.node, layerSelector + ' .fl-shape' );

			this.updateCSSRule( shapeSelector, 'fill', fill );

			// Set stops
			var stopEls = gradientDef.find('stop');
			for( var i in values.stops ) {
				var stopVal = values.stops[i],
					stop = stopEls.eq(i),
					color = stopVal.color,
					offset = stopVal.stop,
					opacity = 1;

				if ( color.indexOf( 'rgba' ) === 0 ) {
					var  rawValues = color.substring( color.indexOf('(') + 1, color.lastIndexOf(')') ).split( /,\s*/ );
					opacity = rawValues.pop();
					color = 'rgb(' + rawValues.join(',') + ')';
				}

				stop.attr('stop-color', FLBuilderPreview.formatColor( color ) );
				stop.attr('stop-opacity', opacity );
				stop.attr('offset', offset + '%' );
			}

			// Set Angle
			if ( 'linear' === values.type && 'undefined' !== typeof gradientDef[0] ) {

				gradientDef[0].setAttribute( 'gradientTransform', 'rotate(' + values.angle + ' .5 .5 )' );
			}

			// Set Position
			if ( 'radial' === values.type ) {

				// Split string by space
				parts = values.position.split(' ');

				var x = parts[0],
					y = parts[1],
					cx,
					cy,
					r;

				switch( x ) {
					case 'top':
					case 'left':
						cx = 0;
						break;
					case 'center':
						cx = .5;
						break;
					case 'bottom':
					case 'right':
						cx = 1;
						break;
				}

				switch( y ) {
					case 'top':
					case 'left':
						cy = 0;
						break;
					case 'center':
						cy = .5;
						break;
					case 'bottom':
					case 'right':
						cy = 1;
						break;
				}

				r = .5;
				if ( cx !== .5 || cy !== .5 ) r = 1;

				gradientDef.attr( 'cx', cx );
				gradientDef.attr( 'cy', cy );
				gradientDef.attr( 'r', r );
			}
		},

		/**
		 * Process the transform control values
		 *
		 * @param {Object} args - a collection of helper references setup on field init
		 * @param Event e - the event passed by the event listener
		 * @return void
		 */
		previewShapeTransform: function ( args, e ) {
			var form = args.form,
				preview = args.preview,
				prefix = preview.prefix,
				layerSelector = this._getPreviewSelector( this.classes.node, preview.selector ),
				shapeSelector = layerSelector + ' > *',
				values = args.getValues(),
				shapeTransforms = [];

			Object.keys( values ).map( function( prop ) {
				var value = values[prop];

				var unit = '',
					sign = '';

				switch( prop ) {
					case 'scaleXSign':
					case 'scaleYSign':
						return;

					case 'scaleX':
					case 'scaleY':
						if ( !value || '' === value || 0 === value ) value = '1';

						sign = 'scaleX' === prop ? values['scaleXSign'] : values['scaleYSign'] ; // Positive or negative?

						if ( 'invert' === sign ) {
							value = -Math.abs( value );
						} else {
							value = Math.abs( value );
						}

						shapeTransforms.push( prop + '(' + value + ')' ); // scale has no unit
						break;

					case 'translateX':
					case 'translateY':
						if ( value ) {
							unit = 'px';
							shapeTransforms.push( prop + '(' + value + unit + ')' );
						}
						break;

					case 'skewX':
					case 'skewY':
						if ( value ) {
							unit = 'deg';
							shapeTransforms.push( prop + '(' + value + unit + ')' );
						}
						break;

					case 'rotate':
						unit = 'deg';
						if ( value !== '' && value !== '0' ) {
							shapeTransforms.push( 'rotate(' + value + unit + ')' );
						}

						break;
				}
			} );
			this.updateCSSRule( shapeSelector, 'transform', shapeTransforms.join(' ') );
		}
	}

})(jQuery);
