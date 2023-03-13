<?php

/**
 * @since 6.0
 */
class WPBDP_Admin_Notices {

	public static function load_hooks() {
		add_action( 'admin_footer', __CLASS__ . '::admin_footer' );
	}

	/**
	 * Show admin notification icon in footer.
	 */
	public static function admin_footer() {
		if ( ! WPBDP_App_Helper::is_bd_page() ) {
			return;
		}
		self::show_bell();
	}

	/**
	 * Admin floating notification bell.
	 *
	 * @since 6.0
	 */
	public static function show_bell() {
		?>
		<div class="wpbdp-bell-notifications hidden">
			<a href="#" class="wpbdp-bell-notifications-close"><?php esc_html_e( 'Hide notifications', 'business-directory-plugin' ); ?></a>
			<ul class="wpbdp-bell-notifications-list"></ul>
		</div>
		<div class="wpbdp-bell-notification">
			<a class="wpbdp-bell-notification-icon" href="#">
				<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 60 60"><rect width="60" height="60" fill="#fff" rx="12"/><path stroke="#3C4B5D" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M37.5 25a7.5 7.5 0 0 0-15 0c0 8.8-3.8 11.3-3.8 11.3h22.6s-3.8-2.5-3.8-11.3ZM32.2 41.3a2.5 2.5 0 0 1-4.4 0"/><circle class="wpbdp-bell-notification-icon-indicator" cx="39.4" cy="20.6" r="6.1" fill="#FF5A5A" stroke="#fff"><animate attributeName="r" from="6" to="8" dur="1.5s" begin="0s" repeatCount="indefinite"/><animate attributeName="opacity" from="1" to="0.8" dur="1.5s" begin="0s" repeatCount="indefinite"/></circle></svg>
			</a>
		</div>
		<div id="wpbdp-snackbar-notices"></div>
		<?php
	}

	/**
	 * Show the settings notice.
	 * Renders settings notice in notification area.
	 *
	 * @since 6.0
	 */
	public static function settings_errors() {
		$settings_errors = get_settings_errors();

		foreach ( $settings_errors as $details ) {
			// The WP docs on this are incorrect as of 2022-04-28.
			/** @phpstan-ignore-next-line */
			wpbdp_admin_message( $details['message'], $details['type'] );
		}

		wpbdp_admin_notices();
	}
}
