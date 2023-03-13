jQuery(function($) {
	
	/**
	 * Check if the user requires an OTP field and if so, display it
	 *
	 * @param String form - DOM selector string
	 *
	 * @uses show_otp_field()
	 *
	 * @return Boolean - true if we got involved
	 */
	function check_and_possibly_show_otp_field(form) {

		// If this is a "lost password" form, then exit
		if ($(form).attr('id') === 'lostpasswordform' ||  $(form).attr('id') === 'resetpasswordform') return false;
		
		// 'username' is used by WooCommerce
		var username = $(form).find('[name="log"], [name="username"], #user_login, #affwp-login-user-login, #affwp-user-login').first().val();
		
		if (!username.length) return false;
		
		var $submit_button = $(form).find('input[name="wp-submit"], input[type="submit"], button[type="submit"]').first();

		if (simba_tfasettings.hasOwnProperty('spinnerimg')) {
			var styling = 'float:right; margin:6px 12px; width: 20px; height: 20px;';
			if ($('#theme-my-login #wp-submit').length >0) {
				styling = 'margin-left: 4px; position: relative; top: 4px; width: 20px; height: 20px; border:0px; box-shadow:none;';
			}	
			$submit_button.after('<img class="simbaotp_spinner" src="'+simba_tfasettings.spinnerimg+'" style="'+styling+'">');
		}

		$.ajax({
			url: simba_tfasettings.ajaxurl,
			type: 'POST',
			data: {
				action: 'simbatfa-init-otp',
				user: username
			},
			dataType: 'text',
			success: function(resp) {
				try {
					var json_begins = resp.search('{"jsonstarter":"justhere"');
					if (json_begins > -1) {
						if (json_begins > 0) {
							console.log("Expected JSON marker found at position: "+json_begins);
							resp = resp.substring(json_begins);
						}
					} else {
						console.log("Expected JSON marker not found");
						console.log(resp);
					}
					
					response = JSON.parse(resp);
					
					if (response.hasOwnProperty('php_output')) {
						console.log("PHP output was returned (follows)");
						console.log(response.php_output);
					}
					
					if (response.hasOwnProperty('extra_output')) {
						console.log("Extra output was returned (follows)");
						console.log(response.extra_output);
					}
					
					if (true === response.status) {
						// Don't bother to remove the spinner if the form is being submitted.
						$('.simbaotp_spinner').remove();

						var user_can_trust = (response.hasOwnProperty('user_can_trust') && response.user_can_trust) ? true : false;
						
						var user_already_trusted = (response.hasOwnProperty('user_already_trusted') && response.user_can_trust) ? true : false;
						
						console.log("Simba TFA: User has OTP enabled: showing OTP field (user_can_trust="+user_can_trust+")");
						
						show_otp_field(form, user_can_trust, user_already_trusted);
						
					} else {
						console.log("Simba TFA: User does not have OTP enabled: submitting form");
						
						// For some reason, .submit() stopped working with TML 7.x. N.B. Used to do this only for form_type == 2 ("TML shortcode or widget, WP Members, bbPress, Ultimate Membership Pro, WooCommerce or Elementor login form")
						$(form).find('input[type="submit"], button[type="submit"]').first().trigger('click');
						// $('#wp-submit').parents('form').first().trigger('submit');
					}
					
				} catch(err) {
					$('#login').html(resp);
					console.log("Simba TFA: Error when processing response");
					console.log(err);
					console.log(resp);
				}
			},
			error: function(jq_xhr, text_status, error_thrown) {
				console.log("Simba TFA: AJAX error: "+error_thrown+": "+text_status);
				console.log(jq_xhr);
				if (jq_xhr.hasOwnProperty('responseText')) {
					console.log(jq_xhr.responseText);
					$(form).append('<p class="error" style="clear:left;">'+simba_tfasettings.error+'</p>');
				}
			}
		});
		return true;
	}
	
	// Parameters: see check_and_possibly_show_otp_field
	function show_otp_field(form, user_can_trust, user_already_trusted) {
		
		var $submit_button;
		
		user_can_trust = ('undefined' == typeof user_can_trust) ? false : user_can_trust;
		user_already_trusted = ('undefined' == typeof user_already_trusted) ? false : user_already_trusted;
		
		if ('https:' != window.location.protocol && 'localhost' !== location.hostname && '127.0.0.1' !== location.hostname && /^\.localdomain$/.test(location.hostname)) {
			user_can_trust = false;
		}
		
		if (!user_can_trust) { user_already_trusted = false; }
		
		// name="Submit" is WP-Members. 'submit' is Theme My Login starting from 7.x
		$submit_button = $(form).find('input[name="wp-submit"], input[name="Submit"], input[name="submit"]');
		// This hasn't been needed for anything yet (Jul 2018), but is a decent back-stop that would have prevented some breakage in the past that needed manual attention:
		if (0 == $submit_button.length) {
			$submit_button = $(form).find('input[type="submit"], button[type="submit"]').first();
		}

		// Hide all elements in a browser-safe way
		// .user-pass-wrap is the wrapper used (instead of a paragraph) on wp-login.php from WP 5.3
		$submit_button.parents('form').first().find('p, .impu-form-line-fr, .tml-field-wrap, .user-pass-wrap, .elementor-field-type-text, .elementor-field-type-submit, .elementor-remember-me, .bbp-username, .bbp-password, .bbp-submit-wrapper').each(function(i) {
			$(this).css('visibility', 'hidden').css('position', 'absolute');
			// On the WooCommerce form, the 'required' asterisk in the child <span> still shows without this
			$(this).find('span').css('visibility', 'hidden').css('position', 'absolute');
		});
		
		// WP-Members
		$submit_button.parents('#wpmem_login').find('fieldset').css('visibility', 'hidden').css('position', 'absolute');
		
		// Add new field and controls
		var html = '';
		
		if (user_already_trusted) {
			
			html += '<br><span class="simbaotp_is_trusted">'+simba_tfasettings.is_trusted+'</span>';
			
		} else {
			
			html += '<label for="simba_two_factor_auth">' + simba_tfasettings.otp + '<br><input type="text" name="two_factor_code" id="simba_two_factor_auth" autocomplete="off" data-lpignore="true"';
			
			if ($(form).hasClass('woocommerce-form-login')) {
				// Retain compatibility with previous full-width layout
				html += ' style="width: 100%;"';
			}
			
			html += '></label>';
			
			html += '<p class="forgetmenot" style="font-size:small;';
			if (!$(form).hasClass('woocommerce-form-login')) {
				// Retain compatibility with previous full-width layout
				html += ' max-width: 60%;';
			}
			html += '">'+simba_tfasettings.otp_login_help;
			
			if (user_can_trust) {
			
				html += '<br><input type="checkbox" name="simba_tfa_mark_as_trusted" id="simba_tfa_mark_as_trusted" value="1"><label for="simba_tfa_mark_as_trusted">'+ simba_tfasettings.mark_as_trusted+'</label>';
				
			}
		}
		
		html += '</p>';
		
		var submit_button_text;
		var submit_button_name;
		
		if ('button' == $submit_button.prop('nodeName').toLowerCase()) {
			submit_button_text = $submit_button.text().trim();
			submit_button_name = $submit_button.attr('name');
		} else {
			submit_button_text = $submit_button.val();
			submit_button_name = $submit_button.attr('name');
		}
		
		html += '<p class="submit"><input id="tfa_login_btn" class="button button-primary button-large" type="submit" ';
		
		if ('undefined' !== typeof submit_button_name && '' != submit_button_name) { html += 'name="'+submit_button_name+'" '; }
		
		html += 'value="' + submit_button_text + '"></p>';
		
		$submit_button.prop('disabled', true);
		
		$submit_button.parents('form').first().prepend(html);

		$('#login_error').hide();
			
		if (user_already_trusted) {
			$('#tfa_login_btn').trigger('click');
		} else {

			$('#simba_two_factor_auth').trigger('focus');

			// Hide extra boxes of third party plugins
			jQuery('.hide-when-displaying-tfa-input').hide();
		}

	}
	
	/**
	 * This function gets attached to a form submission handler and decides whether to add an OTP field or not.
	 *
	 * @param Object e - submission event
	 *
	 * @return Boolean - whether to proceed with the submission or not
	 */
	var form_submit_handler = function(e) {
		
		console.log('Simba TFA: form submit request');

		var form = e.target;
		$(form).off();

		if (check_and_possibly_show_otp_field(form)) {
			e.preventDefault();
			return false;
		}
		
		return true;
		
	};
	
	if (simba_tfasettings.login_form_off_selectors) {
		$(simba_tfasettings.login_form_off_selectors).off('submit');
	}
	
	$(simba_tfasettings.login_form_selectors).on('submit', form_submit_handler);
	
});
