/**
 * Send an action over AJAX. A wrapper around jQuery.ajax. In future, all consumers can be reviewed to simplify some of the options, where there is historical cruft.
 *
 * @param {string}   action   - the action to send
 * @param {*}        data     - data to send
 * @param {Function} callback - will be called with the results
 * @param {object}   options  -further options. Relevant properties include:
 * - [json_parse=true] - whether to JSON parse the results
 * - [alert_on_error=true] - whether to show an alert box if there was a problem (otherwise, suppress it)
 * - [action='aios_ajax'] - what to send as the action parameter on the AJAX request (N.B. action parameter to this function goes as the 'subaction' parameter on the AJAX request)
 * - [nonce=aios_ajax_nonce] - the nonce value to send.
 * - [nonce_key='nonce'] - the key value for the nonce field
 * - [timeout=null] - set a timeout after this number of seconds (or if null, none is set)
 * - [async=true] - control whether the request is asynchronous (almost always wanted) or blocking (would need to have a specific reason)
 * - [type='POST'] - GET or POST
 */
function aios_send_command(action, data, callback, options) {

    default_options = {
        json_parse: true,
        alert_on_error: true,
        action: 'aios_ajax',
        nonce: aios_data.ajax_nonce,
        nonce_key: 'nonce',
        timeout: null,
        async: true,
        type: 'POST'
    };

    if ('undefined' === typeof options) options = {};

    for (var opt in default_options) {
        if (!options.hasOwnProperty(opt)) { options[opt] = default_options[opt]; }
    }

    var ajax_data = {
        action: options.action,
        subaction: action,
    };

    ajax_data[options.nonce_key] = options.nonce;
    ajax_data.data = data;

    var ajax_opts = {
        type: options.type,
        url: ajaxurl,
        data: ajax_data,
        success: function(response, status) {
            if (options.json_parse) {
                try {
                    var resp = aios_parse_json(response);
                } catch (e) {
                    if ('function' == typeof options.error_callback) {
                        return options.error_callback(response, e, 502, resp);
                    } else {
                        console.log(e);
                        console.log(response);
                        if (options.alert_on_error) { alert(aios_trans.unexpected_response+' '+response); }
                        return;
                    }
                }
                if (resp.hasOwnProperty('fatal_error')) {
                    if ('function' == typeof options.error_callback) {
                        // 500 is internal server error code
                        return options.error_callback(response, status, 500, resp);
                    } else {
                        console.error(resp.fatal_error_message);
                        if (options.alert_on_error) { alert(resp.fatal_error_message); }
                        return false;
                    }
                }
                if ('function' == typeof callback) callback(resp, status, response);
            } else {
                if ('function' == typeof callback) callback(response, status);
            }
        },
        error: function(response, status, error_code) {
            if ('function' == typeof options.error_callback) {
                options.error_callback(response, status, error_code);
            } else {
                console.log("aios_send_command: error: "+status+" ("+error_code+")");
                console.log(response);
            }
        },
        dataType: 'text',
        async: options.async
    };

    if (null != options.timeout) { ajax_opts.timeout = options.timeout; }

    jQuery.ajax(ajax_opts);

}

/**
 * Parse JSON string, including automatically detecting unwanted extra input and skipping it
 *
 * @param {string}  json_mix_str - JSON string which need to parse and convert to object
 * @param {boolean} analyse		 - if true, then the return format will contain information on the parsing, and parsing will skip attempting to JSON.parse() the entire string (will begin with trying to locate the actual JSON)
 *
 * @throws SyntaxError|String (including passing on what JSON.parse may throw) if a parsing error occurs.
 *
 * @returns Mixed parsed JSON object. Will only return if parsing is successful (otherwise, will throw). If analyse is true, then will rather return an object with properties (mixed)parsed, (integer)json_start_pos and (integer)json_end_pos
 */
function aios_parse_json(json_mix_str, analyse) {

    analyse = ('undefined' === typeof analyse) ? false : true;

    // Just try it - i.e. the 'default' case where things work (which can include extra whitespace/line-feeds, and simple strings, etc.).
    if (!analyse) {
        try {
            var result = JSON.parse(json_mix_str);
            return result;
        } catch (e) {
            console.log('AIOS: Exception when trying to parse JSON (1) - will attempt to fix/re-parse based upon first/last curly brackets');
            console.log(json_mix_str);
        }
    }

    var json_start_pos = json_mix_str.indexOf('{');
    var json_last_pos = json_mix_str.lastIndexOf('}');

    // Case where some php notice may be added after or before json string
    if (json_start_pos > -1 && json_last_pos > -1) {
        var json_str = json_mix_str.slice(json_start_pos, json_last_pos + 1);
        try {
            var parsed = JSON.parse(json_str);
            if (!analyse) { console.log('AIOS: JSON re-parse successful'); }
            return analyse ? { parsed: parsed, json_start_pos: json_start_pos, json_last_pos: json_last_pos + 1 } : parsed;
        } catch (e) {
            console.log('AIOS: Exception when trying to parse JSON (2) - will attempt to fix/re-parse based upon bracket counting');

            var cursor = json_start_pos;
            var open_count = 0;
            var last_character = '';
            var inside_string = false;

            // Don't mistake this for a real JSON parser. Its aim is to improve the odds in real-world cases seen, not to arrive at universal perfection.
            while ((open_count > 0 || cursor == json_start_pos) && cursor <= json_last_pos) {

                var current_character = json_mix_str.charAt(cursor);

                if (!inside_string && '{' == current_character) {
                    open_count++;
                } else if (!inside_string && '}' == current_character) {
                    open_count--;
                } else if ('"' == current_character && '\\' != last_character) {
                    inside_string = inside_string ? false : true;
                }

                last_character = current_character;
                cursor++;
            }
            console.log("Started at cursor="+json_start_pos+", ended at cursor="+cursor+" with result following:");
            console.log(json_mix_str.substring(json_start_pos, cursor));

            try {
                var parsed = JSON.parse(json_mix_str.substring(json_start_pos, cursor));
                console.log('AIOS: JSON re-parse successful');
                return analyse ? { parsed: parsed, json_start_pos: json_start_pos, json_last_pos: cursor } : parsed;
            } catch (e) {
                // Throw it again, so that our function works just like JSON.parse() in its behaviour.
                throw e;
            }
        }
    }

    throw "AIOS: could not parse the JSON";

}

