(function($){
	$.validator.addMethod( "alphanumeric", function( value, element ) {
		return this.optional( element ) || /^\w+$/i.test( value );
	}, "Letters, numbers, and underscores only please" );

	FLBuilder.registerModuleHelper('contact-form', {

		rules: {
			recaptcha_action: {
				alphanumeric: true
			}
		},

		init: function()
		{
			var form = $( '.fl-builder-settings' ),
			icon = form.find( 'input[name=btn_icon]' );
			icon.on( 'change', this._flipSettings );
			this._flipSettings();
			// Toggle reCAPTCHA display
			this._toggleReCaptcha();
			this._toggleAction();
			$( 'select[name=recaptcha_toggle]' ).on( 'change', $.proxy( this._toggleReCaptcha, this ) );
			$( 'select[name=recaptcha_toggle]' ).on( 'change', $.proxy( this._toggleAction, this ) );
			$( 'input[name=recaptcha_site_key]' ).on( 'change', $.proxy( this._toggleReCaptcha, this ) );
			$( 'select[name=recaptcha_validate_type]' ).on( 'change', $.proxy( this._toggleReCaptcha, this ) );
			$( 'select[name=recaptcha_theme]' ).on( 'change', $.proxy( this._toggleReCaptcha, this ) );
			$( 'input[name=btn_bg_color]' ).on( 'change', this._previewButtonBackground );

			// Render reCAPTCHA after layout rendered via AJAX
			if ( window.onLoadFLReCaptcha ) {
				$( FLBuilder._contentClass ).on( 'fl-builder.layout-rendered', onLoadFLReCaptcha );
			}
		},

		_flipSettings: function() {
			var form  = $( '.fl-builder-settings' ),
					icon = form.find( 'input[name=btn_icon]' );
			if ( -1 !== icon.val().indexOf( 'fad fa') ) {
				$('#fl-field-btn_duo_color1').show();
				$('#fl-field-btn_duo_color2').show();
			} else {
				$('#fl-field-btn_duo_color1').hide();
				$('#fl-field-btn_duo_color2').hide();
			}
		},

		/**
		 * Custom preview method for reCAPTCHA settings
		 *
		 * @param  object event  The event type of where this method been called
		 * @since 1.9.5
		 */
		_toggleReCaptcha: function(event)
		{
			var form      	= $( '.fl-builder-settings' ),
				nodeId    	= form.attr( 'data-node' ),
				toggle    	= form.find( 'select[name=recaptcha_toggle]' ),
				captchaKey	= form.find( 'input[name=recaptcha_site_key]' ).val(),
				captType    = form.find( 'select[name=recaptcha_validate_type]' ).val(),
				theme		= form.find( 'select[name=recaptcha_theme]' ).val(),
				reCaptcha 	= $( '.fl-node-'+ nodeId ).find( '.fl-grecaptcha' ),
				reCaptchaId = nodeId +'-fl-grecaptcha',
				target		= typeof event !== 'undefined' ? $(event.currentTarget) : null,
				inputEvent	= target != null && typeof target.attr('name') !== typeof undefined && target.attr('name') === 'recaptcha_site_key',
				selectEvent	= target != null && typeof target.attr('name') !== typeof undefined && target.attr('name') === 'recaptcha_toggle',
				typeEvent	= target != null && typeof target.attr('name') !== typeof undefined && target.attr('name') === 'recaptcha_validate_type',
				themeEvent	= target != null && typeof target.attr('name') !== typeof undefined && target.attr('name') === 'recaptcha_theme',
				scriptTag 	= $('<script>'),
				isRender 	= false;

			// Add library if not exists
			if ( 0 === $( 'script#g-recaptcha-api' ).length ) {
				scriptTag
					.attr('src', 'https://www.google.com/recaptcha/api.js?onload=onLoadFLReCaptcha&render=explicit')
					.attr('type', 'text/javascript')
					.attr('id', 'g-recaptcha-api')
					.attr('async', 'async')
					.attr('defer', 'defer')
					.appendTo('body');
			}

			if ( 'show' === toggle.val() && captchaKey.length ) {

				// reCAPTCHA is not yet exists
				if ( 0 === reCaptcha.length ) {
					isRender = true;
				}
				// If reCAPTCHA element exists, then reset reCAPTCHA if existing key does not matched with the input value
				else if ( ( inputEvent || selectEvent || typeEvent || themeEvent ) && ( reCaptcha.data('sitekey') != captchaKey || reCaptcha.data('validate') != captType || reCaptcha.data('theme') != theme )
				) {
					reCaptcha.parent().remove();
					isRender = true;
				}
				else {
					reCaptcha.parent().show();
				}

				if ( isRender ) {
					this._renderReCaptcha( nodeId, reCaptchaId, captchaKey, captType, theme );
				}
			}
			else if ( 'show' === toggle.val() && captchaKey.length === 0 && reCaptcha.length > 0 ) {
				reCaptcha.parent().remove();
			}
			else if ( 'hide' === toggle.val() && reCaptcha.length > 0 ) {
				reCaptcha.parent().hide();
			}
		},

		/**
		 * Render Google reCAPTCHA
		 *
		 * @param  string nodeId  		The current node ID
		 * @param  string reCaptchaId  	The element ID to render reCAPTCHA
		 * @param  string reCaptchaKey  The reCAPTCHA Key
		 * @param  string reCaptType  	Checkbox or invisible
		 * @param  string theme         Light or dark
		 * @since 1.9.5
		 */
		_renderReCaptcha: function( nodeId, reCaptchaId, reCaptchaKey, reCaptType, theme )
		{
			var captchaField	= $( '<div class="fl-input-group fl-recaptcha">' ),
				captchaElement 	= $( '<div id="'+ reCaptchaId +'" class="fl-grecaptcha">' ),
				widgetID;

			if ( 'invisible_v3' == reCaptType ) {
				reCaptType = 'invisible';
			}

			captchaElement.attr( 'data-sitekey', reCaptchaKey );
			captchaElement.attr( 'data-validate', reCaptType );
			captchaElement.attr( 'data-theme', theme );

			// Append recaptcha element to an appended element
			captchaField
				.html(captchaElement)
				.insertAfter( $('.fl-node-'+ nodeId ).find('.fl-contact-form > .fl-message') );

			widgetID = grecaptcha.render( reCaptchaId, {
				sitekey : reCaptchaKey,
				size    : reCaptType,
				theme	: theme
			});
			captchaElement.attr('data-widgetid', widgetID);
		},

		_toggleAction: function()
		{
			var form   = $( '.fl-builder-settings' ),
				toggle = form.find( 'select[name=recaptcha_toggle]' ).val(),
				captType = form.find( 'select[name=recaptcha_validate_type]' ).val(),
				action = form.find('input[name=recaptcha_action]');

				if ( 'show' == toggle && 'invisible_v3' == captType ) {
					action.closest( 'tr' ).show();
				}
				else {
					action.closest( 'tr' ).hide();
				}
		},

		_previewButtonBackground: function( e ) {
			var preview	= FLBuilder.preview,
				selector = preview.classes.node + ' a.fl-button, ' + preview.classes.node + ' a.fl-button:visited',
				form = $( '.fl-builder-settings:visible' ),
				style = form.find( 'select[name=btn_style]' ).val(),
				bgColor = form.find( 'input[name=btn_bg_color]' ).val();

			if ( 'flat' === style ) {
				if ( '' !== bgColor && bgColor.indexOf( 'rgb' ) < 0 ) {
					bgColor = '#' + bgColor;
				}
				preview.updateCSSRule( selector, 'background-color', bgColor );
				preview.updateCSSRule( selector, 'border-color', bgColor );
			} else {
				preview.delayPreview( e );
			}
		},

	});

})(jQuery);
