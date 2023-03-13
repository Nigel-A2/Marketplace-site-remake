( function( $ ) {

	/**
	 * Helper for rendering builder settings forms.
	 *
	 * @since 2.0
	 * @class FLBuilderSettingsForms
	 */
	FLBuilderSettingsForms = {

		/**
		 * Config for the current form that is rendering.
		 *
		 * @since 2.0
		 * @property {Object} config
		 */
		config : null,

		/**
		 * Settings cache for the current form so we can compare
		 * later and see if settings have changed before saving.
		 *
		 * @since 2.0
		 * @property {Object} settings
		 */
		settings : null,

		/**
		 * A reference to the AJAX object for rendering legacy settings.
		 *
		 * @since 2.0
		 * @property {Object} legacyXhr
		 */
		legacyXhr : null,

		/**
		 * @since 2.0
		 * @method init
		 */
		init: function() {
			this.bind();
		},

		/**
		 * @since 2.0
		 * @method bind
		 */
		bind: function() {
			FLBuilder.addHook( 'didDeleteRow', this.closeOnDeleteNode );
			FLBuilder.addHook( 'didDeleteColumn', this.closeOnDeleteNode );
			FLBuilder.addHook( 'didDeleteModule', this.closeOnDeleteNode );
		},

		/**
		 * Renders a settings form.
		 *
		 * @since 2.0
		 * @method render
		 * @param {Object} config
		 * @param {Function} callback
		 */
		render: function( config, callback ) {
			var forms    = FLBuilderSettingsConfig.forms,
				modules  = FLBuilderSettingsConfig.modules,
				defaults = {
					type      : 'general',
					id        : null,
					nodeId    : null,
					className : '',
					attrs     : '',
					title     : '',
					badges	  : [],
					tabs      : [],
					activeTab : null,
					buttons	  : [],
					settings  : {},
					legacy    : null,
					rules	    : null,
					preview   : null,
					helper 	  : null,
					messages  : null
				};

			// Load settings from the server if we have a node but no settings.
			if ( config.nodeId && ! config.settings ) {
				this.loadNodeSettings( config, callback );
				return;
			}

			// Merge the config into the defaults and make sure we have a callback.
			config   = $.extend( defaults, config );
			callback = undefined === callback ? function(){} : callback;

			// Add the form data to the config.
			if ( ! config.id ) {
				return;
			} else if ( 'general' === config.type && undefined !== forms[ config.id ] ) {
				config = $.extend( true, config, forms[ config.id ] );
			} else if ( 'module' === config.type && undefined !== modules[ config.id ] ) {
				config = $.extend( true, config, modules[ config.id ] );
			} else {
				return;
			}

			// Store the config so it can be accessed by forms.
			this.config = config;

			// Render the lightbox and form.
			if ( this.renderLightbox( config ) ) {

				// Finish rendering.
				if ( config.legacy || ! this.renderLegacySettings( config, callback ) ) {
					this.renderComplete( config, callback );
				} else {
					this.showLightboxLoader();
				}
			}

			// Clear any visible registered panels
			const panel = FL.Builder.data.getSystemState().currentPanel
			if ( null !== panel && 'outline' !== panel ) {
				const actions = FL.Builder.data.getSystemActions()
				actions.hideCurrentPanel()
			}
		},

		/**
		 * Cache current settings
		 *
		 * @method cacheCurrentSettings
		 */
		cacheCurrentSettings: function() {
			var form = $('.fl-builder-settings:visible');

			if (!form.closest('.fl-lightbox-wrap[data-parent]').length) {
				this.settings = FLBuilder._getSettingsForChangedCheck(this.config.nodeId, form);

				if (FLBuilder.preview) {
					FLBuilder.preview._savedSettings = this.settings;
				}
			}
		},

		/**
		 * Loads node settings for a form if they do not exist in
		 * the settings config cache.
		 *
		 * @since 2.1
		 * @method loadNodeSettings
		 * @param {Object} config
		 * @param {Function} callback
		 * @return {Boolean}
		 */
		loadNodeSettings: function( config, callback ) {
			FLBuilder.showAjaxLoader();
			FLBuilder.ajax( {
				action 	 : 'get_node_settings',
				node_id  : config.nodeId,
			}, function( response ) {
				config.settings = FLBuilder._jsonParse( response );

				FLBuilderSettingsConfig.nodes[ config.nodeId ] = config.settings;
				FLBuilderSettingsForms.render( config, callback );
				FLBuilder.hideAjaxLoader();
			} );
		},

		/**
		 * Renders the lightbox for a settings form.
		 *
		 * @since 2.0
		 * @method renderLightbox
		 * @param {Object} config
		 * @return {Boolean}
		 */
		renderLightbox: function( config ) {
			var template 	= wp.template( 'fl-builder-settings' ),
				form	 	= FLBuilder._lightbox._node.find( 'form.fl-builder-settings' ),
				nested   	= $( '.fl-lightbox-wrap[data-parent]' ),
				cachedTabId = localStorage.getItem( 'fl-builder-settings-tab' );

			// Don't render a node form if it's already open.
			if ( config.nodeId && config.nodeId === form.data( 'node' ) && ! config.lightbox ) {
				FLBuilder._focusFirstSettingsControl();
				return false;
			}

			if ( config.hide ) {
				return true;
			}

			// Set the active tab from local storage.
			if ( cachedTabId ) {
				for ( var tabId in config.tabs ) {
					if ( tabId === cachedTabId.replace( 'fl-builder-settings-tab-', '' ) ) {
						config.activeTab = tabId;
					}
				}
			}

			// Make sure we have an active tab.
			if ( ! config.activeTab ) {
				config.activeTab = Object.keys( config.tabs ).shift();
			}

			// Render the lightbox and form.
			if ( ! config.lightbox ) {

				// Save existing settings first if any exist. Don't proceed if it fails.
				if ( ! FLBuilder._triggerSettingsSave( true, true ) ) {
					return false;
				}

				// Cancel any preview refreshes.
				if ( FLBuilder.preview ) {
					FLBuilder.preview.cancel();
				}

				FLBuilder._closePanel();
				FLBuilder._showLightbox( template( config ) );
			} else {
				config.lightbox.setContent( template( config ) );
			}

			return true;
		},

		/**
		 * Initializes a form when rendering is complete.
		 *
		 * @since 2.0
		 * @method renderComplete
		 * @param {Object} config
		 * @param {Function} callback
		 */
		renderComplete: function( config, callback ) {
			// This is done on a timeout to keep it from delaying painting
			// of the settings form in the DOM by a fraction of a second.
			setTimeout( function() {
				if ( config.legacy ) {
					this.renderLegacySettingsComplete( config.legacy );
				}

				callback();

				FLBuilder._initSettingsForms();

				if ( config.rules ) {
					FLBuilder._initSettingsValidation( config.rules, config.messages );
				}
				if ( config.preview ) {
					FLBuilder.preview = new FLBuilderPreview( config.preview );
				}
				if ( config.helper ) {
					config.helper.init();
				}

				// Cache current settings.
				this.cacheCurrentSettings();

			}.bind( this ), 1 );
		},

		/**
		 * Renders the fields for a section in a settings form.
		 *
		 * @since 2.0
		 * @method renderFields
		 * @param {Object} fields
		 * @param {Object} settings
		 * @return {String}
		 */
		renderFields: function( fields, settings ) {
			var template         = wp.template( 'fl-builder-settings-row' ),
				html             = '',
				field            = null,
				name             = null,
				value 			 = null,
				isMultiple       = false,
				responsive		 = null,
				responsiveFields = [ 'align', 'border', 'dimension', 'unit', 'photo', 'select', 'typography' ],
				settings		 = ! settings ? this.config.settings : settings,
				globalSettings   = FLBuilderConfig.global;

			for ( name in fields ) {

				field 				= fields[ name ];
				isMultiple 		 	= field.multiple ? true : false;
				supportsResponsive 	= $.inArray( field['type'], responsiveFields ) > -1,
				value 			 	= ! _.isUndefined( settings[ name ] ) ? settings[ name ] : '';

				// Make sure this field has a type, if not the sky falls.
				if ( ! field.type ) {
					continue;
				}

				// Use a default value if not set in the settings.
				if ( _.isUndefined( settings[ name ] ) && field['default'] ) {
					value = field['default'];
				}

				// Check to see if responsive is enabled for this field.
				if ( field['responsive'] && globalSettings.responsive_enabled && ! isMultiple && supportsResponsive ) {
					responsive = field['responsive'];
				} else {
					responsive = null;
				}

				html += template( {
					field    		 : field,
					name 			 : name,
					rootName		 : name,
					value 			 : value,
					preview			 : JSON.stringify( field['preview'] ? field['preview'] : { type: 'refresh' } ),
					responsive		 : responsive,
					rowClass		 : field['row_class'] ? ' ' + field['row_class'] : '',
					isMultiple     	 : isMultiple,
					supportsMultiple : 'editor' !== field.type && 'service' !== field.type,
					settings 		 : settings,
					globalSettings   : globalSettings,
					template		 : $( '#tmpl-fl-builder-field-' + field.type )
				} );
			}

			return html;
		},

		/**
		 * Renders a single field for a settings form.
		 *
		 * @since 2.0
		 * @method renderField
		 * @param {Object} config
		 * @return {String}
		 */
		renderField: function( config ) {
			var template = wp.template( 'fl-builder-field' );
			return template( config );
		},

		/**
		 * Renders a custom template for a section.
		 *
		 * @since 2.0
		 * @method renderSectionTemplate
		 * @param {Object} section
		 * @param {Object} settings
		 * @return {String}
		 */
		renderSectionTemplate: function( section, settings ) {
			var template = wp.template( section.template.id );

			return template( {
				section  : section,
				settings : settings
			} );
		},

		/**
		 * Renders a custom template for a tab.
		 *
		 * @since 2.0
		 * @method renderTabTemplate
		 * @param {Object} tab
		 * @param {Object} settings
		 * @return {String}
		 */
		renderTabTemplate: function( tab, settings ) {
			var template = wp.template( tab.template.id );

			return template( {
				tab  	 : tab,
				settings : settings
			} );
		},

		/**
		 * Renders any legacy custom fields that need to be
		 * rendered on the server with PHP.
		 *
		 * @since 2.0
		 * @method renderLegacyField
		 * @param {Object} config
		 * @param {Function} callback
		 * @return {Boolean}
		 */
		renderLegacySettings: function( config, callback ) {
			var form     = $( '.fl-builder-settings:visible' ),
				name     = null,
				ele      = null,
				render   = false,
				data 	 = {
					'tabs' 		: [],
					'sections' 	: [],
					'fields' 	: [],
					'settings'	: null,
					'node_id'	: null
				};

			// Fields
			form.find( '.fl-legacy-field' ).each( function() {
				ele = $( this );
				data.fields.push( ele.attr( 'data-field' ) );
				FLBuilderSettingsForms.showFieldLoader( ele );
				render = true;
			} );

			// Sections
			form.find( '.fl-legacy-settings-section' ).each( function() {
				ele = $( this );
				data.sections.push( { tab: ele.attr( 'data-tab' ), section: ele.attr( 'data-section' ) } );
				render = true;
			} );

			// Tabs
			form.find( '.fl-legacy-settings-tab' ).each( function() {
				ele = $( this );
				data.tabs.push( ele.attr( 'data-tab' ) );
				render = true;
			} );

			// Send a node ID if we have it, otherwise, send the settings.
			if ( form.attr( 'data-node' ) ) {
				data.node_id = form.attr( 'data-node' );
			} else {
				data.settings = FLBuilder._getOriginalSettings( form, true );
			}

			// Cancel an existing legacy AJAX request if we have one.
			if ( this.legacyXhr ) {
				this.legacyXhr.abort();
				this.legacyXhr = null;
			}

			// We still fire the AJAX request even if we don't need to render new
			// tabs, sections or fields just in case any field extras need to render.
			this.legacyXhr = FLBuilder.ajax( $.extend( this.getLegacyVars(), {
				action 	 : 'render_legacy_settings',
				data   	 : data,
				form     : form.attr( 'data-form-id' ),
				group    : form.attr( 'data-form-group' ),
				lightbox : form.closest( '.fl-builder-lightbox' ).attr( 'data-instance-id' )
			} ), function( response ) {
				FLBuilderSettingsForms.renderLegacySettingsComplete( response );
				if ( render ) {
					FLBuilderSettingsForms.renderComplete( config, callback );
				}
				FLBuilderSettingsForms.hideLightboxLoader();
			} );

			return render;
		},

		/**
		 * Callback for when legacy settings are done rendering.
		 *
		 * @since 2.0
		 * @method renderLegacySettingsComplete
		 * @param {String} response
		 */
		renderLegacySettingsComplete: function( response ) {
			var data 	 = 'object' === typeof response ? response : FLBuilder._jsonParse( response ),
				lightbox = null,
				form  	 = null,
				name 	 = '',
				field    = null,
				section  = null,
				tab      = null,
				settings = null;

			// Get the form object.
			if ( data.lightbox ) {
				lightbox = $( '.fl-builder-lightbox[data-instance-id=' + data.lightbox + ']' );
				form = lightbox.length ? lightbox.find( '.fl-builder-settings' ) : null;
			} else {
				form = $( '.fl-builder-settings:visible' );
				lightbox = form.closest( '.fl-builder-lightbox' );
			}

			// Bail if the form no longer exists.
			if ( ! form || ! form.length ) {
				return;
			}

			// Fields
			for ( name in data.fields ) {
				field = $( '#fl-field-' + name ).attr( 'id', '' );
				field.after( data.fields[ name ] ).remove();
			}

			// Field extras
			for ( name in data.extras ) {
				field = $( '#fl-field-' + name ).find( '.fl-field-control-wrapper' );
				if ( data.extras[ name ].multiple ) {
					field.each( function( i, field_item ) {
						if ( ( i in data.extras[ name ].before ) && ( data.extras[ name ].before[ i ] != "" ) ) {
							$( this ).prepend(
								'<div class="fl-form-field-before">' +
								data.extras[ name ].before[ i ] +
								'</div>'
							);
						}
						if ( ( i in data.extras[ name ].after ) && ( data.extras[ name ].after[ i ] != "" ) ) {
							$( this ).append(
								'<div class="fl-form-field-after">' +
								data.extras[name].after[ i ] +
								'</div>'
							);
						}
					});
				} else {
					if ( data.extras[ name ].before != "" ) {
						field.prepend(
							'<div class="fl-form-field-before">' +
							data.extras[name].before +
							'</div>'
						);
					}
					if ( data.extras[ name ].after != "" ) {
						field.append(
							'<div class="fl-form-field-after">' +
							data.extras[name].after +
							'</div>'
						);
					}
				}
			}

			// Sections
			for ( tab in data.sections ) {
				for ( name in data.sections[ tab ] ) {
					section = $( '#fl-builder-settings-section-' + name );
					section.html( data.sections[ tab ][ name ] );
				}
			}

			// Tabs
			for ( name in data.tabs ) {
				tab = $( '#fl-builder-settings-tab-' + name );
				tab.html( data.tabs[ name ] );
			}

			// Refresh cached settings only if it's the main form.
			if ( ! lightbox.data( 'parent' ) ) {
				this.settings = FLBuilder._getSettingsForChangedCheck( this.config.nodeId, form );

				if ( FLBuilder.preview ) {
					this.settings = $.extend( this.settings, FLBuilder.preview._savedSettings );
					FLBuilder.preview._savedSettings = this.settings;
				}
			}

			// Support for Themer before it supported JS fields. This can be removed in a future version.
			if ( ! _.isUndefined( window.FLThemeBuilderFieldConnections ) ) {
				FLThemeBuilderFieldConnections._initSettingsForms();
			}

			// Clear the legacy AJAX object.
			this.legacyXhr = null;
		},

		/**
		 * Returns legacy variables that were sent in AJAX requests
		 * when a nested settings form was rendered.
		 *
		 * @since 2.0
		 * @method getLegacyVars
		 * @return {Object}
		 */
		getLegacyVars: function() {
			var form     = $( '.fl-builder-settings:visible' ),
				lightbox = form.closest( '.fl-builder-lightbox' ),
				parent   = lightbox.attr( 'data-parent' ),
				settings = null,
				nodeId   = null,
				vars     = {};

			if ( parent ) {
				parent   = $( '.fl-builder-lightbox[data-instance-id=' + parent + ']' );
				form     = parent.find( 'form.fl-builder-settings' );
				settings = FLBuilder._getSettings( form );
				nodeId   = form.attr( 'data-node' );

				if ( nodeId ) {
					vars.node_id       = nodeId;
					vars.node_settings = settings;
				}
			}

			return vars;
		},

		/**
		 * Checks to see if the main form settings has changed.
		 *
		 * @since 2.0
		 * @method settingsHaveChanged
		 * @return {Boolean}
		 */
		settingsHaveChanged: function()
		{
			var form 	 = FLBuilder._lightbox._node.find( 'form.fl-builder-settings' ),
				settings = FLBuilder._getSettings( form ),
				result   = ! this.settings ? false :  JSON.stringify( this.settings ) != JSON.stringify( settings );

			return result;
		},

		/**
		 * Closes the settings lightbox when an associated node is deleted.
		 *
		 * @since 2.0
		 * @method closeOnDeleteNode
		 * @param {Object} e
		 */
		closeOnDeleteNode: function( e )
		{
			var settings = $( '.fl-builder-settings[data-node]' ),
				selector = FLBuilder._contentClass + ' .fl-node-' + settings.data( 'node' );

			if ( settings.length && ! $( selector ).length ) {
				FLLightbox.closeAll();
			}
		},

		/**
		 * Shows the loader for the current lightbox that is visible.
		 *
		 * @since 2.0
		 * @method showLightboxLoader
		 */
		showLightboxLoader: function() {
			$( '.fl-builder-settings:visible' ).append( '<div class="fl-builder-loading"></div>' );
		},

		/**
		 * Hides the loader for the current lightbox that is visible.
		 *
		 * @since 2.0
		 * @method hideLightboxLoader
		 */
		hideLightboxLoader: function( ele ) {
			$( '.fl-builder-settings:visible .fl-builder-loading' ).remove();
		},

		/**
		 * Shows the loader for a field that is loading.
		 *
		 * @since 2.0
		 * @method showFieldLoader
		 * @param {Object} ele
		 */
		showFieldLoader: function( ele ) {
			var wrapper = ele.closest( '.fl-field-control' ).find( '.fl-field-control-wrapper' );
			wrapper.hide().after( '<div class="fl-field-loader">' + FLBuilderStrings.fieldLoading + '</div>' );
		},

		/**
		 * Hides the loader for a field that is loading.
		 *
		 * @since 2.0
		 * @method hideFieldLoader
		 * @param {Object} ele
		 */
		hideFieldLoader: function( ele ) {
			var field   = ele.closest( '.fl-field' ),
				wrapper = ele.closest( '.fl-field-control' ).find( '.fl-field-control-wrapper' );

			wrapper.show();
			field.find( '.fl-field-loader' ).remove();
		}
	};

	/**
	 * Helper for working with settings forms config.
	 *
	 * @since 2.0
	 * @class FLBuilderSettingsConfig
	 */
	FLBuilderSettingsConfig = 'undefined' === typeof FLBuilderSettingsConfig ? {} : FLBuilderSettingsConfig;

	$.extend( FLBuilderSettingsConfig, {

		/**
		 * @since 2.0
		 * @method init
		 */
		init: function() {

			// Save settings
			FLBuilder.addHook( 'didSaveNodeSettings', this.updateOnNodeEvent.bind( this ) );
			FLBuilder.addHook( 'didSaveNodeSettingsComplete', this.updateOnNodeEvent.bind( this ) );
			FLBuilder.addHook( 'didSaveLayoutSettingsComplete', this.updateOnSaveLayoutSettings.bind( this ) );
			FLBuilder.addHook( 'didSaveGlobalSettingsComplete', this.updateOnSaveGlobalSettings.bind( this ) );
			FLBuilder.addHook( 'didSaveGlobalSettingsComplete', this.reload );

			// Add nodes
			FLBuilder.addHook( 'didAddRow', this.updateOnNodeEvent.bind( this ) );
			FLBuilder.addHook( 'didAddColumnGroup', this.updateOnNodeEvent.bind( this ) );
			FLBuilder.addHook( 'didAddColumn', this.updateOnNodeEvent.bind( this ) );
			FLBuilder.addHook( 'didAddModule', this.updateOnNodeEvent.bind( this ) );

			// Delete nodes
			FLBuilder.addHook( 'didDeleteRow', this.updateOnNodeEvent.bind( this ) );
			FLBuilder.addHook( 'didDeleteColumn', this.updateOnNodeEvent.bind( this ) );
			FLBuilder.addHook( 'didDeleteModule', this.updateOnNodeEvent.bind( this ) );

			// Duplicate nodes
			FLBuilder.addHook( 'didDuplicateRow', this.updateOnNodeEvent.bind( this ) );
			FLBuilder.addHook( 'didDuplicateColumn', this.updateOnNodeEvent.bind( this ) );
			FLBuilder.addHook( 'didDuplicateModule', this.updateOnNodeEvent.bind( this ) );

			// Resize nodes
			FLBuilder.addHook( 'didResizeRow', this.updateOnRowResize.bind( this ) );
			FLBuilder.addHook( 'didResizeColumn', this.updateOnColumnResize.bind( this ) );

			// Reset node widths
			FLBuilder.addHook( 'didResetRowWidth', this.updateOnResetRowWidth.bind( this ) );
			FLBuilder.addHook( 'didResetColumnWidths', this.updateOnResetColumnWidths.bind( this ) );

			// Apply templates
			FLBuilder.addHook( 'didApplyTemplateComplete', this.updateOnApplyTemplate.bind( this ) );
			FLBuilder.addHook( 'didApplyRowTemplateComplete', this.updateOnApplyTemplate.bind( this ) );
			FLBuilder.addHook( 'didApplyColTemplateComplete', this.updateOnApplyTemplate.bind( this ) );
			FLBuilder.addHook( 'didSaveGlobalNodeTemplate', this.updateOnApplyTemplate.bind( this ) );

			// Revisions and history
			FLBuilder.addHook( 'didRestoreRevisionComplete', this.updateOnApplyTemplate.bind( this ) );
			FLBuilder.addHook( 'didRestoreHistoryComplete', this.updateOnHistoryRestored.bind( this ) );
		},

		/**
		 * Reloads the core settings config from the server.
		 *
		 * @since 2.2.2
		 * @method reload
		 */
		reload: function() {
			var url = FLBuilderConfig.editUrl + '&fl_builder_load_settings_config=core';

			$( 'script[src*="fl_builder_load_settings_config=core"]' ).remove();
			$( 'head' ).append( '<script src="' + url + '"></script>' );
		},

		/**
		 * Updates the global settings when they are saved.
		 *
		 * @since 2.0
		 * @method updateOnSaveGlobalSettings
		 * @param {Object} e
		 * @param {Object} settings
		 */
		updateOnSaveGlobalSettings: function( e, settings ) {
			this.settings.global = settings;
			FLBuilderConfig.global = settings;
		},

		/**
		 * Updates the layout settings when they are saved.
		 *
		 * @since 2.0
		 * @method updateOnSaveLayoutSettings
		 * @param {Object} e
		 * @param {Object} settings
		 */
		updateOnSaveLayoutSettings: function( e, settings ) {
			this.settings.layout = settings;
		},

		/**
		 * Updates the node config when an event is triggered.
		 *
		 * @since 2.0
		 * @method updateOnNodeEvent
		 */
		updateOnNodeEvent: function() {

			var event = arguments[0];

			if ( event.namespace.indexOf( 'didAdd' ) > -1 ) {
				this.addNode( 'object' === typeof arguments[1] ? arguments[1].nodeId : arguments[1] );
			} else if ( event.namespace.indexOf( 'didSaveNodeSettings' ) > -1 ) {
				this.updateNode( arguments[1].nodeId, arguments[1].settings );
			} else if ( event.namespace.indexOf( 'didDelete' ) > -1 ) {
				this.deleteNodes( 'object' === typeof arguments[1] ? arguments[1].nodeId : arguments[1] );
			} else if ( event.namespace.indexOf( 'didDuplicate' ) > -1 ) {
				this.duplicateNode( arguments[1].oldNodeId, arguments[1].newNodeId );
			}
		},

		/**
		 * Updates the node config when a row is resized.
		 *
		 * @since 2.0
		 * @method updateOnRowResize
		 * @param {Object} e
		 * @param {Object} data
		 */
		updateOnRowResize: function( e, data ) {
			this.nodes[ data.rowId ].max_content_width = data.rowWidth;
		},

		/**
		 * Updates the node config when a row width is reset.
		 *
		 * @since 2.0
		 * @method updateOnResetRowWidth
		 * @param {Object} e
		 * @param {String} nodeId
		 */
		updateOnResetRowWidth: function( e, nodeId ) {
			this.nodes[ nodeId ].max_content_width = '';
		},

		/**
		 * Updates the node config when a column is resized.
		 *
		 * @since 2.0
		 * @method updateOnColumnResize
		 * @param {Object} e
		 * @param {Object} data
		 */
		updateOnColumnResize: function( e, data ) {
			this.nodes[ data.colId ].size = data.colWidth;
			this.nodes[ data.siblingId ].size = data.siblingWidth;
		},

		/**
		 * Updates the node config when column widths are reset.
		 *
		 * @since 2.0
		 * @method updateOnResetColumnWidths
		 * @param {Object} e
		 * @param {Object} data
		 */
		updateOnResetColumnWidths: function( e, data ) {
			var self = this;

			data.cols.each( function() {
				var col   = $( this ),
					colId = col.attr( 'data-node' );

				if ( self.nodes[ colId ] ) {
					self.nodes[ colId ].size = parseFloat( col[0].style.width );
				}
			} );
		},

		/**
		 * Updates the node config when a template is applied.
		 *
		 * @since 2.0
		 * @method updateOnApplyTemplate
		 * @param {Object} e
		 * @param {Object} config
		 */
		updateOnApplyTemplate: function( e, config ) {
			this.nodes = config.nodes;
			this.attachments = config.attachments;
		},

		/**
		 * Updates the node config when a history state is rendered.
		 *
		 * @since 2.0
		 * @method updateOnHistoryRestored
		 * @param {Object} e
		 * @param {Object} data
		 */
		updateOnHistoryRestored: function( e, data ) {
			this.nodes = data.config.nodes
			this.attachments = data.config.attachments
			this.settings.layout = data.settings.layout
			this.settings.global = data.settings.global
			FLBuilderConfig.global = data.settings.global
		},

		/**
		 * Adds the settings config for a new node.
		 *
		 * @since 2.0
		 * @method addNode
		 * @param {String} nodeId
		 * @param {Object} settings
		 */
		addNode: function( nodeId, settings ) {

			var node 		= $( '.fl-node-' + nodeId ),
				isRow 		= node.hasClass( 'fl-row' ),
				isCol 		= node.hasClass( 'fl-col' ),
				isColGroup 	= node.hasClass( 'fl-col-group' ),
				isModule 	= node.hasClass( 'fl-module' ),
				self 		= this;

			if ( this.nodes[ nodeId ] ) {
				return;
			}

			if ( ! settings ) {

				if ( isRow ) {
					settings = $.extend( {}, this.defaults.row );
				} else if ( isCol ) {
					settings = $.extend( {}, this.defaults.column );
				} else if ( isModule ) {
					settings = $.extend( {}, this.defaults.modules[ node.attr( 'data-type' ) ] );
				}

				if ( isRow || isColGroup ) {
					node.find( '.fl-col' ).each( function() {
						var col = $( this ), defaults = $.extend( {}, self.defaults.column );
						defaults.size = parseFloat( col[0].style.width );
						self.addNode( col.attr( 'data-node' ), defaults );
					} );
				} else if ( isModule ) {
					self.addNode( node.closest( '.fl-row' ).attr( 'data-node' ) );
					self.addNode( node.closest( '.fl-col' ).attr( 'data-node' ) );
					self.updateOnResetColumnWidths( null, {
						cols: node.closest( '.fl-col-group' ).find( '> .fl-col' )
					} );
				}
			}

			if ( settings ) {
				this.nodes[ nodeId ] = settings;
			}
		},

		/**
		 * Update the settings config for a node.
		 *
		 * @since 2.0
		 * @method updateNode
		 * @param {String} nodeId
		 * @param {Object} settings
		 */
		updateNode: function( nodeId, settings ) {
			var node  = $( '.fl-node-' + nodeId ),
				self  = this;

			if ( node.hasClass( 'fl-col' ) ) {
				node.closest( '.fl-col-group' ).find( '> .fl-col' ).each( function() {
					var col = $( this ), colId = col.attr( 'data-node' );
					self.nodes[ colId ].size 				= parseFloat( col[0].style.width );
					self.nodes[ colId ].equal_height 		= settings.equal_height;
					self.nodes[ colId ].content_alignment 	= settings.content_alignment;
					self.nodes[ colId ].responsive_order 	= settings.responsive_order;
				} );
			}

			this.nodes[ nodeId ] = settings;
		},

		/**
		 * Duplicates settings config for a node.
		 *
		 * @since 2.0
		 * @method duplicateNode
		 * @param {String} oldNode
		 * @param {String} newNode
		 */
		duplicateNode: function( oldNodeId, newNodeId ) {

			var newNode  = $( '.fl-node-' + newNodeId ),
				newNodes = newNode.find( '[data-node]' ),
				oldNode  = $( '.fl-node-' + oldNodeId ),
				oldNodes = oldNode.find( '[data-node]' ),
				self     = this;

			this.nodes[ newNodeId ] = this.nodes[ oldNodeId ];

			newNodes.each( function( i ) {

				oldNodeId = oldNodes.eq( i ).attr( 'data-node' );
				newNodeId = $( this ).attr( 'data-node' );

				if ( self.nodes[ oldNodeId ] ) {
					self.nodes[ newNodeId ] = self.nodes[ oldNodeId ];
				}
			} );
		},

		/**
		 * Deletes any nodes that are no longer in the DOM.
		 *
		 * @since 2.0
		 * @method deleteNodes
		 */
		deleteNodes: function() {

			var nodeId  = '',
				content = $( FLBuilder._contentClass ).html();

			if ( ! content ) {
				return
			}

			for ( nodeId in this.nodes ) {
				if ( content.indexOf( nodeId ) === -1 ) {
					this.nodes[ nodeId ] = null;
					delete this.nodes[ nodeId ];
				}
			}
		}
	} );

	$( function() {
		FLBuilderSettingsConfig.init();
		FLBuilderSettingsForms.init();
	} );

} )( jQuery );
