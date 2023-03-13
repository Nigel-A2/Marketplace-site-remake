<?php if (!defined('AIO_WP_SECURITY_PATH')) die('No direct access allowed'); ?>
<span class="aiowps_more_info_anchor"><span class="aiowps_more_info_toggle_char">+</span><span class="aiowps_more_info_toggle_text"><?php _e('More Info', 'all-in-one-wp-security-and-firewall'); ?></span></span>
	<div class="aiowps_more_info_body">
	<?php
		echo '<p class="description">' . __('Each IP address must be on a new line.', 'all-in-one-wp-security-and-firewall') . '</p>';
		echo '<p class="description">' . __('To specify an IPv4 range use a wildcard "*" character. Acceptable ways to use wildcards is shown in the examples below:', 'all-in-one-wp-security-and-firewall') . '</p>';
		echo '<p class="description">' . __('Example 1: 195.47.89.*', 'all-in-one-wp-security-and-firewall') . '</p>';
		echo '<p class="description">' . __('Example 2: 195.47.*.*', 'all-in-one-wp-security-and-firewall') . '</p>';
		echo '<p class="description">' . __('Example 3: 195.*.*.*', 'all-in-one-wp-security-and-firewall') . '</p>';
		echo '<p class="description">' . __('To specify an IPv6 range use CIDR format as shown in the examples below:', 'all-in-one-wp-security-and-firewall') . '</p>';
		echo '<p class="description">' . __('Example 4: 2401:4900:54c3:af15:2:2:5dc0:0/112', 'all-in-one-wp-security-and-firewall') . '</p>';
		echo '<p class="description">' . __('Example 5: 2001:db8:1263::/48', 'all-in-one-wp-security-and-firewall') . '</p>';
	?>
</div>