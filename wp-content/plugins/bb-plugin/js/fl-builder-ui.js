(function($, FLBuilder) {

    /**
    * Polyfill for String.startsWith()
    */
    if (!String.prototype.startsWith) {
        String.prototype.startsWith = function(searchString, position){
          position = position || 0;
          return this.substr(position, searchString.length) === searchString;
      };
    }

		/**
		 * Polyfill for String.endsWidth()
		 */
		if (!String.prototype.endsWith) {
			String.prototype.endsWith = function(searchString, position) {
				var subjectString = this.toString();
				if (typeof position !== 'number' || !isFinite(position) || Math.floor(position) !== position || position > subjectString.length) {
					position = subjectString.length;
				}
				position -= searchString.length;
				var lastIndex = subjectString.indexOf(searchString, position);
				return lastIndex !== -1 && lastIndex === position;
			};
		}

    // Calculate width of text from DOM element or string. By Phil Freo <http://philfreo.com>
    $.fn.textWidth = function(text, font) {
        if (!$.fn.textWidth.fakeEl) $.fn.textWidth.fakeEl = $('<span>').hide().appendTo(document.body);
        $.fn.textWidth.fakeEl.text(text || this.val() || this.text()).css('font', font || this.css('font'));
        return $.fn.textWidth.fakeEl.width();
    };

    /**
    * Base object that all view objects can delegate to.
    * Has the ability to create new objects with itself as the new object's prototype.
    */
    FLExtendableObject = {
        /**
        * Create new object with the current object set as its prototype.
        * @var mixin - Object with properties to be mixed into the final object.
        * @return object
        */
        create: function(mixin) {
            // create a new object with this object as it's prototype.
            var obj = Object.create(this);
            // mix any given properties into it
            obj = $.extend(obj, mixin);

            $(this).trigger('onCreate');

            return obj;
        },
    };

    /**
    * jQuery function to set a class while removing all other classes from
    * the same element that start with the prefix.
    * This is to allow for class states where only one state from the group of classes
    * can be present at any time.
    */
    $.fn.switchClass = function (prefix, ending) {
        return this.each(function() {

            $(this).removeClass(function(i, classesString) {
                var classesToRemove = [];
                var classes = classesString.split(' ');
                for(var i in classes) {
                    if (classes[i].startsWith(prefix)) {
                        classesToRemove.push(classes[i]);
                    }
                }
                return classesToRemove.join(' ');
            });
            return $(this).addClass(prefix + ending);
        });
    };


    var KeyShortcuts = {

        /**
        * Initialize the keyboard shortcut manager.
        * @return void
        */
        init: function() {

            FLBuilder.addHook('cancelTask', this.onCancelTask.bind(this));
            FLBuilder.addHook('showSavedMessage', this.onSaveShortcut.bind(this));
            FLBuilder.addHook('goToNextTab', this.onNextPrevTabShortcut.bind(this, 'next'));
            FLBuilder.addHook('goToPrevTab', this.onNextPrevTabShortcut.bind(this, 'prev'));

            FLBuilder.addHook('endEditingSession', this.onEndEditingSession.bind(this));
            FLBuilder.addHook('restartEditingSession', this.onRestartEditingSession.bind(this));

            this.setDefaultKeyboardShortcuts();
        },

        /**
        * Add single keyboard shortcut
        * @var string A hook to be triggered by `FLBuilder.triggerhook(hook)`
        * @var string The key combination to trigger the command.
        * @var bool isGlobal - If the shortcut should work even inside inputs.
        * @return void
        */
        addShortcut: function( hook, key, isGlobal ) {
            var fn = $.proxy(this, 'onTriggerKey', hook);
            if ( isGlobal ) {
                Mousetrap.bindGlobal(key, fn);
            } else {
                Mousetrap.bind(key, fn);
            }
        },

        /**
        * Clear all registered key commands.
        * @return void
        */
        reset: function() {
            Mousetrap.reset();
        },

        /**
        * Set the default shortcuts
        * @return void
        */
        setDefaultKeyboardShortcuts: function() {
            this.reset();

            for( var action in FLBuilderConfig.keyboardShortcuts ) {
	            var code = FLBuilderConfig.keyboardShortcuts[action].keyCode,
                    isGlobal = FLBuilderConfig.keyboardShortcuts[action].isGlobal;

                this.addShortcut( action, code, isGlobal);
            }
        },

        /**
        * Handle a key command by triggering the associated hook.
        * @var string the hook to be fired.
        * @return void
        */
        onTriggerKey: function(hook, e) {
            FLBuilder.triggerHook(hook);

            if (e.preventDefault) {
                e.preventDefault();
            } else {
                // internet explorer
                e.returnValue = false;
            }
        },

        /**
        * Cancel out of the current task - triggered by pressing ESC
        * @return void
        */
        onCancelTask: function() {

            // Is the editor in preview mode?
            if (EditingUI.isPreviewing) {
                EditingUI.endPreview();
                return;
            }

            // Are the publish actions showing?
            if (PublishActions.isShowing) {
                PublishActions.hide();
                return;
            }

            // Is the content panel showing?
            if (FLBuilder.ContentPanel.isShowing) {
                FLBuilder.ContentPanel.hide();
                return;
            }
        },

        /**
        * Pause the active keyboard shortcut listeners.
        * @return void
        */
        pause: function() {
            Mousetrap.pause();
        },

        /**
        * Unpause the active keyboard shortcut listeners.
        * @return void
        */
        unpause: function() {
            Mousetrap.unpause();
        },

        /**
        * Handle ending the editing session
        * @return void
        */
        onEndEditingSession: function() {

            const actions = FL.Builder.data.getSystemActions()
            actions.setIsEditing( false )

			document.documentElement.classList.remove( 'fl-builder-assistant-visible' )

            this.reset();
            this.addShortcut('restartEditingSession', 'mod+e');
        },

        /**
        * Handle restarting the editing session
        * @return void
        */
        onRestartEditingSession: function() {

            const actions = FL.Builder.data.getSystemActions()
            actions.setIsEditing( true )

			const currentPanel = FL.Builder.data.getSystemState().currentPanel
			if ( 'assistant' === currentPanel ) {
				document.documentElement.classList.add( 'fl-builder-assistant-visible' )
			}

            this.reset();
            this.setDefaultKeyboardShortcuts();
        },

        /**
         * Handle CMD+S Save Shortcut
         *
         * @return void
         */
        onSaveShortcut: function() {
            if (FLBuilder.SaveManager.layoutNeedsPublish()) {

                var message = FLBuilderStrings.savedStatus.hasAlreadySaved;
                FLBuilder.SaveManager.showStatusMessage(message);

                setTimeout(function() {
                    FLBuilder.SaveManager.resetStatusMessage();
                }, 2000);

            } else {
                var message = FLBuilderStrings.savedStatus.nothingToSave;
                FLBuilder.SaveManager.showStatusMessage(message);

                setTimeout(function() {
                    FLBuilder.SaveManager.resetStatusMessage();
                }, 2000);
            }
        },

        onNextPrevTabShortcut: function( direction, e ) {

            var $lightbox = $('.fl-lightbox:visible'),
                $tabs = $lightbox.find('.fl-builder-settings-tabs a'),
                $activeTab,
                $nextTab;

            if ( $lightbox.length > 0 ) {
                $activeTab = $tabs.filter('a.fl-active');

                if ( 'next' == direction ) {

                    if ( $activeTab.is( $tabs.last() ) ) {

                        $nextTab = $tabs.first();

                    } else {

                        $nextTab = $activeTab.next('a');
                    }

                } else {

                    if ( $activeTab.is( $tabs.first() ) ) {
                        $nextTab = $tabs.last();
                    } else {
                        $nextTab = $activeTab.prev('a');
                    }
                }
                $nextTab.trigger('click');
            }

            FLBuilder._calculateSettingsTabsOverflow();
            e.preventDefault();
        },
    };


    /**
    * Publish actions button bar UI
    */
    var PublishActions = FLExtendableObject.create({

        /**
        * Is the button bar showing?
        * @var bool
        */
        isShowing: false,

        /**
        * Setup the bar
        * @return void
        */
        init: function() {

            this.$el = $('.fl-builder-publish-actions');
            this.$defaultBarButtons = $('.fl-builder-bar-actions');
            this.$clickAwayMask = $('.fl-builder-publish-actions-click-away-mask');

            this.$doneBtn = this.$defaultBarButtons.find('.fl-builder-done-button');
            this.$doneBtn.on('click', this.onDoneTriggered.bind(this));

            this.$actions = this.$el.find('.fl-builder-button');
            this.$actions.on('click touchend', this.onActionClicked.bind(this));

            FLBuilder.addHook('triggerDone', this.onDoneTriggered.bind(this));

            var hide = this.hide.bind(this);
            FLBuilder.addHook('cancelPublishActions', hide);
            FLBuilder.addHook('endEditingSession', hide);
            this.$clickAwayMask.on('click', hide );
        },

        /**
        * Fired when the done button is clicked or hook is triggered.
        * @return void
        */
        onDoneTriggered: function() {
            if (FLBuilder.SaveManager.layoutNeedsPublish()) {
                this.show();
            } else {
                if ( FLBuilderConfig.shouldRefreshOnPublish ) {
					FLBuilder._exit();
				} else {
					FLBuilder._exitWithoutRefresh();
				}
            }
        },

        /**
        * Display the publish actions.
        * @return void
        */
        show: function() {
            if (this.isShowing) return;

            // Save existing settings first if any exist. Don't proceed if it fails.
			if ( ! FLBuilder._triggerSettingsSave( false, true ) ) {
				return;
			}

            this.$el.removeClass('is-hidden');
            this.$defaultBarButtons.css('opacity', '0');
            this.$clickAwayMask.show();
            this.isShowing = true;
            FLBuilder.triggerHook('didShowPublishActions');
        },

        /**
        * Hide the publish actions.
        * @return void
        */
        hide: function() {
            if (!this.isShowing) return;
            this.$el.addClass('is-hidden');
            this.$defaultBarButtons.css('opacity', '1');
            this.$clickAwayMask.hide();
            this.isShowing = false;
        },

        /**
        * Fired when a publish action (or cancel) is clicked.
        * @return void
        */
        onActionClicked: function(e) {
            var action = $(e.currentTarget).data('action');
            switch(action) {
                case "dismiss":
                    this.hide();
                    break;
                case "discard":
                    this.hide();
                    EditingUI.muteToolbar();
                    FLBuilder._discardButtonClicked();
                    break;
                case "publish":
                    this.hide();
                    EditingUI.muteToolbar();
                    FLBuilder._publishButtonClicked();
                    FLBuilder._destroyOverlayEvents();
                    break;
                case "draft":
                    this.hide();
                    EditingUI.muteToolbar();
                    FLBuilder._draftButtonClicked();
                    break;
                default:
                    // draft
                    this.hide();
                    EditingUI.muteToolbar();
                    FLBuilder._draftButtonClicked();
            }
            FLBuilder.triggerHook( action + 'ButtonClicked' );
        },
    });


    /**
    * Editing UI State Controller
    */
    var EditingUI = {

        /**
        * @var bool - whether or not the editor is in preview mode.
        */
        isPreviewing: false,

        /**
        * Setup the controller.
        * @return void
        */
        init: function() {
            this.$el = $('body');
            this.$mainToolbar = $('.fl-builder-bar');
            this.$mainToolbarContent = this.$mainToolbar.find('.fl-builder-bar-content');
            this.$wpAdminBar = $('#wpadminbar');
            this.$endPreviewBtn = $('.fl-builder--preview-actions .end-preview-btn');

            FLBuilder.addHook('endEditingSession', this.endEditingSession.bind(this) );
            FLBuilder.addHook('previewLayout', this.togglePreview.bind(this) );

            // End preview btn
            this.$endPreviewBtn.on('click', this.endPreview.bind(this));

            // Preview mode device size icons
            this.$deviceIcons = $('.fl-builder--preview-actions i');
            this.$deviceIcons.on('click', this.onDeviceIconClick.bind(this));

            // Admin bar link to re-enable editor
            var $link = this.$wpAdminBar.find('#wp-admin-bar-fl-builder-frontend-edit-link > a, #wp-admin-bar-fl-theme-builder-frontend-edit-link > a');
            $link.on('click', this.onClickPageBuilderToolbarLink.bind(this));

            // Take admin bar links out of the tab order
            $('#wpadminbar a').attr('tabindex', '-1');

            var restart = this.restartEditingSession.bind(this);
            FLBuilder.addHook('restartEditingSession', restart);

            FLBuilder.addHook('didHideAllLightboxes', this.unmuteToolbar.bind(this));
            FLBuilder.addHook('didCancelDiscard', this.unmuteToolbar.bind(this));
            FLBuilder.addHook('didEnterRevisionPreview', this.hide.bind(this));
            FLBuilder.addHook('didExitRevisionPreview', this.show.bind(this));
            FLBuilder.addHook('didPublishLayout', this.onPublish.bind(this));
			FLBuilder.addHook('didPublishLayout', this.onPublishCacheClear.bind(this));
        },

        /**
        * Handle exit w/o preview
        * @return void
        */
        endEditingSession: function() {
            FLBuilder._destroyOverlayEvents();
            FLBuilder._removeAllOverlays();
            FLBuilder._removeEmptyRowAndColHighlights();
            FLBuilder._removeColHighlightGuides();
            FLBuilder._unbindEvents();

            $('html').removeClass('fl-builder-edit').addClass('fl-builder-show-admin-bar');
            $('body').removeClass('fl-builder-edit');
            $('#wpadminbar a').attr('tabindex', null );
			$( FLBuilder._contentClass ).removeClass( 'fl-builder-content-editing' );
            this.hideMainToolbar();
            FLBuilder.ContentPanel.hide();
            FLBuilderLayout.init();
        },

        /**
        * Re-enter the editor without refresh after having left without refresh.
        * @return void
        */
        restartEditingSession: function(e) {

            FLBuilder._initTemplateSelector();
            FLBuilder._bindOverlayEvents();
            FLBuilder._highlightEmptyCols();
			FLBuilder._rebindEvents();

            $('html').addClass('fl-builder-edit').removeClass('fl-builder-show-admin-bar');
            $('body').addClass('fl-builder-edit');
            $('#wpadminbar a').attr('tabindex', '-1');
			$( FLBuilder._contentClass ).addClass( 'fl-builder-content-editing' );
            this.showMainToolbar();

            e.preventDefault();
        },

        /**
        * Handle re-entering the editor when you click the toolbar button.
        * @return void
        */
        onClickPageBuilderToolbarLink: function(e) {
			FLBuilder.triggerHook('restartEditingSession');
            e.preventDefault();
        },

        /**
         * Make admin bar dot green
         *
         * @return void
         */
        onPublish: function() {
            var $dot = this.$wpAdminBar.find('#wp-admin-bar-fl-builder-frontend-edit-link > a span');
            $dot.css('color', '#6bc373');
        },

		/**
		 * Reload url via ajax, this rebuilds the cache files.
		 */
		onPublishCacheClear: function() {

			FLBuilder.ajax({
				action: 'clear_cache_for_layout',
			}, function(response) {
				console.log(response);
			});
		},



        /**
        * Hides the entire UI.
        * @return void
        */
        hide: function() {
	        if ( $( 'html' ).hasClass( 'fl-builder-edit' ) ) {
	            FLBuilder._unbindEvents();
	            FLBuilder._destroyOverlayEvents();
	            FLBuilder._removeAllOverlays();
	            $('html').removeClass('fl-builder-edit')
	            $('body').removeClass('admin-bar');
	            this.hideMainToolbar();
	            FLBuilder.ContentPanel.hide();
	            FLBuilderLayout.init();
	            FLBuilder.triggerHook('didHideEditingUI');
	        }
        },

        /**
        * Shows the UI when it's hidden.
        * @return void
        */
        show: function() {
	        if ( ! $( 'html' ).hasClass( 'fl-builder-edit' ) ) {
				FLBuilder._rebindEvents();
	            FLBuilder._bindOverlayEvents();
	            this.showMainToolbar();
	            FLBuilderResponsiveEditing._switchTo('default');
	            $('html').addClass('fl-builder-edit');
	            $('body').addClass('admin-bar');
	            FLBuilder.triggerHook('didShowEditingUI');
	        }
        },

        /**
        * Enter Preview Mode
        * @return void
        */
        beginPreview: function() {

	        // Save existing settings first if any exist. Don't proceed if it fails.
			if ( ! FLBuilder._triggerSettingsSave( false, true ) ) {
				return;
			}

            this.isPreviewing = true;
            this.hide();
            $('html').addClass('fl-builder-preview');
            $('html, body').removeClass('fl-builder-edit');
            FLBuilder._removeEmptyRowAndColHighlights();
            FLBuilder._removeColHighlightGuides();
            FLBuilder.triggerHook('didBeginPreview');
			FLBuilderResponsivePreview.enter();
        },

        /**
        * Leave preview module
        * @return void
        */
        endPreview: function() {
            this.isPreviewing = false;
            this.show();
            FLBuilder._highlightEmptyCols();
            FLBuilderResponsivePreview.exit();
            $('html').removeClass('fl-builder-preview');
            $('html, body').addClass('fl-builder-edit');
        },

        /**
        * Toggle in and out of preview mode
        * @return void
        */
        togglePreview: function() {
            if (this.isPreviewing) {
                this.endPreview();
            } else {
                this.beginPreview();
            }
        },

        /**
        * Hide the editor toolbar
        * @return void
        */
        hideMainToolbar: function() {
            this.$mainToolbar.addClass('is-hidden');
            $('html').removeClass('fl-builder-is-showing-toolbar');
        },

        /**
        * Show the editor toolbar
        * @return void
        */
        showMainToolbar: function() {
            this.unmuteToolbar();
            this.$mainToolbar.removeClass('is-hidden');
            $('html').addClass('fl-builder-is-showing-toolbar');
        },

        /**
        * Handle clicking a responsive device icon while in preview
        * @return void
        */
        onDeviceIconClick: function(e) {
            var mode = $(e.target).data('mode');
            FLBuilderResponsivePreview.switchTo(mode);
            FLBuilderResponsivePreview._showSize(mode);
        },

        /**
        * Make toolbar innert
        * @return void
        */
        muteToolbar: function() {
            this.$mainToolbarContent.addClass('is-muted');
            FLBuilder._hideTipTips();
        },

        /**
        * Re-activate the toolbar
        * @return void
        */
        unmuteToolbar: function() {
            this.$mainToolbarContent.removeClass('is-muted');
        },
    };

	/**
    * Browser history logic.
    */
	var BrowserState = {

        isEditing: true,

        /**
         * Init the browser state controller
         *
         * @return void
         */
        init: function() {

            if ( history.pushState ) {
                FLBuilder.addHook('endEditingSession', this.onLeaveBuilder.bind(this) );
                FLBuilder.addHook('restartEditingSession', this.onEnterBuilder.bind(this) );
            }
        },

        /**
         * Handle restarting the edit session.
         *
         * @return void
         */
        onEnterBuilder: function() {
            history.replaceState( {}, document.title, FLBuilderConfig.editUrl );
            const actions = FL.Builder.data.getSystemActions()
            actions.setIsEditing( true )
            this.isEditing = true;
        },

        /**
         * Handle exiting the builder.
         *
         * @return void
         */
        onLeaveBuilder: function() {
            history.replaceState( {}, document.title, FLBuilderConfig.url );
            const actions = FL.Builder.data.getSystemActions()
            actions.setIsEditing( false )
            this.isEditing = false;
        },
    };

    /**
    * Content Library Search
    */
    var SearchUI = {

        /**
        * Setup the search controller
        * @return void
        */
        init: function() {
            this.$searchBox = $('.fl-builder--search');
            this.$searchBoxInput = this.$searchBox.find('input#fl-builder-search-input');
            this.$searchBoxClear = this.$searchBox.find('.search-clear');

            this.$searchBoxInput.on('focus', this.onSearchInputFocus.bind(this));
            this.$searchBoxInput.on('blur', this.onSearchInputBlur.bind(this));
            this.$searchBoxInput.on('keyup', this.onSearchTermChange.bind(this));
            this.$searchBoxClear.on('click', this.onSearchTermClearClicked.bind(this));

            this.renderSearchResults = wp.template('fl-search-results-panel');
            this.renderNoResults = wp.template('fl-search-no-results');

            FLBuilder.addHook('didStartDrag', this.hideSearchResults.bind(this));
            FLBuilder.addHook('focusSearch', this.focusSearchBox.bind(this));
        },

        focusSearchBox: function() {
            this.$searchBoxInput.trigger('focus');
        },

        /**
        * Fires when focusing on the search field.
        * @return void
        */
        onSearchInputFocus: function() {
            this.$searchBox.addClass('is-expanded');
            FLBuilder.triggerHook('didFocusSearchBox');
        },

        /**
        * Fires when blurring out of the search field.
        * @return void
        */
        onSearchInputBlur: function(e) {
            this.$searchBox.removeClass('is-expanded has-text');
            this.$searchBoxInput.val('');
            this.hideSearchResults();
        },

        /**
        * Fires when a key is pressed inside the search field.
        * @return void
        */
        onSearchTermChange: function(e) {
        	if (e.key == 'Escape') {
        		this.$searchBoxInput.blur();
        		return;
        	}
        	FLBuilder.triggerHook('didBeginSearch');

            var value = this.$searchBoxInput.val();
            if (value != '') {
                this.$searchBox.addClass('has-text');
            } else {
                this.$searchBox.removeClass('has-text');
            }

            var results = FLBuilder.Search.byTerm(value);
            if (results.term != "") {
            	this.showSearchResults(results);
            } else {
            	this.hideSearchResults();
            }
        },

		/**
		* Fires when the clear button is clicked.
        * @return void
		*/
        onSearchTermClearClicked: function() {
            this.$searchBox.removeClass('has-text').addClass('is-expanded');
            this.$searchBoxInput.val('').focus();

            this.hideSearchResults();
        },

        /**
        * Display the found results in the results panel.
        * @var Object - the found results
        * @return void
        */
        showSearchResults: function(data) {

            if (data.total > 0) {
                var $html = $(this.renderSearchResults(data)),
                    $panel = $('.fl-builder--search-results-panel');
    			$panel.html($html);

    			FLBuilder._initSortables();
            } else {
                var $html = $(this.renderNoResults(data)),
                    $panel = $('.fl-builder--search-results-panel');
    			$panel.html($html);
            }
			$('body').addClass('fl-builder-search-results-panel-is-showing');
        },

        /**
        * Hide the search results panel
        * @return void
        */
        hideSearchResults: function() {
        	$('body').removeClass('fl-builder-search-results-panel-is-showing');
        },
    };

    var RowResize = {

        /**
        * @var {jQuery}
        */
        $row: null,

        /**
        * @var {jQuery}
        */
        $rowContent: null,

        /**
        * @var {Object}
        */
        row: null,

        /**
        * @var {Object}
        */
        drag: {},

        /**
        * Setup basic events for row content overlays
        * @return void
        */
        init: function() {

            if ( this.userCanResize() ) {
                var $layoutContent = $( FLBuilder._contentClass );

				$layoutContent.on( 'mouseenter touchstart', '.fl-row', this.onDragHandleHover.bind(this) );
                $layoutContent.on( 'mousedown touchstart', '.fl-block-row-resize', this.onDragHandleDown.bind(this) );
            }
        },

        /**
        * Check if the user is able to resize rows
        *
        * @return bool
        */
        userCanResize: function() {
            return FLBuilderConfig.rowResize.userCanResizeRows;
        },

        /**
        * Hover over a row resize drag handle.
        * @return void
        */
        onDragHandleHover: function(e) {

			if (this.drag.isDragging) {
				return
			};

            var $this = this,
			    originalWidth,
            	$handle = $(e.target),
				row = $handle.closest('.fl-row'),
				node = row.data('node'),
				form = $( '.fl-builder-row-settings[data-node=' + node + ']' ),
				unitField = form.find( '[name=max_content_width_unit]' ),
				unit = 'px';

			$this.onSettingsReady(node, function(settings){

				// Get unit.
				if (unitField.length) {
					unit =  unitField.length;
				} else if ('undefined' !== typeof settings) {
					unit = settings.max_content_width_unit;
				}

				$this.$row = row;
				$this.$rowContent = $this.$row.find('.fl-row-content');

	            $this.row = {
	                node: node,
	                form: form,
					unit: unit,
	                isFixedWidth: $this.$row.hasClass('fl-row-fixed-width'),
					parentWidth: 'vw' === unit ? $( window ).width() : $this.$row.parent().width(),
	            };

	            $this.drag = {
	                edge: null,
	                isDragging: false,
	                originalPosition: null,
	                originalWidth: null,
	                calculatedWidth: null,
	                operation: null,
	            };

	            if ($this.row.isFixedWidth) {
	                $this.drag.originalWidth = $this.$row.width();
	            } else {
	                $this.drag.originalWidth = $this.$rowContent.width();
	            }

	            $this.dragInit();
			});
        },

		/**
        * Check if FLBuilderSettingsConfig.node is available.
        * @return void
        */
		onSettingsReady: function(nodeId, callback) {
			var nodes = 'undefined' !== typeof FLBuilderSettingsConfig.nodes ? FLBuilderSettingsConfig.nodes : null;

			if (null !== nodes && 'undefined' !== typeof nodes[ nodeId ] ) {
				callback( nodes[ nodeId ] );

				if (null != RowResize._mouseEnterTimeout) {
					clearTimeout( RowResize._mouseEnterTimeout );
					RowResize._mouseEnterTimeout = null;
				}
			} else {
				// If settings is not yet available, check again by timeout.
				clearTimeout( RowResize._mouseEnterTimeout );
				RowResize._mouseEnterTimeout = setTimeout(this.onSettingsReady.bind(this), 350, nodeId, callback);
			}
		},

        /**
        * Handle mouse down on the drag handle
        * @return void
        */
        onDragHandleDown: function() {
            $('body').addClass( 'fl-builder-row-resizing' );

			if (null != RowResize._mouseEnterTimeout) {
				clearTimeout( RowResize._mouseEnterTimeout );
				RowResize._mouseEnterTimeout = null;
			}
        },

        /**
        * Setup the draggable handler
        * @return void
        */
        dragInit: function(e) {
            this.$row.find('.fl-block-row-resize').draggable( {
				axis 	: 'x',
				start 	: this.dragStart.bind(this),
				drag	: this.dragging.bind(this),
				stop 	: this.dragStop.bind(this)
			});
        },

        /**
        * Handle drag started
        * @var {Event}
        * @var {Object}
        * @return void
        */
        dragStart: function(e, ui) {

	        var body    = $( 'body' ),
	        	$handle = $(ui.helper);

            this.drag.isDragging = true;

            if (this.row.isFixedWidth) {
                this.drag.originalWidth = this.$row.width();
            } else {
                this.drag.originalWidth = this.$rowContent.width();
            }

            if ($handle.hasClass( 'fl-block-col-resize-e' )) {
				this.drag.edge = 'e';
                this.$feedback = $handle.find('.fl-block-col-resize-feedback-left');
			}
            if ($handle.hasClass( 'fl-block-col-resize-w' )) {
				this.drag.edge = 'w';
                this.$feedback = $handle.find('.fl-block-col-resize-feedback-right');
			}

	        body.addClass( 'fl-builder-row-resizing' );
			FLBuilder._colResizing = true;
			FLBuilder._destroyOverlayEvents();
			FLBuilder._closePanel();
        },

        /**
        * Handle drag
        * @var {Event}
        * @var {Object}
        * @return void
        */
        dragging: function(e, ui) {

            var currentPosition = ui.position.left,
                originalPosition = ui.originalPosition.left,
                originalWidth = this.drag.originalWidth,
                distance = 0,
                edge = this.drag.edge,
                minAllowedWidth = FLBuilderConfig.rowResize.minAllowedWidth,
                maxAllowedWidth = FLBuilderConfig.rowResize.maxAllowedWidth;

            if (originalPosition !== currentPosition) {

                if ( FLBuilderConfig.isRtl ) {
                    edge = ( 'w' == edge ) ? 'e' : 'w'; // Flip the direction
                }

                if (originalPosition > currentPosition) {
                    if (edge === 'w') {
                        this.drag.operation = '+';
                    } else {
                        this.drag.operation = '-';
                    }
                } else {
                    if (edge === 'e') {
                        this.drag.operation = '+';
                    } else {
                        this.drag.operation = '-';
                    }
                }

                distance = Math.abs(originalPosition - currentPosition);

                if (this.drag.operation === '+') {
                    this.drag.calculatedWidth = originalWidth + (distance * 2);
                } else {
                    this.drag.calculatedWidth = originalWidth - (distance * 2);
                }

                if ( false !== minAllowedWidth && this.drag.calculatedWidth < minAllowedWidth ) {
	                this.drag.calculatedWidth = minAllowedWidth;
                }

                if ( false !== maxAllowedWidth && this.drag.calculatedWidth > maxAllowedWidth ) {
	                this.drag.calculatedWidth = maxAllowedWidth;
                }

                if (this.row.isFixedWidth) {
                    this.$row.css('max-width', this.drag.calculatedWidth + 'px');
                }

                this.$rowContent.css('max-width', this.drag.calculatedWidth + 'px');

				if ( 'px' !== this.row.unit ) {
					this.drag.calculatedWidth = Math.round( this.drag.calculatedWidth / this.row.parentWidth * 100 );
				}

                if (!_.isUndefined(this.$feedback)) {
                    this.$feedback.html(this.drag.calculatedWidth + this.row.unit).show();
                }

                if ( this.row.form.length ) {
	                this.row.form.find( '[name=max_content_width]' ).val( this.drag.calculatedWidth );
                }

                // Dispatch update to store
				requestAnimationFrame( () => {
					const actions = FL.Builder.data.getLayoutActions()
					actions.resizeRowContent( this.row.node, this.drag.calculatedWidth, false )
				} )
            }
        },

        /**
        * Handle drag ended
        * @var {Event}
        * @var {Object}
        * @return void
        */
        dragStop: function(e, ui) {
            this.drag.isDragging = false;

            if (!_.isUndefined(this.$feedback)) {
                this.$feedback.hide();
            }

            // Dispatch update to store
            const actions = FL.Builder.data.getLayoutActions()
            actions.resizeRowContent( this.row.node, this.drag.calculatedWidth )

            FLBuilder._bindOverlayEvents();
            $( 'body' ).removeClass( 'fl-builder-row-resizing' );

            $( '.fl-block-overlay' ).each( function() {
	            FLBuilder._buildOverlayOverflowMenu( $( this ) );
            } );

            // Set the resizing flag to false with a timeout so other events get the right value.
			setTimeout( function() { FLBuilder._colResizing = false; }, 50 );

			FLBuilder.triggerHook( 'didResizeRow', {
				rowId	 : this.row.node,
				rowWidth : this.drag.calculatedWidth
			} );
        },
    };


    var Toolbar = {

        /**
        * wp.template id suffix
        */
        templateName: 'fl-toolbar',

        /**
        * Initialize the toolbar controller
        *
        * @return void
        */
        init: function() {
            this.template = wp.template(this.templateName);
            this.render();
            this.initTipTips();

            /* "Add Content" Button */
            var $addContentBtn = this.$el.find('.fl-builder-content-panel-button');
			$addContentBtn.on('click', FLBuilder._togglePanel );

            this.$el.find('.fl-builder-buy-button').on('click', FLBuilder._upgradeClicked);
			this.$el.find('.fl-builder-upgrade-button').on('click', FLBuilder._upgradeClicked);

            this.$el.find('#fl-builder-toggle-notifications').on('click', this.onNotificationsButtonClicked.bind(this) );

            FLBuilder.addHook('notificationsLoaded', this.onNotificationsLoaded.bind(this));
        },

        /**
        * Render the toolbar html
        * @param object
        * @return void
        */
        render: function(data) {
            var $html = $(this.template(data));
            this.$el = $html;
            this.el = $html.get(0);
            EditingUI.$mainToolbar = this.$el;
            $('body').prepend($html);
            $('html').addClass('fl-builder-is-showing-toolbar');
        },

        /**
        * Add tooltips
        *
        * @return void
        */
        initTipTips: function() {

            // Saving indicator tooltip
			$('.fl-builder--saving-indicator').tipTip({
				defaultPosition: 'bottom',
				edgeOffset: 14
			});

			// Publish actions tooltip
			$('.fl-builder-publish-actions .fl-builder-button-group .fl-builder-button').tipTip({
				defaultPosition: 'bottom',
				edgeOffset: 6
			});
        },

        onNotificationsButtonClicked: function() {
            FLBuilder.triggerHook('toggleNotifications');
        },

        onNotificationsLoaded: function() {
            $('body').removeClass('fl-builder-has-new-notifications');

            var data = {
	                action: 'fl_builder_notifications',
	                read: true,
	            }
            FLBuilder.ajax(data);
        }
    };

    /**
    * Kick off initializers when FLBuilder inits.
    */
    $(function() {

        // Render Order matters here
        FLBuilder.ContentPanel.init();

        if ( !FLBuilderConfig.simpleUi ) {
            FLBuilder.MainMenu.init();
        }

        if ( FLBuilderConfig.showToolbar ) {
            Toolbar.init();
            FLBuilder.ContentPanel.alignPanelArrow();
        } else {
            $('html').addClass('fl-builder-no-toolbar');
        }
        // End Render Order

        KeyShortcuts.init();
        EditingUI.init();
        BrowserState.init();
        RowResize.init();
        PublishActions.init();

        FLBuilder.triggerHook( 'didInitUI' );
    });

})(jQuery, FLBuilder);
