(function($) {

	$(function() {
		$('.fl-embed-video').fitVids();

		// Fix multiple videos where autoplay is enabled.
		if (( $('.fl-module-video .fl-wp-video video').length > 1 ) && typeof $.fn.mediaelementplayer !== 'undefined' ) {
			$('.fl-module-video .fl-wp-video video').mediaelementplayer( {pauseOtherPlayers: false} );
		}

	});

	/**
	 * Class for the Video Module
	 * @since 2.4
	 */

	FLBuilderVideo = function( settings ){

		// Set params
		this.nodeID           	 = settings.id;
		this.nodeClass           = '.fl-node-' + settings.id;
		this.wrapperClass        = this.nodeClass + ' .fl-video';

		this._initVideo();
        this._initStickyOnScroll();

	};

	FLBuilderVideo.prototype = {

		_initVideo: function(){
            var origTop = $( this.nodeClass ).offset().top,
                origLeft = $( this.nodeClass ).offset().left,
                origHeight = $( this.nodeClass ).outerHeight(),
                origWidth = $( this.nodeClass ).outerWidth();

            $( this.nodeClass ).attr( 'data-orig-top', origTop );
            $( this.nodeClass ).attr( 'data-orig-left', origLeft );
            $( this.nodeClass ).attr( 'data-orig-height', origHeight );
            $( this.nodeClass ).attr( 'data-orig-width', origWidth );
        },

        _makeSticky: function(){
            var origLeft = $( this.nodeClass ).data( 'orig-left'),
                origHeight = $( this.nodeClass ).data( 'orig-height'),
                origWidth = $( this.nodeClass ).data( 'orig-width');

            $( this.nodeClass ).addClass( 'fl-video-sticky' );
            $( this.nodeClass ).css( 'left', origLeft );
            $( this.nodeClass ).css( 'height', origHeight );
            $( this.nodeClass ).css( 'width', origWidth );

        },

        _removeSticky: function(){
            $( this.nodeClass ).removeClass( 'fl-video-sticky' );
        },

        _initStickyOnScroll: function(){
            $( window ).on( 'scroll', $.proxy( function( e ) {

    			var win = $( window ),
    				winTop = win.scrollTop(),
                    nodeTop = $( this.nodeClass ).data( 'orig-top' );
                    isSticky = $( this.nodeClass ).hasClass( 'fl-video-sticky' );

    			if ( winTop >= nodeTop ) {
                    if ( ! isSticky ){
                        this._makeSticky();
                    }
    			} else {
                    this._removeSticky();
    			}

    		}, this ) );
        },

	};
})(jQuery);
