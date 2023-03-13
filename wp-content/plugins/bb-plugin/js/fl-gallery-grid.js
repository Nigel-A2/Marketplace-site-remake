(function($) {

	/**
	 * Builds a gallery grid of items.
	 *
	 * @class FLBuilderGalleryGrid
	 * @since 1.2.3
	 */
	FLBuilderGalleryGrid = function(settings)
	{
		$.extend(this, settings);

		if($(this.wrapSelector).length > 0) {
			$(window).on('resize', $.proxy(this.resize, this));
			this.resize();
		}
	};

	/**
	 * Prototype for new instances.
	 *
	 * @since 1.2.3
	 * @property {Object} prototype
	 */
	FLBuilderGalleryGrid.prototype = {

		/**
		 * A CSS selector for the element that wraps
		 * the gallery items.
		 *
		 * @since 1.2.3
		 * @property {String} wrapSelector
		 */
		wrapSelector    : '.fl-gallery-grid',

		/**
		 * A CSS selector for the gallery items.
		 *
		 * @since 1.2.3
		 * @property {String} itemSelector
		 */
		itemSelector    : '> *',

		/**
		 * The maximum width of the items.
		 *
		 * @since 1.2.3
		 * @property {Number} itemWidth
		 */
		itemWidth       : 400,

		/**
		 * A ratio to use for the item height.
		 *
		 * @since 1.2.3
		 * @property {Number} itemHeight
		 */
		itemHeight      : 0.75,

		/**
		 * RTL support
		 *
		 * @since 1.10.8
		 * @property {Boolean} isRTL
		 */
		isRTL           : false,

		/**
		 * Callback that fires when the window is resized
		 * to resize the gallery items.
		 *
		 * @since 1.2.3
		 * @method resize
		 */
		resize: function()
		{
			if ( ! $(this.wrapSelector).length ) {
				return;
			}
			var winWidth    = $(window).width(),
				wrap        = $(this.wrapSelector),
				wrapWidth   = wrap[0].getBoundingClientRect().width,
				numCols     = winWidth > 480 ? Math.ceil(wrapWidth/this.itemWidth) : 1,
				items       = wrap.find(this.itemSelector),
				itemWidth   = wrapWidth/numCols,
				itemHeight  = itemWidth * this.itemHeight,
				direction   = this.isRTL ? 'right' : 'left';

			// Browser bug fix. One column images are streched otherwise.
			if ( 1 === numCols ) {
				itemWidth -= 0.5;
			}

			// Set the item width and height.
			items.css({
				'float'  : direction,
				'height' : itemHeight + 'px',
				'width'  : itemWidth + 'px'
			});
		}
	};

})(jQuery);
