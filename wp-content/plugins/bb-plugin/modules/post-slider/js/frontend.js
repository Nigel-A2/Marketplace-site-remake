(function($) {
	
	/**
	 * Class for Post Slider Module
	 *
	 * @since 1.5.9
	 */
	FLBuilderPostSlider = function( settings ){

		// set params
		this.settings 		     = settings.settings;
		this.nodeClass           = '.fl-node-' + settings.id;
		this.wrapperClass        = this.nodeClass + ' .fl-post-slider-wrapper';
		this.postClass           = this.nodeClass + ' .fl-post-slider-post';
		this.prevSliderBtn       = $( this.nodeClass + ' .slider-prev' );
		this.nextSliderBtn       = $( this.nodeClass + ' .slider-next' );
		this.navigation          = settings.navigationControls;

		// check if module have posts
		if(this._hasPosts()) {
			// initialize the slider
			this._initSlider();
		}
	};

	FLBuilderPostSlider.prototype = {
		settings                : {},
		nodeClass               : '',
		wrapperClass            : '',
		postClass               : '',
		prevSliderBtn			: '',
		nextSliderBtn			: '',
		navigation				: false,
		slider 			        : '',

		/**
		 * Check if the module have posts.
		 * 
		 * @since  1.5.9
		 * @return bool
		 */
		_hasPosts: function(){
			return $( this.postClass ).length > 0;
		},

		/**
		 * build an object with slider options.
		 *
		 * @since  1.5.9
		 * @return obj 		The slider options object.
		 */
		_getSettings: function(){
			var settings = {
				    onSliderLoad: function() { 
						$( this.wrapperClass ).addClass( 'fl-post-slider-loaded' ); 
					}.bind( this ),
				}

			return $.extend( {}, this.settings, settings );
		},

		/**
		 * Creates a new Swiper instance and initialize prev and next buttons.
		 *
		 * @since  1.5.9
		 * @return void
		 */
		_initSlider: function(){

			this.slider = $( this.wrapperClass ).bxSlider( this._getSettings() );
			
			$( this.wrapperClass ).data( 'bxSlider', this.slider );

			if( this.navigation ){

				this.prevSliderBtn.on( 'click', function( e ){
					e.preventDefault();
					this.slider.goToPrevSlide();
				}.bind( this ) );

				this.nextSliderBtn.on( 'click', function( e ){
					e.preventDefault();
					this.slider.goToNextSlide();
				}.bind( this ) );
				
			}

		},
				
	};
		
})(jQuery);