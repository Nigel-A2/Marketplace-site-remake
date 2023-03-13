jQuery( function( $ ) {
    // Datepicker for expiration date editing.
    var _addNeverButton = function( instance ) {
        setTimeout( function() {
            var $buttonPane = $(instance.dpDiv).find( '.ui-datepicker-buttonpane' );

            if ( $buttonPane.find( '.ui-datepicker-never' ).length > 0 )
                return;

            var $button = $( '<button>', {
                text: 'Never Expires',
                click: function() {
                    $(instance.input).val('');
                    $(instance.input).datepicker( 'hide' );
                },
            }).addClass( 'ui-datepicker-never ui-state-default ui-priority-primary ui-corner-all' );

			$buttonPane.append($button);
        }, 1 );
    };

    $( '#wpbdp-listing-metabox-plan-info input[name="listing_plan[expiration_date]"]' ).datepicker({
        dateFormat: 'yy-mm-dd',
        showButtonPanel: true,
        beforeShow: function( input, instance ) {
            _addNeverButton( instance );

			$( '#ui-datepicker-div' ).addClass( 'wpbdp-datepicker' );
        },
        onChangeMonthYear: function( year, month, instance ) {
            _addNeverButton( instance );
        }
    });

    var $metabox_tab = $('#wpbdp-listing-metabox-plan-info');

	// Makes sure texts displayed inside the metabox are in sync with the settings.
	var updateText = function( plan ) {
		var images = $('input[name="listing_plan[fee_images]"]').val();
		$('#wpbdp-listing-plan-prop-images').html(images);

		if ( plan ) {
			$('#wpbdp-listing-plan-prop-label').html(
				wpbdpListingMetaboxL10n.planDisplayFormat.replace('{{plan_id}}', plan.id)
					.replace('{{plan_label}}', plan.label)
			);
			$( '#wpbdp-listing-plan-prop-amount' ).html( plan.amount ? plan.amount : '-' );
			$( '#wpbdp-listing-plan-prop-is_sticky' ).html( plan.sticky );
			$( '#wpbdp-listing-plan-prop-is_recurring' ).html( plan.recurring );
			$( '#wpbdp-listing-plan-prop-expiration' ).html( plan.formated_date );
		}
	};

	// Hide the thumbnail box.
	setTimeout(
		function() {
            var featured = document.querySelector( '.editor-post-featured-image' );
            if ( featured !== null ) {
                featured.closest( '.components-panel__body' ).classList.add( 'hidden' );
            }

			// If there's no plan selected, make it clear how to add it.
			var selectedPlan = document.querySelector( 'input[name="listing_plan[fee_id]"]' );
			if ( selectedPlan !== null && ! selectedPlan.value ) {
				var planPos, panel,
					publishBtn = document.querySelector( '.edit-post-header__settings .is-primary' );

				if ( publishBtn !== null ) {
					publishBtn.classList.add( 'wpbdp-error-btn' );
					publishBtn.title = 'The Listing plan is required.';

					// Auto scroll to the plan selector box.
					panel = document.querySelector( '.interface-interface-skeleton__sidebar' );
					planPos = document.getElementById( 'wpbdp-listing-plan' ).getBoundingClientRect();
					if ( panel !== null ) {
						panel.scrollTop = planPos.y;
					}

					// Clear the error warning.
					$metabox_tab.on( 'change', 'input[name="listing_plan[fee_id]"]', function() {
						publishBtn.classList.remove( 'wpbdp-error-btn' );
						publishBtn.title = '';
					});
				}
			}
		},
		3500
	);

    // Properties editing.
	$metabox_tab.on( 'click', 'a.edit-value-toggle', function(e) {
        e.preventDefault();

        var $dd = $(this).parents('dd');
        var $editor = $dd.find('.value-editor');
        var $display = $dd.find('.display-value');
        var $input = $editor.find('input[type="text"], input[type="checkbox"], select');
        var current_value = $input.is(':checkbox') ? $input.is(':checked') : $input.val();

        $input.data('before-edit-value', current_value);

        $(this).hide();
        $display.hide();
        $editor.show();
    });
	$metabox_tab.on( 'click', '.value-editor a.update-value, .value-editor a.cancel-edit', function(e) {
        e.preventDefault();

        var $dd = $(this).parents('dd');
        var $editor = $dd.find('.value-editor');
        var $display = $dd.find('.display-value');
        var $input = $editor.find('input[type="text"], input[type="checkbox"], select');

        if ( $(this).is( '.cancel-edit' ) ) {
            var prev_value = $input.data('before-edit-value');

            if ($input.is(':checkbox'))
                $input.prop('checked', prev_value);
            else
                $input.val(prev_value);
        } else if ( $input.is('#wpbdp-listing-plan-select') && $input.val() ) {
            // Plan changes are handled in a special way.
			var listing_id = $( 'input[name="post_ID"]' ).val();

			$.ajax(ajaxurl, {
				data: {
					action: 'wpbdp-assign-plan-to-listing',
					nonce: wpbdp_global.nonce,
					listing_id: listing_id,
					plan_id: $input.val()
				},
				type: 'POST',
				dataType: 'json',
				success: function(res) {
					if ( res.success ) {
						var plan = res.data;
						$metabox_tab.find('input[name="listing_plan[fee_id]"]').val(plan.id);
						$metabox_tab.find('input[name="listing_plan[expiration_date]"]').val(plan.expiration_date);
						$metabox_tab.find('input[name="listing_plan[fee_images]"]').val(plan.images);
						updateText( plan );
					}
				}
			});
		} else {
			// Handle updating other fields.
			$display.html( $input.val() );
		}

        $editor.hide();
        $display.show();
        $dd.find('.edit-value-toggle').show();
    });

    $payments_tab = $('#wpbdp-listing-metabox-payments');

	$payments_tab.on( 'click', 'a[name="delete-payments"]', function(e) {
        e.preventDefault();
        $.post( ajaxurl, { 'action': 'wpbdp-clear-payment-history', 'listing_id': $( this ).attr( 'data-id' ) }, function (res) {
            if ( ! res.success ) {
                if ( res.data.error )
                    $('#wpbdp-listing-payment-message').addClass('error').html(res.data.error).fadeIn();

                return;
            }

            $( '.wpbdp-payment-items', $payments_tab ).fadeOut( 'fast', function() {
                $( this ).html( '' );
                $( '#wpbdp-listing-payment-message', $payments_tab ).html( res.data.message ).fadeIn();
            } );

        } );
    });

} );
