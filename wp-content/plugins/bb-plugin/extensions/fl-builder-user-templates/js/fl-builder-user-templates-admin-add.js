( function( $ ) {
	
	/**
	 * Handles logic for the user templates admin add new interface.
	 *
	 * @class FLBuilderUserTemplatesAdminAdd
	 * @since 1.10
	 */
	FLBuilderUserTemplatesAdminAdd = {
		
		/**
		 * Initializes the user templates admin add new interface.
		 *
		 * @since 1.10
		 * @access private
		 * @method _init
		 */
		_init: function()
		{
			this._bind();
		},

		/**
		 * Binds events for the Add New form.
		 *
		 * @since 1.10
		 * @access private
		 * @method _bind
		 */
		_bind: function()
		{
			$( 'select.fl-template-type' ).on( 'change', this._templateTypeChange );
			$( 'form.fl-new-template-form .dashicons-editor-help' ).tipTip();
			$( 'form.fl-new-template-form' ).validate();
			
			this._templateTypeChange();
		},

		/**
		 * Callback for when the template type select changes.
		 *
		 * @since 1.10
		 * @access private
		 * @method _templateTypeChange
		 */
		_templateTypeChange: function()
		{
			var val    = $( 'select.fl-template-type' ).val(),
				module = $( '.fl-template-module-row' ),
				global = $( '.fl-template-global-row' ),
				add    = $( '.fl-template-add' );
			
			module.toggle( 'module' == val );
			global.toggle( ( 'row' == val || 'module' == val ) );
			
			if ( '' == val ) {
				add.val( FLBuilderConfig.strings.addButton.add );
			}
			else {
				add.val( FLBuilderConfig.strings.addButton[ val ] );
			}
		}
	};
	
	// Initialize
	$( function() { FLBuilderUserTemplatesAdminAdd._init(); } );

} )( jQuery );