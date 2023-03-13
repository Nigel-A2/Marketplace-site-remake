( function( $ ){

	/* Internal shorthand */
	var api = wp.customize;

	/**
	 * Helper class for live Customizer previews.
	 *
	 * @since 1.2.0
	 * @class FLCustomizerPreview
	 */
	FLCustomizerPreview = {

		/**
		 * An instance of FLStyleSheet for live previews.
		 *
		 * @since 1.2.0
		 * @access private
		 * @property {FLStyleSheet} _styleSheet
		 */
		_styleSheet: null,

		/**
		 * A reference to the current value of fl-heading-style
		 *
		 * @since 1.7
		 * @access private
		 * @property String _headingStyleValue
		 */
		_headingStyleValue: '',

		/**
		 * A reference of responsive controls.
		 *
		 * @since 1.7.2
		 * @access private
		 * @property String _responsiveControls
		 */
		_responsiveControls: {},

		/**
		 * A reference of the current previewed device.
		 *
		 * @since 1.7.6
		 * @access private
		 * @property String _previewedDevice
		 */
		_previewedDevice: 'desktop',

		/**
		 * Initializes all live Customizer previews.
		 *
		 * @since 1.2.0
		 * @method init
		 */
		init: function()
		{
			var fixedHeader = api('fl-fixed-header').get(),
				headerTopPadding = api('fl-fixed-header-padding-top').get();
			
			// Create the stylesheet.
			this._styleSheet = new FLStyleSheet();

			// Bind CSS callbacks.
			this._css( 'fl-body-bg-image', 'body', 'background-image', 'url({val})', 'none' );
			this._css( 'fl-body-bg-repeat', 'body', 'background-repeat' );
			this._css( 'fl-body-bg-position', 'body', 'background-position' );
			this._css( 'fl-body-bg-attachment', 'body', 'background-attachment' );
			this._css( 'fl-body-bg-size', 'body', 'background-size' );

			this._css( 'fl-title-text-color', 'h1, h1 a', 'color' );
			this._css( 'fl-title-font-format', 'h1', 'text-transform');

			this._css( 'fl-h1-font-size', 'h1', 'font-size', '{val}px', '36px', 'int', true );
			this._css( 'fl-h2-font-size', 'h2', 'font-size', '{val}px', '30px', 'int', true );
			this._css( 'fl-h3-font-size', 'h3', 'font-size', '{val}px', '24px', 'int', true );
			this._css( 'fl-h4-font-size', 'h4', 'font-size', '{val}px', '18px', 'int', true );
			this._css( 'fl-h5-font-size', 'h5', 'font-size', '{val}px', '14px', 'int', true );
			this._css( 'fl-h6-font-size', 'h6', 'font-size', '{val}px', '12px', 'int', true );
			this._css( 'fl-topbar-bg-repeat', '.fl-page-bar', 'background-repeat' );
			this._css( 'fl-topbar-bg-position', '.fl-page-bar', 'background-position' );
			this._css( 'fl-topbar-bg-attachment', '.fl-page-bar', 'background-attachment' );
			this._css( 'fl-topbar-bg-size', '.fl-page-bar', 'background-size' );
			this._css( 'fl-header-bg-repeat', '.fl-page-header', 'background-repeat' );
			this._css( 'fl-header-bg-position', '.fl-page-header', 'background-position' );
			this._css( 'fl-header-bg-attachment', '.fl-page-header', 'background-attachment' );
			this._css( 'fl-header-bg-size', '.fl-page-header', 'background-size' );
			this._css( 'fl-logo-font-size', '.fl-logo-text', 'font-size', '{val}px', '40px', 'int' );
			this._css( 'fl-nav-bg-repeat', '.fl-page-nav-wrap', 'background-repeat' );
			this._css( 'fl-nav-bg-position', '.fl-page-nav-wrap', 'background-position' );
			this._css( 'fl-nav-bg-attachment', '.fl-page-nav-wrap', 'background-attachment' );
			this._css( 'fl-nav-bg-size', '.fl-page-nav-wrap', 'background-size' );
			this._css( 'fl-nav-font-size', '.fl-page-nav .navbar-nav', 'font-size', '{val}px', '16px', 'int' );
			this._css( 'fl-nav-font-size', '.fl-page-nav .navbar-nav a', 'font-size', '{val}px', '16px', 'int' );
			this._css( 'fl-nav-font-size', '.fl-page-header-vertical .fl-page-nav-search a.fa-search', 'font-size', '{val}px', '16px', 'int' );
			this._css( 'fl-nav-font-size', '.fl-page-nav .navbar-toggle', 'font-size', '{val}px', '16px', 'int' );
			this._css( 'fl-nav-font-format', '.fl-page-nav .navbar-nav', 'text-transform' );
			this._css( 'fl-nav-font-format', '.fl-page-nav .navbar-nav a', 'text-transform' );
			this._css( 'fl-nav-font-format', '.fl-page-nav .navbar-toggle', 'text-transform' );
			this._css( 'fl-content-bg-repeat', '.fl-page-content', 'background-repeat' );
			this._css( 'fl-content-bg-position', '.fl-page-content', 'background-position' );
			this._css( 'fl-content-bg-attachment', '.fl-page-content', 'background-attachment' );
			this._css( 'fl-content-bg-size', '.fl-page-content', 'background-size' );
			this._css( 'fl-footer-widgets-bg-repeat', '.fl-page-footer-widgets', 'background-repeat' );
			this._css( 'fl-footer-widgets-bg-position', '.fl-page-footer-widgets', 'background-position' );
			this._css( 'fl-footer-widgets-bg-attachment', '.fl-page-footer-widgets', 'background-attachment' );
			this._css( 'fl-footer-bg-size', '.fl-page-footer-widgets', 'background-size' );
			this._css( 'fl-footer-bg-repeat', '.fl-page-footer', 'background-repeat' );
			this._css( 'fl-footer-bg-position', '.fl-page-footer', 'background-position' );
			this._css( 'fl-footer-bg-attachment', '.fl-page-footer', 'background-attachment' );
			this._css( 'fl-footer-bg-size', '.fl-page-footer', 'background-size' );
			this._css( 'fl-vertical-header-width', '.fl-page-header-vertical', 'width', '{val}px', '230px', 'int' );
			this._css( 'fl-vertical-header-width', '.fl-nav-vertical-left .fl-page-bar, .fl-nav-vertical-left .fl-page-content, .fl-nav-vertical-left .fl-page-footer-wrap, .fl-nav-vertical-left footer.fl-builder-content', 'margin-left', '{val}px', '230px', 'int' );
			this._css( 'fl-vertical-header-width', '.fl-nav-vertical-right .fl-page-bar, .fl-nav-vertical-right .fl-page-content, .fl-nav-vertical-right .fl-page-footer-wrap, .fl-nav-vertical-right footer.fl-builder-content', 'margin-right', '{val}px', '230px', 'int' );
			this._css( 'fl-header-padding', '.fl-page-header-vertical .fl-page-header-logo, .fl-page-header-vertical .fl-page-nav-collapse ul.navbar-nav > li > a, .fl-page-header-vertical .fl-page-nav-search a.fa-search', 'padding-left', '{val}px', '30px', 'int' );
			this._css( 'fl-header-padding', '.fl-page-header-vertical .fl-page-header-logo, .fl-page-header-vertical .fl-page-nav-collapse ul.navbar-nav > li > a, .fl-page-header-vertical .fl-page-nav-search a.fa-search', 'padding-right', '{val}px', '30px', 'int' );
			this._css( 'fl-header-logo-top-spacing', '.fl-nav-vertical .fl-page-header-vertical .fl-page-header-container', 'padding-top', '{val}px', '50px', 'int' );
			this._css( 'fl-nav-item-spacing', '.fl-page-header-vertical .fl-page-nav-collapse ul.navbar-nav > li > a', 'padding-bottom', '{val}px', '15px', 'int' );
			this._css( 'fl-nav-menu-top-spacing', '.fl-page-header-vertical .fl-page-nav-collapse ul.navbar-nav', 'padding-top', '{val}px', '30px', 'int' );
			this._css( 'fl-body-font-size', 'body', 'font-size', '{val}px', '14px', 'int', true );
			this._css( 'fl-body-line-height', 'body', 'line-height', '{val}', '1.45', false, true );
			this._css( 'fl-h1-line-height', 'h1', 'line-height', '{val}', '1.4', false, true );
			this._css( 'fl-h1-letter-spacing', 'h1', 'letter-spacing', '{val}px', '0', false, true );
			this._css( 'fl-h2-line-height', 'h2', 'line-height', '{val}', '1.4', false, true  );
			this._css( 'fl-h2-letter-spacing', 'h2', 'letter-spacing', '{val}px', '0', false, true  );
			this._css( 'fl-h3-line-height', 'h3', 'line-height', '{val}', '1.4', false, true  );
			this._css( 'fl-h3-letter-spacing', 'h3', 'letter-spacing', '{val}px', '0', false, true  );
			this._css( 'fl-h4-line-height', 'h4', 'line-height', '{val}', '1.4', false, true  );
			this._css( 'fl-h4-letter-spacing', 'h4', 'letter-spacing', '{val}px', '0', false, true  );
			this._css( 'fl-h5-line-height', 'h5', 'line-height', '{val}', '1.4', false, true  );
			this._css( 'fl-h5-letter-spacing', 'h5', 'letter-spacing', '{val}px', '0', false, true  );
			this._css( 'fl-h6-line-height', 'h6', 'line-height', '{val}', '1.4', false, true  );
			this._css( 'fl-h6-letter-spacing', 'h6', 'letter-spacing', '{val}px', '0', false, true  );
			this._css( 'fl-layout-spacing', 'body', 'padding', '{val}px 0', '0' );
			this._css( 'fl-header-padding', '.fl-page-nav-centered-inline-logo .fl-page-header-container, .fl-page-nav-bottom .fl-page-header-container, .fl-page-nav-right .fl-page-header-container, .fl-page-nav-left .fl-page-header-container, .fl-page-nav-centered .fl-page-header-container', 'padding', '{val}px 0', '30' );
			this._css( 'fl-nav-item-spacing', '.fl-page-header-vertical .navbar-nav > li > a', 'padding-botom', '{val}px', '0' );
			this._css( 'fl-nav-item-spacing', '.fl-page-nav .navbar-nav > li > a', 'padding-left', '{val}px', '0' );
			this._css( 'fl-nav-item-spacing', '.fl-page-nav .navbar-nav > li > a', 'padding-right', '{val}px', '0' );

			if ('hidden' === fixedHeader || 'fadein' === fixedHeader) {
				this._css('fl-fixed-header-padding-top-custom', '.fl-page', 'padding-top', '0', '0', '', false);
			} else if ( 'custom' === headerTopPadding ){
				this._css('fl-fixed-header-padding-top-custom', '.fl-page', 'padding-top', '{val}px !important', '0', '', false);
			}
		
			// Bind HTML callbacks.
			this._html( 'fl-topbar-col1-text', '.fl-page-bar-text-1' );
			this._html( 'fl-topbar-col2-text', '.fl-page-bar-text-2' );
			this._html( 'fl-logo-text', '.fl-logo-text' );
			this._html( 'fl-footer-col1-text', '.fl-page-footer-text-1' );
			this._html( 'fl-footer-col2-text', '.fl-page-footer-text-2' );

			// Bind custom callbacks.
			this._bind( 'fl-css-code', this._cssCodeChanged );

			// Setup Button Styles Preview
			if ( 'custom' === api( 'fl-button-style' ).get()  ){
				this._initButtonStyles();
			}
			
			// Bind responsive controls callback.
			this._initResponsiveStyles();

			// Setup responsive controls preview.
			this._initResponsivePreview();
		},

		/**
		 * Binds a callback function to be fired when a setting changes.
		 *
		 * @since 1.2.0
		 * @access private
		 * @method _bind
		 * @param {String} key The key of the setting to bind to.
		 * @param {Function} callback The callback function to bind.
		 */
		_bind: function( key, callback )
		{
			api( key, function( val ) {
				val.bind( function( newVal ) {
					callback.call( FLCustomizerPreview, newVal );
				});
			});
		},

		/**
		 * Applies a CSS preview when a setting changes.
		 *
		 * @since 1.2.0
		 * @access private
		 * @method _css
		 * @param {String} key The key of the setting to bind to.
		 * @param {String} selector The CSS selector to apply the change to.
		 * @param {String} property The CSS property to apply the change to.
		 * @param {String} format (Optional) A format in brackets for the value such as "{val}px".
		 * @param {String} fallback (Optional) A fallback value if the value is empty.
		 * @param {String} sanitizeCallback (Optional) The type of sanitization function to call on the value.
		 * @param {Boolean} responsive (Optional) If a control is responsive, then add CSS property for each device.
		 */
		_css: function( key, selector, property, format, fallback, sanitizeCallback, responsive )
		{
			api( key, function( val ) {

				val.bind( function( newVal ) {
					var updateAllowed = true;

					switch ( sanitizeCallback ) {
						case 'int':
						newVal = FLCustomizerPreview._sanitizeInt( newVal );
						break;
					}

					if ( 'undefined' != typeof fallback && null != fallback && '' == newVal ) {
						newVal = fallback;
					}
					else if ( 'undefined' != typeof format && null != format ) {
						newVal = format.replace( '{val}', newVal );
					}

					if ( 'undefined' !== typeof responsive && false === responsive && 'desktop' != FLCustomizerPreview._previewedDevice ) {
						updateAllowed = false;
					}

					if ( updateAllowed ) {
						FLCustomizerPreview._styleSheet.updateRule( selector, property, newVal );
					}
				});
			});

			if ( 'undefined' !== typeof responsive ) {
				FLCustomizerPreview._responsiveControls[ key ] = {
					selector: selector,
					property: property,
					format  : format,
					fallback: fallback,
					sanitizeCallback: sanitizeCallback,
					responsive: responsive
				};
			}
		},

		/**
		 * Applies an HTML preview when a setting changes.
		 *
		 * @since 1.2.0
		 * @access private
		 * @method _html
		 * @param {String} key The key of the setting to bind to.
		 * @param {String} selector The CSS selector to apply the change to.
		 */
		_html: function( key, selector )
		{
			api( key, function( val ) {
				val.bind( function( newVal ) {
					$( selector ).html( newVal );
				});
			});
		},

		/**
		 * Makes sure a value is a number.
		 *
		 * @since 1.2.0
		 * @access private
		 * @method _sanitizeInt
		 * @param {Number} val The value to sanitize.
		 * @return {Number}
		 */
		_sanitizeInt: function( val )
		{
			var number = parseInt( val );

			return isNaN( number ) ? 0 : number;
		},

		/**
		 * Callback for when the custom CSS field is changed.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _cssCodeChanged
		 * @param {String} val
		 */
		_cssCodeChanged: function( val )
		{
			$( '#fl-theme-custom-css' ).html( val );
		},

		_initButtonStyles: function() {
			
			var mainSelector =  		'.fl-page button:visited,' +
										'.fl-page input[type=button],' +
										'.fl-page input[type=submit],' +
										'.fl-page a.fl-button,' +
										'.fl-page a.fl-button:visited,' +
										'.fl-responsive-preview-content button,' +
										'.fl-responsive-preview-content button:visited,' +
										'.fl-responsive-preview-content input[type=button],' +
										'.fl-responsive-preview-content input[type=submit],' +
										'.fl-responsive-preview-content a.fl-button,' +
										'.fl-responsive-preview-content a.fl-button:visited,' +
										'.fl-page .fl-page-nav-toggle-button .fl-page-nav .navbar-toggle,' +
										'.fl-page .fl-page-nav-toggle-button .fl-page-nav .navbar-toggle:visited,' +
										'.fl-page .bc-btn,' +
										'.fl-page button.bc-btn,' +
										'.fl-page button.bc-btn[disabled],' +
										'.fl-page button.bc-link,' +
										'.fl-page a.bc-btn, ' +
										'.fl-page .woocommerce a.button,' +
										'.fl-page .woocommerce a.button:visited,' +
										'body.theme-bb-theme.woocommerce-page .fl-page a.button,' +
										'body.theme-bb-theme.woocommerce-page .fl-page a.button:visited,'+
										'body.theme-bb-theme.woocommerce-page .fl-page .product a.button,' +
										'body.theme-bb-theme.woocommerce-page .fl-page .product a.button:visited,'+
										'body.theme-bb-theme.woocommerce-page .fl-page .product button.button,' +
										'body.theme-bb-theme.woocommerce-page .fl-page .product button.button:visited,'+
										'body.theme-bb-theme.woocommerce-page .fl-page .woocommerce button.button,' +
										'body.theme-bb-theme.woocommerce-page .fl-page .woocommerce button.button:visited,'+
										'body.theme-bb-theme:not(.woocommerce-page) .fl-page .fl-module-woocommerce a.button,' +
										'body.theme-bb-theme:not(.woocommerce-page) .fl-page .fl-module-woocommerce a.button:visited,' + 
										'body.theme-bb-theme:not(.woocommerce-page) .fl-page .fl-post-module-woo-button a.button,' +
										'body.theme-bb-theme:not(.woocommerce-page) .fl-page .fl-post-module-woo-button a.button:visited';
      
			var mainInsideSelector =    '.fl-page button:visited *,' +
										'.fl-page input[type=button] *,' +
										'.fl-page input[type=submit] *,' +
										'.fl-page a.fl-button *,' +
										'.fl-responsive-preview-content button *,' +
										'.fl-responsive-preview-content button:visited *,' +
										'.fl-responsive-preview-content input[type=button] *,' +
										'.fl-responsive-preview-content input[type=submit] *,' +
										'.fl-responsive-preview-content a.fl-button *,' +
										'.fl-responsive-preview-content a.fl-button:visited *,' +
										'.fl-page .fl-page-nav-toggle-button .fl-page-nav .navbar-toggle *,' +
										'.fl-page .fl-page-nav-toggle-button .fl-page-nav .navbar-toggle:visited *,' +
										'.fl-page .bc-btn *,' +
										'.fl-page button.bc-btn *,' +
										'.fl-page button.bc-btn[disabled] *,' +
										'.fl-page button.bc-link *,' +
										'.fl-page a.bc-btn *,' +
										'.fl-page .woocommerce a.button *,' +
										'.fl-page .woocommerce a.button:visited *';

			var hoverSelector = 		'.fl-page input[type=button]:hover,' +
										'.fl-page input[type=submit]:hover,' +
										'.fl-page a.fl-button:hover,' +
										'.fl-responsive-preview-content button:hover,' +
										'.fl-responsive-preview-content input[type=button]:hover,' +
										'.fl-responsive-preview-content input[type=submit]:hover,' +
										'.fl-page .fl-responsive-preview-content a.fl-button:hover,' +
										'.fl-page .fl-page-nav-toggle-button .fl-page-nav .navbar-toggle:hover,' +
										'.fl-responsive-preview-content a.fl-button:hover,' +
										'.fl-page .bc-btn:hover,' +
										'.fl-page button.bc-btn:hover,' +
										'.fl-page button.bc-btn[disabled]:hover,' +
										'.fl-page button.bc-link:hover,' +
										'.fl-page a.bc-btn:hover,' +
										'.fl-page .woocommerce a.button:hover,' +
										'body.theme-bb-theme.woocommerce-page .fl-page a.button:hover,'+
										'body.theme-bb-theme.woocommerce-page .fl-page .product button.button:hover,'+
										'body.theme-bb-theme.woocommerce-page .fl-page .product a.button:hover,'+
										'body.theme-bb-theme.woocommerce-page .fl-page .woocommerce button.button:hover,'+
										'body.theme-bb-theme:not(.woocommerce-page) .fl-page .fl-module-woocommerce a.button:hover,' + 
										'body.theme-bb-theme:not(.woocommerce-page) .fl-page .fl-post-module-woo-button a.button:hover';

			var hoverInsideSelector = 	'.fl-page input[type=button]:hover *,' +
										'.fl-page input[type=submit]:hover *,' +
										'.fl-page a.fl-button:hover *,' +
										'.fl-responsive-preview-content button:hover *,' +
										'.fl-responsive-preview-content input[type=button]:hover *,' +
										'.fl-responsive-preview-content input[type=submit]:hover *,' +
										'.fl-page .fl-responsive-preview-content a.fl-button:hover *,' +
										'.fl-page .fl-page-nav-toggle-button .fl-page-nav .navbar-toggle:hover *,' +
										'.fl-responsive-preview-content a.fl-button:hover *,' +
										'.fl-page .bc-btn:hover *,' +
										'.fl-page button.bc-btn:hover *,' +
										'.fl-page button.bc-btn[disabled]:hover *,' +
										'.fl-page button.bc-link:hover *,' +
										'.fl-page a.bc-btn:hover *,' +
										'.fl-page .woocommerce a.button:hover *';
						
			// Font Size
			this._css( 'fl-button-font-size', mainSelector, 'font-size', '{val}px', '16px', 'int', true );
			
			// Line Height
			this._css( 'fl-button-line-height', mainSelector, 'line-height', '{val}', '1.2', null, true );
			
			// Color
			this._css( 'fl-button-color', mainSelector, 'color' );
			this._css( 'fl-button-color', mainInsideSelector, 'color' );
			// Hover Color
			this._css( 'fl-button-hover-color', hoverSelector, 'color' );
			this._css( 'fl-button-hover-color', hoverInsideSelector, 'color' );
			// Background Color
			this._css( 'fl-button-background-color', mainSelector, 'background-color' );
			// Background Hover
			this._css( 'fl-button-background-hover-color', hoverSelector, 'background-color' );
			// Border Color
			this._css( 'fl-button-border-color', mainSelector, 'border-color' );
			this._css( 'fl-button-border-hover-color', hoverSelector, 'border-color' );
			// Border Width
			this._css( 'fl-button-border-width', mainSelector, 'border-width', '{val}px' );
			this._css( 'fl-button-border-width', hoverSelector, 'border-width', '{val}px' );
			// Border Style
			this._css( 'fl-button-border-style', mainSelector, 'border-style' );
			this._css( 'fl-button-border-style', hoverSelector, 'border-style' );
			// Border Radius
			this._css( 'fl-button-border-radius', mainSelector, 'border-radius', '{val}px' );
			this._css( 'fl-button-border-radius', hoverSelector, 'border-radius', '{val}px' );
			// Text Transform
			this._css( 'fl-button-text-transform', mainSelector, 'text-transform' );
		},

		/**
		 * Callback for when the responsive controls are changed.
		 *
		 * @since 1.7.2
		 * @access private
		 * @method _initResponsiveStyles
		 */
		_initResponsiveStyles: function() {
			var controls 	= FLCustomizerPreview._responsiveControls,
				breakpoints = ['medium', 'mobile'];

			if ( ! $.isEmptyObject( controls ) ) {
				for (i = 0; i < breakpoints.length; i++ ) {
					$.each( controls, function( key, data ) {
						if ( false === data.responsive ) {
							return;
						}

						FLCustomizerPreview._css( key + '_' + breakpoints[i], data.selector, data.property, data.format, data.fallback, data.sanitizeCallback );
					});
				}
			}
		},

		/**
		 * Adds preview for responsive controls.
		 *
		 * @since 1.7.2
		 * @access private
		 * @method _initResponsivePreview
		 */
		_initResponsivePreview: function() {
			var controls = FLCustomizerPreview._responsiveControls,
			    newVal = '',
				controlValues = [];

			// Listen for a previewed-device message.
    		api.preview.bind( 'previewed-device', function( newDevicePreview ) {
				controlValues = api.get();
				FLCustomizerPreview._previewedDevice = newDevicePreview;

				if ( ! $.isEmptyObject( controls ) ) {
					$.each( controls, function( key, data ) {

						if ( 'desktop' != newDevicePreview && false !== data.responsive ) {
							newVal = controlValues[ key + '_' + newDevicePreview ];
						}
						else if ( 'desktop' == newDevicePreview ) {
							newVal = controlValues[ key ];
						}
						else {
							newVal = '';
						}

						switch ( data.sanitizeCallback ) {
							case 'int':
							newVal = FLCustomizerPreview._sanitizeInt( newVal );
							break;
						}

						if ( 'undefined' != typeof data.fallback && null != data.fallback && '' == newVal ) {
							newVal = data.fallback;
						}
						else if ( 'undefined' != typeof data.format && null != data.format ) {
							newVal = data.format.replace( '{val}', newVal );
						}

						FLCustomizerPreview._styleSheet.updateRule( data.selector, data.property, newVal );
					});
				}

			});
		},
	};

	$( function() { FLCustomizerPreview.init(); } );

})( jQuery );
