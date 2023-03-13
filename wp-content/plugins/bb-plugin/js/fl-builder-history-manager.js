( function( $ ) {

	/**
	 * Manages undo/redo history for the builder.
	 */
	FLBuilderHistoryManager = {

		/**
		 * Array of change state labels.
		 */
		states: [],

		/**
		 * Array index for the current state.
		 */
		position: 0,

		/**
		 * Whether a state is currently rendering or not.
		 */
		rendering: false,

		/**
		 * Initializes hooks for saving state when changes
		 * in the builder are made.
		 */
		init: function() {
			var config = FLBuilderConfig.history
			var self = this

			this.states = config.states
			this.position = parseInt( config.position )
			this.setupMainMenuData()

			$.each( config.hooks, function( hook, label ) {
				FLBuilder.addHook( hook, function( e, data ) {
					self.saveCurrentState( label, data )
				} )
			} )

			FLBuilder.addHook( 'didPublishLayout', this.clearStatesOnPublish.bind( this ) )
			FLBuilder.addHook( 'restartEditingSession', this.saveCurrentStateOnRestartSession.bind( this ) )
			FLBuilder.addHook( 'historyItemClicked', this.itemClicked.bind( this ) )
			FLBuilder.addHook( 'undo', this.onUndo.bind( this ) )
			FLBuilder.addHook( 'redo', this.onRedo.bind( this ) )
		},

		/**
		 * Makes a request to save the current layout state.
		 */
		saveCurrentState: function( label, data ) {
			var self = this
			var data = 'undefined' === typeof data ? {} : data
			var moduleType = null

			if ( 'undefined' !== typeof data.moduleType && data.moduleType ) {
				moduleType = data.moduleType
			}

			const actions = FL.Builder.data.getLayoutActions()
			actions.saveHistoryState( label, moduleType )
		},

		/**
		 * Makes a request to save the current state when restarting
		 * the builder editing session if no states exist.
		 */
		saveCurrentStateOnRestartSession: function( e ) {
			if ( this.states.length ) {
				return
			}

			this.saveCurrentState( 'draft_created' )
		},

		/**
		 * Makes a request to clear all states for the current layout
		 * when publishing and exiting the builder.
		 */
		clearStatesOnPublish: function( e, data ) {
			var self = this

			this.states = []
			this.position = 0
			this.setupMainMenuData()

			const actions = FL.Builder.data.getLayoutActions()
			actions.clearHistoryStates( FLBuilderConfig.postId, data.shouldExit )
		},

		/**
		 * Makes a request to render a layout state with
		 * the specified position.
		 */
		renderState: function( position ) {
			var self = this

			if ( this.rendering || ! this.states.length ) {
				return
			}
			if ( $( '.fl-builder-settings:visible' ).length ) {
				return
			}

			var timeout = setTimeout( FLBuilder.showAjaxLoader, 2000 )
			this.rendering = true

			const actions = FL.Builder.data.getLayoutActions()
			const callback = function( response ) {
				var data = JSON.parse( response )
				if ( ! data.error ) {
					self.position = parseInt( data.position )
					FLBuilder.triggerHook( 'didRestoreHistoryComplete', data )
					FLBuilder._renderLayout( data.layout )
					self.setupMainMenuData()
				}
				clearTimeout( timeout )
				self.rendering = false
			}
			actions.renderHistoryState( position, callback )
		},

		/**
		 * Renders the previous state.
		 */
		onUndo: function() {
			const actions = FL.Builder.data.getLayoutActions()
			actions.undo()
		},

		/**
		 * Renders the next state.
		 */
		onRedo: function() {
			const actions = FL.Builder.data.getLayoutActions()
			actions.redo()
		},

		/**
		 * Adds history states to the main menu data.
		 */
		setupMainMenuData: function() {
			var labels = FLBuilderConfig.history.labels
			var label = ''
			FLBuilderConfig.mainMenu.history.items = []

			for ( var i = this.states.length - 1; 0 <= i; i-- ) {

				if ( 'string' === typeof this.states[ i ] ) {
					label = labels[ this.states[ i ] ] ? labels[ this.states[ i ] ] : this.states[ i ]
				} else {
					label = labels[ this.states[ i ].label ] ? labels[ this.states[ i ].label ] : this.states[ i ].label

					if ( this.states[ i ].moduleType || -1 < this.states[ i ].label.indexOf( 'module' ) ) {
						label = label.replace( '%s', this.getModuleName( this.states[ i ].moduleType ) )
					}
				}

				FLBuilderConfig.mainMenu.history.items.push( {
					eventName: 'historyItemClicked',
					type: 'event',
					label: wp.template( 'fl-history-list-item' )( {
						label: label,
						current: i === this.position ? 1 : 0,
						position: i,
					} )
				} )
			}

			if ( ! FLBuilderConfig.history.enabled ) {
				FLBuilderConfig.mainMenu.history.items.push( {
					eventName: 'historyItemClicked',
					type: 'event',
					label: wp.template( 'fl-history-list-item' )( {
						label: FLBuilderConfig.history.labels.history_disabled,
						current: 0,
						position: 0,
					} )
				} )
			}

			if ( undefined !== FLBuilder.MainMenu ) {
				FLBuilder.MainMenu.renderPanel( 'history' )
			}
		},

		/**
		 * Returns a module's name by passing the type.
		 */
		getModuleName: function( type ) {
			var modules = FLBuilderConfig.contentItems.module
			var i = 0

			if ( 'widget' === type ) {
				return FLBuilderStrings.widget
			}

			for ( ; i < modules.length; i++ ) {
				if ( 'undefined' === typeof modules[ i ].slug ) {
					continue
				}
				if ( type === modules[ i ].slug ) {
					return modules[ i ].name
				}
			}

			return FLBuilderStrings.module
		},

		/**
		 * Callback for when a history item in the tools
		 * menu is clicked to render that state.
		 */
		itemClicked: function( e, item ) {
			var button = $( item ).find( '.fl-history-list-item' )
			var position = button.attr( 'data-position' )
			var current = $( '.fl-history-list-item[data-current=1]' )

			if ( $( '.fl-builder-settings:visible' ).length ) {
				FLBuilder._closeNestedSettings()
				FLBuilder._lightbox.close()
			}

			current.attr( 'data-current', 0 )
			button.attr( 'data-current', 1 )

			this.renderState( position )
		},
	}

	$( function() {
		FLBuilderHistoryManager.init()
	} )

} ( jQuery ) );
