<?php
/**
 * Font Awesome compatibility
 *
 * @package WPBDP/Includes/Compatibility/FA
 */

/**
 * Class WPBDP_FA_Compat
 */
class WPBDP_FA_Compat {

 	/*
	 * @since 5.15.3
	 */
	public function __construct() {
		if ( ! is_admin() ) {
			return;
		}

		add_action( 'wpbdp_admin_notices', array( &$this, 'admin_notice' ) );
		add_action( 'wpbdp_admin_ajax_dismiss_notification_fontawesome', array( &$this, 'ajax_dismiss_notification_fontawesome' ) );
		add_action( 'wpbdp_register_settings', array( &$this, 'register_setting' ), 50 );
	}

	/**
	 * Check old inline fontawesome setting.
	 * If it was enabled by default, we show an admin notice.
	 * On dismiss we keep the setting.
	 *
	 * @since 5.15.3
	 */
	public function admin_notice() {
		if ( ! WPBDP_App_Helper::is_bd_page() ) {
			return;
		}

		if ( ! wpbdp_get_option( 'enqueue-fontawesome-styles', false ) ) {
			return;
		}

		$plugin_url = admin_url( 'plugin-install.php?s=fontawesome&tab=search&type=author' );
		wpbdp_admin_message(
			sprintf(
				__( 'Good news! Business Directory Plugin now integrates with the official Font Awesome plugin. %1$sInstall Font Awesome now%2$s.', 'business-directory-plugin' ),
				'<a class="wpbdp-notice-dismiss" href="' . esc_url( $plugin_url ) . '" data-dismissible-id="fontawesome" data-nonce="' . esc_attr( wp_create_nonce( 'dismiss notice fontawesome' ) ) . '">',
				'</a>'
			),
			'notice-error is-dismissible',
			array( 'dismissible-id' => 'fontawesome' )
		);
	}

	/**
	 * Set fontawesome notice as dismissed.
	 *
	 * @since 5.15.3
	 */
	public function ajax_dismiss_notification_fontawesome() {
		if ( ! current_user_can( 'install_plugins' ) ) {
			return;
		}
		wpbdp_set_option( 'enqueue-fontawesome-styles', false );
	}

	/**
	 * Let people know they can add Font Awesome.
	 *
	 * @since 5.15.3
	 */
	public function register_setting() {
		if ( self::is_enabled() ) {
			return;
		}

		// Old setting switched to hidden. This can be removed later.
		wpbdp_register_setting(
			array(
				'id'      => 'enqueue-fontawesome-styles',
				'type'    => 'hidden',
				'class'   => 'hidden',
				'default' => false,
				'group'   => 'general/advanced',
			)
		);

		$fa_install_url = admin_url( 'plugin-install.php?s=fontawesome&tab=search&type=author' );
		wpbdp_register_setting(
			array(
				'id'    => 'fontawesome-enabled',
				'desc'  => wp_kses_post(
					sprintf(
						esc_html__( 'Did you know you can use icons in directory listings and custom fields? %1$sInstall Font Awesome now%2$s', 'business-directory-plugin' ),
						'<a href="' . esc_url( $fa_install_url ) . '" target="_blank" rel="noopener nofollow">',
						'</a>'
					)
				),
				'type'  => 'education',
				'group' => 'themes',
			)
		);
	}

	/**
	 * Check if font awesome is enabled.
	 * This checks if the plugin is active or the styles are loaded.
	 *
	 * @since 5.15.3
	 *
	 * @return bool
	 */
	public static function is_enabled() {
		$possible_styles = array( 'font-awesome', 'bfa-font-awesome' );
		foreach ( $possible_styles as $style ) {
			if ( wp_style_is( $style ) ) {
				return true;
			}
		}
		return is_plugin_active( 'font-awesome/index.php' ) || is_plugin_active( 'better-font-awesome/better-font-awesome.php' );
	}
}
