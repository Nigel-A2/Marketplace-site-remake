( function( $ ) {

	/**
	 * Helper for simulating media queries without resizing
	 * the viewport.
	 *
	 * Parts based on Respond.js by Scott Jehl (https://github.com/scottjehl/Respond)
	 *
	 * @since 1.10
	 * @class SimulateMediaQuery
	 */
	var SimulateMediaQuery = {

		/**
		 * Strings to look for in stylesheet URLs that are
		 * going to be parsed. If a string matches, that
		 * stylesheet won't be parsed.
		 *
		 * @since 1.10
		 * @property {Array} ignored
		 */
		ignored: [],

		/**
		 * Strings to look for in stylesheet URLs. If a
		 * string matches, that stylesheet will be reparsed
		 * on each updated.
		 *
		 * @since 1.10
		 * @property {Array} reparsed
		 */
		reparsed: [],

		/**
		 * The current viewport width to simulate.
		 *
		 * @since 1.10
		 * @property {Number} width
		 */
		width: null,

		/**
		 * A callback to run when an update completes.
		 *
		 * @since 1.10
		 * @property {Function} callback
		 */
		callback: null,

		/**
		 * Cache of original stylesheets.
		 *
		 * @since 1.10
		 * @property {Object} sheets
		 */
		sheets: {},

		/**
		 * Style tags used for rendering simulated
		 * media query styles.
		 *
		 * @since 1.10
		 * @property {Array} styles
		 */
		styles: [],

		/**
		 * AJAX queue for retrieving rules from a sheet.
		 *
		 * @since 1.10
		 * @property {Array} queue
		 */
		queue: [],

		/**
		 * The value of 1em in pixels.
		 *
		 * @since 1.10
		 * @access private
		 * @property {Number} emPxValue
		 */
		emPxValue: null,

		/**
		 * Regex for parsing styles.
		 *
		 * @since 1.10
		 * @property {Object} _regex
		 */
		regex: {
			media: /@media[^{]*{([\s\S]+?})\s*}/ig,
			empty: /@media[^{]*{([^{}]*?)}/ig,
			keyframes: /@(?:\-(?:o|moz|webkit)\-)?keyframes[^\{]+\{(?:[^\{\}]*\{[^\}\{]*\})+[^\}]*\}/gi,
			comments: /\/\*[^*]*\*+([^/][^*]*\*+)*\//gi,
			urls: /(url\()['"]?([^\/\)'"][^:\)'"]+)['"]?(\))/g,
			findStyles: /@media *([^\{]+)\{([\S\s]+?)\}$/,
			only: /(only\s+)?([a-zA-Z]+)\s?/,
			minw: /\(\s*min\-width\s*:\s*(\s*[0-9\.]+)(px|em)\s*\)/,
			maxw: /\(\s*max\-width\s*:\s*(\s*[0-9\.]+)(px|em)\s*\)/,
			minmaxwh: /\(\s*m(in|ax)\-(height|width)\s*:\s*(\s*[0-9\.]+)(px|em)\s*\)/gi,
			other: /\([^\)]*\)/g
		},

		/**
		 * Adds strings to look for in stylesheet URLs
		 * that are going to be parsed. If a string matches,
		 * that stylesheet won't be parsed.
		 *
		 * @since 1.10
		 * @method ignore
		 * @param {Array} strings
		 */
		ignore: function( strings )
		{
			Array.prototype.push.apply( this.ignored, strings );
		},

		/**
		 * Adds strings to look for in stylesheet URLs. If a
		 * string matches, that stylesheet will be reparsed.
		 *
		 * @since 1.10
		 * @method reparse
		 * @param {Array} strings
		 */
		reparse: function( strings )
		{
			Array.prototype.push.apply( this.reparsed, strings );
		},

		/**
		 * Updates all simulated media query rules.
		 *
		 * @since 1.10
		 * @method update
		 * @param {Number} width The viewport width to simulate.
		 * @param {Function} callback
		 */
		update: function( width, callback )
		{
			this.width    = undefined === width ? null : width;
			this.callback = undefined === callback ? null : callback;

			ForceJQueryValues.update();

			if ( this.queueSheets() ) {
				this.runQueue();
			}
			else {
				this.applyStyles();
			}
		},

		/**
		 * Adds all sheets that aren't already cached
		 * to the AJAX queue for fetching <link> sheets.
		 *
		 * @since 1.10
		 * @method queueSheets
		 * @return {Boolean}
		 */
		queueSheets: function()
		{
			var sheet  		= null,
				href   		= null,
				id   		= null,
				tagName		= null,
				rel			= null,
				media  		= null,
				key			= null,
				isCSS  		= null,
				ignore 		= false,
				i      		= 0,
				k      		= 0;

			for ( ; i < document.styleSheets.length; i++ ) {

				element  = document.styleSheets[ i ].ownerNode;
				href   	 = element.href;
				id		 = element.id;
				tagName  = element.tagName.toLowerCase();
				rel		 = element.rel;
				media  	 = element.media;
				key		 = !! href ? href.split( '?' ).shift() : !! id ? id : 'style-' + i;
				isCSS 	 = true;
				ignore 	 = false;

				if ( 'style' === tagName || ( !! href && rel && rel.toLowerCase() === 'stylesheet' ) ) {

					for ( k = 0; k < this.ignored.length; k++ ) {
						if ( key.indexOf( this.ignored[ k ] ) > -1 ) {
							ignore = true;
							break;
						}
					}

					if ( ignore ) {
						continue;
					}

					for ( k = 0; k < this.reparsed.length; k++ ) {
						if ( key.indexOf( this.reparsed[ k ] ) > -1 ) {
							this.sheets[ key ] = null;
							break;
						}
					}

					if ( undefined === this.sheets[ key ] || ! this.sheets[ key ] ) {
						this.queue.push( {
							docSheet : document.styleSheets[ i ],
							element  : $( element ),
							key		 : key,
							tagName  : tagName,
							href  	 : href,
							id		 : id,
							media 	 : media
						} );
					}
				}
			}

			return this.queue.length;
		},

		/**
		 * Send AJAX requests to get styles from all
		 * stylesheets in the queue.
		 *
		 * @since 1.10
		 * @method runQueue
		 */
		runQueue: function()
		{
			var item;

			if ( this.queue.length ) {

				item = this.queue.shift();

				if ( 'style' === item.tagName ) {
					this.parse( item.element.html(), item );
					this.runQueue();
				} else {
					$.get( item.href, $.proxy( function( response ) {
						this.parse( response, item );
						this.runQueue();
					}, this ) ).fail( this.runQueue.bind( this ) );
				}
			}
			else {
				this.applyStyles();
			}
		},

		/**
		 * Parse a stylesheet that has been returned
		 * from an AJAX request.
		 *
		 * @since 1.10
		 * @method parse
		 * @param {String} styles
		 * @param {Array} item
		 */
		parse: function( styles, item )
		{
			var re         = this.regex,
				cleaned    = this.cleanStyles( styles ),
				allQueries = cleaned.match( re.media ),
				length     = allQueries && allQueries.length || 0,
				useMedia   = ! length && item.media,
				query      = null,
				queries    = null,
				media      = null,
				all        = '',
				i          = 0,
				k          = 0;

			if ( allQueries ) {
				all = cleaned.replace( re.media, '' );
			}
			else if ( useMedia && 'all' != item.media ) {
				length = 1;
			}
			else {
				all = cleaned;
			}

			this.sheets[ item.key ] = {
				docSheet : item.docSheet,
				element  : item.element,
				key		 : item.key,
				tagName  : item.tagName,
				href  	 : item.href,
				id		 : item.id,
				all      : all,
				queries  : []
			};

			for ( i = 0; i < length; i++ ) {

				if ( useMedia ) {
					query   = item.media;
					cleaned = this.convertURLs( cleaned, item.href );
				}
				else{
					query   = allQueries[ i ].match( re.findStyles ) && RegExp.$1;
					cleaned = RegExp.$2 && this.convertURLs( RegExp.$2, item.href );
				}

				queries = query.split( ',' );

				for ( k = 0; k < queries.length; k++ ) {

					query = queries[ k ];
					media = query.split( '(' )[ 0 ].match( re.only ) && RegExp.$2;

					if ( 'print' == media ) {
						continue;
					}
					if ( query.replace( re.minmaxwh, '' ).match( re.other ) ) {
						continue;
					}

					this.sheets[ item.key ].queries.push( {
						minw     : query.match( re.minw ) && parseFloat( RegExp.$1 ) + ( RegExp.$2 || '' ),
						maxw     : query.match( re.maxw ) && parseFloat( RegExp.$1 ) + ( RegExp.$2 || '' ),
						styles   : cleaned
					} );
				}
			}
		},

		/**
		 * Applies simulated media queries to the page.
		 *
		 * @since 1.10
		 * @method applyStyles
		 */
		applyStyles: function()
		{
			var head    = $( 'head' ),
				styles 	= { all: '', queries: [] },
				style   = null,
				sheet   = null,
				key     = null,
				query   = null,
				i       = null,
				min     = null,
				max     = null,
				added   = false,
				value	= null;

			// Clear previous styles.
			this.clearStyles();

			// Build the all, min, and max query styles object.
			for ( key in this.sheets ) {

				sheet = this.sheets[ key ];

				if ( ! sheet.queries.length || ! this.width ) {
					continue;
				}

				styles.all += sheet.all;

				for ( i = 0; i < sheet.queries.length; i++ ) {

					query = sheet.queries[ i ];
					min   = query.minw;
					max   = query.maxw;
					added = false;

					if ( min ) {

						min = parseFloat( min ) * ( min.indexOf( 'em' ) > -1 ? this.getEmPxValue() : 1 );

						if ( this.width >= min ) {
							styles.queries.push( {
								media: 'min',
								width: min,
								styles: query.styles,
							} );
							added = true;
						}
					}

					if ( max && ! added ) {

						max = parseFloat( max ) * ( max.indexOf( 'em' ) > -1 ? this.getEmPxValue() : 1 );

						if ( this.width <= max ) {
							styles.queries.push( {
								media: 'max',
								width: max,
								styles: query.styles,
							} );
						}
					}
				}

				sheet.docSheet.disabled = true;
			}

			// Render the all, min, and max query styles.
			if ( '' !== styles.all ) {
				style = $( '<style class="fl-builder-media-query" data-query="all"></style>' );
				this.styles.push( style );
				head.append( style );
				style.html( styles.all );
			}

			for ( i = 0; i < styles.queries.length; i++ ) {
				query = styles.queries[ i ];
				style = $( '<style class="fl-builder-media-query" data-query="' + query.media + '" data-value="' + query.width + '"></style>' );
				this.styles.push( style );
				head.append( style );
				style.html( query.styles );
			}

			// Fire the callback now that we're done.
			if ( this.callback ) {
				this.callback();
				this.callback = null;
			}
		},

		/**
		 * Clears all style tags used to render
		 * simulated queries.
		 *
		 * @since 1.10
		 */
		clearStyles: function()
		{
			var key    = null,
				styles = this.styles.slice( 0 );

			this.styles = [];

			for ( key in this.sheets ) {
				this.sheets[ key ].docSheet.disabled = false;
			}

			for ( var i = 0; i < styles.length; i++ ) {
				styles[ i ].empty();
				styles[ i ].remove();
			}
		},

		/**
		 * Disables style tags used to render simulated queries
		 * equal to or below the specified width.
		 *
		 * @since 2.2
		 * @param {Number} width
		 */
		disableStyles: function( width )
		{
			var style, query, value;

			for ( var i = 0; i < this.styles.length; i++ ) {

				style = this.styles[ i ];
				query = style.attr( 'data-query' );
				value = parseInt( style.attr( 'data-value' ) );

				if ( 'max' === query && ! isNaN( value ) && value <= width ) {
					this.styles[ i ][0].sheet.disabled = true;
				}
			}
		},

		/**
		 * Enables all style tags used to render simulated queries.
		 *
		 * @since 2.2
		 */
		enableStyles: function()
		{
			for ( var i = 0; i < this.styles.length; i++ ) {
				// Fix for Chrome 85.0.4183.83 bug with stylesheet.disabled.
				this.styles[ i ][0].sheet.disabled = false;
				this.styles[ i ][0].sheet.disabled = true;
				this.styles[ i ][0].sheet.disabled = false;
			}
		},

		/**
		 * Removes comments, keyframes and empty media
		 * queries from a CSS style string.
		 *
		 * @since 2.0.6
		 */
		cleanStyles: function( styles )
		{
			var re = this.regex;
			return styles.replace( re.comments, '' ).replace( re.keyframes, '' ).replace( re.empty, '' );
		},

		/**
		 * Converts relative URLs to absolute URLs since the
		 * styles will be added to a <style> tag.
		 *
		 * @since 1.10
		 * @method convertURLs
		 * @param {String} styles
		 * @param {String} href
		 */
		convertURLs: function( styles, href )
		{
			if ( ! href ) {
				return styles;
			}

			href = href.substring( 0, href.lastIndexOf( '/' ) );

			if ( href.length ) {
				href += '/';
			}

			return styles.replace( this.regex.urls, "$1" + href + "$2$3" );
		},

		/**
		 * Returns the value of 1em in pixels.
		 *
		 * @since 1.10
		 * @method getEmPixelValue
		 * @return {Number}
		 */
		getEmPxValue: function()
		{
			if ( this.emPxValue ) {
				return this.emPxValue;
			}

			var value                = null,
				doc                  = window.document,
				docElem              = doc.documentElement,
				body                 = doc.body,
				div                  = doc.createElement( 'div' ),
				originalHTMLFontSize = docElem.style.fontSize,
				originalBodyFontSize = body && body.style.fontSize,
				fakeUsed             = false;

			div.style.cssText = 'position:absolute;font-size:1em;width:1em';

			if ( ! body ) {
				body = fakeUsed = doc.createElement( 'body' );
				body.style.background = 'none';
			}

			// 1em in a media query is the value of the default font size of the browser.
			// Reset docElem and body to ensure the correct value is returned.
			docElem.style.fontSize = '100%';
			body.style.fontSize = '100%';

			body.appendChild( div );

			if ( fakeUsed ) {
				docElem.insertBefore( body, docElem.firstChild );
			}

			// Get the em px value.
			value = parseFloat( div.offsetWidth );

			// Remove test elements.
			if ( fakeUsed ) {
				docElem.removeChild( body );
			}
			else {
				body.removeChild( div );
			}

			// Restore the original values.
			docElem.style.fontSize = originalHTMLFontSize;

			if ( originalBodyFontSize ) {
				body.style.fontSize = originalBodyFontSize;
			}
			else {
				body.style.fontSize = '';
			}

			this.emPxValue = value;

			return value;
		}
	};

	/**
	 * Force jQuery functions to return certain values
	 * based on the current simulated media query.
	 *
	 * @since 1.10
	 * @class ForceJQueryValues
	 */
	var ForceJQueryValues = {

		/**
		 * jQuery functions that have been overwritten. Saved for
		 * restoring them later.
		 *
		 * @since 1.10
		 * @access private
		 * @property {Object} _functions
		 */
		_functions: null,

		/**
		 * Updates forced jQuery methods.
		 *
		 * @since 1.10
		 * @method update
		 */
		update: function()
		{
			var fn;

			// Cache the original jQuery functions.
			if ( ! this._functions ) {

				this._functions = {};

				for ( fn in ForceJQueryFunctions ) {
					this._functions[ fn ] = jQuery.fn[ fn ];
				}
			}

			// Reset the jQuery functions if no width, otherwise, override them.
			if ( ! SimulateMediaQuery.width ) {
				for ( fn in this._functions ) {
					jQuery.fn[ fn ] = this._functions[ fn ];
				}
			}
			else {
				for ( fn in ForceJQueryFunctions ) {
					jQuery.fn[ fn ] = ForceJQueryFunctions[ fn ];
				}
			}
		}
	};

	/**
	 * jQuery functions that get overwritten by
	 * the ForceJQueryValues class.
	 *
	 * @since 1.10
	 * @class ForceJQueryFunctions
	 */
	var ForceJQueryFunctions = {

		/**
		 * @since 1.10
		 * @method width
		 */
		width: function( val )
		{
			if ( undefined != val ) {
				return ForceJQueryValues._functions['width'].call( this, val );
			}

			if ( $.isWindow( this[0] ) ) {
				return SimulateMediaQuery.width;
			}

			return ForceJQueryValues._functions['width'].call( this );
		}
	};

	/**
	 * Public API
	 */
	FLBuilderSimulateMediaQuery = {
		ignore: function( strings ) {
			SimulateMediaQuery.ignore( strings );
		},
		reparse: function( strings ) {
			SimulateMediaQuery.reparse( strings );
		},
		update: function( width, callback ) {
			SimulateMediaQuery.update( width, callback );
		},
		disableStyles: function( width ) {
			SimulateMediaQuery.disableStyles( width );
		},
		enableStyles: function() {
			SimulateMediaQuery.enableStyles();
		}
	};

} )( jQuery );
