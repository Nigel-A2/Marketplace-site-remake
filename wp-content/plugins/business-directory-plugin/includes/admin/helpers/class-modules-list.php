<?php

/**
 * @since 5.10
 */
class WPBDP_Show_Modules {

	/**
	 * @since 5.10
	 */
	public static function list_addons() {
		$license_type = '';

		$addons = self::get_api_addons();
		$errors = array();

		if ( isset( $addons['error'] ) ) {
			include_once dirname( __FILE__ ) . '/class-modules-api.php';
			$api    = new WPBDP_Modules_API();
			$errors = $api->get_error_from_response( $addons );
			$license_type = isset( $addons['error']['type'] ) ? $addons['error']['type'] : '';
			unset( $addons['error'] );
		}
		self::prepare_addons( $addons );

		$pricing = wpbdp_admin_upgrade_link( 'addons' );

		include WPBDP_PATH . 'includes/admin/views/modules/list.php';
	}

	/**
	 * @since 5.10
	 */
	protected static function get_api_addons() {
		include_once dirname( __FILE__ ) . '/class-modules-api.php';
		$api    = new WPBDP_Modules_API();
		$addons = $api->get_api_info();

		if ( empty( $addons ) ) {
			$addons = self::fallback_plugin_list();
		} else {
			foreach ( $addons as $k => $addon ) {
				if ( empty( $addon['excerpt'] ) && $k !== 'error' ) {
					unset( $addons[ $k ] );
				}
			}
		}

		return $addons;
	}

	/**
	 * If the API is unable to connect, show something on the addons page
	 *
	 * @since 5.10
	 * @return array
	 */
	protected static function fallback_plugin_list() {
		$list = array(
			'premium'    => array(
				'title'   => 'Business Directory Premium',
				'link'    => 'pricing/',
				'docs'    => '',
				'excerpt' => 'Enhance your basic Formidable forms with a plethora of Pro field types and features. Create advanced forms and data-driven applications in minutes.',
			),
			'paypal'     => array(
				'title'   => 'PayPal Standard',
				'link'    => 'downloads/paypal-gateway-module/',
				'excerpt' => 'Automate your business by collecting instant payments from your clients. Collect information and send them on to PayPal.',
			),
			'googlemaps' => array(
				'title'   => 'Google Maps',
				'link'    => 'downloads/google-maps-module/',
				'excerpt' => 'Allow users to display their physical location using Google Maps and their address field.',
			),
		);

		$defaults = array(
			'released' => '',
		);

		foreach ( $list as $k => $info ) {
			$info['slug'] = $k;
			$list[ $k ]   = array_merge( $defaults, $info );
		}
		return $list;
	}

	protected static function prepare_addons( &$addons ) {
		$activate_url = '';
		if ( current_user_can( 'activate_plugins' ) ) {
			$activate_url = add_query_arg( array( 'action' => 'activate' ), admin_url( 'plugins.php' ) );
		}

		$loop_addons = $addons;
		foreach ( $loop_addons as $id => $addon ) {
			if ( is_numeric( $id ) ) {
				$slug      = str_replace( array( '-wordpress-plugin', '-wordpress' ), '', $addon['slug'] );
				$file_name = $addon['plugin'];
			} else {
				$slug = $id;
				if ( isset( $addon['file'] ) ) {
					$base_file = $addon['file'];
				} else {
					$base_file = 'business-directory-' . $slug;
				}
				$file_name = $base_file . '/' . $base_file . '.php';
				if ( ! isset( $addon['plugin'] ) ) {
					$addon['plugin'] = $file_name;
				}
			}

			$addon['installed']    = self::is_installed( $file_name );
			$addon['activate_url'] = '';

			if ( $addon['installed'] && ! empty( $activate_url ) && ! is_plugin_active( $file_name ) ) {
				$addon['activate_url'] = add_query_arg(
					array(
						'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $file_name ),
						'plugin'   => $file_name,
					),
					$activate_url
				);
			}

			if ( ! isset( $addon['link'] ) ) {
				$addon['link'] = 'downloads/' . $slug . '/';
			}
			self::prepare_addon_link( $addon['link'] );

			self::set_addon_status( $addon );
			$addons[ $id ] = $addon;
		}
	}


