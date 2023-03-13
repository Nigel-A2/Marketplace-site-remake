<?php if (!defined('ABSPATH')) die('No direct access.'); ?>

<div id="tfa_trusted_devices_box_inner">

	<p><?php _e('Trusted devices are devices which have previously logged in with a second factor, belonging to users who have been permitted to mark devices as trusted, and for which the user checked the checkbox on the login form to trust the device.', 'all-in-one-wp-security-and-firewall'); ?></p>

	<?php

	global $current_user;

	$trusted_devices = $this->user_get_trusted_devices($current_user->ID);

	if (empty($trusted_devices)) {
		echo '<em>'.__('(none)', 'all-in-one-wp-security-and-firewall').'</em>';
	}

	foreach ($trusted_devices as $device_id => $device) {
		
		if (!isset($device['token']) || '' == $device['token']) continue;
		
		$user_agent = empty($device['user_agent']) ? __('(unspecified)', 'all-in-one-wp-security-and-firewall'): $device['user_agent'];
		
		echo '<span class="simbatfa_trusted_device">'.sprintf(__('User agent %s logged in from IP address %s and is trusted until %s', 'all-in-one-wp-security-and-firewall'), '<strong>'.htmlspecialchars($user_agent).'</strong>', '<strong><a target="_blank" href="https://ipinfo.io/'.$device['ip'].'">'.htmlspecialchars($device['ip']).'</a></strong>', '<strong>'.date_i18n(get_option('time_format').' '.get_option('date_format'), $device['until']).'</strong>').' - <a href="#" class="simbatfa-trust-remove" data-trusted-device-id="'.esc_attr($device_id).'">'.__('Remove trust', 'all-in-one-wp-security-and-firewall').'</a></span><br>';
		
	}

	?>

</div>
