<?php
if (!defined('AIO_WP_SECURITY_PATH')) die('No direct access allowed');

if (!AIOWPSecurity_Utility::is_apache_server()) {
	?>
	<div class="aio_red_box">
		<p>
			<?php
			echo '<strong>' . __('Attention:', 'all-in-one-wp-security-and-firewall') . '</strong> ' . __('This feature works only on the Apache server.', 'all-in-one-wp-security-and-firewall') . ' ';
			echo htmlspecialchars(sprintf(__("You are using the non-apache server %s, so this feature won't work on your site.", 'all-in-one-wp-security-and-firewall'), esc_html(AIOWPSecurity_Utility::get_server_software())));
			?>
		</p>
	</div>
	<?php
}