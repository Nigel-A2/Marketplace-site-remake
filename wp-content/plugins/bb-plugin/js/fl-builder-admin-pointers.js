jQuery( document ).ready( function( $ ) {

	$.each( FLBuilderAdminPointersConfig.pointers, function( i, pointer ) {
		
		var options = $.extend( pointer.options, {
			pointerClass: 'wp-pointer fl-builder-admin-pointer',
			close: function() {
				$.post( FLBuilderAdminPointersConfig.ajaxurl, {
					pointer: pointer.id,
					action: 'dismiss-wp-pointer'
				} );
			}
		} );

		$( pointer.target ).pointer( options ).pointer( 'open' );
	} );
} );
