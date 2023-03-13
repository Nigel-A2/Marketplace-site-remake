jQuery(function($) {
	
	// Render any QR codes on the page
	$('.simbaotp_qr_container').qrcode({
		'render': 'image',
		'text': $('.simbaotp_qr_container:first').data('qrcode'),
	});
	
	function update_otp_code() {
		
		$('.simba_current_otp').html('<em>'+simbatfa_totp.updating+'</em>');
		
		var got_code = '';
		
		$.post(simbatfa_totp.ajax_url, {
			action: 'simbatfa_shared_ajax',
			subaction: 'refreshotp',
			nonce: simbatfa_totp.tfa_shared_nonce
		}, function(response) {
			
			try {
				var resp = JSON.parse(response);
				got_code = resp.code;
			} catch(err) {
				if ('' !== simbatfa_totp.also_try) {
					alert(simbatfa_totp.response+" "+response);
				}
				console.log(response);
				console.log(err);
			}
			
			if ('' === got_code && '' !== simbatfa_totp.also_try) {
				$.post(simbatfa_totp.also_try, {
					action: 'simbatfa_shared_ajax',
					subaction: 'refreshotp',
					nonce: simbatfa_totp.tfa_shared_nonce
				}, function(response) {
					try {
						var resp = JSON.parse(response);
						if (resp.code) {
							$('.simba_current_otp').html(resp.code);
						} else {
							console.log(response);
							console.log("TFA: no code found");
						}
					} catch(err) {
						alert(simbatfa_totp.response+" "+response);
						console.log(response);
						console.log(err);
					}
				});
			} else if ('' != got_code) {
				$('.simba_current_otp').html(got_code);
			} else {
				console.log("TFA: no code found");
			}
		});
	}
	
	var min_refresh_after = 30;
	
	if (0 == $('body.settings_page_two-factor-auth').length) {
		$('.simba_current_otp').each(function(ind, obj) {
			var refresh_after = $(obj).data('refresh_after');
			if (refresh_after > 0 && refresh_after < min_refresh_after) {
				min_refresh_after = refresh_after;
			}
		});
		
		// Update after the given seconds, and then every 30 seconds
		setTimeout(function() {
			setInterval(update_otp_code, 30000)
			update_otp_code();
		}, min_refresh_after * 1000);
	}
	
	// Handle clicks on the 'refresh' link
	$('.simbaotp_refresh').on('click', function(e) {
		e.preventDefault();
		update_otp_code();
	});
	
	$('#tfa_trusted_devices_box').on('click', '.simbatfa-trust-remove', function(e) {
		e.preventDefault();
		var device_id = $(this).data('trusted-device-id');
		$(this).parents('.simbatfa_trusted_device').css('opacity', '0.5');
		if ('undefined' !== typeof device_id) {
			$.post(simbatfa_totp.ajax_url, {
				action: 'simbatfa_shared_ajax',
				subaction: 'untrust_device',
				nonce: simbatfa_totp.tfa_shared_nonce,
				device_id: device_id
			}, function(response) {
				var resp = JSON.parse(response);
				if (resp.hasOwnProperty('trusted_list')) {
					$('#tfa_trusted_devices_box_inner').html(resp.trusted_list);
				}
			});
		}
	});
});
