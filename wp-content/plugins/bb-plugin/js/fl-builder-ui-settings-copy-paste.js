( function( $ ) {

	FLBuilderSettingsCopyPaste = {

		init: function() {
			FLBuilder.addHook( 'settings-form-init', this.initExportButton );
			FLBuilder.addHook( 'settings-form-init', this.initImportButton );
		},

		initExportButton: function() {

			new ClipboardJS( 'button.module-export-all', {
				text: function( trigger ) {
					var nodeId    = $( '.fl-builder-module-settings' ).data( 'node' ),
						form      = $( '.fl-builder-module-settings[data-node=' + nodeId + ']' ),
						type      = $( '.fl-builder-module-settings' ).data( 'type' ),
						settings  = FLBuilder._getSettings( form ),
						d         = new Date(),
						date      = d.toDateString(),
						wrap      = '/// {type:' + type + '} ' + date + ' ///',
						btn		  = $( 'button.module-export-all' ),
						btnText	  = btn.attr( 'title' );

					btn.text( FLBuilderStrings.module_import.copied );
					setTimeout( function() { btn.text( btnText ) }, 1000 );

					return wrap + "\n" + JSON.stringify( settings );
				}
			});

			new ClipboardJS( 'button.module-export-style', {
				text: function( trigger ) {
					var nodeId    = $( '.fl-builder-module-settings' ).data( 'node' ),
						form      = $( '.fl-builder-module-settings[data-node=' + nodeId + ']' ),
						type      = $( '.fl-builder-module-settings' ).data( 'type' ),
						settings  = FLBuilder._getSettings( form ),
						d         = new Date(),
						date      = d.toDateString(),
						wrap      = '/// {type:' + type + '} ' + date + ' ///',
						btn		  = $( 'button.module-export-style' ),
						btnText	  = btn.attr( 'title' ),
						styles	  = {};

					for ( var key in settings ) {
						var singleInput = form.find( '[name="' + key + '"]' ),
							arrayInput = form.find( '[name*="' + key + '["]' ),
							isStyle = false;

						if ( singleInput.length ) {
							isStyle = singleInput.closest( '.fl-field' ).data( 'is-style' );
						} else if ( arrayInput.length ) {
							isStyle = arrayInput.closest( '.fl-field' ).data( 'is-style' );
						}

						if ( isStyle ) {
							styles[ key ] = settings[ key ];
						}
					}

					btn.text( FLBuilderStrings.module_import.copied );
					setTimeout( function() { btn.text( btnText ) }, 1000 );

					return wrap + "\n" + JSON.stringify( styles );
				}
			});
		},

		initImportButton: function() {

			$( 'button.module-import-apply' ).click( function() {
				var form        = $( '.fl-builder-settings-lightbox .fl-builder-settings' ),
					data        = $( '.module-import-input' ).val(),
					t           = data.match( /\/\/\/\s\{type:([_a-z0-9-]+)/i ),
					type        = false,
					moduleType  = $( '.fl-builder-module-settings' ).data( 'type' ),
					errorDiv    = $( '.fl-builder-settings-lightbox .module-import-error' );

				errorDiv.hide();

				if( t && typeof t[1] !== 'undefined' ) {
					type = t[1];
				}

				if ( type && type === moduleType ) {
					var cleandata = data.replace( /\/\/\/.+\/\/\//, '' );
					try {
						var importedSettings = JSON.parse( cleandata );
					} catch ( err ) {
						var importedSettings = false;
						errorDiv.html( FLBuilderStrings.module_import.error ).show();
						return false;
					}
				} else {
					errorDiv.html( FLBuilderStrings.module_import.type ).show();
					return false;
				}

				if ( importedSettings ) {
					var nodeId = form.attr( 'data-node' );

					var merged = $.extend( {}, FLBuilderSettingsConfig.nodes[ nodeId ], importedSettings );

					FLBuilderSettingsConfig.nodes[ nodeId ] = merged;

					// Dispatch to store
					const actions = FL.Builder.data.getLayoutActions()
					const callback = FLBuilder._saveSettingsComplete.bind( this, true, null )
					actions.updateNodeSettings( nodeId, merged, callback )

					FLBuilder.triggerHook( 'didSaveNodeSettings', {
						nodeId   : nodeId,
						settings : merged
					} );

					FLBuilder._lightbox.close();
				}
			});
		},
	};

	$( function() {
		FLBuilderSettingsCopyPaste.init();
	} );

} )( jQuery );
