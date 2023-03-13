(function($, FLBuilder) {

    /**
    * Save Manager Object
    */
    var SaveManager = {

        /**
         * Indicates whether or not the current layout is clean or has changes that require
         * publish actions.
         *
         * @var bool
         */
        layoutNeedsPublish: false,

        /**
         * The message that is displayed whenever `resetStatusMessage()` is called.
         *
         * @var string
         */
        defaultIndicatorMessage: "",

        /**
         * The tooltip that is displayed whenever 'resetStatusMessage()' is called.
         *
         * @var string
         */
        defaultTooltipMessage: "",

        /**
         * Local reference to strings pertaining to saving states.
         *
         * @var object
         */
        messages: null,

        /**
         * Setup the Save Manager object.
         *
         * @return void
         */
        init: function() {
	        this.messages = FLBuilderStrings.savedStatus;
            this.$savingIndicator = $('.fl-builder--saving-indicator');

            FLBuilder.addHook('didBeginAJAX', this.onLayoutSaving.bind(this));
            FLBuilder.addHook('didCompleteAJAX', this.onLayoutSaved.bind(this));
            FLBuilder.addHook('didPublishLayout', this.onLayoutPublished.bind(this));
            FLBuilder.addHook('publishAndRemain', this.onPublishAndRemain.bind(this));

            // We have to set the layout as needing publish when settings are opened because
            // we can't yet reliably set it to needing publish when settings are changed.
            FLBuilder.addHook('didShowLightbox', this.setLayoutNeedsPublish.bind(this));

            if ( FLBuilderConfig.layoutHasDraftedChanges || ! FLBuilderConfig.builderEnabled ) {    
                this.setLayoutNeedsPublish();
                this.resetStatusMessage();
            }
        },

        /**
         * Flag the layout as needing to be published.
         *
         * @return void
         */
        setLayoutNeedsPublish: function() {
            if ( !this.layoutNeedsPublish ) {
                this.layoutNeedsPublish = true;
                $('body').addClass('fl-builder--layout-has-drafted-changes');
            }
        },

        /**
         * Fires when layout begins saving.
         *
         * @return void
         */
        onLayoutSaving: function(e, data) {;

            if ( this.isPublishingLayout( data.action ) ) {
                this.showStatusMessage( this.messages.publishing, this.messages.publishingTooltip );
            }
            else if ( this.isUpdatingLayout( data.action ) ) {
                this.setLayoutNeedsPublish();
                this.showStatusMessage( this.messages.saving, this.messages.savingTooltip );
            }
        },

        /**
         * Check if the current ajax action is publishing the layout
         *
         * @var String action
         * @return bool
         */
        isPublishingLayout: function( action ) {

            if ( 'save_layout' == action ) {
                return true;
            }
            return false;
        },

        /**
         * Checks if the current ajax action is updating the layout or some other part of the system.
         *
         * @var String action
         * @return bool
         */
        isUpdatingLayout: function( action ) {

            if ( this.isPublishingLayout() ) return false;

            if ( action.startsWith('render') ) {
                if ( action.startsWith('render_new') ) return true;
                return false;
            }
            if ( action.startsWith('duplicate') ) return false;
            if ( action.startsWith('refresh') ) return false; // Like refresh_revision_items
            if ( 'save_ui_skin' == action ) return false;
			if ( 'save_lightbox_position' == action ) return false;
            if ( 'save_pinned_ui_position' == action ) return false;
            if ( 'fl_builder_notifications' == action ) return false;
            if ( action.indexOf( 'history' ) > -1 ) return false; // HistoryManager

            return true;
        },

        /**
         * Fires after layout has been successfully saved.
         * Display the "Saved" message, wait a bit and reset to the default message.
         *
         * @return void
         */
        onLayoutSaved: function( e, data ) {
	        if ( this.isUpdatingLayout( data.fl_builder_data.action ) ) {
	            this.showStatusMessage(this.messages.saved, this.messages.savedTooltip);

	            var obj = this;
	            setTimeout(function() {
	                obj.resetStatusMessage();
	            }, 2000);
	        }
        },

        /**
         * Handle layout published
         *
         * @return void
         */
        onLayoutPublished: function() {
            this.layoutNeedsPublish = false;
            $('body').removeClass('fl-builder--layout-has-drafted-changes');
            this.resetStatusMessage();
        },

        /**
         * Set the status message area to a string of text.
         *
         * @var string Message to display
         * @return void
         */
        showStatusMessage: function(message, toolTip) {
            this.$savingIndicator.html(message);
            if (! FLBuilder.isUndefined(toolTip)) {
                this.$savingIndicator.attr('title', toolTip);
                $('.fl-builder--saving-indicator').tipTip({
					defaultPosition: 'bottom',
					edgeOffset: 14
				});
            }
        },

        /**
         * Set the status message back to it's default state.
         *
         * @return void
         */
        resetStatusMessage: function() {
            if(this.layoutNeedsPublish) {
                this.defaultIndicatorMessage = this.messages.edited + '<i class="fas fa-question-circle"></i>';
                this.defaultTooltipMessage = this.messages.editedTooltip;
            } else {
                this.defaultIndicatorMessage = "";
                this.defaultTooltipMessage = "";
            }
            this.showStatusMessage(this.defaultIndicatorMessage, this.defaultTooltipMessage );
        },

        /**
         * Handle publish key command
         *
         * @return void
         */
        onPublishAndRemain: function() {
			FLBuilder.MainMenu.hide();
            if (this.layoutNeedsPublish || FLBuilderSettingsForms.settingsHaveChanged()) {
                FLBuilder._publishLayout(false, true);
            } else {
                this.showStatusMessage(this.messages.noChanges);

                var manager = this;
                setTimeout(function() {
                    manager.resetStatusMessage();
                }, 2000);
            }
        }
    };

    /**
    * Pubic Interface
    */
    FLBuilder.SaveManager = {

        /**
         * Check if the current layout has unpublished changes
         *
         * @return bool
         */
        layoutNeedsPublish: function() {
            return SaveManager.layoutNeedsPublish;
        },

        /**
         * Show a status message
         *
         * @var String message
         * @var String toolTip
         * @return void
         */
        showStatusMessage: function(message, toolTip) {
            SaveManager.showStatusMessage(message, toolTip);
        },

        /**
         * Reset status message to contextual default
         *
         * @return void
         */
        resetStatusMessage: function() {
            SaveManager.resetStatusMessage();
        }
    }

    /**
    * Kick off init.
    */
    $(function() {
        SaveManager.init();
    });

})(jQuery, FLBuilder);
