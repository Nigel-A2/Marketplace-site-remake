// {{ Create main page warning.
(function($) {
    $( document ).on( 'click', 'a.wpbdp-create-main-page-button' ,function( e ) {
		e.preventDefault();
		var button = $( this ),
			$msg = button.parents('.wpbdp-notice'),
			nonce = button.attr('data-nonce');
		$.ajax({
			'url': ajaxurl,
			'data': { 'action': 'wpbdp-create-main-page',
					  '_wpnonce': nonce },
			'dataType': 'json',
			success: function(res) {
				if ( ! res.success )
					return;

				$msg.fadeOut( 'fast', function() {
					$(this).html( '<p>' + res.message + '</p>' );
					$(this).removeClass('error');
					$(this).addClass('updated');
					$(this).fadeIn( 'fast' );
				} );
			}
		});
	});
})(jQuery);
// }}