	/**
	 * Check if a plugin is installed before showing an update for it
	 *
	 * @since 5.10
	 *
	 * @param string $plugin - the folder/filename.php for a plugin
	 *
	 * @return bool - True if installed
	 */
	protected static function is_installed( $plugin ) {
		if ( ! function_exists( 'get_plugins' ) ) {
			require_once ABSPATH . 'wp-admin/includes/plugin.php';
		}

		$all_plugins = get_plugins();

		return isset( $all_plugins[ $plugin ] );
	}

	/**
	 * @since 5.10
	 */
	protected static function prepare_addon_link( &$link ) {
		$site_url = 'https://businessdirectoryplugin.com/';
		if ( strpos( $link, 'http' ) !== 0 ) {
			$link = $site_url . $link;
		}
		$query_args = array(
			'utm_source'   => 'WordPress',
			'utm_medium'   => 'addons',
			'utm_campaign' => 'liteplugin',
		);
		$link       = add_query_arg( $query_args, $link );
	}

	/**
	 * Add the status to the addon array. Status options are:
	 * installed, active, not installed
	 *
	 * @since 5.10
	 */
	protected static function set_addon_status( &$addon ) {
		if ( ! empty( $addon['activate_url'] ) ) {
			$addon['status'] = array(
				'type'  => 'installed',
				'label' => __( 'Installed', 'business-directory-plugin' ),
			);
		} elseif ( $addon['installed'] ) {
			$addon['status'] = array(
				'type'  => 'active',
				'label' => __( 'Active', 'business-directory-plugin' ),
			);
		} else {
			$addon['status'] = array(
				'type'  => 'not-installed',
				'label' => __( 'Not Installed', 'business-directory-plugin' ),
			);
		}
	}

	/**
	 * Render a conditional action button for an add on
	 *
	 * @since 5.10
	 *
	 * @param array $atts includes $atts[addon]
	 *                    string|false $atts[license_type]
	 *                    $atts[plan_required]
	 *                    $atts[upgrade_link]
	 */
	public static function show_conditional_action_button( $atts ) {
		if ( is_callable( 'WPBDP_Addons::show_conditional_action_button' ) ) {
			return WPBDP_Addons::show_conditional_action_button( $atts );
		}

		/** @phpstan-ignore-next-line */
		if ( ! empty( $atts['addon']['status']['type'] ) && $atts['addon']['status']['type'] === 'installed' ) {
			self::addon_activate_link( $atts['addon'] );
		} else {
			self::addon_upgrade_link( $atts['addon'], $atts['upgrade_link'] );
		}
	}

	/**
	 * @since 5.10
	 */
	public static function addon_activate_link( $addon ) {
		?>
		<a rel="<?php echo esc_attr( $addon['plugin'] ); ?>" class="button button-primary wpbdp-activate-addon <?php echo esc_attr( empty( $addon['activate_url'] ) ? 'wpbdp_hidden' : '' ); ?>">
			<?php esc_html_e( 'Activate', 'business-directory-plugin' ); ?>
		</a>
		<?php
	}

	/**
	 * @since 5.10
	 */
	public static function addon_upgrade_link( $addon, $upgrade_link ) {
		?>
		<a class="install-now button button-primary" href="<?php echo esc_url( $upgrade_link ); ?>" target="_blank" rel="noopener" aria-label="<?php esc_attr_e( 'Upgrade Now', 'business-directory-plugin' ); ?>">
			<?php esc_html_e( 'Upgrade', 'business-directory-plugin' ); ?>
		</a>
		<?php
	}
}
