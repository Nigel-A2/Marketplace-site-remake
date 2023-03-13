( function( $ ) {
	
	/**
	 * Handles logic for the user templates admin edit interface.
	 *
	 * @class FLBuilderUserTemplatesAdminEdit
	 * @since 1.10
	 */
	FLBuilderUserTemplatesAdminEdit = {
		
		/**
		 * Initializes the user templates admin edit interface.
		 *
		 * @since 1.10
		 * @access private
		 * @method _init
		 */
		_init: function()
		{
			this._setupPageTitle();
			$( '#fl-builder-launch-button').on( 'click', this._launchBuilder );
		},

		/**
		 * Adds to correct title to the edit screen and changes the 
		 * Add New button URL to point to our custom Add New page.
		 *
		 * @since 1.10
		 * @access private
		 * @method _setupPageTitle
		 */
		_setupPageTitle: function()
		{
			var button = $( '.page-title-action' ),
				url    = FLBuilderConfig.addNewURL + '&fl-builder-template-type=' + FLBuilderConfig.userTemplateType,
				h1     = $( '.wp-heading-inline' );
				
			h1.html( FLBuilderConfig.pageTitle + ' ' ).append( button );
			button.attr( 'href', url ).show();
		},

		/**
		 * Sets a value to a hidden field to indicate that the Launch Builder button was clicked.
		 *
		 * @since 2.5.1 
		 * @access private 
		 * @method _launchBuilder
		 * @param event
		 */
		 _launchBuilder: function( event )
		 {
			 var launchBuilder = $( event.target ).parent().find( '#fl-builder-launch' );
		
			 $( launchBuilder ).val( 'true' );
		 },
	};
	
	// Initialize
	$( function() { FLBuilderUserTemplatesAdminEdit._init(); } );

} )( jQuery );