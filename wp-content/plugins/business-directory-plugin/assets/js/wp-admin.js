( function ( $ ) {
	/**
	 * Triggered when the dismissable notice is clicked.
	 */
	$( document ).on(
		'click',
		'.wpbdp-notice.is-dismissible > .notice-dismiss, .wpbdp-notice .wpbdp-notice-dismiss',
		function ( e ) {
			e.preventDefault();

			var $button = $( this ),
				$notice = $button.closest( '.wpbdp-notice' ),
				link = $button.attr( 'href' );

			if ( link ) {
				window.open( link, '_blank' ).focus();
			}

			wpbdpDismissNotice( $notice, $button );
		}
	);

	/**
	 * Dismisses a notice.
	 *
	 * @param {HTMLElement} $notice The notice element.
	 */
	var wpbdpDismissNotice = function ( $notice ) {
		const id = $notice.data( 'dismissible-id' );

		if ( ! id || id !== 'missing_premium' ) {
			return;
		}

		$.post( ajaxurl, {
				action: 'wpbdp_dismiss_notification',
				id: id,
				nonce: $notice.data( 'nonce' ),
			}, function () {
				$notice.fadeOut( 'fast', function () {
					$notice.remove();
				} );
			}
		);
	};
} )( jQuery );
