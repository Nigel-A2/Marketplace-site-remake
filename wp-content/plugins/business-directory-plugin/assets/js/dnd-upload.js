var wpbdp = window.wpbdp || {};

( function( $ ) {
    var dnd = wpbdp.dnd = {
        setup: function( $area, options ) {
            options = $.extend( options, {} );
            var $input = $area.find( 'input[type="file"]' );

            $area.data( 'dnd-working', false );
            $area.on( 'dragover',
                      function( e ) {
                        if ( ! $( this ).hasClass( 'dragging' ) )
                            $( this ).addClass( 'dragging' );
                      } )
                 .on( 'dragleave',
                      function( e ) {
                        if ( $( this ).hasClass('dragging') )
                            $( this ).removeClass( 'dragging' );
                      } );
            $input.fileupload({
                url: $area.attr( 'data-action' ) ? $area.attr( 'data-action' ) : options.url,
                sequentialUploads: true,
                dataType: 'json',
                singleFileUploads: false,
                dropZone: $area,
                formData: function( form ) {
                    return [ { name: 'images_count', value: $( '#wpbdp-uploaded-images .wpbdp-image input[type="hidden"]' ).length } ];
                },
                send: function( e, data ) {
                    if ( $area.data('dnd-working' ) )
                        return false;

                    if ( 'undefined' !== typeof options.validate )
                        if ( ! options.validate.call( $area, data ) )
                            return false;

                    $area.removeClass( 'dragging' );
                    $area.removeClass( 'error' );
                    $area.data( 'dnd-working', true );

                    $area.find( '.dnd-area-inside' ).fadeOut( 'fast', function() {
                        // TODO: use some text-based options instead of requiring additional <div>s inside $area.
                        $area.find( '.dnd-area-inside-working span' ).text( data.files.length );
                        $area.find( '.dnd-area-inside-working' ).fadeIn( 'fast' );
                    } );

                    return true;
                },
                done: function( e, data ) {
                    var res = data.result;

                    $area.data( 'dnd-working', false );
                    $area.find( '.dnd-area-inside-working' ).hide();

                    if ( res.success ) {
                        $area.find( '.dnd-area-inside' ).fadeIn( 'fast' );

                        if ( res.data.is_admin ) {
                            $area.removeClass( 'error' );
                            $area.find( '.dnd-area-inside-error' ).hide();
                        }
                    }

                    if ( 'undefined' !== typeof options.done )
                        options.done.call( $area, res );
                }
            });

            if ( 'undefined' !== typeof options.init ) {
                options.init.call( $area );
            }
        }
    };

} )( jQuery );
