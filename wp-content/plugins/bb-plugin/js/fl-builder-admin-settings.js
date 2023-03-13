(function($){

	/**
	 * Helper class for dealing with the builder's admin
	 * settings page.
	 *
	 * @class FLBuilderAdminSettings
	 * @since 1.0
	 */
	FLBuilderAdminSettings = {

		/**
		 * An instance of wp.media used for uploading icons.
		 *
		 * @since 1.4.6
		 * @access private
		 * @property {Object} _iconUploader
		 */
		_iconUploader: null,

		/**
		 * Initializes the builder's admin settings page.
		 *
		 * @since 1.0
		 * @method init
		 */
		init: function()
		{
			this._bind();
			this._maybeShowWelcome();
			this._initNav();
			this._initNetworkOverrides();
			this._initLicenseSettings();
			this._initMultiSelects();
			this._initUserAccessSelects();
			this._initUserAccessNetworkOverrides();
			this._templatesOverrideChange();
			this._iconPro();
			this._alphaSettings();
		},

		/**
		 * Binds events for the builder's admin settings page.
		 *
		 * @since 1.0
		 * @access private
		 * @method _bind
		 */
		_bind: function()
		{
			$('.fl-settings-nav a').on('click', FLBuilderAdminSettings._navClicked);
			$('.fl-override-ms-cb').on('click', FLBuilderAdminSettings._overrideCheckboxClicked);
			$('.fl-ua-override-ms-cb').on('click', FLBuilderAdminSettings._overrideUserAccessCheckboxClicked);
			$('.fl-module-all-cb').on('click', FLBuilderAdminSettings._moduleAllCheckboxClicked);
			$('.fl-module-cb').on('click', FLBuilderAdminSettings._moduleCheckboxClicked);
			$('input[name=fl-templates-override]').on('keyup click', FLBuilderAdminSettings._templatesOverrideChange);
			$('input[name=fl-upload-icon]').on('click', FLBuilderAdminSettings._showIconUploader);
			$('.fl-delete-icon-set').on('click', FLBuilderAdminSettings._deleteCustomIconSet);
			$('#uninstall-form').on('submit', FLBuilderAdminSettings._uninstallFormSubmit);
			$( '.fl-settings-form .dashicons-editor-help' ).tipTip();
			$( '.subscription-form .subscribe-button' ).on( 'click', FLBuilderAdminSettings._welcomeSubscribe);
		},

		_welcomeSubscribe: function()
		{
			form  = $('.subscription-form')
			var error = form.find('.error')
			var spinner = form.find('.dashicons')

			if( error.css('display') != 'none' ) {
				error.hide()
			}

			name   = form.find( '.input-group-field.name').val()
			email  = form.find( '.input-group-field.email').val()
			nonce  = form.find( '#_wpnonce' ).val()

			if ( ! email || ! name ) {
				error.html('Please enter required fields').fadeIn()
				return false;
			}
			spinner.css('color', '#fff')
			spinner.addClass('spin')

			data = {
				'action'  : 'fl_welcome_submit',
				'name'    : name,
				'email'   : email,
				'_wpnonce': nonce
			}

			console.log(data)

			$.post(ajaxurl, data, function(response) {
				spinner.css( 'color', '#0a3c4b' );
				spinner.removeClass( 'spin' );
				if( response.success ) {
					$('.subscribe-button').hide()
					$('.input-group').remove()
					spinner.remove()
					$( error ).html( '<h2>' + response.data.message + '</h2>' ).fadeIn()
				} else {
					$( error ).html(response.data.message).fadeIn()
				}
			});
		},

		/**
		 * Show the welcome page after the license has been saved.
		 *
		 * @since 1.7.4
		 * @access private
		 * @method _maybeShowWelcome
		 */
		_maybeShowWelcome: function()
		{
			var onLicense    = 'license' == window.location.hash.replace( '#', '' ),
				isUpdated    = $( '.wrap .updated' ).length,
				licenseError = $( '.fl-license-error' ).length;

			if ( onLicense && isUpdated && ! licenseError ) {
				window.location.hash = 'welcome';
			}
		},

		/**
		 * Initializes the nav for the builder's admin settings page.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initNav
		 */
		_initNav: function()
		{
			var links  = $('.fl-settings-nav a'),
				hash   = window.location.hash,
				active = hash === '' ? [] : links.filter('[href~="'+ hash +'"]');

			$('a.fl-active').removeClass('fl-active');
			$('.fl-settings-form').hide();

			if(hash === '' || active.length === 0) {
				active = links.eq(0);
			}

			active.addClass('fl-active');
			$('#fl-'+ active.attr('href').split('#').pop() +'-form').fadeIn();
		},

		/**
		 * Fires when a nav item is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _navClicked
		 */
		_navClicked: function()
		{
			if($(this).attr('href').indexOf('#') > -1) {
				$('a.fl-active').removeClass('fl-active');
				$('.fl-settings-form').hide();
				$(this).addClass('fl-active');
				$('#fl-'+ $(this).attr('href').split('#').pop() +'-form').fadeIn();
			}
		},

		/**
		 * Initializes the checkboxes for overriding network settings.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initNetworkOverrides
		 */
		_initNetworkOverrides: function()
		{
			$('.fl-override-ms-cb').each(FLBuilderAdminSettings._initNetworkOverride);
		},

		/**
		 * Initializes a checkbox for overriding network settings.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initNetworkOverride
		 */
		_initNetworkOverride: function()
		{
			var cb      = $(this),
				content = cb.closest('.fl-settings-form').find('.fl-settings-form-content');

			if(this.checked) {
				content.show();
			}
			else {
				content.hide();
			}
		},

		/**
		 * Fired when a network override checkbox is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _overrideCheckboxClicked
		 */
		_overrideCheckboxClicked: function()
		{
			var cb      = $(this),
				content = cb.closest('.fl-settings-form').find('.fl-settings-form-content');

			if(this.checked) {
				content.show();
			}
			else {
				content.hide();
			}
		},

		/**
		 * Initializes custom multi-selects.
		 *
		 * @since 1.10
		 * @access private
		 * @method _initMultiSelects
		 */
		_initMultiSelects: function()
		{
			$( 'select[multiple]' ).multiselect( {
				selectAll: true,
				texts: {
					deselectAll     : FLBuilderAdminSettingsStrings.deselectAll,
					noneSelected    : FLBuilderAdminSettingsStrings.noneSelected,
					placeholder     : FLBuilderAdminSettingsStrings.select,
					selectAll       : FLBuilderAdminSettingsStrings.selectAll,
					selectedOptions : FLBuilderAdminSettingsStrings.selected
				}
			} );
		},

		/**
		 * Initializes user access select options.
		 *
		 * @since 1.10
		 * @access private
		 * @method _initUserAccessSelects
		 */
		_initUserAccessSelects: function()
		{
			var config  = FLBuilderAdminSettingsConfig,
				options = null,
				role    = null,
				select  = null,
				key     = null,
				hidden  = null;

			$( '.fl-user-access-select' ).each( function() {

				options = [];
				select  = $( this );
				key     = select.attr( 'name' ).replace( 'fl_user_access[', '' ).replace( '][]', '' );

				for( role in config.roles ) {
					options.push( {
						name    : config.roles[ role ],
						value   : role,
						checked : 'undefined' == typeof config.userAccess[ key ] ? false : config.userAccess[ key ][ role ]
					} );
				}

				select.multiselect( 'loadOptions', options );
			} );
		},

		/**
		 * Initializes the checkboxes for overriding user access
		 * network settings.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initUserAccessNetworkOverrides
		 */
		_initUserAccessNetworkOverrides: function()
		{
			$('.fl-ua-override-ms-cb').each(FLBuilderAdminSettings._initUserAccessNetworkOverride);
		},

		/**
		 * Initializes a checkbox for overriding user access
		 * network settings.
		 *
		 * @since 1.0
		 * @access private
		 * @method _initUserAccessNetworkOverride
		 */
		_initUserAccessNetworkOverride: function()
		{
			var cb     = $(this),
				select = cb.closest('.fl-user-access-setting').find('.ms-options-wrap');

			if(this.checked) {
				select.show();
			}
			else {
				select.hide();
			}
		},

		/**
		 * Fired when a network override checkbox is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _overrideCheckboxClicked
		 */
		_overrideUserAccessCheckboxClicked: function()
		{
			var cb     = $(this),
				select = cb.closest('.fl-user-access-setting').find('.ms-options-wrap');

			if(this.checked) {
				select.show();
			}
			else {
				select.hide();
			}
		},

		/**
		 * Fires when the "all" checkbox in the list of enabled
		 * modules is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _moduleAllCheckboxClicked
		 */
		_moduleAllCheckboxClicked: function()
		{
			if($(this).is(':checked')) {
				$('.fl-module-cb').prop('checked', true);
			} else {
				$('.fl-module-cb').prop('checked', false);
			}
		},

		/**
		 * Fires when a checkbox in the list of enabled
		 * modules is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _moduleCheckboxClicked
		 */
		_moduleCheckboxClicked: function()
		{
			var allChecked = true;

			$('.fl-module-cb').each(function() {

				if(!$(this).is(':checked')) {
					allChecked = false;
				}
			});

			if(allChecked) {
				$('.fl-module-all-cb').prop('checked', true);
			}
			else {
				$('.fl-module-all-cb').prop('checked', false);
			}
		},

		/**
		 * @since 1.7.4
		 * @access private
		 * @method _initLicenseSettings
		 */
		_initLicenseSettings: function()
		{
			$( '.fl-new-license-form .button' ).on( 'click', FLBuilderAdminSettings._newLicenseButtonClick );
		},

		/**
		 * @since 1.7.4
		 * @access private
		 * @method _newLicenseButtonClick
		 */
		_newLicenseButtonClick: function()
		{
			$( '.fl-new-license-form' ).hide();
			$( '.fl-license-form' ).show();
		},

		/**
		 * Fires when the templates override setting is changed.
		 *
		 * @since 1.6.3
		 * @access private
		 * @method _templatesOverrideChange
		 */
		_templatesOverrideChange: function()
		{
			var input 			= $('input[name=fl-templates-override]'),
				val 			= input.val(),
				overrideNodes 	= $( '.fl-templates-override-nodes' ),
				toggle 			= false;

			if ( 'checkbox' == input.attr( 'type' ) ) {
				toggle = input.is( ':checked' );
			}
			else {
				toggle = '' !== val;
			}

			overrideNodes.toggle( toggle );
		},

		/**
		 * Shows the media library lightbox for uploading icons.
		 *
		 * @since 1.4.6
		 * @access private
		 * @method _showIconUploader
		 */
		_showIconUploader: function()
		{
			if(FLBuilderAdminSettings._iconUploader === null) {
				FLBuilderAdminSettings._iconUploader = wp.media({
					title: FLBuilderAdminSettingsStrings.selectFile,
					button: { text: FLBuilderAdminSettingsStrings.selectFile },
					library : { type : 'application/zip' },
					multiple: false
				});
			}

			FLBuilderAdminSettings._iconUploader.once('select', $.proxy(FLBuilderAdminSettings._iconFileSelected, this));
			FLBuilderAdminSettings._iconUploader.open();
		},

		/**
		 * Callback for when an icon set file is selected.
		 *
		 * @since 1.4.6
		 * @access private
		 * @method _iconFileSelected
		 */
		_iconFileSelected: function()
		{
			var file = FLBuilderAdminSettings._iconUploader.state().get('selection').first().toJSON();

			$( 'input[name=fl-new-icon-set]' ).val( file.id );
			$( '#icons-form' ).submit();
		},

		/**
		 * Fires when the delete link for an icon set is clicked.
		 *
		 * @since 1.4.6
		 * @access private
		 * @method _deleteCustomIconSet
		 */
		_deleteCustomIconSet: function()
		{
			var set = $( this ).data( 'set' );

			$( 'input[name=fl-delete-icon-set]' ).val( set );
			$( '#icons-form' ).submit();
		},

		/**
		 * Fires when the uninstall button is clicked.
		 *
		 * @since 1.0
		 * @access private
		 * @method _uninstallFormSubmit
		 * @return {Boolean}
		 */
		_uninstallFormSubmit: function()
		{
			var result = prompt(FLBuilderAdminSettingsStrings.uninstall.replace(/&quot;/g, '"'), '');

			if(result == 'uninstall') {
				return true;
			}

			return false;
		},
		_iconPro: function() {
			form = $('#icons-form')
			checkbox = form.find('input[name=fl-enable-fa-pro]').prop('checked')
			light = form.find('input[value=font-awesome-5-light]').parent()
			duo   = form.find('input[value=font-awesome-5-duotone]').parent()

			if ( true === checkbox ) {
				light.css('font-weight', '800')
			//	light.css('color', '#0E5A71')
				duo.css('font-weight', '800')
			//	duo.css('color', '#0E5A71')
			}

		},
		_alphaSettings: function() {
			form = $('#beta-form');

			form.find('.alpha-checkbox').on('click', function(){
				console.log('checked?')
				if ( true === $(this).prop('checked') ) {
					if ( confirm( 'Are you sure you want to enable Alpha releases?') ) {
						// do nothing
					} else {
						$(this).prop('checked',false);
					}

				}
			})
		}

	};

	/* Initializes the builder's admin settings. */
	$(function(){
		FLBuilderAdminSettings.init();
	});

})(jQuery);
