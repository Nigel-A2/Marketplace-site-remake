(function( $ ) {

	/**
	 * Enable/Disable usage stats helper.
	 *
	 * @since 2.1
	 */
	var FLBuilderUsage = {

		init: function() {
			FLBuilderUsage._fadeToggle()
			FLBuilderUsage._enableClick()
			FLBuilderUsage._disableClick()
		},
		_fadeToggle: function() {
			$( 'a.stats-info' ).click( function( e ) {
				e.preventDefault();
				$( '.stats-info-data' ).fadeToggle()
			})
		},
		_enableClick: function() {
			$( '.buttons span.enable-stats' ).click( function( e ) {

				nonce = $(this).closest('.buttons').find('#_wpnonce').val()

				data = {
					'action'  : 'fl_usage_toggle',
					'enable'  : 1,
					'_wpnonce': nonce
				}
				FLBuilderUsage._doAjax( data )
			})
		},
		_disableClick: function() {
			$( '.buttons span.disable-stats' ).click( function( e ) {

				nonce = $(this).closest('.buttons').find('#_wpnonce').val()

				data = {
					'action'  : 'fl_usage_toggle',
					'enable'  : 0,
					'_wpnonce': nonce
				}
				FLBuilderUsage._doAjax( data )
			})
		},
		_doAjax: function( data ) {
			$.post(ajaxurl, data, function(response) {
				FLBuilderUsage._close()
			});
		},

		_close: function() {
			$( '.fl-usage').closest('.notice').fadeToggle()
		}
	};

	$( function() {
		FLBuilderUsage.init();
	});

})( jQuery );
