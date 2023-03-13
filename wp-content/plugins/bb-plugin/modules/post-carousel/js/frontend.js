(function($) {
	
	/**
	 * Class for Post Carousel Module
	 *
	 * @since 1.6.1
	 */
	FLBuilderPostCarousel = function( settings ){

		// set params
		this.settings 		     = settings.settings;
		this.transitionType	     = settings.transition;
		this.nodeClass           = '.fl-node-' + settings.id;
		this.wrapperClass        = this.nodeClass + ' .fl-post-carousel-wrapper';
		this.postClass           = this.nodeClass + ' .fl-post-carousel-post';
		this.prevCarouselBtn     = $( this.nodeClass + ' .carousel-prev' );
		this.nextCarouselBtn     = $( this.nodeClass + ' .carousel-next' );
		this.layout              = settings.layout;
		this.navigation          = settings.navigationControls;
		this.slideWidth          = settings.slideWidth;
		this.currentBrowserWidth = $( window ).width();

		// check if module have posts
		if( this._hasPosts() ) {

			// initialize the slider
			this._initCarousel();

			// check if viewport is resizing
			$( window ).on( 'resize', function( e ){
 
 				var width = $( window ).width();
 				
 				// if screen width is resized, reload the carousel
 			    if( width != this.currentBrowserWidth ){

 					this._resizeDebounce();
 			    	this.currentBrowserWidth = width;
 				}
 
			}.bind( this ) );

		}
	};

	FLBuilderPostCarousel.prototype = {
		settings                : {},
		nodeClass               : '',
		wrapperClass            : '',
		postClass               : '',
		prevCarouselBtn			: '',
		nextCarouselBtn			: '',
		layout      			: '',
		navigation 				: false,
		slideWidth   			: 0,
		carousel 			    : '',

		/**
		 * Check if the module have posts.
		 * 
		 * @since  1.6.1
		 * @return bool
		 */
		_hasPosts: function(){
			return $( this.postClass ).length > 0;
		},


		/**
		 * When screen is resized, reloads the carousel in a determinded interval.
		 *
		 * @see    this._reloadCarousel()
		 * @since  1.6.1
		 * @return void
		 */
 		_resizeDebounce: function(){	
 			clearTimeout( this.resizeTimer );
 			this.resizeTimer = setTimeout( function() {
 				this._reloadCarousel();
 			}.bind( this ), 250 );
 
 		},

 		/**
 		 * Checks screen size, and returns the number of slides for the current viewport.
 		 *
 		 * @since  1.6.1
 		 * @return int        	The number of slides for the current screen size.
 		 */
 		_getSlidesNumber: function(){
 			var $wrapperWidth  = this._getWrapperWidth(),
 				$slideWidth    = $( this.postClass ).width(),
 				columns = Math.ceil( $wrapperWidth / this.slideWidth );

			return columns;
 		},

 		/**
 		 * Calculates individual slide with based on screen size.
 		 *
 		 * @see    this._getSlidesNumber()
 		 * @since  1.6.1
 		 * @return int        	The correct slide width.
 		 */
 		_getSlideWidth: function(){
 			return Math.ceil( ( this._getWrapperWidth() - ( this.settings.slideMargin * ( this._getSlidesNumber() - 1 ) ) ) / this._getSlidesNumber() );
 		},

 		/**
 		 * Get the carousel module real width both visible and hidden element
 		 *
 		 * @since 1.8.1
 		 * @return int
 		 */
 		_getWrapperWidth: function()
 		{
 			var $wrapper = $( this.nodeClass + ' .fl-post-carousel' );
 				$width 	 = $wrapper.width();

 			if ( $width === 0 && $wrapper.is(':hidden') ) {

 				$clone = $wrapper.clone()
 					.css("visibility","hidden")
 					.appendTo($('.fl-row-content'));

		    	$width = $clone.outerWidth();
		    	$clone.remove();
 			}

 			return $width;
 		},

 		/**
 		 * Calculates slides variables and return an object with carousel options.
 		 *
 		 * @see this._getSlideWidth()
 		 * @see this._getSlidesNumber()
 		 * @since  1.6.1
 		 * @return obj 			The carousel options object.
 		 */
		_getSettings: function(){
			var newSettings,
				settings = {
					slideWidth: this._getSlideWidth(),
					minSlides: this._getSlidesNumber(),
					maxSlides: this._getSlidesNumber(),
				    onSliderLoad: function() { 
						$( this.wrapperClass ).addClass( 'fl-post-carousel-loaded' ); 
					}.bind( this ),

				}

			newSettings = $.extend( {}, this.settings, settings );

			return newSettings;
		},

		/**
		 * Initialize the carousel.
		 *
		 * @see this._getSettings()
		 * @since  1.6.1
		 * @return void
		 */
		_initCarousel: function(){

			this.carousel = $( this.wrapperClass ).bxSlider( this._getSettings() );			
			
			if( this.navigation ){

				this.prevCarouselBtn.on( 'click', function( e ){
					e.preventDefault();
					this.carousel.goToPrevSlide();
				}.bind( this ) );

				this.nextCarouselBtn.on( 'click', function( e ){
					e.preventDefault();
					this.carousel.goToNextSlide();
				}.bind( this ) );
				
			}

		},

		/**
		 * Reloads the carousel.
		 *
		 * @see this._getSettings()
		 * @since  1.6.1
		 * @return void
		 */
		_reloadCarousel: function(){
			var bxObject = this.carousel.data('bxSlider');

			if ( bxObject ) {
				bxObject.reloadSlider( this._getSettings() );
			}
			else {
				this.carousel.reloadSlider( this._getSettings() );
			}
		},
	
	};
		
})(jQuery);