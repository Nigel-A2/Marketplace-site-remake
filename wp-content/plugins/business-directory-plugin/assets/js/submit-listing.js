jQuery(function($) {

    var wpbdp = window.wpbdp || {};
    wpbdp.submit_listing = wpbdp.submit_listing || {};

    // Fee_Selection_Helper {{
    wpbdp.submit_listing.Fee_Selection_Helper = function( $submit, editing ) {
        this.editing = ( 'undefined' === typeof(editing) || ! editing ) ? false : true;
        this.reset();
    };
    $.extend( wpbdp.submit_listing.Fee_Selection_Helper.prototype, {
        reset: function() {
            this.field_wrapper = $( '.wpbdp-submit-listing-section-plan_selection .wpbdp-form-field-association-category' );
            this.field_type = '';
            this.plan_autoselect = false;

            if ( $( '.wpbdp-js-select2', this.field_wrapper ).length > 0 ) {
                this.field_type = 'select2';
            } else if ( this.field_wrapper.hasClass( 'wpbdp-form-field-type-select' ) ) {
                this.field_type = 'select';
            } else if ( this.field_wrapper.hasClass( 'wpbdp-form-field-type-checkbox' ) ) {
                this.field_type = 'checkbox';
            } else if ( this.field_wrapper.hasClass( 'wpbdp-form-field-type-radio' ) ) {
                this.field_type = 'radio';
            }

            this.field = this.field_wrapper.find( 'select, input[type="checkbox"], input[type="radio"]' );

            if ( ! this.field_type ) {
				// This shouldn't happen if the form has been loaded.
                return;
            }

            // First mark categories that were disabled since the beginning via HTML.
            if ( 'select2' === this.field_type ) {
                this.field.find( 'option[disabled="disabled"]' ).data( 'keep_disabled', true );
                // Workaround for https://github.com/select2/select2/issues/3992.
                var self = this;
                setTimeout(function() {
                    if ( this.field_type === 'select2' && $.fn.selectWoo ) {
                        self.field.selectWoo({placeholder: wpbdpSubmitListingL10n.categoriesPlaceholderTxt});
                    }
                } );
            }

            if ( this.editing ) {
                return;
            }

            this.skip_plan_selection = ( 1 === $( 'input[type="hidden"][name="skip_plan_selection"][value="1"]' ).length );
            if ( this.skip_plan_selection ) {
                return;
            }

            this.$plans_container = $( '.wpbdp-plan-selection-wrapper' );
            this.$plan_selection = this.$plans_container.find( '.wpbdp-plan-selection' );
            this.plans = this.$plan_selection.find( '.wpbdp-plan' );

            this.$plan_selection.hide();

            this.selected_categories = [];
            this.available_plans = this.plans.map(function() {
                return $(this).data('id');
            }).get();

            this.field.change( $.proxy( this.categories_changed, this ) );
            this.maybe_limit_category_options();
            this.field.first().trigger('change');
        },

        categories_changed: function() {
            this.selected_categories = [];

            if ( 'select2' === this.field_type || 'select' === this.field_type ) {
                this.selected_categories = this.field.val();
            } else if ( 'checkbox' === this.field_type ) {
                this.selected_categories = this.field.filter( ':checked' ).map(function() {
                    return $( this ).val();
                }).get();
            } else if ( 'radio' === this.field_type ) {
                this.selected_categories = this.field.filter( ':checked' ).val();
            }

            if ( ! this.selected_categories ) {
                this.selected_categories = [];
            }

            if ( ! Array.isArray( this.selected_categories ) ) {
                this.selected_categories = [this.selected_categories];
			}

            if ( ! this.selected_categories ) {
                this.selected_categories = [];
            }

            this.selected_categories = $.map( this.selected_categories, function(x) { return parseInt( x ); } );

            this.update_plan_list();
            this.update_plan_prices();

            if ( 'checkbox' === this.field_type || this.field.is( '[multiple]' ) ) {
                this.maybe_limit_category_options();
            }

            if ( 0 === this.selected_categories.length ) {
                this.plans.find( 'input[name="listing_plan"]' ).prop( {
                    'disabled': 0 === this.selected_categories.length,
                    'checked': false
                } );
            } else {
                this.plans.find( 'input[name="listing_plan"]' ).prop( 'disabled', false );
            }

            var self = this;
            if ( this.selected_categories.length > 0 ) {
                this.$plans_container.show();
                Reusables.Breakpoints.evaluate();

                this.$plan_selection.fadeIn( 'fast' );
            } else {
                this.$plans_container.fadeOut( 'fast', function() {
                    self.$plan_selection.hide();
                } );
            }

            if ( this.available_plans.length === 1 && this.plan_autoselect ) {
                $( '#wpbdp-plan-select-radio-' + this.available_plans[0] ).trigger( 'click' );
            }

            if ( ! this.plan_autoselect && 'checkbox' !== this.field_type && !$( this.field_wrapper ).hasClass( 'wpbdp-form-field-type-multiselect' ) ) {
                this.plan_autoselect = true;
            }

        },

        _enable_categories: function( categories ) {
            if ( 'none' !== categories && 'all' !== categories ) {
                this._enable_categories( 'none' );
            }

            if ( 'none' === categories || 'all' === categories ) {
                if ( 'select2' === this.field_type ) {
                    this.field.find( 'option' ).each(function(i, v) {
                        if ( true === $( this ).data( 'keep_disabled' ) ) {
                            // $( this ).prop( 'disabled', true );
                        } else {
                            $( this ).prop( 'disabled', ( 'all' === categories ) ? false : true );
                        }
                    });
                } else {

                    if ( 'all' === categories ) {
                        this.field_wrapper.find( '.wpbdp-form-field-checkbox-item, .wpbdp-form-field-radio-item' ).removeClass( 'disabled' );
                    } else {
                        this.field_wrapper.find( '.wpbdp-form-field-checkbox-item, .wpbdp-form-field-radio-item' ).addClass( 'disabled' );
                    }
                }

                return;
            }

            if ( 'select2' === this.field_type ) {
                this.field.find( 'option' ).each(function(i, v) {
                    if ( true === $( this ).data( 'keep_disabled' ) ) {
                    } else {
                        $( this ).prop( 'disabled', -1 === $.inArray( parseInt( $( this ).val() ), categories ) );
                    }
                });
            } else {
                this.field.each(function(i, v) {
                    if ( -1 !== $.inArray( parseInt( $( this ).val() ), categories ) ) {
                        $( this ).prop( 'disabled', false );
                        $( this ).parents().filter( '.wpbdp-form-field-checkbox-item, .wpbdp-form-field-radio-item' ).removeClass( 'disabled' );
                    }
                });
            }

        },

        maybe_limit_category_options: function() {
            var all_cats = false;
            var cats = [];
            var self = this;

            $.each(this.available_plans, function(i, v) {
                if ( all_cats ) {
                    return;
				}

                var plan_cats = self.plans.filter('[data-id="' + v + '"]').data('categories');

                if ( 'all' === plan_cats ) {
                    all_cats = true;
                } else {
                    cats = $.unique( cats.concat( plan_cats.toString().split( ',' ) ) );
                    cats = $.map( cats, function(x) { return parseInt( x ); } );
                }
            });

            if ( all_cats ) {
                this._enable_categories( 'all' );
            } else {
                this._enable_categories( cats );
            }
        },

        update_plan_list: function() {
            var self = this;
            var plans = [];

            // Recompute available plans depending on category selection.
            $.each( this.plans, function( i, v ) {
                var $plan = $( v );
                var plan_cats = $plan.data('categories').toString();
                var plan_supports_selection = true;

                if ( 'all' !== plan_cats && self.selected_categories ) {
                    plan_cats = $.map( plan_cats.split(','), function( x ) { return parseInt(x); } );

                    $.each( self.selected_categories, function( j, c ) {
                        if ( ! plan_supports_selection ) {
                            return;
						}

                        if ( -1 === $.inArray( c, plan_cats ) ) {
                            plan_supports_selection = false;
                        }
                    } );
                }

                if ( plan_supports_selection ) {
                    plans.push( $plan.data('id') );
                    $plan.show();
                } else {
                    $plan.hide();
                }
            } );

            self.available_plans = plans;
        },

        update_plan_prices: function() {
            var self = this;

            $.each( self.available_plans, function( i, plan_id ) {
                var $plan = self.plans.filter('[data-id="' + plan_id + '"]');
                var pricing = $plan.data('pricing-details');
                var price = null;

                switch ( $plan.data( 'pricing-model' ) ) {
                    case 'variable':
                        price = 0.0;

                        $.each( self.selected_categories, function( j, cat_id ) {
                            price += parseFloat(pricing[cat_id]);
                        } );
                        break;
                    case 'extra':
                        price = parseFloat( $plan.data( 'amount' ) ) + ( parseFloat( pricing.extra ) * self.selected_categories.length );
                        break;
                    default:
                        price = parseFloat( $plan.data( 'amount' ) );
                        break;
                }

                $plan.find( '.wpbdp-plan-price-amount' ).text( price ? $plan.data( 'amount-format' ).replace( '[amount]', price.toFixed(2) ) : $plan.data( 'free-text' ) );

                if ( self.available_plans.length === 1 ) {
                    $plan.find( '#wpbdp-plan-select-radio-' + plan_id ).prop( 'checked', true );
                }

            } );
        }
    });
    // }}

    wpbdp.submit_listing.Handler = function( $submit ) {
		var editField;
        this.$submit = $submit;
        this.$form = this.$submit.find( 'form' );
		this.ajax_url = this.$form.attr( 'data-ajax-url' );
        this.doing_ajax = false;

		editField = this.$form.find( 'input[name="editing"]' );
		if ( editField.length === 0 ) {
			// The placeholder form needs to be replaced.
			this.loadForm();
			this.editing = false;
		} else {
			this.editing = ( this.$form.find( 'input[name="editing"]' ).val() == '1' );
			this.skip_plan_selection = ( 1 === $( 'input[type="hidden"][name="skip_plan_selection"][value="1"]' ).length );
		}

        this.plan_handling();

        var self = this;
        this.$form.on( 'click', 'a.reset', function( e ) {
            e.preventDefault();
            self.$form.find('input[name="save_listing"]').val( '' );
            self.$form.find('input[name="reset"]').val( 'reset' );
            self.$form.submit();
        } );

        $( window ).on( 'wpbdp_submit_refresh', function( event, submit, section_id ) {
            self.fee_helper.reset();
        } );

        // Create account form.
        $( '#wpbdp-submit-listing' ).on( 'change', '#wpbdp-submit-listing-create_account', function( e ) {
            $( '#wpbdp-submit-listing-account-details' ).toggle();
        } );

        $( '#wpbdp-submit-listing' ).on( 'keyup', '#wpbdp-submit-listing-account-details input[type="password"]', function( e ) {
            self.check_password_strength( $( this) );
        } );

        $( '#wpbdp-submit-listing' ).on( 'click', '.wpbdp-inner-field-option-select_all', function( e ) {
            var $options = $( this ).parent().find( 'input[type="checkbox"]' );
            $options.prop( 'checked', $( this ).find( 'input' ).is(':checked') );
        } );

        $( '#wpbdp-submit-listing' ).on( 'click', 'button.submit-back-button', function( e ) {
            e.preventDefault();
            var prev_section_id = $( this ).attr( 'data-previous-section' );
            var prev_section    = self.$form.find( '.wpbdp-submit-listing-section' ).filter( '[data-section-id="' + prev_section_id + '"]' );
            if ( prev_section.length ) {
                var current_section_id = self.$form.find('input[name="current_section"]').val();
                var current_section    = $( this ).parents('.wpbdp-submit-listing-section');
                self.$form.find('input[name="current_section"]').val(prev_section_id);
                current_section.addClass('hidden').hide();
                prev_section.removeClass('hidden').show();

                self.$form.find('.wpbdp-submit-rootline .wpbdp-submit-section-' + current_section_id ).removeClass('wpbdp-submit-checked wpbdp-submit-section-current');
                self.$form.find('.wpbdp-submit-rootline .wpbdp-submit-section-' + prev_section_id ).addClass('wpbdp-submit-checked wpbdp-submit-section-current');
            }

            $( 'html, body' ).animate({
                scrollTop: self.$form.find('.wpbdp-submit-rootline').offset().top
            }, 500, function() {
                Reusables.Breakpoints.evaluate();
            });
        } );

        $( window ).trigger( 'wpbdp_submit_init' );
    };
    $.extend( wpbdp.submit_listing.Handler.prototype, {
        ajax: function( data, callback ) {
            if ( this.doing_ajax ) {
                alert( wpbdpSubmitListingL10n.waitAMoment );
                return;
            }

            this.doing_ajax = true;
            var self = this;

            $.post( this.ajax_url, data, function( res ) {
                if ( ! res.success ) {
                    alert( wpbdpSubmitListingL10n.somethingWentWrong );
                    return;
                }

                self.doing_ajax = false;
				if ( res.data && res.data.redirect ) {
					window.location.href = res.data.redirect;
				}
                callback.call( self, res.data );
            }, 'json' );
        },

		// Load the form with ajax to avoid page caching.
		loadForm: function() {
			var self = this,
				data = {
					action: 'wpbdp_ajax',
					handler: 'submit_listing__load_form'
				};

			
			self.ajax( data, function( res ) {
				if ( typeof res.form !== 'undefined' ) {
					self.$submit.html( res.form );
					this.plan_handling(); // Trigger a plan reset.
				}
			} );
		},

        plan_handling: function() {
            this.fee_helper = new wpbdp.submit_listing.Fee_Selection_Helper( this.$submit, this.editing );

            if ( this.editing ) {
                var $plan = this.$form.find( this.skip_plan_selection ? '.wpbdp-plan-selection .wpbdp-plan' : '.wpbdp-current-plan .wpbdp-plan' );
                var plan_cats = $plan.length ? $plan.data( 'categories' ).toString() : '';

                if ( 'all' !== plan_cats ) {
                    var supported_categories = $.map( $.unique( plan_cats.split( ',' ) ), function(x) { return parseInt(x); } );
                    this.fee_helper._enable_categories( supported_categories );
                }

                return;
            }

            var self = this;

            this.$submit.on( 'click', '#change-plan-link a', function(e) {
                e.preventDefault();

                self.$form.find('input[name="current_section"]').val('');
                var data = self.$form.serialize();
                data += '&action=wpbdp_ajax&handler=submit_listing__reset_plan';

                self.ajax( data, function( res ) {
                    self.refresh( res );
                } );
            }) ;
        },

        refresh: function(data) {
            var sections = data.sections;
            var messages = data.messages;

            var current_sections = this.$form.find( '.wpbdp-submit-listing-section' );
            current_sections.hide();
            var new_sections = sections;

            var self = this;

            // Update sections.
            $.each( new_sections, function( section_id, section_details ) {
                var $section = current_sections.filter( '[data-section-id="' + section_id + '"]' );
                var $new_html = $( section_details.html );

                $section.find( '.wpbdp-editor-area' ).each( function() {
                    wp.editor.remove( $( this ).attr( 'id' ) );
                } );

                $section.attr( 'class', $new_html.attr( 'class' ) );
                $section.find( '.wpbdp-submit-listing-section-content' ).fadeOut( 'fast', function() {
                    var $new_content = $new_html.find( '.wpbdp-submit-listing-section-content' );

                    $( this ).replaceWith( $new_content );

                    $section.find( '.wpbdp-editor-area' ).each( function() {
                        var id = $( this ).attr( 'id' );
                        wp.editor.initialize( id, WPBDPTinyMCESettings[ id ] );
                    } );

                    $( window ).trigger( 'wpbdp_submit_refresh', [self, section_id, $section] );

                    if ( ! $section.hasClass('hidden') ) {
                        self.$form.find('input[name="current_section"]').val( $section.attr( 'data-section-id' ) );
                        $section.show();
                    }

                    // Refresh things.
                    Reusables.Breakpoints.scan( $new_content );
                } );
            } );
        },

        check_password_strength: function( $input ) {
            var pass = $input.val();
            var $result = $input.siblings( '.wpbdp-password-strength-meter' );

            $result.removeClass( 'strength-0 strength-2 strength-3 strength-4' )
                   .html('');

            if ( ! pass ) {
                return;
            }

            var strength = wp.passwordStrength.meter( pass, wp.passwordStrength.userInputBlacklist(), '' );
            var strength_msg = '';

            switch ( strength ) {
                case 2:
                    strength_msg = pwsL10n.bad;
                    break;
                case 3:
                    strength_msg = pwsL10n.good;
                    break;
                case 4:
                    strength_msg = pwsL10n.strong;
                    break;
                case 5:
                    strength_msg = pwsL10n.mismatch;
                    break;
                default:
                    strength_msg = pwsL10n.short;
                    break;
            }

            $result.addClass( 'strength-' + ( ( strength < 5 && strength >= 2 ) ? strength : '0' ) );
            $result.html( strength_msg );

        }

        // function checkPasswordStrength( $pass1,
        //                                 $pass2,
        //                                 $strengthResult,
        //                                 $submitButton,
        //                                 blacklistArray ) {
        //         var pass1 = $pass1.val();
        //     var pass2 = $pass2.val();
        //
        //     // Reset the form & meter
        //     $submitButton.attr( 'disabled', 'disabled' );
        //         $strengthResult.removeClass( 'short bad good strong' );
        //
        //     // Extend our blacklist array with those from the inputs & site data
        //     blacklistArray = blacklistArray.concat( wp.passwordStrength.userInputBlacklist() )
        //
        //     // Get the password strength
        //     var strength = wp.passwordStrength.meter( pass1, blacklistArray, pass2 );
        //
        //     // Add the strength meter results
        //     return strength;
        // }

    });

    var $submit = $( '#wpbdp-submit-listing' );

    $( window ).on( 'wpbdp_submit_init', function() {
        $submit.find( '.wpbdp-editor-area' ).each( function() {
            var id = $( this ).attr( 'id' );
            wp.editor.initialize( id, WPBDPTinyMCESettings[ id ] );
        } );
    } );

    if ( $submit.length > 0 ) {
        var x = new wpbdp.submit_listing.Handler( $submit );
	}

});
