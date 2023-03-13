( function( $ ) {

	/**
	 * @class FLBuilderTemplateDataExporter
	 * @since 1.8
	 */
	FLBuilderTemplateDataExporter = {
	
		/**
		 * @since 1.8
		 * @method init
		 */ 
		init: function()
		{
			$( 'input[name="fl-builder-template-data-exporter-all"]' ).on( 'click', FLBuilderTemplateDataExporter._allCheckboxClicked );
			$( '.fl-builder-template-data-checkbox' ).on( 'click', FLBuilderTemplateDataExporter._checkboxClicked );
		},
		
		/**
		 * @since 1.8
		 * @access private
		 * @method _allCheckboxClicked
		 */
		_allCheckboxClicked: function()
		{
			var checkbox   = $( this ),
				parent     = checkbox.parents( '.fl-builder-template-data-section ' ),
				checkboxes = parent.find( '.fl-builder-template-data-checkbox' );
			
			if ( checkbox.is( ':checked' ) ) {
				checkboxes.prop( 'checked', true );
			}
			else {
				checkboxes.prop( 'checked', false );	
			}
		},
		
		/**
		 * @since 1.8
		 * @access private
		 * @method _checkboxClicked
		 */
		_checkboxClicked: function()
		{
			var allChecked  = true,
				parent      = $( this ).parents( '.fl-builder-template-data-section ' ),
				checkboxes  = parent.find( '.fl-builder-template-data-checkbox' ),
				allCheckbox = parent.find( 'input[name="fl-builder-template-data-exporter-all"]' );
			
			checkboxes.each( function() {
				if ( ! $( this ).is( ':checked' ) ) {
					allChecked = false;
				}
			});
			
			allCheckbox.prop( 'checked', allChecked );
		}
	};

	$( FLBuilderTemplateDataExporter.init );

} )( jQuery );