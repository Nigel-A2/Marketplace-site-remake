(function($){

	/**
	 * Helper class for the icon selector lightbox.
	 *
	 * @class FLIconSelector
	 * @since 1.0
	 */
	FLIconSelector = {

		/**
		 * A reference to the lightbox HTML content that is
		 * loaded via AJAX.
		 *
		 * @since 1.0
		 * @access private
		 * @property {String} _content
		 */
		_content    : null,

		/**
		 * A reference to a FLLightbox object.
		 *
		 * @since 1.0
		 * @access private
		 * @property {FLLightbox} _lightbox
		 */
		_lightbox   : null,

		/**
		 * A flag for whether the content has already
		 * been rendered or not.
		 *
		 * @since 1.0
		 * @access private
		 * @property {Boolean} _rendered
		 */
		_rendered   : false,

		/**
		 * The text that is used to filter the selection
		 * of visible icons.
		 *
		 * @since 1.0
		 * @access private
		 * @property {String} _filterText
		 */
		_filterText : '',

		_liveFilterText: '',

		/**
		 * Opens the icon selector lightbox.
		 *
		 * @since 1.0
		 * @method open
		 * @param {Function} callback A callback that fires when an icon is selected.
		 */
		open: function(callback)
		{
			if(!FLIconSelector._rendered) {
				FLIconSelector._render();
			}

			if(FLIconSelector._content === null) {

				FLIconSelector._lightbox.open('<div class="fl-builder-lightbox-loading"></div>');

				FLBuilder.ajax({
					action: 'render_icon_selector'
				}, FLIconSelector._getContentComplete);
			}
			else {
				FLIconSelector._lightbox.open();
				$('.fl-icons-filter-text-live').focus();
			}

			FLIconSelector._lightbox.on('icon-selected', function(event, icon){
				FLIconSelector._lightbox.off('icon-selected');
				FLIconSelector._lightbox.close();
				callback(icon);
			});
		},

		/**
		 * Renders a new instance of FLLightbox.
		 *
		 * @since 1.0
		 * @access private
		 * @method _render
		 */
		_render: function()
		{
			FLIconSelector._lightbox = new FLLightbox({
				className: 'fl-icon-selector'
			});

			FLIconSelector._rendered = true;

			FLBuilder.addHook( 'endEditingSession', function() {
				FLIconSelector._lightbox.close()
			} );
		},

		/**
		 * Callback for when the lightbox content
		 * has been returned via AJAX.
		 *
		 * @since 1.0
		 * @access private
		 * @method _getContentComplete
		 * @param {String} response The JSON with the HTML lightbox content.
		 */
		_getContentComplete: function(response)
		{
			var data = FLBuilder._jsonParse(response);


			FLIconSelector._content = data.html;
			FLIconSelector._lightbox.setContent(data.html);

			$('.fl-icons-filter-text-live').on('keyup', $.debounce( 1000, FLIconSelector.livefilter ));
			$('.fl-icons-filter-text-live').focus();
			$('.fl-icons-list i').on('click', FLIconSelector._select);
			$('.fl-icon-selector-cancel').on('click', $.proxy(FLIconSelector._lightbox.close, FLIconSelector._lightbox));
			FLIconSelector.renderRecent();
		},

		renderRecent: function() {
			var recent   = FLBuilderConfig.recentIcons;
			if ( recent.length < 1 ) {
				$('.fl-icons-section.recent h2.recent').hide();
				return false;
			}
			$('.fl-icons-section.recent h2.recent').show();
			$('.fl-icons-section.recent').show()
			$('.recent-icons').html('');
			$.each(recent, function( i, icon ) {
				$('.recent-icons').append( '<i class="' + icon + '"></i>');
			});
			$('.recent-icons').show();
			$('.recent-icons i').on('click', FLIconSelector._select);

			// check if recent icons have ::before, if they dont the css for set is missing, so hide icon
			recents = $('.recent-icons i');
			$.each( recents, function( i,icon ) {
				var str = window.getComputedStyle($(icon)[0], ':before').getPropertyValue('content');
				if ( 'none' == str ) {
					$(icon).hide();
				}
			});
		},

		livefilter: function() {
			var text    = $( '.fl-icons-filter-text-live' ).val();

			if ( text === FLIconSelector._liveFilterText ) {
				return false;
			}

			$('.fl-icons-section.results').html('')

			if ( '' === text ) {
				FLIconSelector._liveFilterText = '';
				$( '.fl-icons-section' ).show();
				FLIconSelector.renderRecent();
			} else {
				$('.fl-icons-section.recent').hide();
				$('.fl-icons-section.all-icons').hide()
				$('.fl-icons-section.results').html('<i class="fas fa-spinner fa-spin"></i>')
				FLIconSelector._liveFilterText = text;

				FLBuilder.ajax({
					action: 'query_icons',
					text: text
				}, FLIconSelector._query_result);

			}
		},

		_query_result: function(result) {

			var results = $('.fl-icons-section.results'),
						html = '';

			if ( ! result || '[]' === result ) {
				html = '<h2>No Icons Found</h2>'
				FLIconSelector.renderRecent();
				results.html(html);
				results.show();
				return false;
			}

			var data = FLBuilder._jsonParse( result ),
			prefix = '';

			$.each(data, function(i,section) {
					html += '<h2>' + section.name + '</h2>';
					$.each(section.data, function( i, icon ) {
						$.each(icon.styles, function( i,style) {
							prefix = '';
							switch( style ) {
								case 'solid':
									prefix = 'fas';
									break;
								case 'regular':
									prefix = 'far';
									break;
								case 'light':
									prefix = 'fal';
									break;
								case 'duotone':
									prefix = 'fad';
									break;
								case 'thin':
									prefix = 'fa-thin' // fa6
									break;
								case 'brands':
									prefix = 'fa-brands fab' // fa6 + fa5
									break;
								case 'legacy':
									prefix = section.prefix;
							}
							if ( prefix.length > 0 ) {
								prefix += ' ';
							}
							html += '<i class="' + prefix + icon.tag + '" title="' + icon.label + '"></i>';
						})
					});
			})

		//	FLBuilder.hideAjaxLoader()
			results.html(html);
			results.show();
			$('.fl-icons-section.results i').on('click', FLIconSelector._select);
		},

		/**
		 * Filters the selection of visible icons based on
		 * the library select and search input text.
		 *
		 * @since 1.0
		 * @access private
		 * @method _filter
		 */
		_filter: function()
		{
			var section = $( '.fl-icons-filter-select' ).val(),
				text    = $( '.fl-icons-filter-text' ).val() || '';

			// Filter sections.
			if ( 'all' == section ) {
				$( '.fl-icons-section' ).show();
			}
			else {
				$( '.fl-icons-section' ).hide();
				$( '.fl-' + section ).show();
			}

			// Filter icons.
			FLIconSelector._filterText = text;

			if ( '' !== text ) {
				$( '.fl-icons-list i' ).each( FLIconSelector._filterIcon );
			}
			else {
				$( '.fl-icons-list i' ).show();
			}
		},

		/**
		 * Shows or hides an icon based on the filter text.
		 *
		 * @since 1.0
		 * @access private
		 * @method _filterIcon
		 */
		_filterIcon: function()
		{
			var icon = $( this );

			if ( -1 == icon.attr( 'class' ).indexOf( FLIconSelector._filterText ) ) {
				icon.hide();
			}
			else {
				icon.show();
			}
		},

		/**
		 * Called when an icon is selected and fires the
		 * icon-selected event on the lightbox.
		 *
		 * @since 1.0
		 * @access private
		 * @method _select
		 */
		_select: function()
		{
			var icon = $(this).attr('class');

			FLBuilder.ajax({
				action: 'recent_icons',
				icon: icon
			}, FLIconSelector._updateRecents);

			FLIconSelector._lightbox.trigger('icon-selected', icon);
		},

		_updateRecents: function(result) {
			FLBuilderConfig.recentIcons = FLBuilder._jsonParse(result);
		}
	};

})(jQuery);
