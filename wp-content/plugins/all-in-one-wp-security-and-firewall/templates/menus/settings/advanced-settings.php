<div class="postbox">
	<h3 class="hndle"><label for="title"><?php _e('IP address detection settings', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
	<div class="inside">
		<div class="aio_blue_box">
			<?php
			echo '<p>'.__('The IP address detection settings allow you to specify how visitors\' IP addresses are made known to PHP (and hence to WordPress and its plugins).', 'all-in-one-wp-security-and-firewall').
				'<br />'.__('Usually, this is automatic and there is only one choice.', 'all-in-one-wp-security-and-firewall').
				' '.__('However in some setups, such as those using proxies (including load-balancers and security firewalls like Cloudflare), it may be necessary to set this manually.', 'all-in-one-wp-security-and-firewall').
				'</p><p><strong>'.__('Attention', 'all-in-one-wp-security-and-firewall').':</strong> '.__('It is important to set this correctly - otherwise you may make it possible for a hacker to ban all your visitors (e.g. via banning Cloudflare from connecting to you) instead of being banned himself.', 'all-in-one-wp-security-and-firewall').'</p><p>'.__("The default is to use the REMOTE_ADDR PHP server variable. If this variable does not contain the visitor's IP address, then whilst you can make a different selection below, it is better to ask your web hosting company to have it correctly set.", 'all-in-one-wp-security-and-firewall') . ' ' .
				__("This is the most secure setup, because when set correctly it is immune from being spoofed by an attacker.", 'all-in-one-wp-security-and-firewall').'</p>';
			?>
		</div>

		<?php
		if (empty($server_suitable_ip_methods)) {
			echo '<br><strong>'.__('You have no available IP address detection method(s); you must contact your web hosting company.', 'all-in-one-wp-security-and-firewall').'</strong>';
		}
		?>

		<br><br>
		<?php
		echo sprintf(__('Your detected IP address according to %s:', 'all-in-one-wp-security-and-firewall'), 'Cloudflare');
		?>
		<span id="aios-cloudflare-ip-address"></span>
		<br>
		<?php
		echo sprintf(__('Your detected IP address according to %s:', 'all-in-one-wp-security-and-firewall'), 'ipify (IPv4)');
		?>
		<span id="aios-ipify-ip-address"></span>
		<br>
		<?php
		echo sprintf(__('Your detected IP address according to %s:', 'all-in-one-wp-security-and-firewall'), 'ipify (IPv6)');
		?>
		<span id="aios-ipify-ip-64-address"></span>
		<?php
		if ($is_localhost) {
			echo '<br>';
			echo __("If your site is setup on localhost, you won't see your external IP address using your server's IP detection setting; but on a localhost-served site (not available to the outside world), the setting is irrelevant and can be ignored.", 'all-in-one-wp-security-and-firewall');
		}
		?>
		<style>
			#aiowps_ip_retrieve_method option:disabled { color: #cccccc; }
			.aios-ip-error { color: #ff0000; }
		</style>
		<form action="" method="POST">
			<?php wp_nonce_field('aiowpsec-ip-settings-nonce'); ?>
			<table class="form-table">
				<tr valign="top">
					<td>
						<select id="aiowps_ip_retrieve_method" name="aiowps_ip_retrieve_method">
							<?php
							$current_ip_retrieve_method = $aio_wp_security->configs->get_value('aiowps_ip_retrieve_method');
							foreach ($ip_retrieve_methods as $ip_method_id => $vals) {
							?>
								<option value="<?php echo esc_attr($ip_method_id); ?>" <?php selected($current_ip_retrieve_method, $ip_method_id); ?> <?php disabled($vals['is_enabled'], false); ?>><?php echo $vals['ip_method']; ?></option>
							<?php
							}
							?>
						</select>
						<span class="description">
							<?php _e("Choose a \$_SERVER variable you would like to detect visitors' IP address using.", 'all-in-one-wp-security-and-firewall'); ?>
						</span>
						<span class="aiowps_more_info_anchor"><span class="aiowps_more_info_toggle_char">+</span><span class="aiowps_more_info_toggle_text"><?php _e('More Info', 'all-in-one-wp-security-and-firewall'); ?></span></span>
						<div class="aiowps_more_info_body">
							<p class="description">
								<?php
								_e('If your chosen server variable fails the plugin will automatically fall back to retrieving the IP address from $_SERVER["REMOTE_ADDR"]', 'all-in-one-wp-security-and-firewall');
								?>
							</p>
						</div>
						<div class="description">
							<?php
							echo __('Your IP address if using this setting:', 'all-in-one-wp-security-and-firewall') . ' ';
							?>
							<span id="aios-ip-address-of-method"><?php _e('fetching...', 'all-in-one-wp-security-and-firewall'); ?></span>
						</div>
						<script>
							jQuery(function() {
								var get_ip_error_count = 0;
								var unexpected_response_text = '<?php _e('Unexpected response:', 'updraftplus'); ?> ';
								var getting_text = ' ' + '<?php _e('getting...', 'all-in-one-wp-security-and-firewall'); ?>'


								jQuery('#aios-cloudflare-ip-address').html(getting_text);

								function aios_get_ip_error() {
									get_ip_error_count++;
									if (get_ip_error_count > 3) {
										var additional_error_msg = ' ' + '(<?php _e('look-up possibly blocked by an ad-blocker or similar tool', 'all-in-one-wp-security-and-firewall'); ?>)';
										jQuery('#aios-cloudflare-ip-address').html(getting_text);
										jQuery('#aios-ipify-ip-address').html(getting_text);
										jQuery('#aios-ipify-ip-64-address').html(getting_text);


									}
								}

								jQuery.ajax({
									type: 'GET',
									url: 'https://www.cloudflare.com/cdn-cgi/trace',
									success: function (response, status) {
										try {
											// Convert key-value pairs to JSON
											// https://stackoverflow.com/a/39284735/452587
											var data = response.trim().split('\n').reduce(function (obj, pair) {
												pair = pair.split('=');
												return obj[pair[0]] = pair[1], obj;
											}, {});
										} catch (e) {
											var error_msg =
												unexpected_response_text+' '+response;
											jQuery('#aios-cloudflare-ip-address').addClass('aios-ip-error').html(error_msg);
											console.log(e);
											console.log(response);
											return;
										}

										if (data.hasOwnProperty('ip')) {
											jQuery('#aios-cloudflare-ip-address').html(data.ip);
										} else {
											var error_msg = "failure: The IP line doesn't exist in the response. Response: " + response  + " Status: " + status;
											jQuery('#aios-cloudflare-ip-address').addClass('aios-ip-error').html(error_msg);
											console.log(error_msg);
											console.log(response);
										}
									},
									error: function (response, status, error_code) {
										var error_msg = "failure: " + status + " (" + error_code + ")";
										jQuery('#aios-cloudflare-ip-address').addClass('aios-ip-error').html(error_msg);
										console.log(error_msg);
										console.log(response);
										aios_get_ip_error();
									}
								});

								function aios_fill_ipify_ip_address($is_ipv6) {
									if ($is_ipv6) {
										var url = 'https://api64.ipify.org?format=json';
										var selector = '#aios-ipify-ip-64-address';
										var error_msg = 'IPv4 ';
									} else { // IPv4
										var url = 'https://api.ipify.org?format=json';
										var selector = '#aios-ipify-ip-address';
										var error_msg = 'IPv6 ';
									}

									jQuery(selector).html(getting_text);

									jQuery.ajax({
										type: 'GET',
										dataType: 'json',
										url: url,
										success: function (response, status) {
											if (response.hasOwnProperty('ip')) {
												jQuery(selector).html(response.ip);
											} else {
												error_msg += "failure: The IP line doesn't exist in the response. Response: " + JSON.stringify(response)  + " Status: " + status;
												jQuery(selector).addClass('aios-ip-error').html(error_msg);
												console.log(error_msg);
												console.log(response);
											}
										},
										error: function (response, status, error_code) {
											error_msg += "failure: " + status + " (" + error_code + ")";
											jQuery(selector).addClass('aios-ip-error').html(error_msg);
											console.log(error_msg);
											console.log(response);
											aios_get_ip_error();
										}
									});
								}
								aios_fill_ipify_ip_address(false);
								aios_fill_ipify_ip_address(true); // IPv6


								jQuery('#aiowps_ip_retrieve_method').on('change', function() {
									jQuery('#aios-ip-address-of-method').html(getting_text);
									var ip_retrieve_method = jQuery('#aiowps_ip_retrieve_method').val();

									// If selected disabled option, we get null value.
									// Previously saved value and the option is disabled now.
									if (null == ip_retrieve_method) {
										jQuery('#aios-ip-address-of-method').html('');
										return;
									}

									aios_send_command('get_ip_address_of_given_method', {
										ip_retrieve_method: jQuery('#aiowps_ip_retrieve_method').val()
									}, function (resp) {
										jQuery('#aios-ip-address-of-method').html("<?php _e('getting...', 'all-in-one-wp-security-and-firewall'); ?>");
										if (resp.hasOwnProperty('ip_address')) {
											jQuery('#aios-ip-address-of-method').html(resp.ip_address);
										} else {
											alert(unexpected_response_text + JSON.stringify(resp));
											console.log(resp);
										}
									}, {
										error_callback: function (response, status, error_code, resp) {
											if (typeof resp !== 'undefined' && resp.hasOwnProperty('fatal_error')) {
												console.error(resp.fatal_error_message);
												alert(resp.fatal_error_message);
											} else {
												var error_message = "aios_send_command: error: " + status + " (" + error_code + ")";
												console.log(error_message);
												alert(error_message);
												console.log(response);
											}
										}
									});
								});
								jQuery('#aiowps_ip_retrieve_method').change();
							});
						</script>
					</td>
				</tr>
			</table>
			<input type="submit" name="aiowps_save_advanced_settings" value="<?php _e('Save settings', 'all-in-one-wp-security-and-firewall');?>" class="button-primary" />
		</form>
	</div>
</div>
