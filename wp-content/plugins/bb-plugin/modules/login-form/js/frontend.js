(function ($) {

	FLBuilderLoginForm = function (settings) {
		this.settings = settings;
		this.nodeClass = '.fl-node-' + settings.id;
		this.loginform = $(this.nodeClass + ' .fl-login-form.login');
		this.loginbutton = this.loginform.find('a.fl-button');
		this.logoutform = $(this.nodeClass + ' .fl-login-form.logout');
		this.logoutbutton = this.logoutform.find('a.fl-button');
		this._init();
	};

	FLBuilderLoginForm.prototype = {
		settings: {},
		nodeClass: '',
		form: null,
		button: null,

		_init: function () {
			this.loginbutton.on('click', $.proxy(this._loginForm, this));
			this.logoutbutton.on('click', $.proxy(this._logoutForm, this));
			this.loginform.find('input[type="password"]').on('keypress', $.proxy(this._onEnterKey, this));
		},

		_loginForm: function (e) {
			var submitButton = $(e.currentTarget),
				currentForm = submitButton.closest('.fl-login-form'),
				postId = currentForm.closest('.fl-builder-content').data('post-id'),
				templateId = currentForm.data('template-id'),
				templateNodeId = currentForm.data('template-node-id'),
				nodeId = currentForm.closest('.fl-module').data('node'),
				buttonText = submitButton.find('.fl-button-text').text(),
				waitText = submitButton.closest('.fl-form-button').data('wait-text'),
				name = currentForm.find('input[name=fl-login-form-name]'),
				password = currentForm.find('input[name=fl-login-form-password]'),
				remember = currentForm.find('input[name=fl-login-form-remember]'),
				nonce = this.loginform.find('input#fl-login-form-nonce').val(),
				valid = true,
				ajaxData = null;

			e.preventDefault();

			if (submitButton.hasClass('fl-form-button-disabled')) {
				return; // Already submitting
			}

			if (name.length > 0 && name.val() == '') {
				name.addClass('fl-form-error');
				name.siblings('.fl-form-error-message').show();
				valid = false;
			}
			if ('' == password.val()) {
				password.addClass('fl-form-error');
				password.siblings('.fl-form-error-message').show();
				valid = false;
			}

			if (valid) {
				currentForm.find('> .fl-form-error-message').hide();
				submitButton.find('.fl-button-text').text(waitText);
				submitButton.data('original-text', buttonText);
				submitButton.addClass('fl-form-button-disabled');

				ajaxData = {
					action: 'fl_builder_login_form_submit',
					name: name.val(),
					password: password.val(),
					post_id: postId,
					remember: remember.is(':checked'),
					template_id: templateId,
					template_node_id: templateNodeId,
					node_id: nodeId,
					nonce: nonce
				};

				$.post(FLBuilderLayoutConfig.paths.wpAjaxUrl, ajaxData, $.proxy(function (response) {
					this._loginFormComplete(response, submitButton);
				}, this));
			}
		},

		_logoutForm: function (e) {
			var submitButton = $(e.currentTarget),
				nonce = this.logoutform.find('input#fl-login-form-nonce').val();

			e.preventDefault();

			ajaxData = {
				action: 'fl_builder_logout_form_submit',
				nonce: nonce
			};

			$.post(FLBuilderLayoutConfig.paths.wpAjaxUrl, ajaxData, $.proxy(function (response) {
				this._logoutFormComplete(response, submitButton);
			}, this));
		},

		_logoutFormComplete: function (response, button) {

			if ( this.settings.lo_url.length > 0 ) {
				window.location.href = this.settings.lo_url;
			} else {
				location.reload()
			}
		},

		_loginFormComplete: function (response, button) {
			var buttonText = button.data('original-text'),
				form = button.closest('.fl-login-form');

			if (false === response.success) {
				form.find('> .fl-form-error-message').html(response.data);
				form.find('> .fl-form-error-message').show();
				button.removeClass('fl-form-button-disabled');
				button.find('.fl-button-text').text(buttonText);
			} else {
				if ('current' == response.data.url) {
					window.location.reload();
				} else if ('referrer' == response.data.url) {
					document.referrer ? window.location = document.referrer : history.back()
				} else {
					window.location.href = response.data.url;
				}
			}
		},

		_onEnterKey: function (e) {
			if (e.which == 13) {
				var currentForm = $(e.currentTarget).closest('.fl-login-form');
				currentForm.find('a.fl-button').trigger('click');
			}
		}
	}

})(jQuery);
