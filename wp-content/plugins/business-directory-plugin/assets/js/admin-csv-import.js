jQuery(function( $ ) {
    var csvimport = {};

    csvimport.CSV_Import = function() {
        this.import_id = $( '#wpbdp-csv-import-state' ).attr( 'data-import-id' );

        this.in_progress = false;
        this.canceled = false;

        this.processed_rows = 0;
        this.total_rows = 0;
        this.imported_rows = 0;
        this.rejected_rows = 0;
        this.warnings = [];

        this.$state = $( '#wpbdp-csv-import-state' );
        this.$success = $( '#wpbdp-csv-import-summary' );
        this.$progress_bar = new WPBDP_Admin.ProgressBar( $( '.import-progress' ) );

        this._setup_events();
    };

    $.extend( csvimport.CSV_Import.prototype, {
        _setup_events: function() {
            var t = this;

            $( 'a.cancel-import' ).on( 'click', function(e) {
                e.preventDefault();
                t.cancel();
            });

            $( 'a.resume-import' ).on( 'click', function(e) {
                e.preventDefault();
                t.start_or_resume();
            });
        },

        _advance: function() {
            var t = this;

            if ( ! t.in_progress )
                return;

            if ( t.in_progress && t.canceled ) {
                t.in_progress = false;
            }

            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: { 'action': 'wpbdp-csv-import', 'import_id': t.import_id },
                success: function( res ) {
                    if ( ! res || ! res.success )
                        return t._fatal_error( res.error );

                    t.processed_rows = res.data.progress;
                    t.total_rows = res.data.total;
                    t.imported_rows = res.data.imported;
                    t.rejected_rows = res.data.rejected;
                    t.$progress_bar.set( t.processed_rows, t.total_rows );

                    if ( res.data.done ) {
                        t.in_progress = false;
                        t.warnings = res.data.warnings;
                        t._show_success_screen();
                    } else {
                        t._advance();
                    }

                },
                error: function() {
                    return t._fatal_error();
                }
            });
        },

        _show_success_screen: function() {
            var t = this;

            t.$state.fadeOut( function() {
                t.$state.remove();

                t.$success.find( '.placeholder-imported-rows' ).html( t.imported_rows );
                t.$success.find( '.placeholder-rejected-rows' ).html( t.rejected_rows );

                if ( 0 == t.warnings.length ) {
                    t.$success.find( '.no-warnings' ).show();
                    t.$success.fadeIn( 'fast' );
                    return;
                }

                var $warnings_table = t.$success.find( '.wpbdp-csv-import-warnings tbody' );
                var $template_row = $warnings_table.find( '.row-template' );

                $.each( t.warnings, function( i, v ) {
                    var $r = $template_row.clone();

                    $r.find( '.col-line-no' ).html( v.line );
                    $r.find( '.col-line-content' ).html( v.content );
                    $r.find( '.col-warning' ).html( v.error );
                    $warnings_table.append( $r.show() );
                } );

                t.$success.find( '.with-warnings' ).show();
                t.$success.find( '.wpbdp-csv-import-warnings' ).show();
                t.$success.fadeIn( 'fast' );
            } );
        },

        _fatal_error: function( msg ) {
            var t = this;

            var $fatal_error = $( '#wpbdp-csv-import-fatal-error' );
            var $with_reason = $fatal_error.find( '.with-reason' );
            var $no_reason = $fatal_error.find( '.no-reason' );

            if ( msg ) {
                $with_reason.html( $with_reason.html().replace( '%s', msg ) ).show();
            } else {
                $no_reason.show();
            }

            $fatal_error.show();
            $( 'html, body' ).animate( { scrollTop: 0 }, 'medium' );

            t.cancel();
        },

        start_or_resume: function() {
            if ( this.in_progress || this.canceled )
                return;

            this.in_progress = true;

            $( 'a.resume-import' ).css( 'opacity', '0.4' );
            $( '.status-msg .not-started' ).hide();
            $( '.status-msg .in-progress' ).show();

            this._advance();
        },

        cancel: function() {
            var t = this;

            t.canceled = true;
            $( '.canceled-import' ).show();
            t.$state.remove();

            // Try to clean up.
            $.ajax({
                url: ajaxurl,
                type: 'POST',
                dataType: 'json',
                data: { 'action': 'wpbdp-csv-import', 'import_id': t.import_id, 'cleanup': 1 }
            });
        }
    } );

	// Import example page.
	$( 'a.wpbdp-example-csv' ).on( 'click', function(e) {
		e.preventDefault();
		$.ajax({
			url: ajaxurl,
			type: 'POST',
			data: {
				'action': 'wpbdp-example-csv',
				'nonce': wpbdp_global.nonce
			},
			success: function( res ) {
				var link = document.createElement( 'a' );
				var fileData = ['\ufeff'+res];

				var fileObject = new Blob( fileData, {
					type: 'text/csv;charset=utf-8;'
				});

				var url = URL.createObjectURL( fileObject );
				link.href = url;
				link.download = 'bd-example.csv';

				// Actually download CSV.
				document.body.appendChild( link );
				link.click();
				document.body.removeChild( link );
			}
		});
	});

    // Import progress page.
    if ( $( '#wpbdp-csv-import-state' ).length > 0 ) {
        var import_in_page = new csvimport.CSV_Import();
        return;
    }

    // Import config. page.
    $( '.wpbdp-page-csv-import .file-local-selection a.toggle-selection' ).on( 'click', function(e) {
        e.preventDefault();
        var $files = $( this ).siblings( 'ul' );
        $files.toggle();

        if ( ! $files.is(':visible') )
            $files.find( 'input[type="radio"]' ).prop( 'checked', false );
    });

	$( '.wpbdp-page-csv-import' ).on( 'change', '.file-local-selection input[type="radio"]', function(e) {
        var sel = $(this).filter(':checked').val();

        if ( "" == sel ) {
            $(this).prop( 'checked', false );
            $(this).parents( '.file-local-selection' ).hide();
        }
    });

    // Default User field in Import config. page.
    (function() {
        var $form = $( 'form#wpbdp-csv-import-form' ),
            $use_default_user_checkbox = $form.find( 'input.use-default-listing-user' ),
            $default_user_field;

		$form.on( 'change', 'input.assign-listings-to-user', function(e){
            if ( $(this).is(':checked') ) {
                $form.find( '.default-user-selection' ).show();
            } else {
                $form.find( '.default-user-selection' ).hide();
            }

			$use_default_user_checkbox.trigger( 'change' );
		});
		$( 'input.assign-listings-to-user' ).trigger( 'change' );

		$( document ).on( 'change', $use_default_user_checkbox, function(){
            if ( $(this).is(':checked') ) {
                $form.find( 'select.default-user, input.default-user' ).closest( 'tr' ).show();
            } else {
                $form.find( 'select.default-user, input.default-user' ).closest( 'tr' ).hide();
            }
		});
		$use_default_user_checkbox.trigger( 'change' );

        function update_textfield_value( event, ui ) {
            event.preventDefault();

            if ( typeof ui.item == 'undefined' ) {
                return;
            }

            $default_user_field.val( ui.item.label );
            $default_user_field.siblings( '#' + $default_user_field.attr( 'data-hidden-field' ) )
                .val( ui.item.value );
        }

        $default_user_field = $form.find( '.wpbdp-user-autocomplete' ).autocomplete({
            source: ajaxurl + '?action=wpbdp-autocomplete-user',
            delay: 500,
            minLength: 2,
            select: update_textfield_value,
            focus: update_textfield_value
        });
    })();

});
