var FLBuilderCountdown;
var FLBuilderCountdownIntervals = FLBuilderCountdownIntervals || [];

(function($) {

	/**
	 * Class for Countdown Module
	 *
	 * @since 1.6.4
	 */
	FLBuilderCountdown = function( settings ){

		// set params
		this.nodeID           	 = settings.id;
		this.nodeClass           = '.fl-node-' + settings.id;
		this.wrapperClass        = this.nodeClass + ' .fl-countdown';
		this.dateWrapper		 = this.nodeClass + ' .fl-countdown-days';
		this.dateLabel 			 = $( this.dateWrapper + ' .fl-countdown-unit-label' ).data( 'label' );
		this.hoursWrapper		 = this.nodeClass + ' .fl-countdown-hours';
		this.hoursLabel			 = $( this.hoursWrapper + ' .fl-countdown-unit-label' ).data( 'label' );
		this.minutesWrapper		 = this.nodeClass + ' .fl-countdown-minutes';
		this.minutesLabel		 = $( this.minutesWrapper + ' .fl-countdown-unit-label' ).data( 'label' );
		this.secondsWrapper		 = this.nodeClass + ' .fl-countdown-seconds';
		this.secondsLabel		 = $( this.secondsWrapper + ' .fl-countdown-unit-label' ).data( 'label' );
		this.timestamp			 = settings.time;
		this.type				 = settings.type;

		// initialize the countdown
		this._initCountdown();

	};

	FLBuilderCountdown.prototype = {
		nodeClass               : '',
		wrapperClass            : '',
		countdown 	            : '',
		dateWrapper	            : '',
		dateLabel	            : '',
		hoursWrapper            : '',
		hoursLabel              : '',
		minutesWrapper          : '',
		minutesLabel            : '',
		secondsWrapper          : '',
		secondsLabel            : '',
		timestamp               : '',
		_timeInterval			: '',

		/**
		 * Gets the defined timestamp and return the remaining time.
		 *
		 * @since  1.6.4
		 * @return {Object}
		 */
		_getTimeRemaining: function( endtime ){
			var t       = Date.parse( endtime ) - Date.parse( new Date() );
			var seconds = Math.floor( (t/1000) % 60 );
			var minutes = Math.floor( (t/1000/60) % 60 );
			var hours   = Math.floor( (t/(1000*60*60)) % 24 );
			var days    = Math.floor( t/(1000*60*60*24) );

			return {
				'total'  : t,
				'days'   : ( days < 10 ) ? ( '0' + days ) : days,
				'hours'  : ('0' + hours).slice(-2),
				'minutes': ('0' + minutes).slice(-2),
				'seconds': ('0' + seconds).slice(-2)
			};
		},

		/**
		 * Gets the remaining time and updates the respective DOM elements.
		 *
		 * @see    _getTimeRemaining()
		 * @since  1.6.4
		 * @return void
		 */
		_setTimeRemaining: function(){
			var t        = this._getTimeRemaining( this.timestamp ),
				wrappers = {
					days  	: $( this.dateWrapper ),
					hours 	: $( this.hoursWrapper ),
					minutes : $( this.minutesWrapper ),
					seconds : $( this.secondsWrapper ),
				},
				labels = {
					days  	: this.dateLabel,
					hours 	: this.hoursLabel,
					minutes : this.minutesLabel,
					seconds : this.secondsLabel,
				};

			if( t.total <= 0 ){
				clearInterval( this._timeInterval );
				$.each( wrappers, function( type, element ){
					element.find('.fl-countdown-unit-number').html( '00' );
				} );

			} else {
				$.each( wrappers, function( type, element ){
					element.find('.fl-countdown-unit-number').html( t[type] );
					var $el = element.find('.fl-countdown-unit-label');
					var label = parseInt( t[type] ) != 1 ? labels[type].plural : labels[type].singular;
					$el.html( label );
				} );
			}
		},

		_setCircleCount: function(){
			var t   = this._getTimeRemaining( this.timestamp ),
				max = {
					days  	: 365,
					hours 	: 24,
					minutes : 60,
					seconds : 60
				},
				circles = {
					days    : $( this.dateWrapper ).find( 'svg' ),
					hours   : $( this.hoursWrapper ).find( 'svg' ),
					minutes : $( this.minutesWrapper ).find( 'svg' ),
					seconds : $( this.secondsWrapper ).find( 'svg' ),
				}

			$.each( circles, function( type, element ){
				var $circle   = element.find( '.fl-number' ),
					r      	  = $circle.attr('r'),
					circle 	  = Math.PI*(r*2),
					val    	  = t[type],
					total 	  = max[type],
					stroke 	  = ( 1 - ( val / total ) ) * circle;

			    $circle.css({ strokeDashoffset: stroke });
			} );

		},

		/**
		 * Initialize the logic for the countdown.
		 *
		 * @see    _setTimeRemaining()
		 * @since  1.6.4
		 * @return void
		 */
		_initCountdown: function(){
			var self = this;

			if ( 0 === $( this.wrapperClass ).length ) {
				return;
			}

			this._setTimeRemaining();
			if( this.type == 'circle' ){
				this._setCircleCount();
			}

			clearInterval( FLBuilderCountdownIntervals[ this.nodeID ] );
			FLBuilderCountdownIntervals[ this.nodeID ] = setInterval( function(){
				self._setTimeRemaining();
				if( self.type == 'circle' ){
					self._setCircleCount();
				}
			}, 1000 );

		},

	};

})(jQuery);
