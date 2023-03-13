<?php
if ( ! defined( 'ABSPATH' ) ) {
	die( 'You are not allowed to call this page directly.' );
}

/**
 * @since 5.9.2
 */
class WPBDP_Addons_Controller {

	public static function load_hooks() {
		add_action( 'wp_ajax_wpbdp_install_addon', 'WPBDP_Addons_Controller::ajax_install_addon' );
		add_action( 'wp_ajax_wpbdp_activate_addon', 'WPBDP_Addons_Controller::ajax_activate_addon' );
	}

	/**
	 * @since 5.9.2
	 */
	public static function ajax_install_addon() {
		self::install_addon_permissions();

		self::download_and_activate();

		// Send back a response.
		echo json_encode( __( 'Your plugin has been installed. Please reload the page to see more options.', 'business-directory-plugin' ) );
		wp_die();
	}

	/**
	 * @since 5.9.2
	 */
	protected static function download_and_activate() {
		// Set the current screen to avoid undefined notices.
		if ( is_admin() ) {
			global $hook_suffix;
			set_current_screen();
		}

		self::maybe_show_cred_form();

		$installed = self::install_addon();
		if ( is_array( $installed ) && isset( $installed['message'] ) ) {
			return $installed;
		}
		self::maybe_activate_addon( $installed );
	}

	/**
	 * @since 5.9.2
	 */
	protected static function maybe_show_cred_form() {
		if ( ! function_exists( 'request_filesystem_credentials' ) ) {
			include_once ABSPATH . 'wp-admin/includes/file.php';
		}

		// Start output bufferring to catch the filesystem form if credentials are needed.
		ob_start();

		$show_form = false;
		$method    = '';
		$url       = add_query_arg( array( 'page' => 'wpbdp-settings' ), admin_url( 'admin.php' ) );
		$url       = esc_url_raw( $url );
		$creds     = request_filesystem_credentials( $url, $method );

		if ( false === $creds ) {
			$show_form = true;
		} elseif ( ! WP_Filesystem( $creds ) ) {
			request_filesystem_credentials( $url, $method, true );
			$show_form = true;
		}

		if ( $show_form ) {
			$form     = ob_get_clean();
			$message  = __( 'Sorry, your site requires FTP authentication. Please download plugins from BusinessDirectoryPlugin.com and install them manually.', 'business-directory-plugin' );
			$data     = $form;
			$response = array(
				'success' => false,
				'message' => $message,
				'form'    => $form,
			);
			wp_send_json( $response );
		}

		ob_end_clean();
	}

	/**
	 * We do not need any extra credentials if we have gotten this far,
	 * so let's install the plugin.
	 *
	 * @since 5.9.2
	 */
	protected static function install_addon() {
		require_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

		$download_url = wpbdp_get_var( array( 'param' => 'plugin', 'sanitize' => 'esc_url_raw' ), 'post' );

		// Create the plugin upgrader with our custom skin.
		require_once WPBDP_INC . 'models/class-installer-skin.php';
		$installer = new Plugin_Upgrader( new WPBDP_Installer_Skin() );
		$installer->install( $download_url );

		// Flush the cache and return the newly installed plugin basename.
		wp_cache_flush();

		$plugin = $installer->plugin_info();
		if ( empty( $plugin ) ) {
			return array(
				'message' => 'Plugin was not installed. ' . print_r( $installer->result, true ),
				'success' => false,
			);
		}
		return $plugin;
	}

	/**
	 * @since 5.9.2
	 */
	public static function ajax_activate_addon() {

		self::install_addon_permissions();

		// Set the current screen to avoid undefined notices.
		global $hook_suffix;
		set_current_screen();

		$plugin = wpbdp_get_var( array( 'param' => 'plugin', 'sanitize' => 'sanitize_text_field' ), 'post' );
		self::maybe_activate_addon( $plugin );

		// Send back a response.
		echo json_encode( __( 'Your plugin has been activated. Please reload the page to see more options.', 'business-directory-plugin' ) );
		wp_die();
	}

	/**
	 * @since 5.9.2
	 *
	 * @param string $installed The plugin folder name with file name
	 */
	protected static function maybe_activate_addon( $installed ) {
		if ( ! $installed ) {
			return;
		}

		$activate = activate_plugin( $installed );
		if ( is_wp_error( $activate ) ) {
			// Ignore the invalid header message that shows with nested plugins.
			if ( $activate->get_error_code() !== 'no_plugin_header' ) {
				if ( wp_doing_ajax() ) {
					echo json_encode( array( 'error' => $activate->get_error_message() ) );
					wp_die();
				}
				return array(
					'message' => $activate->get_error_message(),
					'success' => false,
				);
			}
		}
	}

	/**
	 * Run security checks before installing
	 *
	 * @since 5.9.2
	 */
	protected static function install_addon_permissions() {
		check_ajax_referer( 'wpbdp_ajax', 'nonce' );

		if ( ! current_user_can( 'activate_plugins' ) || ! isset( $_POST['plugin'] ) ) {
			echo json_encode( true );
			wp_die();
		}
	}
}
