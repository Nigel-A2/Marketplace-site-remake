(function($) {

	FLBuilderAccordion = function( settings )
	{
		this.settings 	= settings;
		this.nodeClass  = '.fl-node-' + settings.id;
		this._init();
	};

	FLBuilderAccordion.prototype = {

		settings	: {},
		nodeClass   : '',

		_init: function()
		{
			$( this.nodeClass + ' .fl-accordion-button' ).on('click', $.proxy( this._buttonClick, this ) );
			$( this.nodeClass + ' .fl-accordion-button' ).on('keypress', $.proxy( this._buttonClick, this ) );
			$( this.nodeClass + ' .fl-accordion-button' ).on('focus', $.proxy( this._focusIn, this ) );
			$( this.nodeClass + ' .fl-accordion-button' ).on('focusout', $.proxy( this._focusOut, this ) );

			FLBuilderLayout.preloadAudio( this.nodeClass + ' .fl-accordion-content' );
			
			this._openActiveAccordion();
		},

		_openActiveAccordion: function (e) {
			var activeAccordion = $( this.nodeClass + ' .fl-accordion-item.fl-accordion-item-active' );
			
			if ( activeAccordion.length > 0 ) {
				activeAccordion.find('.fl-accordion-content').show();
			}
		},

		_buttonClick: function( e )
		{
			var button      = $( e.target ).closest('.fl-accordion-button'),
				accordion   = button.closest('.fl-accordion'),
				item	    = button.closest('.fl-accordion-item'),
				allContent  = accordion.find('.fl-accordion-content'),
				allIcons    = accordion.find('.fl-accordion-button i.fl-accordion-button-icon'),
				content     = button.siblings('.fl-accordion-content'),
				icon        = button.find('i.fl-accordion-button-icon');

			// Click or keyboard (enter or spacebar) input?
			if(!this._validClick(e)) {
				return;
			}

			// Prevent scrolling when the spacebar is pressed
			e.preventDefault();

			if(accordion.hasClass('fl-accordion-collapse')) {
				accordion.find( '.fl-accordion-item-active' ).removeClass( 'fl-accordion-item-active' );
				accordion.find( '.fl-accordion-button' ).attr('aria-expanded', 'false');
				accordion.find( '.fl-accordion-content' ).attr('aria-hidden', 'true');
				allContent.slideUp('normal');

				if( allIcons.find('svg').length > 0 ) {
					allIcons.find('svg').attr("data-icon",'plus');
				} else {
					allIcons.removeClass( this.settings.activeIcon );
					allIcons.addClass( this.settings.labelIcon );
				}
			}

			if(content.is(':hidden')) {
				button.attr('aria-expanded', 'true');
				item.addClass( 'fl-accordion-item-active' );
				item.find( '.fl-accordion-content' ).attr('aria-hidden', 'false');
				content.slideDown('normal', this._slideDownComplete);

				if( icon.find('svg').length > 0 ) {
					icon.find('svg').attr("data-icon",'minus');
				} else {
					icon.removeClass( this.settings.labelIcon );
					icon.addClass( this.settings.activeIcon );
				}

				icon.find('span').text( 'Collapse' );
			}
			else {
				button.attr('aria-expanded', 'false');
				item.removeClass( 'fl-accordion-item-active' );
				item.find( '.fl-accordion-content' ).attr('aria-hidden', 'true');
				content.slideUp('normal', this._slideUpComplete);

				if( icon.find('svg').length > 0 ) {
					icon.find('svg').attr("data-icon",'plus');
				} else {
					icon.removeClass( this.settings.activeIcon );
					icon.addClass( this.settings.labelIcon );
				}

				icon.find('span').text( 'Expand' );
			}
		},

		_focusIn: function( e )
		{
			var button      = $( e.target ).closest('.fl-accordion-button');

			button.attr('aria-selected', 'true');
		},

		_focusOut: function( e )
		{
			var button      = $( e.target ).closest('.fl-accordion-button');

			button.attr('aria-selected', 'false');
		},

		_slideUpComplete: function()
		{
			var content 	= $( this ),
				accordion 	= content.closest( '.fl-accordion' );

			accordion.trigger( 'fl-builder.fl-accordion-toggle-complete' );
		},

		_slideDownComplete: function()
		{
			var content 	= $( this ),
				accordion 	= content.closest( '.fl-accordion' ),
				item 		= content.parent(),
				win  		= $( window );

			FLBuilderLayout.refreshGalleries( content );

			// Grid layout support (uses Masonry)
			FLBuilderLayout.refreshGridLayout( content );

			// Post Carousel support (uses BxSlider)
			FLBuilderLayout.reloadSlider( content );

			// WP audio shortcode support
			FLBuilderLayout.resizeAudio( content );

			// Reload Google Map embed.
			FLBuilderLayout.reloadGoogleMap( content );

			// Slideshow module support.
			FLBuilderLayout.resizeSlideshow();

			if ( item.offset().top < win.scrollTop() + 100 ) {
				$( 'html, body' ).animate({
					scrollTop: item.offset().top - 100
				}, 500, 'swing');
			}

			accordion.trigger( 'fl-builder.fl-accordion-toggle-complete' );
		},

		_validClick: function(e)
		{
			return (e.which == 1 || e.which == 13 || e.which == 32 || e.which == undefined) ? true : false;
		}
	};

})(jQuery);
