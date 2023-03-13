( function( $ ) {
	$( function() {
		$( '.fl-map' ).on( 'click', function() {
			$( this ).find( 'iframe' ).css( 'pointer-events', 'auto' );
		} );
	} );
} )( jQuery );