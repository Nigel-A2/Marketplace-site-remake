jQuery(function($) {
    var $startButton = $('.wpbdp-admin-page-manual-upgrade a.start-upgrade');
    var $pauseButton = $('.wpbdp-admin-page-manual-upgrade a.pause-upgrade');
    var $progressArea = $('textarea#manual-upgrade-progress');
    var inProgress = false;

    var makeProgress = function() {
        if (!inProgress)
            return;

        var data = { action: 'wpbdp-manual-upgrade' };
        $.get(ajaxurl, data, function(response) {
            var currentText = $progressArea.val();
            var newLine = (response.ok ? "*" : "!") + " " + response.status;

            $progressArea.val(currentText + newLine + "\n");
            $progressArea.scrollTop($progressArea[0].scrollHeight - $progressArea.height());

            if (response.done) {
                $( 'div.step-upgrade' ).fadeOut(function() { $('div.step-done').fadeIn() });
            } else {
                makeProgress();
            }
        }, 'json');
    };
    
    $startButton.click(function(e) {
        e.preventDefault();

        if (inProgress)
            return;

        inProgress = true;
        makeProgress();
    });

    $pauseButton.click(function(e) {
        e.preventDefault();
        inProgress = false;
    });

    // Migration specific.
    $( '#wpbdp-manual-upgrade-18_0-config #add-fee-form form#wpbdp-fee-form' ).submit(function(e) {
        e.preventDefault();

        var level_id = $( this ).data( 'levelId' );

        if ( ! level_id ) {
            $( '#TB_closeWindowButton' ).click();
            return;
        }

        var data = $( this ).serialize();
        $( 'input[name="level[' + level_id + '][details]"]' ).val( data );

        // Extract some data for the summary.
        $( 'table.new-fee-summary[data-level-id="' + level_id + '"] td[data-attr="fee_label"]' ).html( $( this ).find( 'input[name="fee[label]"]' ).val() );
        $( 'table.new-fee-summary[data-level-id="' + level_id + '"] td[data-attr="fee_amount"]' ).html( $( this ).find( 'input[name="fee[amount]"]' ).val() );
        $( 'table.new-fee-summary[data-level-id="' + level_id + '"] td[data-attr="fee_duration"]' ).html( $( this ).find( 'input[name="fee[days]"]' ).val() );
        $( 'table.new-fee-summary[data-level-id="' + level_id + '"] td[data-attr="fee_images"]' ).html( $( this ).find( 'input[name="fee[images]"]' ).val() );

        $( '#TB_closeWindowButton' ).click();
    });

    $( '#wpbdp-manual-upgrade-18_0-config select.level-migration' ).change(function(e) {
        var selection = $( this ).find( 'option:selected' );
        var $desc = $( this ).siblings( '.option-description' );

        $( this ).siblings( '.option-configuration' ).hide();

        if ( ! selection.val() ) {
            $desc.hide();
            $( this ).siblings( '.option-configuration' ).hide();
            return;
        }

        $desc.html( selection.data( 'description' ) ).show();

        var $config = $( this ).siblings( '.option-configuration' ).filter( '.option-' + selection.val() );
        if ( $config.length > 0 ) {
            $config.show();
        }

        if ( 'create' == selection.val() ) {
            $( 'form#wpbdp-fee-form' ).get(0).reset();
            $( 'form#wpbdp-fee-form' ).data( 'levelId', $( this ).attr( 'data-level-id' ) );
            $config.find( '.new-fee-summary tbody td' ).html( '-' );
            tb_show( $( '#add-fee-form' ).attr( 'data-title' ), '#TB_inline?inlineId=add-fee-form' );
            $( 'form#wpbdp-fee-form input:first' ).focus();
        }
    });

});

