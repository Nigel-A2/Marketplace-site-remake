jQuery(function($) {
    var $form = $( 'form#wpbdp-fee-form' );

    if ( 0 == $form.length )
        return;

    var update_form_ui = function( event ) {
        // Listing run.
        var fee__days = $( 'input[name="_days"]:checked', $form ).val(),
            $textfield = $( 'input[name="fee[days]"]', $form );

        if ( 0 == fee__days ) {
            $textfield.prop( 'disabled', true ).val( '0' );
        } else {
            // A field cannot gain focus if it is disabled
            $textfield.prop( 'disabled', false );

            if ( event && $( '#wpbdp-fee-form-days', $form ).is( event.target ) ) {
                $textfield.focus();
            }
        }

        // Category Policy.
        var $pricing            = $( 'input[name="fee[pricing_model]"]', $form );
        var limit_categories    = $( 'select[name="limit_categories"]', $form ).val() == '1',
            pricing             = $pricing.filter( ':checked' ).val();
        var $category_chooser   = $( '#limit-categories-list', $form );
        var selected_categories = [];

        if ( limit_categories ) {
            $category_chooser.removeClass( 'hidden' );

            if ( $( 'select', $category_chooser ).length > 0 ) {
                selected_categories = $( 'select', $category_chooser ).val();

                if ( ! selected_categories ) {
                    selected_categories = [];
                }
            } else {
                selected_categories = $( 'input:checked', $category_chooser ).map( function( i, cb ){ return $( cb ).val() } ).get();
            }

            if ( selected_categories.length > 0 ) {
                $pricing.filter( '[value="variable"]' ).parent().show();
            } else {
                $pricing.filter( '[value="variable"]' ).parent().hide();
                pricing = $pricing.val(['flat']).val();
            }
        } else {
            $category_chooser.addClass( 'hidden' );
            $pricing.filter( '[value="variable"]' ).parent().show();
        }

        // Show pricing details.
        $( '.fee-pricing-details' ).not( '.pricing-details-' + pricing ).addClass( 'hidden' );
        $( '.fee-pricing-details.pricing-details-' + pricing, $form ).removeClass( 'hidden' );

        // Show only the price-rows that match the category selection.
        if ( 'variable' == pricing ) {
            var $rows = $( '.wpbdp-variable-pricing-configurator-row', $form );

            if ( ! limit_categories ) {
                $rows.removeClass( 'hidden' );
            } else {
                $rows.addClass( 'hidden' );
                $.each( selected_categories, function( i, val ) {
                    $rows.filter( '[data-term-id="' + val + '"]' ).removeClass( 'hidden' );
                });
            }
        }
    };
    update_form_ui();


    $( 'input[name="fee[days]"]', $form ).blur( function() {
        var val = parseInt( $.trim( $( this ).val() ), 10 );
        $( this ).val( isNaN( val ) ? '0' : Math.max( 0, Math.round( val ) ) );
    } );

    $( 'input[name="_days"],' +
       'select[name="limit_categories"],' +
       'input[name="fee[pricing_model]"],' +
       '#limit-categories-list input[type="checkbox"],' +
       '#limit-categories-list select'
    ).change( update_form_ui );

    $( '#limit-categories-list select' ).selectWoo({
        placeholder: $( '#limit-categories-list select' ).attr( 'placeholder' )
    });

    $form.submit(function() {
        $( 'input[name="fee[days]"]', $form ).prop( 'disabled', false );
        return true;
    });

    // Color picker.
	$( '.cpa-color-picker' ).wpColorPicker();

});
