jQuery(function($) {
    $( '#wpbdp-admin-page-payments-details .postbox .handlediv.button-link' ).click( function(e) {
        var $p = $( this ).parents( '.postbox' );
        $p.toggleClass( 'closed' );
        $( this ).attr( 'aria-expanded', ! $p.hasClass( 'closed' ) );
    });

    $( '#wpbdp-admin-payment-info-box input[name="payment[created_on_date]"]' ).datepicker({
        dateFormat: 'yy-mm-dd'
    });

    $( '#wpbdp-payment-notes-add' ).click(function(e) {
        e.preventDefault();
        var $note = $( 'textarea[name="payment_note"]' );

        $.ajax({
            url: ajaxurl,
            method: 'POST',
            dataType: 'json',
            data: {
                action: 'wpbdp_admin_ajax',
                handler: 'payments__add_note',
                payment_id: $( this ).data( 'payment-id' ),
				nonce: wpbdp_global.nonce,
                note: $note.val()
            },
            success: function( res ) {
                if ( ! res.success )
                    return;

                $( '#wpbdp-payment-notes .no-notes' ).hide();
                $( '#wpbdp-payment-notes' ).prepend( res.data.html );

                $note.val('');
            }
        });

        // var border_color = $('#edd-payment-note').css('border-color');
		// 			$('#edd-payment-note').css('border-color', 'red');
		// 			setTimeout( function() {
		// 				$('#edd-payment-note').css('border-color', border_color );
		// 			}, 500 );
    });

    $( document ).on( 'click', '.wpbdp-payment-note .wpbdp-admin-delete-link', function( e ) {
        e.preventDefault();

        var url = $( this ).attr( 'href' );
        $.ajax({
            url: url,
            method: 'GET',
            dataType: 'json',
            success: function( res ) {
                if ( ! res.success )
                    return;

                $( '.wpbdp-payment-note[data-id="' + res.data.note.id + '"]' ).remove();

                if ( 0 == $( '.wpbdp-payment-note' ).length )
                    $( '#wpbdp-payment-notes .no-notes' ).show();
            }
        });
    } );

});
