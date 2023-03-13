<?php
if (!defined('ABSPATH')) {
	exit; //Exit if accessed directly
}

class AIOWPSecurity_Utility_Firewall {

	/**
	 * Returned if the user is required to setup auto_prepend_file manually
	 *
	 * @var null
	 */
	const MANUAL_SETUP = null;

	/**
	 * Returns the current path to our firewall file
	 *
	 * @return string
	 */
	public static function get_firewall_path() {
		return wp_normalize_path(AIO_WP_SECURITY_PATH.'/classes/firewall/wp-security-firewall.php');
	}

	/**
	 * Returns the firewall rules path.
	 *
	 * @return string
	 */
	public static function get_firewall_rules_path() {
		$upload_dir_info = wp_get_upload_dir();
		$firewall_rules_path = trailingslashit($upload_dir_info['basedir'].'/aios/firewall-rules');
		wp_mkdir_p($firewall_rules_path);
		return wp_normalize_path($firewall_rules_path);
	}

	/**
	 * Determines whether we're on the firewall page
	 *
	 * @return boolean
	 */
	public static function is_firewall_page() {
		global $pagenow;
		return ('admin.php' == $pagenow && isset($_GET['page']) && false !== strpos($_GET['page'], AIOWPSEC_MENU_SLUG_PREFIX.'_firewall'));
	}

	/**
	 * Returns the current path to our bootstrap file
	 *
	 * @return string
	 */
	public static function get_bootstrap_path() {
		return path_join(AIOWPSecurity_Utility_File::get_home_path(), 'aios-bootstrap.php');
	}

	/**
	 * Returns the full path to our mu-plugin
	 *
	 * @return string
	 */
	public static function get_muplugin_path() {
		return path_join(AIOWPSecurity_Utility_File::get_mu_plugin_dir(), 'aiowpsec-firewall-loader.php');
	}

	/**
	 * Returns our managed mu-plugin file
	 *
	 * @return AIOWPSecurity_Block_Muplugin
	 */
	public static function get_muplugin_file() {
		return new AIOWPSecurity_Block_Muplugin(AIOWPSecurity_Utility_Firewall::get_muplugin_path());
	}

	/**
	 * Returns our managed wp-config file
	 *
	 * @return AIOWPSecurity_Block_WpConfig
	 */
	public static function get_wpconfig_file() {
		return new AIOWPSecurity_Block_WpConfig(AIOWPSecurity_Utility_File::get_wp_config_file_path());
	}

	/**
	 * Returns our managed bootstrap file
	 *
	 * @return AIOWPSecurity_Block_Bootstrap
	 */
	public static function get_bootstrap_file() {
		return new AIOWPSecurity_Block_Bootstrap(AIOWPSecurity_Utility_Firewall::get_bootstrap_path());
	}

	/**
	 * Gets the auto_prepend_file directive, if already set
	 *
	 * @param string $source - where to check for the directive
	 * @return string - returns the directive if set, or empty string if not set
	 */
	public static function get_already_set_directive($source = '') {

		if (!empty($source)) {
			clearstatcache();
			if (file_exists($source) && is_readable($source)) {
					
				$vals = @parse_ini_file($source); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- ignore this
	
				if (false !== $vals && isset($vals['auto_prepend_file'])) {
					return $vals['auto_prepend_file'];
				}
			}
		} else {
			$directive = ini_get('auto_prepend_file');
			if (false !== $directive) {
				return $directive;
			}
		}
	
		return '';
	}

	/**
	 * Returns the file that's necessary to load our firewall
	 *
	 * @return AIOWPSecurity_Block_File|null   file needed to load the firewall
	 */
	public static function get_server_file() {
		$server_type = AIOWPSecurity_Utility::get_server_type();
		$is_cgi = false;
		$sapi = PHP_SAPI;
	
		if (false !== stripos($sapi, 'cgi')) {
			$is_cgi = true;
		}
		
		if (AIOWPSecurity_Utility::UNSUPPORTED_SERVER_TYPE === $server_type) {
			return self::MANUAL_SETUP;

		} elseif (false === $is_cgi && 'apache' === $server_type) {
		
			$htpath = path_join(get_home_path(), '.htaccess');
			return new AIOWPSecurity_Block_Htaccess($htpath);
			
		} elseif ('litespeed' === $server_type || 'litespeed' === $sapi) {

			$htpath = path_join(get_home_path(), '.htaccess');
			return new AIOWPSecurity_Block_Litespeed($htpath);
		   
		} else {
			$userini = path_join(get_home_path(), '.user.ini');
			return new AIOWPSecurity_Block_Userini($userini);
		}

	}

	/**
	 * Attempts to remove our firewall.
	 *
	 * @return void
	 */
	public static function remove_firewall() {
		global $aio_wp_security;
		
		$firewall_files = array(
			'server'    => AIOWPSecurity_Utility_Firewall::get_server_file(),
			'bootstrap' => AIOWPSecurity_Utility_Firewall::get_bootstrap_file(),
			'wpconfig'  => AIOWPSecurity_Utility_Firewall::get_wpconfig_file(),
			'muplugin'  => AIOWPSecurity_Utility_Firewall::get_muplugin_file(),
		);

		foreach ($firewall_files as $file) {
			if (AIOWPSecurity_Utility_Firewall::MANUAL_SETUP === $file) {
				continue;
			}

			if (true === $file->contains_contents()) {

				$removed = $file->remove_contents();

				if (is_wp_error($removed)) {
					global $aio_wp_security;

					$error_message = $removed->get_error_message();
					$error_message .= ' - ';
					$error_message .= $removed->get_error_data();
					$aio_wp_security->debug_logger->log_debug($error_message, 4);
				}
			}
			
		}

		//Delete our mu-plugin, if it's created
		clearstatcache();
		$muplugin_path = $firewall_files['muplugin'];
		if (file_exists($muplugin_path)) {
			@unlink($muplugin_path); // phpcs:ignore Generic.PHP.NoSilencedErrors.Discouraged -- ignore this
		}

		$aio_wp_security->configs->set_value('aios_firewall_dismiss', false);
		$aio_wp_security->configs->save_config();

	}
}
