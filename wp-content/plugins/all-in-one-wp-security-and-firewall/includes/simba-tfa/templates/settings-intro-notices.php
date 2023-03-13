<p class="simba_tfa_personal_settings_notice simba_tfa_intro_notice">
<?php

echo apply_filters('simba_tfa_message_personal_settings', __('These are your personal settings.', 'all-in-one-wp-security-and-firewall').' '.__('Nothing you change here will have any effect on other users.', 'all-in-one-wp-security-and-firewall'));

if (is_multisite()) {
	if (is_super_admin()) {
		// Since WP 4.9
		$main_site_id = function_exists('get_main_site_id') ? get_main_site_id() : 1;
		$switched = switch_to_blog($main_site_id);
		echo ' <a href="'.esc_url($simba_tfa->get_site_wide_administration_url()).'">'.__('The site-wide administration options are here.', 'all-in-one-wp-security-and-firewall').'</a>';
		if ($switched) restore_current_blog();
	}
} elseif (current_user_can($simba_tfa->get_management_capability())) {
	echo ' <a href="'.esc_url($simba_tfa->get_site_wide_administration_url()).'">'.__('The site-wide administration options are here.', 'all-in-one-wp-security-and-firewall').'</a>';
}

?>
</p>

<p class="simba_tfa_verify_tfa_notice simba_tfa_intro_notice"><strong>

	<?php echo apply_filters('simbatfa_message_you_should_verify', __('If you activate two-factor authentication, then verify that your two-factor application and this page show the same One-Time Password (within a minute of each other) before you log out.', 'all-in-one-wp-security-and-firewall')); ?></strong>

	<?php if (current_user_can($simba_tfa->get_management_capability())) { ?>
		<a href="<?php echo esc_url($simba_tfa->get_faq_url()); ?>"><?php _e('You should also bookmark the FAQs, which explain how to de-activate the plugin even if you cannot log in.', 'all-in-one-wp-security-and-firewall');?></a>
	<?php } ?>
</p>
