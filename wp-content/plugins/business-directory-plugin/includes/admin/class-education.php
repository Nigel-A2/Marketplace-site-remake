<?php
/**
 * Class WPBDP_Admin_Education
 *
 * @package BDP/Includes/Admin
 */

/**
 * Class WPBDP_Admin_Education
 *
 * @since 5.9.1
 */
class WPBDP_Admin_Education {

	/**
	 * @since 5.9.1
	 */
	public static function add_tip_in_settings( $id, $group ) {
		$tip = self::get_tip( $id );
		if ( empty( $tip ) || self::is_installed( $tip['requires'] ) ) {
			return;
		}

		$cta = ' <a href="' . esc_url( $tip['link'] ) . '" target="_blank" rel="noopener">' . esc_html( $tip['cta'] ) . '</a>';

        wpbdp_register_setting(
            array(
                'id'      => $id,
				'desc'    => wp_kses_post( $tip['tip'] ) . $cta,
                'type'    => 'education',
                'group'   => $group,
            )
        );
	}

	/**
	 * @since 5.10
	 */
	public static function show_tip( $id ) {
		$tip = self::get_tip( $id );
		if ( empty( $tip ) || self::is_installed( $tip['requires'] ) ) {
			return;
		}

		$message = wp_kses_post( $tip['tip'] );
		$message .= self::render_cta( $tip );

		self::show_tip_message( $message );
	}

	/**
	 * Render the cta.
	 *
	 * @param array $tip The current tip.
	 *
	 * @since 6.0
	 *
	 * @return string
	 */
	public static function render_cta( $tip ) {
		$cta = '<a href="' . esc_url( $tip['link'] ) . '" target="_blank" rel="noopener">';
		$cta .= esc_html( $tip['cta'] );
		$cta .= '</a>';
		return $cta;
	}

	/**
	 * @since 5.10
	 */
	public static function show_tip_message( $message ) {
		?>
		<div class="wpbdp-pro-tip">
			<svg width="20" height="22" viewBox="0 0 20 22" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M11 1.00003L1 13H10L9 21L19 9.00003H10L11 1.00003Z" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
			<?php
			// phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			echo $message; // already escaped.
			?>
		</div>
		<?php
	}

	/**
	 * @since 5.9.1
	 */
	private static function tips() {
		return array(
			'zip'     => array(
				'requires' => 'zipcodesearch',
				'tip'      => 'Search listings by ZIP/postal code and distance.',
				'cta'      => 'Upgrade to Pro.',
			),
			'abc'     => array(
				'requires' => 'premium',
				'tip'      => 'Add ABC filtering to get listings by the first letter.',
			),
			'abandon' => array(
				'requires' => 'premium',
				'tip'      => 'Want to ask users to come back for abandoned payments?',
			),
			'maps'    => array(
				'requires' => 'googlemaps',
				'tip'      => 'Add Google Maps to your directory listings.',
				'cta'      => 'Upgrade to Pro.',
			),
			'ratings' => array(
				'requires' => 'ratings',
				'tip'      => 'Add more value to listings with visitors reviews and ratings.',
			),
			'attachments' => array(
				'requires' => 'attachments',
				'tip'      => 'Want to allow file uploads with listing submissions?',
			),
			'discounts' => array(
				'requires' => 'discount-codes',
				'tip'      => 'Offer discount & coupon codes to your paid listing customers.',
			),
			'migrator'  => array(
				'requires' => 'migrate',
				'tip'      => 'Need to export, backup, or move your directory settings and listings?',
			),
			'categories'  => array(
				'requires' => 'categories',
				'tip'      => 'Want to show a list of images for your categories?',
			),
			'install-premium'  => array(
				'requires' => 'premium',
				'tip'      => 'Install modules with one click, get table listings, abandonment emails, and more.',
				'link'     => wpbdp_admin_upgrade_link( 'install-modules', '/account/downloads/' ),
				'cta'      => 'Download Now.',
			),
			'table'    => array(
				'requires' => 'premium',
				'tip'      => 'Show listings in a grid or table. <img src="' . esc_url( WPBDP_ASSETS_URL . 'images/premium-layout.svg' ) . '" alt="Directory listing layout setting" style="max-width:645px" />',
			),
		);
		// TODO: Show maps and attachments.
	}

	/**
	 * @param string $id
	 *
	 * @since 5.9.1
	 *
	 * @return array
	 */
	private static function get_tip( $id ) {
		$tips = self::tips();
		$tip  = isset( $tips[ $id ] ) ? $tips[ $id ] : array();
		if ( empty( $tip['link'] ) ) {
			$tip['link'] = wpbdp_admin_upgrade_link( $id );
		}
		if ( empty( $tip['cta'] ) ) {
			$tip['cta'] = 'Upgrade Now.';
		}

		$has_premium = self::is_installed( 'premium' );
		if ( $has_premium && $tip['requires'] === 'premium' ) {
			// Don't show it.
			return array();
		}

		$is_any_upgrade = $tip['cta'] === 'Upgrade Now.' || $tip['cta'] === 'Upgrade to Premium';
		if ( $has_premium && $is_any_upgrade ) {
			$tip['cta']  = 'Install Now.';
			$tip['link'] = admin_url( 'admin.php?page=wpbdp-addons' );
		}

		return $tip;
	}

	/**
	 * @since 5.9.1
	 *
	 * @return bool
	 */
	public static function is_installed( $requires ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$installed = get_plugins();
		$installed = array_keys( $installed );
		foreach ( $installed as $module ) {
			$name = explode( '/', $module )[0];
			$name = str_replace( 'business-directory-', '', $name );
			if ( $name === $requires ) {
				return is_plugin_active( $module );
			}
		}

		return false;
	}
}
