( function( $ ) {

	/**
	 * Handles logic for the user templates admin list interface.
	 *
	 * @class FLBuilderUserTemplatesAdminList
	 * @since 1.10
	 */
	FLBuilderUserTemplatesAdminList = {

		/**
		 * Initializes the user templates admin list interface.
		 *
		 * @since 1.10
		 * @access private
		 * @method _init
		 */
		_init: function()
		{
			this._setupAddNewButton();
			this._setupSearch();
			this._fixCategories();
			this._shortCodeCopy();
		},

		/**
		 * Changes the Add New button URL to point to our
		 * custom Add New page.
		 *
		 * @since 1.10
		 * @access private
		 * @method _setupSearch
		 */
		_setupAddNewButton: function()
		{
			var url = FLBuilderConfig.addNewURL + '&fl-builder-template-type=' + FLBuilderConfig.userTemplateType;

			$( '.page-title-action' ).attr( 'href', url ).show();
		},

		/**
		 * Adds a hidden input to the search for the user
		 * template type.
		 *
		 * @since 1.10
		 * @access private
		 * @method _setupSearch
		 */
		_setupSearch: function()
		{
			var type  = FLBuilderConfig.userTemplateType,
				input = '<input type="hidden" name="fl-builder-template-type" value="' + type + '">'

			$( '.search-box' ).after( input );
		},

		_fixCategories: function() {
			$('.type-fl-builder-template').each( function( i,v ) {

				el = $(v).find('.taxonomy-fl-builder-template-category a');
				url = el.attr('href') + '&fl-builder-template-type=' + FLBuilderConfig.userTemplateType
				el.attr('href', url)
			})
		},
		_shortCodeCopy: function() {
			var that = this;
			clipboard = new ClipboardJS('pre.shortcode');
			clipboard.on('success', function(e) {
				$(e.trigger).html('Copied!').delay(1000).fadeOut(400,function(){
					$(e.trigger).html(e.text).fadeIn()
				})
				e.clearSelection();
			});
		}
	};

	// Initialize
	$( function() { FLBuilderUserTemplatesAdminList._init(); } );

} )( jQuery );
