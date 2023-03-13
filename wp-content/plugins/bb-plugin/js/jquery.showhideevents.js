/**
 * Adds events to jQuery's show and hide functions.
 *
 * http://viralpatel.net/blogs/jquery-trigger-custom-event-show-hide-element/
 */
( function( $ ) {
	$.each( [ 'show', 'hide' ], function( i, ev ) {
		var el = $.fn[ ev ];
		$.fn[ ev ] = function() {
			var result = el.apply( this, arguments );
			this.trigger( ev );
			return result;
		};
	} );
} )( jQuery );
