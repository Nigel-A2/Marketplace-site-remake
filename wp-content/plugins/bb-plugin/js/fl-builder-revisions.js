( function( $ ) {

	/**
	 * Revisions manager for the builder.
	 *
	 * @since 2.0
	 * @class Revisions
	 */
	var Revisions = {

		/**
		 * Initialize builder revisions.
		 *
		 * @since 2.0
		 * @method init
		 */
		init: function()
		{
			this.setupMainMenuData();

			$( '.fl-builder--revision-actions select' ).on( 'change', this.selectChanged );
			$( '.fl-cancel-revision-preview' ).on( 'click', this.exitPreview.bind( this ) );
			$( '.fl-apply-revision-preview' ).on( 'click', this.applyClicked.bind( this ) );

			FLBuilder.addHook( 'revisionItemClicked', this.itemClicked.bind( this ) );
			FLBuilder.addHook( 'didPublishLayout', this.refreshItems.bind( this ) );
		},

		/**
		 * Adds the revision items to the main menu data.
		 *
		 * @since 2.0
		 * @method setupMainMenuData
		 */
		setupMainMenuData: function()
		{
			var posts    = FLBuilderConfig.revisions.posts,
				authors  = FLBuilderConfig.revisions.authors,
				template = wp.template( 'fl-revision-list-item' ),
				select   = $( '.fl-builder--revision-actions select' ),
				date     = '',
				author   = '',
				i        = 0;

			FLBuilderConfig.mainMenu.revisions.items = [];
			select.html( '' );

			if ( 0 === posts.length ) {

				FLBuilderConfig.mainMenu.revisions.items.push( {
					eventName : 'noRevisionsMessage',
					type      : 'event',
					label     : wp.template( 'fl-no-revisions-message' )()
				} );

			} else {

				for ( ; i < posts.length; i++ ) {

					date   = FLBuilderStrings.revisionDate.replace( '%s', posts[ i ].date.diff );
					date  += ' (' + posts[ i ].date.published + ')';
					author = FLBuilderStrings.revisionAuthor.replace( '%s', authors[ posts[ i ].author ].name );

					FLBuilderConfig.mainMenu.revisions.items.push( {
						eventName : 'revisionItemClicked',
						type      : 'event',
						label     : template( {
							id          : posts[ i ].id,
							date 		: date,
							author 		: author,
							avatar		: authors[ posts[ i ].author ].avatar
						} )
					} );

					select.append( '<option value="' + posts[ i ].id + '">' + date + '</option>' );
				}
			}

			if ( undefined !== FLBuilder.MainMenu ) {
				FLBuilder.MainMenu.renderPanel( 'revisions' )
			}
		},

		/**
		 * Refreshes the items in the revisions menu.
		 *
		 * @since 2.0
		 * @method refreshItems
		 */
		refreshItems: function()
		{
			FLBuilder.ajax( {
				action: 'refresh_revision_items'
			}, this.refreshItemsComplete.bind( this ) );
		},

		/**
		 * Re-renders the revision items when they have been refreshed.
		 *
		 * @since 2.0
		 * @method preview
		 * @param {Number} id
		 */
		refreshItemsComplete: function( response )
		{
			FLBuilderConfig.revisions = FLBuilder._jsonParse( response );

			this.setupMainMenuData();
		},

		/**
		 * Callback for when a revision item is clicked
		 * to preview a revision.
		 *
		 * @since 2.0
		 * @method itemClicked
		 * @param {Object} e
		 * @param {Object} item
		 */
		itemClicked: function( e, item )
		{
			var id = $( item ).find( '.fl-revision-list-item' ).attr( 'data-revision-id' );

			// Save existing settings first if any exist. Don't proceed if it fails.
			if ( ! FLBuilder._triggerSettingsSave( false, true ) ) {
				return;
			}

			$( '.fl-builder--revision-actions select' ).val( id );

			this.preview( id );
		},

		/**
		 * Callback for when the revision select is changed.
		 *
		 * @since 2.0
		 * @method selectChanged
		 * @param {Object} e
		 */
		selectChanged: function( e )
		{
			Revisions.preview( $( this ).val() );
		},

		/**
		 * Restores a revision when the apply button is clicked.
		 *
		 * @since 2.0
		 * @method applyClicked
		 * @param {Object} e
		 */
		applyClicked: function( e )
		{
			var id = $( '.fl-builder--revision-actions select' ).val();

			Revisions.restore( id );
		},

		/**
		 * Previews a revision with the specified ID.
		 *
		 * @since 2.0
		 * @method preview
		 * @param {Number} id
		 */
		preview: function( id )
		{
			$( '.fl-builder--revision-actions' ).css( 'display', 'flex' );
			FLBuilder.triggerHook( 'didEnterRevisionPreview' );
			FLBuilder.showAjaxLoader();

			FLBuilder.ajax( {
				action		: 'render_revision_preview',
				revision_id : id
			}, this.previewRenderComplete.bind( this ) );
		},

		/**
		 * Previews a revision with the specified ID.
		 *
		 * @since 2.0
		 * @method previewRenderComplete
		 * @param {String} response
		 */
		previewRenderComplete: function( response )
		{
			FLBuilder._renderLayout( response, function() {
				FLBuilder._destroyOverlayEvents();
				FLBuilder._removeAllOverlays();
			} );
		},

		/**
		 * Exits a revision preview and restores the original layout.
		 *
		 * @since 2.0
		 * @method exitPreview
		 */
		exitPreview: function()
		{
			$( '.fl-builder--revision-actions' ).hide();
			FLBuilder.triggerHook( 'didExitRevisionPreview' );
			FLBuilder._bindOverlayEvents();
			FLBuilder._updateLayout();
		},

		/**
		 * Restores the layout to a revision with the specified ID.
		 *
		 * @since 2.0
		 * @method restore
		 * @param {Number} id
		 */
		restore: function( id )
		{
			$( '.fl-builder--revision-actions' ).hide();
			FLBuilder.triggerHook( 'didExitRevisionPreview' );
			FLBuilder.showAjaxLoader();
			FLBuilder._bindOverlayEvents();

			FLBuilder.ajax( {
				action		: 'restore_revision',
				revision_id : id
			}, Revisions.restoreComplete );
		},

		/**
		 * Callback for when a revision is restored.
		 *
		 * @since 2.0
		 * @method restoreComplete
		 * @param {String} response
		 */
		restoreComplete: function( response ) {
			var data = FLBuilder._jsonParse( response );
			FLBuilder._renderLayout( data.layout );
			FLBuilder.triggerHook( 'didRestoreRevisionComplete', data.config );

			settings = data.settings
			if( typeof( settings.css ) != "undefined" && settings.css !== null) {
    		FLBuilderSettingsConfig.settings.layout.css = settings.css
			}
			if( typeof( settings.js ) != "undefined" && settings.js !== null) {
				FLBuilderSettingsConfig.settings.layout.js = settings.js
			}
		}

	};

	$( function() { Revisions.init(); } );

} )( jQuery );
