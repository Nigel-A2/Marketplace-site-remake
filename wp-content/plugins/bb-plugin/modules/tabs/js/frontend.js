(function($) {

	FLBuilderTabs = function( settings )
	{
		this.settings 	= settings;
		this.nodeClass  = '.fl-node-' + settings.id;
		this._init();
	};

	FLBuilderTabs.prototype = {

		settings	: {},
		nodeClass   : '',

		_init: function()
		{
			var win = $(window);

			$(this.nodeClass + ' .fl-tabs-labels .fl-tabs-label').click($.proxy(this._labelClick, this));
			$(this.nodeClass + ' .fl-tabs-labels .fl-tabs-label').on('keypress', $.proxy(this._labelClick, this));
			$(this.nodeClass + ' .fl-tabs-panels .fl-tabs-label').click($.proxy(this._responsiveLabelClick, this));
			$(this.nodeClass + ' .fl-tabs-panels .fl-tabs-label').on('keypress', $.proxy(this._responsiveLabelClick, this));

			if($(this.nodeClass + ' .fl-tabs-vertical').length > 0) {
				this._resize();
				win.off('resize' + this.nodeClass);
				win.on('resize' + this.nodeClass, $.proxy(this._resize, this));
			}

			FLBuilderLayout.preloadAudio( this.nodeClass + ' .fl-tabs-panel-content' );
		},

		_labelClick: function(e)
		{
			var label       = $(e.target).closest('.fl-tabs-label'),
				index       = label.data('index'),
				wrap        = label.closest('.fl-tabs'),
				allIcons    = wrap.find('.fl-tabs-panels .fl-tabs-label .fas'),
				icon        = wrap.find('.fl-tabs-panels .fl-tabs-label[data-index="' + index + '"] .fas');

			// Click or keyboard (enter or space) input?
			if(!this._validClick(e)) {
				return;
			}

			// Toggle the responsive icons.
			allIcons.addClass('fa-plus');
			icon.removeClass('fa-plus');

			// Toggle the tabs.
			wrap.find('.fl-tabs-labels:first > .fl-tab-active').removeClass('fl-tab-active').attr('aria-selected', 'false').attr('aria-expanded', 'false');
			wrap.find('.fl-tabs-panels:first > .fl-tabs-panel > .fl-tab-active').removeClass('fl-tab-active');
			wrap.find('.fl-tabs-panels:first > .fl-tabs-panel > .fl-tabs-panel-content').attr('aria-hidden', 'true').css('display', '');

			wrap.find('.fl-tabs-labels:first > .fl-tabs-label[data-index="' + index + '"]').addClass('fl-tab-active').attr('aria-selected', 'true').attr('aria-expanded', 'true');
			wrap.find('.fl-tabs-panels:first > .fl-tabs-panel > .fl-tabs-panel-label[data-index="' + index + '"]').addClass('fl-tab-active');
			wrap.find('.fl-tabs-panels:first > .fl-tabs-panel > .fl-tabs-panel-content[data-index="' + index + '"]').addClass('fl-tab-active').attr('aria-hidden', 'false');

			// Gallery module support.
			FLBuilderLayout.refreshGalleries( wrap.find('.fl-tabs-panel-content[data-index="' + index + '"]') );

			// Grid layout support (uses Masonry)
			FLBuilderLayout.refreshGridLayout( wrap.find('.fl-tabs-panel-content[data-index="' + index + '"]') );

			// Post Carousel support (uses BxSlider)
			FLBuilderLayout.reloadSlider( wrap.find('.fl-tabs-panel-content[data-index="' + index + '"]') );

			// WP audio shortcode support
			FLBuilderLayout.resizeAudio( wrap.find('.fl-tabs-panel-content[data-index="' + index + '"]') );

			// Slideshow module support.
			FLBuilderLayout.resizeSlideshow();

			// Reload Google Map embed.
			FLBuilderLayout.reloadGoogleMap( wrap.find('.fl-tabs-panel-content[data-index="' + index + '"]') );

			e.preventDefault();
		},

		_responsiveLabelClick: function(e)
		{
			var label           = $(e.target).closest('.fl-tabs-label'),
				wrap            = label.closest('.fl-tabs'),
				index           = label.data('index'),
				content         = label.siblings('.fl-tabs-panel-content'),
				activeContent   = wrap.find('.fl-tabs-panel-content.fl-tab-active'),
				activeIndex     = activeContent.data('index'),
				allIcons        = wrap.find('.fl-tabs-panels .fl-tabs-label > .fas'),
				icon            = label.find('.fas');

			// Click or keyboard (enter or space) input?
			if(!this._validClick(e)) {
				return;
			}

			// Should we proceed?
			if(index == activeIndex) {
				return;
			}
			if(wrap.hasClass('fl-tabs-animation')) {
				return;
			}

			// Toggle the icons.
			allIcons.addClass('fa-plus');
			icon.removeClass('fa-plus');

			// Run the animations.
			wrap.addClass('fl-tabs-animation');
			activeContent.slideUp('normal');

			content.slideDown('normal', function(){

				wrap.find('.fl-tab-active').removeClass('fl-tab-active').attr('aria-hidden', 'true');
				wrap.find('.fl-tabs-panels:first > .fl-tabs-panel > .fl-tabs-panel-content').attr('aria-hidden', 'true');
				wrap.find('.fl-tabs-label[data-index="' + index + '"]').addClass('fl-tab-active').attr('aria-hidden', 'false');
				wrap.find('.fl-tabs-panels:first > .fl-tabs-panel > .fl-tabs-panel-content[data-index="' + index + '"]').attr('aria-hidden', 'false');
				content.addClass('fl-tab-active');
				wrap.removeClass('fl-tabs-animation');

				// Gallery module support.
				FLBuilderLayout.refreshGalleries( content );

				// Grid layout support (uses Masonry)
				FLBuilderLayout.refreshGridLayout( content );

				// Post Carousel support (uses BxSlider)
				FLBuilderLayout.reloadSlider( content );

				// WP audio shortcode support
				FLBuilderLayout.resizeAudio( content );

				// Reload Google Map embed.
				FLBuilderLayout.reloadGoogleMap( wrap.find('.fl-tabs-panel-content[data-index="' + index + '"]') );

				if(label.offset().top < $(window).scrollTop() + 100) {
					$('html, body').animate({ scrollTop: label.offset().top - 100 }, 500, 'swing');
				}
			});
		},

		_resize: function()
		{
			$(this.nodeClass + ' .fl-tabs-vertical').each($.proxy(this._resizeVertical, this));
		},

		_resizeVertical: function(e)
		{
			var wrap    = $(this.nodeClass + ' .fl-tabs-vertical'),
				labels  = wrap.find('.fl-tabs-labels'),
				panels  = wrap.find('.fl-tabs-panels');

			panels.css('min-height', labels.height() + 'px');
		},

		_validClick: function(e)
		{
			return (e.which == 1 || e.which == 13 || e.which == 32) ? true : false;
		}
	};

})(jQuery);
