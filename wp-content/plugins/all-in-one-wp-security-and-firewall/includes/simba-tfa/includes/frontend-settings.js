var tfa_query_leaving = false;

// Prevent accidental leaving if there are unsaved settings
window.onbeforeunload = function(e) {
	if (tfa_query_leaving) {
		e.returnValue = simba_tfa_frontend.ask;
		return simba_tfa_frontend.ask;
	}
}

jQuery(function($) {
	
	$(".tfa_settings_form input, .tfa_settings_form textarea, .tfa_settings_form select" ).on('change', function() {
		tfa_query_leaving = true;
	});
	
	$(".simbatfa_settings_save").on('click', function() {
		
		$.blockUI({ message: '<div style="margin: 8px;font-size:150%;">'+simba_tfa_frontend.saving+'</div>' });
		
		// https://stackoverflow.com/questions/10147149/how-can-i-override-jquerys-serialize-to-include-unchecked-checkboxes
		var form_data = $('.tfa_settings_form input, .tfa_settings_form textarea, .tfa_settings_form select').serialize();
		
		// Include unchecked checkboxes. Use filter to only include unchecked boxes.
		$.each($('.tfa_settings_form input[type=checkbox]')
		.filter(function(idx) {
			return $(this).prop('checked') === false
		}),
		 function(idx, el){
			 // attach matched element names to the form_data with a chosen value.
			 var emptyVal = '0';
			 form_data += '&' + $(el).attr('name') + '=' + emptyVal;
		 }
		);
		
		$.post(simba_tfa_frontend.ajax_url, {
			action: 'tfa_frontend',
			subaction: 'savesettings',
			settings: form_data,
			nonce: simba_tfa_frontend.nonce
		}, function(response) {
			var settings_saved = false;
			try {
				var resp = JSON.parse(response);
				if (resp.hasOwnProperty('result')) {
					settings_saved = true;
					tfa_query_leaving = false;
					// Allow user code to respond
					$(document).trigger('tfa_settings_saved', resp);
				}
				
				if (resp.hasOwnProperty('message')) {
					alert(resp.message);
				}
				
				if (resp.hasOwnProperty('qr')) {
					$('.simbaotp_qr_container').data('qrcode', resp['qr']).empty().qrcode({
						"render": "image",
						"text": resp['qr'],
					});
				}
				
				if (resp.hasOwnProperty('al_type_disp')) {
					$("#al_type_name").html(resp['al_type_disp']['disp']);
					$("#al_type_desc").html(resp['al_type_disp']['desc']);
				}
				
			} catch(err) {
				console.log(err);
				console.log(response);
				if ('' === simba_tfa_frontend.also_try) {
					alert(simba_tfa_frontend.response+response);
				}
			}
			if ('' != simba_tfa_frontend.also_try) {
				if (!settings_saved) {
					$.post(simba_tfa_frontend.also_try, {
						action: 'tfa_frontend',
						subaction: 'savesettings',
						settings: form_data,
						nonce: simba_tfa_frontend.nonce
					}, function(response) {
						
						try {
							var resp = JSON.parse(response);
							if (resp.hasOwnProperty('result')) {
								settings_saved = true;
								tfa_query_leaving = false;
								// Allow user code to respond
								$(document).trigger('tfa_settings_saved', resp);
							}
							if (resp.hasOwnProperty('message')) {
								alert(resp.message);
							}
							if (resp.hasOwnProperty('qr')) {
								$('.simbaotp_qr_container').data('qrcode', resp['qr']).empty().qrcode({
									"render": "image",
									"text": resp['qr'],
								});
							}
							if (resp.hasOwnProperty('al_type_disp')) {
								$("#al_type_name").html(resp['al_type_disp']['disp']);
								$("#al_type_desc").html(resp['al_type_disp']['desc']);
							}
							
						} catch(err) {
							console.log(err);
							console.log(response);
							alert(simba_tfa_frontend.response+response);
						}
						$.unblockUI();
					});
				} else {
					$.unblockUI();
				}
			} else {
				$.unblockUI();
			}
		});
		
	});
});
