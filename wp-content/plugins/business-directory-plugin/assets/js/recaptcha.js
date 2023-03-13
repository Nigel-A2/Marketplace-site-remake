jQuery( function( $ ) {
    var reCAPTCHA_Handler = function() {
        this.max_attempts = 20;
        this.attempts = 0;
        this.max_delay = 1500;
        this.timeout = false;
    };

    $.extend( reCAPTCHA_Handler.prototype, {
        render_widgets: function() {
            if ( this.timeout )
                clearTimeout( this.timeout );

            $( '.wpbdp-recaptcha' ).each(function(i, v) {
                var $captcha = $(v);

                if ( $captcha.data( 'wpbdp-recaptcha-enabled' ) )
                    return;

                if ( 'v2' === $captcha.attr('data-version') ) {
                    grecaptcha.render(
                        $captcha[0],
                        {
                            'sitekey': $captcha.attr( 'data-key' ),
                            'theme': 'light'
                        }
                    );
                }

                if ( 'v3' === $captcha.attr('data-version') ) {
                    grecaptcha.execute($captcha.attr('data-key'), {'action': 'homepage'} ).then( function ( token ) {
                        $captcha.find( 'input' ).val( token );
                    });
                }

                $captcha.data( 'wpbdp-recaptcha-enabled', true );
            });
        },

        render_widgets_when_ready: function() {
            if ( 'undefined' !== typeof grecaptcha && 'undefined' !== typeof grecaptcha.render ) {
                return this.render_widgets();
            }

            var self = this;
            this.timeout = setTimeout( function() { self.render_widgets_when_ready() }, this.max_delay * Math.pow( this.attempts / this.max_attempts, 2 ) );
            this.attempts++;
        }
    });

    var wpbdp_rh = new reCAPTCHA_Handler();
    wpbdp_rh.render_widgets_when_ready();

    window.wpbdp_recaptcha_callback = function() {
        if ( typeof wpbdp_rh === 'undefined' )
            wpbdp_rh = new reCAPTCHA_Handler();
        wpbdp_rh.render_widgets();
    };

    // Handle submit reCAPTCHA.
    $( window ).on( 'wpbdp_submit_refresh', function( event, submit, section_id ) {
        wpbdp_rh.render_widgets();
    } );
} );
