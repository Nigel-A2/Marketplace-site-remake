<?php
// @codingStandardsIgnoreStart
/*
Plugin Name: All In One WP Security
Version: 5.1.0
Plugin URI: https://wordpress.org/plugins/all-in-one-wp-security-and-firewall/
Update URI: https://wordpress.org/plugins/all-in-one-wp-security-and-firewall/
Author: All In One WP Security & Firewall Team
Author URI: https://teamupdraft.com/
Description: All round best WordPress security plugin!
Text Domain: all-in-one-wp-security-and-firewall
Domain Path: /languages
License: GPLv3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
Requires at least: 5.0
Requires PHP: 5.6
Network: true
*/
// @codingStandardsIgnoreEnd

if (!defined('ABSPATH')) {
	exit; // Exit if accessed directly
}

if (version_compare(phpversion(), '5.6.0', '<')) {
	add_action('all_admin_notices', 'aiowps_php_version_notice');
	return;
}

/**
 * The notice to display if the user does not have the required PHP version
 *
 * @return void
 */
function aiowps_php_version_notice() {

	if (!current_user_can('manage_options')) {
		return;
	}

	?>
	<div class="notice notice-error is-dismissible">
		<p><strong><?php _e('All In One WP Security and Firewall', 'all-in-one-wp-security-and-firewall'); ?></strong></p>
		<p><?php _e('All In One WP Security and Firewall plugin has been deactivated.', 'all-in-one-wp-security-and-firewall');?></p>
		<p><?php printf(htmlspecialchars(__('This plugin requires PHP version %s.', 'all-in-one-wp-security-and-firewall')), '<strong>5.6+</strong>'); ?></p>
		<p><?php printf(htmlspecialchars(__('Your current PHP version is %s.', 'all-in-one-wp-security-and-firewall')), '<strong>'.phpversion().'</strong>'); ?></p>
		<p><?php _e('You will need to ask your web hosting company to upgrade.', 'all-in-one-wp-security-and-firewall'); ?></p>
	</div>
	<?php
	deactivate_plugins(__FILE__, true);

}

require_once(__DIR__.'/wp-security-core.php');

register_activation_hook(__FILE__, array('AIO_WP_Security', 'activate_handler'));//activation hook
register_deactivation_hook(__FILE__, array('AIO_WP_Security', 'deactivation_handler'));// deactivation hook
register_uninstall_hook(__FILE__, array('AIO_WP_Security', 'uninstall_handler'));// uninstallation hook

function aiowps_show_plugin_settings_link($links, $file) {
	if (plugin_basename(__FILE__) == $file) {
		$settings_link = '<a href="admin.php?page=aiowpsec_settings">' . __('Settings') . '</a>';
		array_unshift($links, $settings_link);
	}
	return $links;
}
add_filter('plugin_action_links', 'aiowps_show_plugin_settings_link', 10, 2);

function aiowps_ms_handle_new_site($new_site) {
	global $wpdb;
	$plugin_basename = plugin_basename(__FILE__);
	if (is_plugin_active_for_network($plugin_basename)) {
		if (!class_exists('AIOWPSecurity_Installer')) {
			include_once('classes/wp-security-installer.php');
		}
		$old_blog = $wpdb->blogid;
		switch_to_blog($new_site->blog_id);
		AIOWPSecurity_Installer::create_db_tables();
		switch_to_blog($old_blog);
	}


}
// The priority is 20 instead of 10 because all subsite's tables are created by `add_action( 'wp_initialize_site', 'wp_initialize_site', 10, 2 );`. We should call  the `aiowps_ms_handle_new_site` function after all subsite's tables are created because the  `aiowps_ms_handle_new_site` function adds option in subsite's table.
add_action('wp_initialize_site', 'aiowps_ms_handle_new_site', 20, 1);
