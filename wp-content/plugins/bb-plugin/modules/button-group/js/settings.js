(function ($) {
    FLBuilder.registerModuleHelper( 'button-group', {
        init: function() {
            var form   = $( '.fl-builder-settings' ),
                layout = form.find( 'select[name=layout]' ),
                width  = form.find( 'select[name=width]' );
            
            this._toggleAlignField();
            layout.on( 'change', this._toggleAlignField );
            width.on( 'change', this._toggleAlignField );
        },
        
        /**
         * Toggle the Align field depending on the value of Layout and Width.
         */
        _toggleAlignField: function () {
            var form   = $( '.fl-builder-settings' ),
                layout = form.find( 'select[name=layout]' ).val(),
                width  = form.find( 'select[name=width]' ).val();
            
            if ( 'vertical' === layout && '' === width ) {
                $( '#fl-field-align' ).hide();
            } else {
                $( '#fl-field-align' ).show();
            }
        }
    });
})(jQuery);
