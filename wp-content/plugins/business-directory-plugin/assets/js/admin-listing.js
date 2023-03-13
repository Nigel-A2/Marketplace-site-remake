( function( $ ) {
    // Disable Preview button until the first draft is saved
    $( document ).ready( function() {

        var $form = $( 'body.post-type-wpbdp_listing form#post' ),
            post_status = $form.find( '#original_post_status' );

        if ( post_status.length == 0 || post_status.val() != 'auto-draft' ) {
            return;
        }

        $form.find( '#preview-action .button' ).addClass( 'disabled' );

        $form.find( '#minor-publishing' ).tooltip( {
            items: '#preview-action',
            content: WPBDP_admin_listings_config.messages.preview_button_tooltip,
            position: {
                my: "left top+40",
                at: "left bottom",
                collision: "flipfit",
                within: '#minor-publishing'
            }
        } );
    } );

} )( jQuery );
