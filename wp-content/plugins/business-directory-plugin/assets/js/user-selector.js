jQuery( function($) {
    var id = 0;
    var UserSelector = function( select, options ) {
        var self = this;

        self.$select = $( select );
        self.options = options;

        if (typeof self.options === 'undefined') {
            return false;
        }

        if ( self.options.mode === 'ajax' ) {
            return self.configureAjaxBehavior();
        }

        return self.configureInlineBehavior();
    };

    $.extend( UserSelector.prototype, {

        configureAjaxBehavior: function() {
            var self = this;
            var options = $.extend( true, {}, self.options.select2, {
                ajax: {
                    data: function(params) {
                        params.security = self.options.security;
                        params.listing_id = self.options.listing_id;

                        return params;
                    },
                    processResults: function( data ) {
                        if ( ! data.status ) {
                            return {results: {}};
                        }

                        var items = $.map( data.items, function( item ) {
                            return {
                                id: item.ID,
                                text: item.user_login
                            };
                        } );

                        return { results: items };
                    }
                }
            } );

            self.$select.selectWoo( options );

            if ( self.options.selected.id ) {
                var option = new Option( self.options.selected.text, self.options.selected.id, true, true );
                self.$select.append( option ).trigger( 'change' );
            }

            self.setupEventHandlers();
        },

        setupEventHandlers: function() {
            var self = this;

            self.$select.on( 'change.select2', function() {
                self.onChange();
            } );
        },

        configureInlineBehavior: function() {
            var self = this;

            self.$select.selectWoo( self.options.select2 );

            self.setupEventHandlers();
        },

        onChange: function() {
            var self = this;

            if ( $.isFunction( self.options.onChange ) ) {
                self.options.onChange( self.getSelectedUser() );
            }
        },

        getSelectedUser: function() {
            var self  = this;
            var users = self.$select.selectWoo( 'data' );

            if ( users && users.length ) {
                return { id: users[0].id, name: users[0].text };
            }

            return { id: 0, name: '' };
        },

        clearSelectedUser: function() {
            var self = this;

            self.$select.val( null ).trigger( 'change' );
        }
    } );

    $(document).ready(function() {
        // Edit listing screen
        $userSelect = $('#wpbdp-listing-owner').find( '.wpbdp-user-selector' );

        if ( $userSelect.length > 0 ) {
            $userSelector = new UserSelector( $userSelect, $userSelect.data( 'configuration' ) );
        }
    } );
    
} );
