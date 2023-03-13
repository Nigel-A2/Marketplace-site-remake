var FLBuilderNumber;

(function($) {

	/**
	 * Class for Number Counter Module
	 *
	 * @since 1.6.1
	 */
	FLBuilderNumber = function( settings ){

		// set params
		this.nodeClass           = '.fl-node-' + settings.id;
		this.wrapperClass        = this.nodeClass + ' .fl-number';
		this.layout              = settings.layout;
		this.type                = settings.type;
		this.number              = parseFloat( ( 'undefined' !== typeof window["number_module_" + settings.id] ) ? window["number_module_" + settings.id].number : settings.number );
		this.max                 = parseFloat( ( 'undefined' !== typeof window["number_module_" + settings.id] ) ? window["number_module_" + settings.id].max : settings.max );
		this.speed               = settings.speed;
		this.delay               = settings.delay;
		this.breakPoints         = settings.breakPoints;
		this.currentBrowserWidth = $( window ).width();
		this.animated            = false;
		this.format              = settings.format;

		// initialize the menu
		this._initNumber();

	};

	FLBuilderNumber.prototype = {
		nodeClass               : '',
		wrapperClass            : '',
		layout                  : '',
		type                    : '',
		number                  : 0,
		max                     : 0,
		speed                   : 0,
		delay                   : 0,
		format                  : {},

		_initNumber: function(){

			var self = this;

			if( typeof jQuery.fn.waypoint !== 'undefined' && ! this.animated ) {
				$( this.wrapperClass ).waypoint({
					offset: FLBuilderLayoutConfig.waypoint.offset + '%',
					triggerOnce: true,
					handler: function( direction ){
						self._initCount();
					}
				});
			} else {
				self._initCount();
			}
		},

		_initCount: function(){

			var $number = $( this.wrapperClass ).find( '.fl-number-string' );


			if( !isNaN( this.delay ) && this.delay > 0 ) {
				setTimeout( function(){
					if( this.layout == 'circle' ){
						this._triggerCircle();
					} else if( this.layout == 'bars' ){
						this._triggerBar();
					}
					this._countNumber();
				}.bind( this ), this.delay * 1000 );
			}
			else {
				if( this.layout == 'circle' ){
					this._triggerCircle();
				} else if( this.layout == 'bars' ){
					this._triggerBar();
				}
				this._countNumber();
			}
		},

		_countNumber: function(){

			var $number = $( this.wrapperClass ).find( '.fl-number-string' ),
				$string = $number.find( '.fl-number-int' ),
				number  = $string.data( 'number' ),
				current = 0,
				self    = this;

			if ( ! this.animated ) {
				$string.prop( 'Counter',0 ).animate({
					Counter: number
				}, {
					duration: this.speed,
					easing: 'swing',
					step: function ( now, fx ) {
						$string.text( self._formatNumber( now, fx ) );
					},
					complete: function() {
						self.animated = true;
					}
				});
			}
		},

		_triggerCircle: function(){

			var $bar   = $(this.wrapperClass).find('.fl-bar'),
				r 	   = $bar.attr('r'),
				circle = Math.PI * (r * 2),
				number = $(this.wrapperClass).find('.fl-number-int').data('number'),
				total  = $(this.wrapperClass).find('.fl-number-int').data('total'),
				val    = parseInt( number ),
				max    = this.type == 'percent' ? 100 : parseInt( total );
				
			if (val < 0) { val = 0;}
			if (val > max) { val = max;}

			if( this.type == 'percent' ){
				var pct = ( ( 100 - val ) /100) * circle;
			} else {
				var pct = ( 1 - ( val / max ) ) * circle;
			}

			$bar.animate({
				strokeDashoffset: pct
			}, {
				duration: this.speed,
				easing: 'swing',
				complete: function() {
					this.animated = true;
				}
			});

		},

		_triggerBar: function(){

			var $bar   = $( this.wrapperClass ).find( '.fl-number-bar' ),
				number = $(this.wrapperClass).find('.fl-number-int').data('number'),
				total  = $(this.wrapperClass).find('.fl-number-int').data('total');

			if( this.type == 'percent' ){
				number = number > 100 ? 100 : number;
			} else {
				total = total <= 0 ? number : total;
				number = Math.ceil((number / total) * 100);
			}

			if( ! this.animated ) {
				$bar.animate({
					width: number + '%'
				}, {
					duration: this.speed,
					easing: 'swing',
					complete: function() {
						this.animated = true;
					}
				});
			}
		},

		_formatNumber: function( n, fx ){
			var rgx	= /(\d+)(\d{3})/,
				num = fx.end.toString().split('.'),
				decLimit = 0;

			if ( 1 == num.length ) {
				n = parseInt( n );
			}
			else if ( num.length > 1 ) {
				decLimit = num[1].length > 2 ? 2 : num[1].length;
			}

			n += '';
			x  = n.split('.');
			x1 = x[0];
			x2 = x.length > 1 ? parseFloat( parseFloat( '.' + x[1] ).toFixed( decLimit ) ) : '';
			x2 = '' != x2 ? this.format.decimal + x2.toString().split('.').pop() : '';

			while ( rgx.test( x1 ) ) {
				x1 = x1.replace(rgx, '$1' + this.format.thousands_sep + '$2');
			}

			return x1 + x2;
		},
	};

})(jQuery);
