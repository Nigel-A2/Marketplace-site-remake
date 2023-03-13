(function($, FLBuilder) {

    /**
    * Base prototype for views in the menu
    */
    var PanelView = FLExtendableObject.create({

        templateName: "fl-main-menu-panel-view",

        name: "Untitled View",

        isShowing: false,

        isRootView: false,

        items: {},

        /**
        * Initialize the view
        *
        * @return void
        */
        init: function() {
            this.template = wp.template(this.templateName);
        },

        /**
        * Render the view
        *
        * @return String
        */
        render: function() {
            return this.template(this);
        },

        /**
        * Setup Events
        *
        * @return void
        */
        bindEvents: function() {
            this.$items = this.$el.find('.fl-builder--menu-item');
        },

        /**
        * Make this the current view
        *
        * @return void
        */
        show: function() {
            this.$el.addClass('is-showing');
			this.isShowing = true;
        },

        /**
        * Resign the active view
        *
        * @return void
        */
        hide: function() {
            this.$el.removeClass('is-showing');
			this.isShowing = false;
        },

        /**
        * Handle transitioning the view in
        *
        * @return void
        */
        transitionIn: function(reverse) {
            requestAnimationFrame( this.show.bind(this) );
        },

        /**
        * Handle transition away from the view
        *
        * @return void
        */
        transitionOut: function(reverse) {
            this.hide();
        },
    });

    /**
    * Menu Panel
    */
    var MainMenuPanel = FLExtendableObject.create({

        templateName: 'fl-main-menu-panel',

        template: null,

        menu: null,

        views: {},

        viewNavigationStack: [],

        isShowing: false,

        shouldShowTabs: false,

        /**
        * Setup and render the menu
        *
        * @return void
        */
        init: function() {

            // Render Panel
            this.template = wp.template(this.templateName);
            $('body').prepend( this.template(this) );
            this.$el = $('.fl-builder--main-menu-panel');
            this.$el.find('.fl-builder--main-menu-panel-views').html('');

            // Render Views
            for (var key in FLBuilderConfig.mainMenu) {
                this.renderPanel( key );
            }

            // Event Listeners
            $('body').on('click', '.fl-builder--main-menu-panel .pop-view', this.goToPreviousView.bind(this));

            this.$tabs = this.$el.find('.fl-builder--tabs > span'); /* on purpose */
            this.$tabs.on('click', this.onItemClick.bind(this));

            this.$barTitle = $('.fl-builder-bar-title'); /* on purpose */
            $('body').on('click', '.fl-builder-bar-title', this.toggle.bind(this));

            var hide = this.hide.bind(this);
            FLBuilder.addHook('didShowPublishActions', hide);
            FLBuilder.addHook('didBeginSearch', hide);
            FLBuilder.addHook('didBeginPreview', hide);
            FLBuilder.addHook('didShowContentPanel', hide);
            FLBuilder.addHook('endEditingSession', hide);
            FLBuilder.addHook('didFocusSearchBox', hide);
            FLBuilder.addHook('didEnterRevisionPreview', hide);
            FLBuilder.addHook('didFailSettingsSave', hide);
            FLBuilder.addHook('showKeyboardShortcuts', hide);

            this.$mask = $('.fl-builder--main-menu-panel-mask');
            this.$mask.on('click', hide);

            Tools.init();
            Help.init();
        },

        /**
        * Render the panel
        *
        * @param String key
        * @return void
        */
        renderPanel: function( key ) {
	        var data, view, $html;
			var current = this.views[ key ];

            data = FLBuilderConfig.mainMenu[ key ];
            data.handle = key;
            view = PanelView.create( data );
            view.init();
            $html = $( view.render() );
            view.$el = $html;
            $( '.fl-builder--main-menu-panel-views' ).append( $html );
            view.bindEvents();
            view.$el.find( '.fl-builder--menu-item' ).on( 'click', this.onItemClick.bind( this ) );

			if ( 'undefined' !== typeof current ) {
				current.$el.remove();
				if ( current.isShowing ) {
					this.currentView = view
					view.show();
				}
			}

            if ( view.isRootView ) {
	            this.rootView = view;
	            this.currentView = view;
	        }

            this.views[ key ] = view;
        },

        /**
        * Show the menu
        *
        * @return void
        */
        show: function() {
            if (this.isShowing) return;
            this.$el.addClass('is-showing');
            this.$barTitle.addClass('is-showing-menu');
            this.currentView.transitionIn();
            this.isShowing = true;
            this.$mask.show();
            FLBuilder.triggerHook('didOpenMainMenu');
        },

        /**
        * Hide the menu
        *
        * @return void
        */
        hide: function() {
            if (!this.isShowing) return;
            this.$el.removeClass('is-showing');
            this.$barTitle.removeClass('is-showing-menu');
            this.isShowing = false;
            this.resetViews();
            this.$mask.hide();
        },

        /**
        * Toggle show/hide the menu
        *
        * @return void
        */
        toggle: function() {
            if (this.isShowing) {
                this.hide();
            } else {
                this.show();
            }
        },

        /**
        * Handle item click
        *
        * @param {Event} e
        * @return void
        */
        onItemClick: function(e) {
            var $item = $(e.currentTarget),
                type = $item.data('type');

            switch (type) {
                case "view":
                    var name = $item.data('view');
                    this.goToView(name);
                    break;
                case "event":
                    var hook = $item.data('event');
                    FLBuilder.triggerHook(hook, $item);
                    break;
                case "link":
                    // follow link
                    break;
            }
        },

        /**
        * Display a specific view
        *
        * @param String name
        * @return void
        */
        goToView: function(name) {

            var currentView = this.currentView;
            var newView = this.views[name];

            currentView.transitionOut();
            newView.transitionIn();
            this.currentView = newView;
            this.viewNavigationStack.push(currentView);
        },

        /**
        * Pop a view off the stack
        *
        * @return void
        */
        goToPreviousView: function() {
            var currentView = this.currentView;
            var newView = this.viewNavigationStack.pop();
            currentView.transitionOut(true);
            newView.transitionIn(true);
            this.currentView = newView;
            $('.fl-builder-bar-title-caret').focus();
        },

        /**
        * Reset to root view
        *
        * @return void
        */
        resetViews: function() {
            if (this.currentView != this.rootView ) {
                this.currentView.hide();
                this.rootView.show();
                this.currentView = this.rootView;
                this.viewNavigationStack = [];
            }
        },
    });

    FLBuilder.MainMenu = MainMenuPanel;

    /**
    * Handle tools menu actions
    */
    var Tools = {

        /**
        * Setup listeners for tools actions
        * @return void
        */
        init: function() {
            FLBuilder.addHook('saveTemplate', this.saveTemplate.bind(this));
            FLBuilder.addHook('saveCoreTemplate', this.saveCoreTemplate.bind(this));
            FLBuilder.addHook('duplicateLayout', this.duplicateLayout.bind(this));
            FLBuilder.addHook('showLayoutSettings', this.showLayoutSettings.bind(this));
            FLBuilder.addHook('showGlobalSettings', this.showGlobalSettings.bind(this));
            FLBuilder.addHook('toggleUISkin', this.toggleUISkin.bind(this));
            FLBuilder.addHook('clearLayoutCache', this.clearLayoutCache.bind(this));
            FLBuilder.addHook('launchThemerLayouts', this.launchThemerLayouts.bind(this));

            // Show Keyboard Shortcuts
            if ( 'FL' in window && 'Builder' in FL ) {
                var actions = FL.Builder.data.getSystemActions();

                FLBuilder.addHook( 'showKeyboardShortcuts', function() {
                    actions.setShouldShowShortcuts( true );
                });
            }
        },

        /**
        * Show the save template lightbox
        * @return void
        */
        saveTemplate: function() {
            FLBuilder._saveUserTemplateClicked();
            MainMenuPanel.hide();
        },

        /**
        * Show save core template lightbox
        * @return void
        */
        saveCoreTemplate: function() {
            FLBuilderCoreTemplatesAdmin._saveClicked();
            MainMenuPanel.hide();
        },

        /**
        * Trigger duplicate layout
        * @return void
        */
        duplicateLayout: function() {
            FLBuilder._duplicateLayoutClicked();
            MainMenuPanel.hide();
        },

        /**
        * Show the global settings lightbox
        * @return void
        */
        showGlobalSettings: function() {
            FLBuilder._globalSettingsClicked();
            MainMenuPanel.hide();
        },

        /**
        * Show the layout js/css lightbox
        * @return void
        */
        showLayoutSettings: function() {
            FLBuilder._layoutSettingsClicked();
            MainMenuPanel.hide();
        },

        /**
        * Clear cache for this layout
        * @return void
        */
        clearLayoutCache: function() {
            FLBuilder.ajax({
                action: 'clear_cache'
            }, function() {
                location.href = FLBuilderConfig.editUrl;
            });
            FLBuilder.showAjaxLoader();
            MainMenuPanel.hide();
        },

        /**
        * Toggle between the UI Skins
        * @var Event
        * @return void
        */
        toggleUISkin: function(e) {
            var $item = $( 'a[data-event="toggleUISkin"]' ),
				$body = $( 'body' );

            if ( $body.hasClass('fl-builder-ui-skin--light') ) {
                var fromSkin = 'light';
                var toSkin = 'dark';
            }
            if ( $body.hasClass('fl-builder-ui-skin--dark') ) {
                var fromSkin = 'dark';
                var toSkin = 'light';
            }
            $body.removeClass( 'fl-builder-ui-skin--' + fromSkin ).addClass( 'fl-builder-ui-skin--' + toSkin );

			// FLUID color scheme classes
			$body.removeClass( 'fluid-color-scheme-' + fromSkin ).addClass( 'fluid-color-scheme-' + toSkin );

            if ( 'Builder' in FL && 'data' in FL.Builder ) {
				var actions = FL.Builder.data.getSystemActions()
				actions.setColorScheme( toSkin )
			}

			// ajax save
			FLBuilder.ajax({
				action: 'save_ui_skin',
				skin_name: toSkin,
			});
        },

        /**
        * @return void
        */
        launchThemerLayouts: function() {
			if ( FLBuilderConfig.lite ) {
				FLBuilder._showProMessage( 'Themer Layouts' );
			} else {
				window.open( FLBuilderConfig.themerLayoutsUrl );
			}
			MainMenuPanel.hide();
        },
    }

    var Help = {

        /**
        * Init the help controller
        * @return void
        */
        init: function() {
            FLBuilder.addHook('beginTour', this.onStartTourClicked.bind(this));
        },

        /**
        * Handle tour item click
        *
        * @return void
        */
        onStartTourClicked: function() {
            FLBuilderTour.start();
            MainMenuPanel.hide();
        },
    }

})(jQuery, FLBuilder);