jQuery(function($) {
    //Add Generic Admin Dashboard JS Code in this file

    //Media Uploader - start
    function aiowps_attach_media_uploader(key) {
        jQuery('#' + key + '_button').on('click', function() {
                text_element = jQuery('#' + key).attr('name');
                button_element = jQuery('#' + key + '_button').attr('name');
                tb_show('All In One Security - Please Select a File', 'media-upload.php?referer=aiowpsec&amp;TB_iframe=true&amp;post_id=0width=640&amp;height=485');
                return false;
        });		
        window.send_to_editor = function(html) {
                var self_element = text_element;
                fileurl = jQuery(html).attr('href');
                jQuery('#' + self_element).val(fileurl);
                tb_remove();
        };
    }

    var current_admin_page = getParameterByName('page'); //check query arg of loaded page to see if a gallery needs wm processing
    if(current_admin_page == 'aiowpsec_maintenance'){
        //don't load custom uploader stuff because we want to use standard wp uploader code
    }else{
        aiowps_attach_media_uploader('aiowps_htaccess_file');
        aiowps_attach_media_uploader('aiowps_wp_config_file');
        aiowps_attach_media_uploader('aiowps_import_settings_file');
        aiowps_attach_media_uploader('aiowps_db_file'); //TODO - for future use when we implement DB restore

    }
    //End of Media Uploader
    
    //Triggers the more info toggle link
    $(".aiowps_more_info_body").hide();//hide the more info on page load
    $('.aiowps_more_info_anchor').on('click', function() {
        $(this).next(".aiowps_more_info_body").animate({ "height": "toggle"});
        var toogle_char_ref = $(this).find(".aiowps_more_info_toggle_char");
        var toggle_char_value = toogle_char_ref.text();
        if(toggle_char_value === "+"){
            toogle_char_ref.text("-");
        }
        else{
             toogle_char_ref.text("+");
        }
    });
    //End of more info toggle

    //This function uses javascript to retrieve a query arg from the current page URL
    function getParameterByName(name) {
        var url = window.location.href;
        name = name.replace(/[\[\]]/g, "\\$&");
        var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
            results = regex.exec(url);
        if (!results) return null;
        if (!results[2]) return '';
        return decodeURIComponent(results[2].replace(/\+/g, " "));
    }

	// Start of brute force attack prevention toggle handling
	jQuery('input[name=aiowps_enable_brute_force_attack_prevention]').on('click', function() {
		jQuery('input[name=aiowps_brute_force_secret_word]').prop('disabled', !jQuery(this).prop('checked'));
		jQuery('input[name=aiowps_cookie_based_brute_force_redirect_url]').prop('disabled', !jQuery(this).prop('checked'));
		jQuery('input[name=aiowps_brute_force_attack_prevention_pw_protected_exception]').prop('disabled', !jQuery(this).prop('checked'));
		jQuery('input[name=aiowps_brute_force_attack_prevention_ajax_exception]').prop('disabled', !jQuery(this).prop('checked'));	
	});
	// End of brute force attack prevention toggle handling

	/**
	 * Take a backup with UpdraftPlus if possible.
	 *
	 * @param {String}   file_entities
	 *
	 * @return void
	 */
	function take_a_backup_with_updraftplus(file_entities) {
		// Set default for file_entities to empty string
		if ('undefined' == typeof file_entities) file_entities = '';
		var exclude_files = file_entities ? 0 : 1;

		if (typeof updraft_backupnow_inpage_go === 'function') {
			updraft_backupnow_inpage_go(function () {
				// Close the backup dialogue.
				$('#updraft-backupnow-inpage-modal').dialog('close');
			}, file_entities, 'autobackup', 0, exclude_files, 0);
		}
	}
    if (jQuery('#aios-manual-db-backup-now').length) {
        jQuery('#aios-manual-db-backup-now').on('click', function (e) {
            e.preventDefault();
            take_a_backup_with_updraftplus();
        });
    }

    // Hide 2FA premium advertisement
    if (jQuery('.tfa-premium').length) {
        jQuery('.tfa-premium').hide();
    }


	// Start of trash spam comments toggle handling
	jQuery('input[name=aiowps_enable_trash_spam_comments]').on('click', function() {
		jQuery('input[name=aiowps_trash_spam_comments_after_days]').prop('disabled', !jQuery(this).prop('checked'));
	});
	// End of trash spam comments toggle handling
});
