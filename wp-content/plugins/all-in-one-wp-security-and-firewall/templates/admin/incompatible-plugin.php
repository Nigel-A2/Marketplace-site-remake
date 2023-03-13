<?php if (!defined('ABSPATH')) die('Access denied.'); ?>

<div class="wrap">

	<div>
		<h1><?php _e('Two Factor Authentication', 'all-in-one-wp-security-and-firewall'); ?></h1>
	</div>

	<div class="error">
		<h3><?php _e('Two Factor Authentication currently disabled', 'all-in-one-wp-security-and-firewall');?></h3>
		<p>
			<?php printf(__('Two factor authentication in All In One WP Security is currently disabled because the incompatible plugin %s is active.', 'all-in-one-wp-security-and-firewall'), $incompatible_plugin); ?>
		</p>
	</div>
	
	<div><?php printf(__('Two factor authentication in All In One WP Security is currently disabled because the incompatible plugin %s is active.', 'all-in-one-wp-security-and-firewall'), $incompatible_plugin); ?></div>

</div>
