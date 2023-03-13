( function( $ ) {

	window.onLoadFLReCaptcha = function() {
		var reCaptchaFields = $( '.fl-grecaptcha' ),
			widgetID;

		if ( reCaptchaFields.length > 0 ) {
			reCaptchaFields.each(function(i){
				var self 		= $( this ),
				 	attrWidget 	= self.attr('data-widgetid'),
					newID 		= $(this).attr('id') + '-' + i;

				// Avoid re-rendering as it's throwing API error
				if ( (typeof attrWidget !== typeof undefined && attrWidget !== false) ) {
					return;
				}
				else {

					// Increment ID to avoid conflict with the same form.
					self.attr( 'id', newID );

					widgetID = grecaptcha.render( newID, {
						sitekey : self.data( 'sitekey' ),
						theme	: self.data( 'theme' ),
						size	: self.data( 'validate' ),
						callback: function( response ){
							if ( response != '' ) {
								self.attr( 'data-fl-grecaptcha-response', response );

								// Re-submitting the form after a successful invisible validation.
								if ( 'invisible' == self.data( 'validate' ) ) {
									self.closest( '.fl-subscribe-form' ).find( 'a.fl-button' ).trigger( 'click' );
								}
							}
						}
					});

					self.attr( 'data-widgetid', widgetID );

				}
			});
		}
	};

	FLBuilderSubscribeForm = function( settings )
	{
		this.settings	= settings;
		this.nodeClass	= '.fl-node-' + settings.id;
		this.form 		= $( this.nodeClass + ' .fl-subscribe-form' );
		this.button		= this.form.find( 'a.fl-button' );
		this._init();
	};

	FLBuilderSubscribeForm.prototype = {

		settings	: {},
		nodeClass	: '',
		form		: null,
		button		: null,

		_init: function()
		{
			this.button.on( 'click', $.proxy( this._submitForm, this ) );
			this.button.on( 'keydown, keyup', $.proxy( this._keyupdown, this ) );
			this.form.find( 'input[type="email"]' ).on( 'keypress', $.proxy( this._onEnterKey, this) );
		},

		_keyupdown: function(e) {
			if( e.keyCode === 13 || e.keyCode === 32 ) {
				e.preventDefault();
				this._submitForm(e);
			}
		},

		_submitForm: function( e )
		{
			var submitButton   = $( e.currentTarget ),
				currentForm    = submitButton.closest( '.fl-subscribe-form' ),
				postId         = currentForm.closest( '.fl-builder-content' ).data( 'post-id' ),
				templateId     = currentForm.data( 'template-id' ),
				templateNodeId = currentForm.data( 'template-node-id' ),
				nodeId         = currentForm.closest( '.fl-module' ).data( 'node' ),
				buttonText     = submitButton.find( '.fl-button-text' ).text(),
				waitText       = submitButton.closest( '.fl-form-button' ).data( 'wait-text' ),
				name           = currentForm.find( 'input[name=fl-subscribe-form-name]' ),
				email          = currentForm.find( 'input[name=fl-subscribe-form-email]' ),
				successUrl     = this.settings.successUrl,
				termsCheckbox  = currentForm.find( 'input[name=fl-terms-checkbox]'),
				recaptcha      = currentForm.find( '.fl-grecaptcha' ),
				reCaptchaValue = recaptcha.data( 'fl-grecaptcha-response' ),
				re             = /\S+@\S+\.\S+/,
				valid          = true,
				ajaxData       = null;

			e.preventDefault();

			if ( submitButton.hasClass( 'fl-form-button-disabled' ) ) {
				return; // Already submitting
			}

			if ( name.length > 0 && name.val() == '' ) {
				name.addClass( 'fl-form-error' );
				name.siblings( '.fl-form-error-message' ).show();
				valid = false;
			}
			if ( '' == email.val() || ! re.test( email.val() ) ) {
				email.addClass( 'fl-form-error' );
				email.siblings( '.fl-form-error-message' ).show();
				valid = false;
			}

			if ( termsCheckbox.length ) {
				if ( ! termsCheckbox.is(':checked') ) {
					valid = false;
					termsCheckbox.addClass( 'fl-form-error' );
					termsCheckbox.parent().siblings( '.fl-form-error-message' ).show();
				}
				else {
					termsCheckbox.removeClass( 'fl-form-error' );
					termsCheckbox.parent().siblings( '.fl-form-error-message' ).hide();
				}
			}

			if ( recaptcha.length > 0 && valid ) {
				if ( 'undefined' === typeof reCaptchaValue || reCaptchaValue === false ) {
					if ( 'normal' == recaptcha.data( 'validate' ) ) {
						recaptcha.addClass( 'fl-form-error' );
						recaptcha.siblings( '.fl-form-error-message' ).show();
					} else if ( 'invisible' == recaptcha.data( 'validate' ) ) {

						// Invoke the reCAPTCHA check.
						if ( 'undefined' !== typeof recaptcha.data( 'action' ) ) {
							// V3
							grecaptcha.execute( recaptcha.data( 'widgetid' ), {action: recaptcha.data( 'action' )} );
						}
						else {
							// V2
							grecaptcha.execute( recaptcha.data( 'widgetid' ) );
						}
					}

					valid = false;
				} else {
					recaptcha.removeClass( 'fl-form-error' );
					recaptcha.siblings( '.fl-form-error-message' ).hide();
				}
			}

			if ( valid ) {

				currentForm.find( '> .fl-form-error-message' ).hide();
				submitButton.find( '.fl-button-text' ).text( waitText );
				submitButton.data( 'original-text', buttonText );
				submitButton.addClass( 'fl-form-button-disabled' );

				ajaxData = {
					action           : 'fl_builder_subscribe_form_submit',
					name             : name.val(),
					email            : email.val(),
					success_url      : successUrl,
					terms_checked    : termsCheckbox.is(':checked') ? '1' : '0',
					post_id          : postId,
					template_id      : templateId,
					template_node_id : templateNodeId,
					node_id          : nodeId
				};

				if ( reCaptchaValue ) {
					ajaxData.recaptcha = reCaptchaValue;
				}

				$.post( FLBuilderLayoutConfig.paths.wpAjaxUrl, ajaxData, $.proxy( function( response ){
					this._submitFormComplete( response, submitButton );
				}, this ));
			}
		},

		_submitFormComplete: function( response, button )
		{
			var data        = JSON.parse( response ),
				buttonText  = button.data( 'original-text' ),
				form        = button.closest( '.fl-subscribe-form' );

			if ( data.error ) {

				if ( data.error ) {
					form.find( '> .fl-form-error-message' ).text( data.error );
				}

				form.find( '> .fl-form-error-message' ).show();
				button.removeClass( 'fl-form-button-disabled' );
				button.find( '.fl-button-text' ).text( buttonText );
				if ( typeof data.errorInfo !== 'undefined' ) {
					console.log('Subscribe Form:',data.errorInfo);
				}
			}
			else if ( 'message' == data.action ) {
				form.find( '> *' ).hide();
				$( this.nodeClass + ' .fl-form-success-message' ).show();
			}
			else if ( 'redirect' == data.action ) {
				window.location.href = data.url;
			}
		},

		_onEnterKey: function( e )
		{
			if (e.which == 13) {
				var currentForm = $( e.currentTarget ).closest( '.fl-subscribe-form' );
				currentForm.find( 'a.fl-button' ).trigger( 'click' );
		  	}
		}
	}

})( jQuery );
