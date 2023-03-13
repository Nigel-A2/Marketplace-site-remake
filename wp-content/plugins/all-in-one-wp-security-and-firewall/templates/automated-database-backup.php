<?php if (!defined('AIO_WP_SECURITY_PATH')) die('No direct access allowed'); ?>

<div class="postbox">
	<h3 id="automated-scheduled-backups-heading" class="hndle"><label for="title"><?php _e('Automated scheduled backups', 'all-in-one-wp-security-and-firewall'); ?></label></h3>
	<div class="inside">
		<p>
			<?php
			if (empty($install_activate_link)) {
				$link_title = __('Automate backup in the UpdraftPlus plugin', 'all-in-one-wp-security-and-firewall');
			?>
				<a href="<?php echo add_query_arg(
					array(
						'page' => 'updraftplus',
						'tab'  => 'settings',
					),
				admin_url('options-general.php'));
											?>" title="<?php echo $link_title; ?>" alt="<?php echo $link_title; ?>">
				<?php
				echo __('The AIOS 5.0.0 version release has removed the automated backup feature.', 'all-in-one-wp-security-and-firewall') . ' ' .
					__('The AIOS automated backup had issues that made it less robust than we could be happy with.', 'all-in-one-wp-security-and-firewall') . ' ' .
					__('Follow this link to automate backups in the superior UpdraftPlus backup plugin.', 'all-in-one-wp-security-and-firewall');
				?>
				</a>
			<?php
			} else {
				echo wp_kses($install_activate_link, array('a' => array('title' => array(), 'href' => array())));
			}
			?>
		</p>

	</div>
</div>
