( function( $ ) {

	FLBuilder.addHook( 'didRenderLayoutJSComplete', function() {
		FLBuilder._moduleHelpers.menu._previewSubmenu();
	} );

	FLBuilder.addHook( 'didHideAllLightboxes', function() {
		FLBuilder._moduleHelpers.menu._closeSubmenuPreview();
		FLBuilder._moduleHelpers.menu._closeSearchFormPreview();
	} );

	FLBuilder.addHook( 'didShowLightbox', function() {
		FLBuilder._moduleHelpers.menu._closeSubmenuPreview();
		FLBuilder._moduleHelpers.menu._closeSearchFormPreview();
	} );

	FLBuilder.registerModuleHelper( 'menu', {

		init: function() {
			var form = $( '.fl-builder-menu-settings:visible' ),
			    submenuLinkColor = form.find( 'input[name=submenu_link_color]' ),
				submenuBg = form.find( 'input[name=submenu_bg_color]' ),
				submenuShadow = form.find( 'select[name=drop_shadow]' ),
				submenuSpacing = form.find( '#fl-field-submenu_spacing input' ),
				submenuLinkSpacing = form.find( '#fl-field-submenu_link_spacing input' );
				submenuBorderSelect = form.find( '#fl-field-submenu_border select' );
				submenuBorderInput = form.find( '#fl-field-submenu_border input' );
				submenuTypographySelect = form.find( '#fl-field-submenu_typography select' );
				submenuTypographyInput = form.find( '#fl-field-submenu_typography input' );
				searchStyleSelect = form.find( '#fl-builder-settings-section-search_style select' );
				searchStyleInput = form.find( '#fl-builder-settings-section-search_style input' );
				layout = form.find( 'select[name=menu_layout]' );
				mobileToggle = form.find( 'select[name=mobile_toggle]' );

			submenuLinkColor.on( 'change', this._previewSubmenu );
			submenuBg.on( 'change', this._previewSubmenu );
			submenuShadow.on( 'change', this._previewSubmenu );
			submenuSpacing.on( 'input', this._previewSubmenu );
			submenuLinkSpacing.on( 'input', this._previewSubmenu );
			submenuBorderSelect.on( 'change', this._previewSubmenu );
			submenuBorderInput.on( 'input', this._previewSubmenu );
			submenuTypographySelect.on( 'change', this._previewSubmenu );
			submenuTypographyInput.on( 'input', this._previewSubmenu );
			searchStyleSelect.on( 'change', this._previewSearchForm );
			searchStyleInput.on( 'input change', this._previewSearchForm );
			layout.on( 'change', this._mobileToggle );
			mobileToggle.on( 'change', this._mobileToggle );
			this._mobileToggle();
			this._removeWooSections();
		},

		_previewSubmenu: function() {
			var form = $( '.fl-builder-menu-settings:visible' );
			var preview = FLBuilder.preview;

			if ( ! form.length || ! preview || ! preview.elements.node ) {
				return;
			}

			var node = preview.elements.node;

			if ( node.hasClass( 'fl-submenu-preview' ) ) {
				return;
			}

			var parent = node.find( '.fl-menu-horizontal .fl-has-submenu, .fl-menu-vertical .fl-has-submenu' ).eq( 0 );

			node.addClass( 'fl-submenu-preview' )
			parent.find( '.fl-has-submenu-container a' ).focus();
			parent.addClass( 'focus' );
			FLBuilder._moduleHelpers.menu._closeSearchFormPreview();
		},

		_closeSubmenuPreview: function() {
			$( '.fl-submenu-preview' ).removeClass( 'fl-submenu-preview' );
			$( '.fl-module-menu .fl-has-submenu.focus' ).removeClass( 'focus' );
		},

		_previewSearchForm: function() {
			var form = $( '.fl-builder-menu-settings:visible' );
			var preview = FLBuilder.preview;

			if ( ! form.length || ! preview || ! preview.elements.node ) {
				return;
			}

			var node = preview.elements.node;

			if ( node.hasClass( 'fl-search-menu-preview' ) ) {
				return;
			}

			var form = node.find( '.fl-menu-search-item .fl-search-form-input-wrap' );

			node.addClass( 'fl-search-menu-preview' )
			form.fadeIn(200);
			FLBuilder._moduleHelpers.menu._closeSubmenuPreview();
		},

		_closeSearchFormPreview: function() {
			var form = $( '.fl-builder-menu-settings:visible' );
			var preview = FLBuilder.preview;

			if ( ! form.length || ! preview || ! preview.elements.node ) {
				return;
			}

			var node = preview.elements.node;

			if ( ! node.hasClass( 'fl-search-menu-preview' ) ) {
				return;
			}

			var form  = node.find('.fl-menu-search-item .fl-search-form-input-wrap');

			$( '.fl-search-menu-preview' ).removeClass( 'fl-search-menu-preview' );
			form.fadeOut(200);
		},

		_mobileToggle: function() {
			var form = $( '.fl-builder-menu-settings:visible' ),
				layout = form.find( 'select[name=menu_layout]' ).val(),
				toggle = form.find( 'select[name=mobile_toggle]' ).val(),
				mobileStacked = form.find( '#fl-field-mobile_stacked' );

			if ( 'horizontal' == layout && 'expanded' == toggle ) {
				mobileStacked.show();
			}
			else {
				mobileStacked.hide();
			}
		},

		_removeWooSections: function() {
			if ( FLBuilderConfig.wooActive ) {
				return;
			}

			var form = $( '.fl-builder-menu-settings:visible' );

			form.find( 'a[href*="settings-tab-woo_tab"]' ).remove();
			form.find( '#fl-builder-settings-tab-woo_tab').remove();
		}
	} );

} )( jQuery );
