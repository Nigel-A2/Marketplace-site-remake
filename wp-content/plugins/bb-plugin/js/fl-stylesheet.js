(function($){

	/**
	 * Helper class for dealing with creating
	 * and updating stylesheets.
	 *
	 * @class FLStyleSheet
	 * @since 1.3.3
	 */
	FLStyleSheet = function( o )
	{
		if ( 'object' == typeof o ) {
			$.extend( this, o );
		}

		this._createSheet();
	};

	/**
	 * Prototype for new instances.
	 *
	 * @since 1.3.3
	 * @property {Object} prototype
	 */
	FLStyleSheet.prototype = {

		/**
		 * An ID for the stylesheet element.
		 *
		 * @since 1.9
		 * @property {String} id
		 */
		id              : null,

		/**
		 * A reference to the stylesheet object.
		 *
		 * @since 1.3.3
		 * @access private
		 * @property {Object} _sheet
		 */
		_sheet          : null,

		/**
		 * A reference to the HTML style element.
		 *
		 * @since 1.3.3
		 * @access private
		 * @property {Object} _sheetElement
		 */
		_sheetElement   : null,

		/**
		 * Update a rule for this stylesheet.
		 *
		 * @since 1.3.3
		 * @method updateRule
		 * @param {String} selector The CSS selector to update.
		 * @param {String} property The CSS property to update. Can also be an object of key/value pairs.
		 * @param {String} value The value of the property to update. Can be omitted if property is an object.
		 */
		updateRule: function(selector, property, value)
		{
			var rules   = this._sheet.cssRules ? this._sheet.cssRules : this._sheet.rules,
				rule    = null,
				i       = 0;

			// Find the rule to update.
			for( ; i < rules.length; i++) {

				if(rules[i].selectorText.toLowerCase().replace( /\s/g, '' ) == selector.toLowerCase().replace( /\s/g, '' )) {
					rule = rules[i];
				}
			}

			// Update the existing rule.
			if(rule) {

				if(typeof property == 'object') {

					for(i in property) {
						this.setProperty( rule, i, property[ i ] );
					}
				}
				else {
					this.setProperty( rule, property, value );
				}
			}

			// No rule found. Add a new one.
			else {
				this.addRule(selector, property, value);
			}
		},

		/**
		 * Sets a property for a rule.
		 *
		 * @since 2.2
		 * @method setProperty
		 * @param {Object} rule
		 * @param {String} selector
		 * @param {String} value
		 */
		setProperty: function( rule, property, value )
		{
			var important = '';

			if ( rule.style.setProperty ) {

				if ( value.indexOf( '!important' ) > -1 ) {
					important = 'important';
					value = value.replace( '!important', '' ).trim();
				}

				rule.style.setProperty( property, value, important );
			} else {
				rule.style[ this._toCamelCase( property ) ] = value;
			}
		},

		/**
		 * Add a new rule to this stylesheet.
		 *
		 * @since 1.3.3
		 * @method addRule
		 * @param {String} selector The CSS selector to add.
		 * @param {String} property The CSS property to add. Can also be an object of key/value pairs.
		 * @param {String} value The value of the property to add. Can be omitted if property is an object.
		 */
		addRule: function(selector, property, value)
		{
			var styles  = '',
				i       = '';

			if(typeof property == 'object') {

				for(i in property) {
					styles += i + ':' + property[i] + ';';
				}
			}
			else {
				styles = property + ':' + value + ';';
			}

			if(this._sheet.insertRule) {
				this._sheet.insertRule(selector + ' { ' + styles + ' }', this._sheet.cssRules.length);
			}
			else {
				this._sheet.addRule(selector, styles);
			}
		},

		/**
		 * Completely destroys the sheet by removing the
		 * stylesheet element from the DOM and making the
		 * stored object reference null.
		 *
		 * @since 1.9
		 * @method destroy
		 */
		destroy: function()
		{
			if(this._sheetElement) {
				this._sheetElement.remove();
				this._sheetElement = null;
			}
			if(this._sheet) {
				this._sheet = null;
			}
		},

		/**
		 * Disables the sheet by removing it from the DOM.
		 *
		 * @since 1.9
		 * @method disable
		 */
		disable: function()
		{
			this._sheet.disabled = true;
		},

		/**
		 * Enables the sheet by adding it to the DOM.
		 *
		 * @since 1.9
		 * @method enable
		 */
		enable: function()
		{
			this._sheet.disabled = false;
		},

		/**
		 * Create the style element, add it to the DOM
		 * and save references.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _createSheet
		 */
		_createSheet: function()
		{
			var id 		  = this.id ? ' id="' + this.id + '"' : '',
				className = this.className ? ' class="' + this.className + '"' : '';

			if ( ! this._sheet ) {

				this._sheetElement = $( '<style type="text/css"' + id + className + '></style>' );

				$( 'body' ).append( this._sheetElement );

				this._sheet = this._sheetElement[0].sheet;
			}
		},

		/**
		 * Convert a string to camel case.
		 *
		 * @since 1.3.3
		 * @access private
		 * @method _toCamelCase
		 * @param {String} input The string to convert.
		 */
		_toCamelCase: function(input)
		{
			return input.toLowerCase().replace(/-(.)/g, function(match, group1) {
				return group1.toUpperCase();
			});
		}
	};

})(jQuery);
