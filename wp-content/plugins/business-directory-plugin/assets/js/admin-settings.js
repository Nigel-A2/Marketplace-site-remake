jQuery(function($) {
    var wpbdp_settings_dep_handling = {
        init: function() {
            var self = this;

            self.watch = {};
            self.requirements = {};

            $( '.wpbdp-settings-setting[data-requirements][data-requirements!=""]' ).each(function() {
                var setting_id = $(this).data('setting-id');
                var reqs = $( this ).data( 'requirements' );

                self.requirements[ setting_id ] = reqs;

                $.each( reqs, function(i, req) {
                    var rel_setting_id = req[0];

                    if ( 'undefined' === typeof self.watch[rel_setting_id] ) {
                        self.watch[ rel_setting_id ] = [];
                    }

                    self.watch[ rel_setting_id ].push(setting_id);
                } );
            });

            $.each( self.watch, function( setting_id, affected_settings ) {
                setting_id = setting_id.replace( '!', '' );
                $( '[name="wpbdp_settings[' + setting_id + ']"], [name="wpbdp_settings[' + setting_id + '][]"]' ).change(function(){
                    $.each( affected_settings, function(i, v) {
                        self.check_requirements( v );
                    } );
                });
            } );

            $.each( self.requirements, function( setting_id, reqs ) {
                self.check_requirements( setting_id );
            } );

			if ( $.fn.wpColorPicker) {
            	$( '.cpa-color-picker' ).wpColorPicker();
			}
        },

        check_requirements: function( setting_id ) {
            var reqs     = this.requirements[ setting_id ];
            var $setting = $( '#wpbdp-settings-' + setting_id );
            var $row     = $setting.closest( '.wpbdp-setting-row' );
            var passes   = true;

            for ( var i = 0; i < reqs.length; i++ ) {
                var req_name = reqs[ i ][0].replace( '!', '' );
                var not      = ( -1 !== reqs[ i ][0].indexOf( '!' ) );
                var value    = reqs[ i ][1];
				var $field;

                // Obtain updated value (if possible).
                var $rel_setting = $( '#wpbdp-settings-' + req_name );
                if ( $rel_setting.length > 0 ) {
                    if ( $rel_setting.closest( '.wpbdp-setting-row' ).hasClass( 'wpbdp-setting-disabled' ) ) {
                        value = false;
					} else if ( $rel_setting.hasClass( 'wpbdp-settings-type-select' ) ) {
						$field = $rel_setting.find( '[name="wpbdp_settings[' + req_name + ']"]' ).val();
                        value = $field !== '';
                    } else {
                        $field = $rel_setting.find( '[name="wpbdp_settings[' + req_name + ']"]:checked, [name="wpbdp_settings[' + req_name + '][]"]:checked' );
                        value = $field.length > 0;
                    }
                }

                passes = not ? ( not && ! value ) : value;

                if ( ! passes ) {
                    break;
                }
            }

            if ( passes ) {
                $row.removeClass( 'wpbdp-setting-disabled' );
            } else {
                $row.addClass( 'wpbdp-setting-disabled' );
            }

            // Propagate.
            if ( 'undefined' !== typeof this.watch[ setting_id ] ) {
                $setting.find( '[name="wpbdp_settings[' + setting_id + ']"], [name="wpbdp_settings[' + setting_id + '][]"]' ).trigger( 'change' );
            }
        }
    };
    wpbdp_settings_dep_handling.init();

    /**
     * License activation/deactivation.
     */
    var wpbdp_settings_licensing = {
        init: function() {
            var self = this;

            if ( 0 == $( '.wpbdp-settings-type-license_key' ).length ) {
                return;
            }

            $( '.wpbdp-license-key-activate-btn, .wpbdp-license-key-deactivate-btn' ).click(function(e) {
                e.preventDefault();

                var $button  = $(this);
                var $setting = $(this).parents( '.wpbdp-license-key-activation-ui' );
                var $msg     = $setting.find( '.wpbdp-license-key-activation-status-msg' );
                var $spinner = $setting.find( '.spinner' );
                var activate = $(this).is( '.wpbdp-license-key-activate-btn' );
                var $field   = $setting.find( 'input.wpbdp-license-key-input' );
                var data     = $setting.data( 'licensing' );

                $msg.hide();
                $button.data( 'original_label', $(this).val() );
                $button.val( $(this).data( 'working-msg' ) );
                $button.prop( 'disabled', true );

                if ( activate ) {
                    data['action'] = 'wpbdp_activate_license';
                } else {
                    data['action'] = 'wpbdp_deactivate_license';
                }

                data['license_key'] = $field.val();

                $.post(
                    ajaxurl,
                    data,
                    function( res ) {
                        if ( res.success ) {
                            $msg.removeClass( 'status-error notice-error' ).addClass( 'status-success notice-success' ).html( res.message ).show();

                            if ( activate ) {
                                var classes = $setting.attr( 'class' ).split( ' ' ).filter( function( item ) {
                                    var className = item.trim();

                                    if ( 0 === className.length ) {
                                        return false;
                                    }

                                    if ( className.match( /^wpbdp-license-status/ ) ) {
                                        return false;
                                    }

                                    return true;
                                } );

                                classes.push( 'wpbdp-license-status-valid' );

                                $setting.attr( 'class', classes.join( ' ' ) );
                            } else {
                                $setting.removeClass( 'wpbdp-license-status-valid' ).addClass( 'wpbdp-license-status-invalid' );
                            }

                            $field.prop( 'readonly', activate ? true : false );
                        } else {
                            $msg.removeClass( 'status-success notice-success' ).addClass( 'status-error notice-error' ).html( res.error ).show();
                            $setting.removeClass( 'wpbdp-license-status-valid' ).addClass( 'wpbdp-license-status-invalid' );
                            $field.prop( 'readonly', false );
                        }

                        $button.val( $button.data( 'original_label' ) );
                        $button.prop( 'disabled', false );
                    },
                    'json'
                );
            });
        }
    };
    wpbdp_settings_licensing.init();

    /**
     * E-Mail template editors.
     */
    var wpbdp_settings_email = {
        init: function() {
            var self = this;

            $( '.wpbdp-settings-email-preview, .wpbdp-settings-email-edit-btn' ).click(function(e) {
                e.preventDefault();

                var $email = $( this ).parents( '.wpbdp-settings-email' );
                $( this ).hide();
                $email.find( '.wpbdp-settings-email-editor' ).show();
            });

            $( '.wpbdp-settings-email-editor .cancel' ).click(function(e) {
                e.preventDefault();

                var $email = $( this ).parents( '.wpbdp-settings-email' );
                var $editor = $email.find( '.wpbdp-settings-email-editor' );

                // Add-new editor.
                if ( $email.parent().is( '#wpbdp-settings-expiration-notices-add' ) ) {
                    $email.hide();
                    $( '#wpbdp-settings-expiration-notices-add-btn' ).show();
                    return;
                }

                // Sync editor with old values.
                var subject = $editor.find( '.stored-email-subject' ).val();
                var body = $editor.find( '.stored-email-body' ).val();
                $editor.find( '.email-subject' ).val( subject );
                $editor.find( '.email-body' ).val( body );

                if ( $email.hasClass( 'wpbdp-expiration-notice-email' ) ) {
                    var event = $editor.find( '.stored-notice-event' ).val();
                    var reltime = $editor.find( '.stored-notice-relative-time' ).val();

                    $editor.find( '.notice-event' ).val( event );
                    $editor.find( '.notice-relative-time' ).val( reltime );

                    if ( ! reltime ) {
                        reltime = '0 days';
                    }

                    $editor.find( 'select.relative-time-and-event' ).val( event + ',' + reltime );
                }

                // Hide editor.
                $editor.hide();
                $email.find( '.wpbdp-settings-email-preview' ).show();
            });

            $( '.wpbdp-settings-email-editor .delete' ).click(function(e) {
                e.preventDefault();

                var $email = $( this ).parents( '.wpbdp-settings-email' );
                $email.find( 'input.email-subject' ).val( '' );
                $email.find( 'input.email-body' ).val( '' );
                $( '#wpbdp-admin-page-settings form:first' ).submit();
            });

            // Expiration notices have some additional handling to do.
            $( '.wpbdp-expiration-notice-email select.relative-time-and-event' ).change(function(e) {
                var parts = $( this ).val().split(',');
                var event = parts[0];
                var relative_time = parts[1];

                var $email = $( this ).parents( '.wpbdp-settings-email' );
                $email.find( '.notice-event' ).val( event );
                $email.find( '.notice-relative-time' ).val( relative_time );
            });

            $( '#wpbdp-settings-expiration-notices-add-btn' ).click(function(e) {
                e.preventDefault();

                var $container = $( '#wpbdp-settings-expiration-notices-add .wpbdp-expiration-notice-email' );
                var $editor = $container.find( '.wpbdp-settings-email-editor' );

                $( this ).hide();
                $container.show();
                $editor.show();
            });

            $( '#wpbdp-settings-expiration-notices-add input[type="submit"]' ).click(function(e) {
                var $editor = $( this ).parents( '.wpbdp-settings-email-editor' );

                $editor.find( 'input, textarea, select' ).each( function(i) {
                    var name = $( this ).attr( 'name' );

                    if ( ! name || -1 == name.indexOf( 'new_notice' ) )
                        return;

                    name = name.replace( 'new_notice', 'wpbdp_settings[expiration-notices]' );
                    $( this ).prop( 'name', name );
                } );

                return true;
            });
        },
    };
    wpbdp_settings_email.init();

});

