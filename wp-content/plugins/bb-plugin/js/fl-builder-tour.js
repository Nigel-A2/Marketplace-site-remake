(function( $ ) {

	/**
	 * Logic for the builder's help tour.
	 *
	 * @class FLBuilderTour
	 * @since 1.4.9
	 */
	FLBuilderTour = {

		/**
		 * A reference to the Bootstrap Tour object.
		 *
		 * @since 1.4.9
		 * @access private
		 * @property {Tour} _tour
		 */
		_tour: null,

		/**
		 * Starts the tour or restarts it if it
		 * has already run.
		 *
		 * @since 1.4.9
		 * @method start
		 */
		start: function()
		{
			if ( ! FLBuilderTour._tour ) {
				FLBuilderTour._tour = new Tour( FLBuilderTour._config() );
				FLBuilderTour._tour.init();
			}
			else {
				FLBuilderTour._tour.restart();
			}

			// Save existing settings first if any exist. Don't proceed if it fails.
			if ( ! FLBuilder._triggerSettingsSave( false, true ) ) {
				return;
			}

			FLBuilderTour._tour.start();
		},

		/**
		 * Returns a config object for the tour.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _config
		 * @return {Object}
		 */
		_config: function()
		{
			var config = {
				storage     : false,
				onStart     : FLBuilderTour._onStart,
				onPrev      : FLBuilderTour._onPrev,
				onNext      : FLBuilderTour._onNext,
				onEnd       : FLBuilderTour._onEnd,
				template    : '<div class="popover" role="tooltip"> <i class="fas fa-times" data-role="end"></i> <div class="arrow"></div> <h3 class="popover-title"></h3> <div class="popover-content"></div> <div class="popover-navigation clearfix"> <button class="fl-builder-button fl-builder-button-primary fl-builder-tour-next" data-role="next">' + FLBuilderStrings.tourNext + '</button> </div> </div>',
				steps       : [
					{
						animation   : false,
						element     : '.fl-builder--content-library-panel',
						placement   : 'left',
						title       : FLBuilderStrings.tourTemplatesTitle,
						content     : FLBuilderStrings.tourTemplates,
						onShow		: function() {
							FLBuilder.ContentPanel.show('templates');
						},
					},
					{
						animation   : false,
						element     : '.fl-builder--content-library-panel',
						placement   : 'left',
						title       : FLBuilderStrings.tourAddRowsTitle,
						content     : FLBuilderStrings.tourAddRows,
						onShow      : function() {
							FLBuilder.ContentPanel.show('rows');
						}
					},
					{
						animation   : false,
						element     : '.fl-builder--content-library-panel',
						placement   : 'left',
						title       : FLBuilderStrings.tourAddContentTitle,
						content     : FLBuilderStrings.tourAddContent,
						onShow      : function() {
							FLBuilder.ContentPanel.show('modules');
						}
					},
					{
						animation   : false,
						element     : '.fl-row.fl-builder-tour-demo-content',
						placement   : 'top',
						title       : FLBuilderStrings.tourEditContentTitle,
						content     : FLBuilderStrings.tourEditContent,
						onShow      : function() {
							FLBuilderTour._dimSection( '.fl-builder-bar' );
							FLBuilder._closePanel();
							$( '.fl-row.fl-builder-tour-demo-content' ).trigger( 'mouseenter' );
							$( '.fl-row.fl-builder-tour-demo-content .fl-module' ).eq( 0 ).trigger( 'mouseenter' );
						}
					},
					{
						animation   : false,
						element     : '.fl-row.fl-builder-tour-demo-content .fl-module-overlay .fl-block-overlay-actions',
						placement   : 'top',
						title       : FLBuilderStrings.tourEditContentTitle,
						content     : FLBuilderStrings.tourEditContent2,
						onShow      : function() {
							FLBuilderTour._dimSection( '.fl-builder-bar' );
							FLBuilder._closePanel();
							$( '.fl-row.fl-builder-tour-demo-content' ).trigger( 'mouseenter' );
							$( '.fl-row.fl-builder-tour-demo-content .fl-module' ).eq( 0 ).trigger( 'mouseenter' );
						}
					},
					{
						animation   : false,
						element     : '.fl-builder-content-panel-button',
						placement   : 'bottom',
						title       : FLBuilderStrings.tourAddContentButtonTitle,
						content     : FLBuilderStrings.tourAddContentButton,
						onShow      : function() {
							FLBuilderTour._dimSection( 'body' );
							$( '.fl-row' ).eq( 0 ).trigger( 'mouseleave' );
							$( '.fl-module' ).eq( 0 ).trigger( 'mouseleave' );
						}
					},
					{
						animation   : false,
						element     : '.fl-builder-bar-title',
						placement   : 'bottom',
						title       : FLBuilderStrings.tourToolsButtonTitle,
						content     : FLBuilderStrings.tourToolsButton,
						onShow      : function() {
							FLBuilderTour._dimSection( 'body' );
						}
					},
					{
						animation   : false,
						element     : '.fl-builder-done-button',
						placement   : 'bottom',
						title       : FLBuilderStrings.tourDoneButtonTitle,
						content     : FLBuilderStrings.tourDoneButton,
						onShow      : function() {
							FLBuilderTour._dimSection( 'body' );
						}
					},
					{
						animation   : false,
						orphan      : true,
						backdrop    : true,
						title       : FLBuilderStrings.tourFinishedTitle,
						content     : FLBuilderStrings.tourFinished,
						template    : '<div class="popover" role="tooltip"> <div class="arrow"></div> <i class="fas fa-times" data-role="end"></i> <h3 class="popover-title"></h3> <div class="popover-content"></div> <div class="popover-navigation clearfix"> <button class="fl-builder-button fl-builder-button-primary fl-builder-tour-next" data-role="end">' + FLBuilderStrings.tourEnd + '</button> </div> </div>',
					}
				]
			};

			// Remove the first step if no templates.
			if ( 'disabled' == FLBuilderConfig.enabledTemplates ) {
				config.steps.shift();
			}
			else if ( 'fl-builder-template' == FLBuilderConfig.postType ) {
				config.steps.shift();
			}
			return config;
		},

		/**
		 * Callback for when the tour starts.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _onStart
		 */
		_onStart: function()
		{
			var body = $( 'body' );
			body.scrollTop(0);

			body.append( '<div class="fl-builder-tour-mask"></div>' );

			if ( 'module' != FLBuilderConfig.userTemplateType ) {
				if ( 0 === $( '.fl-row' ).length ) {
					$( '.fl-builder-content' ).append( '<div class="fl-builder-tour-demo-content fl-builder-tour-placeholder-content fl-row fl-row-full-width fl-row-bg-none"> <div class="fl-row-content-wrap"> <div class="fl-row-content fl-row-fixed-width fl-node-content"> <div class="fl-col-group"> <div class="fl-col" style="width: 100%;"> <div class="fl-col-content fl-node-content"> <div class="fl-module fl-module-rich-text" data-type="rich-text" data-name="Text Editor"> <div class="fl-module-content fl-node-content"> <div class="fl-rich-text"> <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vivamus pellentesque ut lorem non cursus. Sed mauris nunc, porttitor iaculis lorem a, sollicitudin lacinia sapien. Proin euismod orci lacus, et sollicitudin leo posuere ac. In hac habitasse platea dictumst. Maecenas elit magna, consequat in turpis suscipit, ultrices rhoncus arcu. Phasellus finibus sapien nec elit tempus venenatis. Maecenas tincidunt sapien non libero maximus, in aliquam felis tincidunt. Mauris mollis ultricies facilisis. Duis condimentum dignissim tortor sit amet facilisis. Aenean gravida lacus eu risus molestie egestas. Donec ut dolor dictum, fringilla metus malesuada, viverra nunc. Maecenas ut purus ac justo aliquet lacinia. Cras vestibulum elementum tincidunt. Maecenas mattis tortor neque, consectetur dignissim neque tempor nec.</p> </div> </div> </div> </div> </div> </div> </div> </div></div>' );
					FLBuilder._setupEmptyLayout();
					FLBuilder._highlightEmptyCols();
				} else {
					$( '.fl-row' ).eq( 0 ).addClass( 'fl-builder-tour-demo-content' );
				}
			}
		},

		/**
		 * Callback for when the tour is navigated
		 * to the previous step.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _onPrev
		 */
		_onPrev: function()
		{
			$( '.fl-builder-tour-dimmed' ).remove();
		},

		/**
		 * Callback for when the tour is navigated
		 * to the next step.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _onNext
		 */
		_onNext: function()
		{
			$( '.fl-builder-tour-dimmed' ).remove();
		},

		/**
		 * Callback for when the tour ends.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _onEnd
		 */
		_onEnd: function()
		{
			$( 'body' ).off( 'fl-builder.template-selector-loaded' );
			$( '.fl-builder-tour-mask' ).remove();
			$( '.fl-builder-tour-dimmed' ).remove();
			$( '.fl-builder-tour-placeholder-content' ).remove();
			$( '.fl-builder-tour-demo-content' ).removeClass( 'fl-builder-tour-demo-content' );

			FLBuilder._setupEmptyLayout();
			FLBuilder._highlightEmptyCols();
			FLBuilder._showPanel();
			FLBuilder._initTemplateSelector();
		},

		/**
		 * Dims a section of the page.
		 *
		 * @since 1.4.9
		 * @access private
		 * @method _dimSection
		 * @param {String} selector A CSS selector for the section to dim.
		 */
		_dimSection: function( selector )
		{
			$( selector ).find( '.fl-builder-tour-dimmed' ).remove();
			$( selector ).append( '<div class="fl-builder-tour-dimmed"></div>' );
		}
	};

})( jQuery );
