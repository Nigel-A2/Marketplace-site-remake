<?php

/**
 * Manages remote updates for all Beaver Builder products.
 *
 * @since 1.0
 */
final class FLUpdater {

	/**
	 * The API URL for the Beaver Builder update server.
	 *
	 * @since 1.0
	 * @access private
	 * @var string $_updates_api_url
	 */
	static private $_updates_api_url = 'https://updates.wpbeaverbuilder.com/';

	/**
	 * An internal array of data for each product.
	 *
	 * @since 1.0
	 * @access private
	 * @var array $_products
	 */
	static private $_products = array();

	/**
	 * An internal array of remote responses with
	 * update data for each product.
	 *
	 * @since 1.8.4
	 * @access private
	 * @var array $_responses
	 */
	static private $_responses = array();

	/**
	 * An internal array of settings for the updater instance.
	 *
	 * @since 1.0
	 * @access private
	 * @var array $settings
	 */
	private $settings = array();

	/**
	 * Updater constructor method.
	 *
	 * @since 1.0
	 * @param array $settings An array of settings for this instance.
	 * @return void
	 */
	public function __construct( $settings = array() ) {
		$this->settings = $settings;

		if ( 'plugin' == $settings['type'] ) {
			add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'update_check' ) );
			add_filter( 'plugins_api', array( $this, 'plugin_info' ), 99, 3 );
			add_action( 'in_plugin_update_message-' . self::get_plugin_file( $settings['slug'] ), array( $this, 'update_message' ), 1, 2 );
		} elseif ( 'theme' == $settings['type'] ) {
			add_filter( 'pre_set_site_transient_update_themes', array( $this, 'update_check' ) );
		}
	}

	/**
	 * Get the update data response from the API.
	 *
	 * @since 1.7.7
	 * @return object
	 */
	public function get_response() {
		$slug = $this->settings['slug'];

		if ( isset( FLUpdater::$_responses[ $slug ] ) ) {
			return FLUpdater::$_responses[ $slug ];
		}

		FLUpdater::$_responses[ $slug ] = FLUpdater::api_request(
			FLUpdater::$_updates_api_url,
			array(
				'fl-api-method' => 'update_info',
				'license'       => FLUpdater::get_subscription_license(),
				'domain'        => FLUpdater::validate_domain( network_home_url() ),
				'product'       => $this->settings['name'],
				'slug'          => $this->settings['slug'],
				'version'       => self::verify_version( $this->settings['version'] ),
				'php'           => phpversion(),
			)
		);

		return FLUpdater::$_responses[ $slug ];
	}

	/**
	 * Checks to see if an update is available for the current product.
	 *
	 * @since 1.0
	 * @param object $transient A WordPress transient object with update data.
	 * @return object
	 */
	public function update_check( $transient ) {
		global $pagenow;

		if ( 'plugins.php' == $pagenow && is_multisite() ) {
			return $transient;
		}
		if ( ! is_object( $transient ) ) {
			$transient = new stdClass();
		}
		if ( ! isset( $transient->checked ) ) {
			$transient->checked = array();
		}

		$response = $this->get_response();
		if ( ! isset( $response->error ) || ( isset( $response->error ) && 'No update available.' === $response->error ) ) {

			$transient->last_checked                       = time();
			$transient->checked[ $this->settings['slug'] ] = $this->settings['version'];

			if ( 'plugin' == $this->settings['type'] ) {

				$plugin = self::get_plugin_file( $this->settings['slug'] );

				$plugin_version_check = self::verify_version( $this->settings['version'] );
				if ( isset( $response->new_version ) ) {
					if ( false === strpos( $response->new_version, 'alpha' ) ) {
						$plugin_version_check = rtrim( $plugin_version_check, '-alpha' );
					}
					if ( false === strpos( $response->new_version, 'beta' ) ) {
						$plugin_version_check = rtrim( $plugin_version_check, '-beta' );
					}
				}

				if ( isset( $response->new_version ) && version_compare( $response->new_version, $plugin_version_check, '>' ) ) {

					$transient->response[ $plugin ]              = new stdClass();
					$transient->response[ $plugin ]->slug        = $response->slug;
					$transient->response[ $plugin ]->plugin      = $plugin;
					$transient->response[ $plugin ]->new_version = $response->new_version;
					$transient->response[ $plugin ]->url         = $response->homepage;
					$transient->response[ $plugin ]->package     = $response->package;
					$transient->response[ $plugin ]->tested      = $response->tested;
					$transient->response[ $plugin ]->icons       = apply_filters(
						'fl_updater_icon',
						array(
							'1x'      => FL_BUILDER_URL . 'img/beaver-128.png',
							'2x'      => FL_BUILDER_URL . 'img/beaver-256.png',
							'default' => FL_BUILDER_URL . 'img/beaver-256.png',
						),
						$response,
						$this->settings
					);

					if ( empty( $response->package ) ) {
						$transient->response[ $plugin ]->upgrade_notice = FLUpdater::get_update_error_message( false, $this->settings );
					}
				} else {
					// no update, for wp 5.5 we have to add a mock item.
					$item = (object) array(
						'id'            => $plugin,
						'slug'          => $this->settings['slug'],
						'plugin'        => $plugin,
						'new_version'   => $this->settings['version'],
						'url'           => '',
						'package'       => '',
						'icons'         => array(),
						'banners'       => array(),
						'banners_rtl'   => array(),
						'tested'        => '',
						'requires_php'  => '',
						'compatibility' => new stdClass(),
					);
					// Adding the "mock" item to the `no_update` property is required
					// for the enable/disable auto-updates links to correctly appear in UI.
					$transient->no_update[ $plugin ] = $item;
				}
			} elseif ( 'theme' == $this->settings['type'] ) {
				$theme_version_check = self::verify_version( $this->settings['version'] );
				if ( isset( $response->new_version ) ) {
					if ( false === strpos( $response->new_version, 'alpha' ) ) {
						$theme_version_check = rtrim( $theme_version_check, '-alpha' );
					}
					if ( false === strpos( $response->new_version, 'beta' ) ) {
						$theme_version_check = rtrim( $theme_version_check, '-beta' );
					}
				}
				if ( isset( $response->new_version ) && version_compare( $response->new_version, $theme_version_check, '>' ) ) {

					$transient->response[ $this->settings['slug'] ] = array(
						'new_version' => $response->new_version,
						'theme'       => $this->settings['slug'],
						'url'         => $response->homepage,
						'package'     => $response->package,
						'tested'      => $response->tested,
					);
				} else {
					// no update, for wp 5.5 we have to add a mock item.
					$item = array(
						'theme'        => $this->settings['slug'],
						'new_version'  => $this->settings['version'],
						'url'          => '',
						'package'      => '',
						'requires'     => '',
						'requires_php' => '',
					);
					// Adding the "mock" item to the `no_update` property is required
					// for the enable/disable auto-updates links to correctly appear in UI.
					$transient->no_update[ $this->settings['slug'] ] = $item;
				}
			}
		}

		return $transient;
	}

	/**
	 * Retrieves the data for the plugin info lightbox.
	 *
	 * @since 1.0
	 * @param bool $false
	 * @param string $action
	 * @param object $args
	 * @return object|bool
	 */
	public function plugin_info( $false, $action, $args ) {
		if ( 'plugin_information' != $action ) {
			return $false;
		}
		if ( ! isset( $args->slug ) || $args->slug != $this->settings['slug'] ) {
			return $false;
		}

		$response  = $this->get_response();
		$changelog = __( 'Could not locate changelog.txt', 'fl-builder' );

		if ( ! isset( $response->error ) ) {

			$info                = new stdClass();
			$info->name          = $this->settings['name'];
			$info->version       = $response->new_version;
			$info->slug          = $response->slug;
			$info->plugin_name   = $response->plugin_name;
			$info->author        = $response->author;
			$info->homepage      = $response->homepage;
			$info->requires      = $response->requires;
			$info->tested        = $response->tested;
			$info->last_updated  = $response->last_updated;
			$info->download_link = $response->package;
			$info->sections      = (array) $response->sections;
			return apply_filters( 'fl_plugin_info_data', $info, $response );
		} else {
			if ( 'bb-plugin' === $this->settings['slug'] && file_exists( trailingslashit( plugin_dir_path( FL_BUILDER_FILE ) ) . '/changelog.txt' ) ) {
				$changelog = file_get_contents( trailingslashit( plugin_dir_path( FL_BUILDER_FILE ) ) . '/changelog.txt' );
			}
			if ( 'bb-theme-builder' === $this->settings['slug'] && file_exists( trailingslashit( plugin_dir_path( FL_THEME_BUILDER_FILE ) ) . '/changelog.txt' ) ) {
				$changelog = file_get_contents( trailingslashit( plugin_dir_path( FL_THEME_BUILDER_FILE ) ) . '/changelog.txt' );
			}
			$info              = new stdClass();
			$info->name        = $this->settings['name'];
			$info->version     = $this->settings['version'];
			$info->slug        = $this->settings['slug'];
			$info->plugin_name = $this->settings['name'];
			$info->homepage    = 'https://www.wpbeaverbuilder.com/';

			$info->sections              = array();
			$info->sections['changelog'] = $changelog;
			return apply_filters( 'fl_plugin_info_data', $info, $response );
		}

		return $false;
	}

	/**
	 * Shows an update message on the plugins page if an update
	 * is available but there is no active subscription.
	 *
	 * @since 1.0
	 * @param array $plugin_data An array of data for this plugin.
	 * @param object $response An object with update data for this plugin.
	 * @return void
	 */
	public function update_message( $plugin_data, $response ) {
		if ( empty( $response->package ) ) {
			echo FLUpdater::get_update_error_message( $plugin_data );
		}
	}

	/**
	 * Static method for initializing an instance of the updater
	 * for each active product.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function init() {
		include FL_UPDATER_DIR . 'includes/config.php';

		foreach ( $config as $path ) {
			if ( file_exists( $path ) ) {
				require_once $path;
			}
		}
	}

	/**
	 * Static method for adding a product to the updater and
	 * creating the new instance.
	 *
	 * @since 1.0
	 * @param array $args An array of settings for the product.
	 * @return void
	 */
	static public function add_product( $args = array() ) {
		if ( is_array( $args ) && isset( $args['slug'] ) ) {

			if ( 'plugin' == $args['type'] ) {
				if ( file_exists( trailingslashit( WP_PLUGIN_DIR ) . $args['slug'] ) ) {
					$args['version']                  = self::verify_version( $args['version'] );
					self::$_products[ $args['name'] ] = $args;
					new FLUpdater( self::$_products[ $args['name'] ] );
				}
			}
			if ( 'theme' == $args['type'] ) {
				if ( file_exists( WP_CONTENT_DIR . '/themes/' . $args['slug'] ) ) {
					$args['version']                  = self::verify_version( $args['version'] );
					self::$_products[ $args['name'] ] = $args;
					new FLUpdater( self::$_products[ $args['name'] ] );
				}
			}
		}
	}

	/**
	 * Static method for rendering the license form.
	 *
	 * @since 1.0
	 * @return void
	 */
	static public function render_form() {
		// Activate a subscription?
		if ( isset( $_POST['fl-updater-nonce'] ) ) {
			if ( wp_verify_nonce( $_POST['fl-updater-nonce'], 'updater-nonce' ) ) {
				$response = self::save_subscription_license( $_POST['license'] );
				if ( '' == $_POST['license'] ) {
					$response->error = __( 'License Removed', 'fl-builder' );
				}
				if ( isset( $response->error ) ) {
					unset( $_POST['fl-updater-nonce'] );
					FLBuilderAdminSettings::add_error( $response->error );
					FLBuilderAdminSettings::render_update_message();
				}
			}
		}

		$license      = self::get_subscription_license();
		$subscription = self::get_subscription_info();

		// Include the form ui.
		include FL_UPDATER_DIR . 'includes/form.php';
	}

	/**
	 * Renders available subscriptions and downloads.
	 *
	 * @since 1.10
	 * @param object $subscription
	 * @return void
	 */
	static public function render_subscriptions( $subscription ) {
		if ( isset( $subscription->error ) || ! $subscription->active || ! $subscription->domain->active || ! isset( $subscription->downloads ) ) {
			return;
		}
		if ( ! FLBuilderModel::is_white_labeled() ) {
			include FL_UPDATER_DIR . 'includes/subscriptions.php';
		}
	}

	/**
	 * Static method for getting the subscription license key.
	 *
	 * @since 1.0
	 * @return string
	 */
	static public function get_subscription_license() {
		$value = get_site_option( 'fl_themes_subscription_email' );

		return $value ? $value : '';
	}

	/**
	 * Static method for updating the subscription license.
	 *
	 * @since 1.0
	 * @param string $license The new license key.
	 * @return $response mixed
	 */
	static public function save_subscription_license( $license ) {

		if ( preg_match( '/[^a-zA-Z\d\s@\.\-_]/', $license ) ) {
			$response        = new StdClass;
			$response->error = __( 'You submitted an invalid license. Non alphanumeric characters found.', 'fl-builder' );
			return $response;
		}
		$response = FLUpdater::api_request(
			self::$_updates_api_url,
			array(
				'fl-api-method' => 'activate_domain',
				'license'       => $license,
				'domain'        => FLUpdater::validate_domain( network_home_url() ),
				'products'      => json_encode( self::$_products ),
			)
		);
		if ( isset( $response->error ) ) {
			$license = '';
		}
		update_site_option( 'fl_themes_subscription_email', $license );
		return $response;
	}


	/**
	 * Static method for retrieving the subscription info.
	 *
	 * @since 1.0
	 * @return bool
	 */
	static public function get_subscription_info() {
		return self::api_request(
			self::$_updates_api_url,
			array(
				'fl-api-method' => 'subscription_info',
				'domain'        => FLUpdater::validate_domain( network_home_url() ),
				'license'       => FLUpdater::get_subscription_license(),
			)
		);
	}

	/**
	 * Returns an update message for if an update
	 * is available but there is no active subscription.
	 *
	 * @since 1.6.4.3
	 * @param array $plugin_data An array of data for this plugin.
	 * @return string
	 */
	static private function get_update_error_message( $plugin_data = null, $settings = false ) {

		$subscription    = FLUpdater::get_subscription_info();
		$license         = get_site_option( 'fl_themes_subscription_email' );
		$message         = '';
		$check_downloads = self::check_downloads( $subscription, $plugin_data, $settings );

		// updates-core.php
		if ( ! $plugin_data ) {

			if ( ! $license ) {
				$message = __( 'Please enter a valid license key to enable automatic updates.', 'fl-builder' );

			} else {
				$message = __( 'Please subscribe to enable automatic updates for this plugin.', 'fl-builder' );
				if ( $check_downloads ) {
					$message = $check_downloads;
				}

				if ( isset( $subscription->error ) && '' !== $subscription->error ) {
					$message .= sprintf( ' The following error was encountered: %s', $subscription->error );
				}
			}
		} else { // plugins.php

			if ( ! $license ) {
				$link = sprintf( '<a href="%s" target="_blank" style="color: #fff; text-decoration: underline;">%s &raquo;</a>', admin_url( '/options-general.php?page=fl-builder-settings#license' ), __( 'Enter License Key', 'fl-builder' ) );
				/* translators: %s: link to license tab */
				$text = sprintf( __( 'Please enter a valid license key to enable automatic updates. %s', 'fl-builder' ), $link );
			} else {
				if ( 'bb-theme-builder/bb-theme-builder.php' === $plugin_data['plugin'] ) {
					$subscribe_link = FLBuilderModel::get_store_url(
						'beaver-themer',
						array(
							'utm_medium'   => 'bb-theme-builder',
							'utm_source'   => 'plugins-admin-page',
							'utm_campaign' => 'subscribe',
						)
					);
				} else {
					$subscribe_link = FLBuilderModel::get_store_url(
						'',
						array(
							'utm_medium'   => 'bb',
							'utm_source'   => 'plugins-admin-page',
							'utm_campaign' => 'subscribe',
						)
					);
				}
				$link = sprintf( '<a href="%s" target="_blank" style="color: #fff; text-decoration: underline;">%s &raquo;</a>', $subscribe_link, __( 'Subscribe Now', 'fl-builder' ) );
				/* translators: %s: subscribe link */
				$text = sprintf( __( 'Please subscribe to enable automatic updates for this plugin. %s', 'fl-builder' ), $link );
			}

			if ( isset( $subscription->error ) && '' !== $subscription->error ) {
				$support_url = FLBuilderModel::get_store_url( 'contact', array(
					'topic'      => 'General Inquiry',
					'utm_medium' => 'bb-pro',
					'utm_source' => 'plugin-updates',
				) );
				$url         = sprintf( '<a target="_blank" style="color: #fff; text-decoration: underline;" href="%s">%s</a>', $support_url, __( 'Contact Support for more information.', 'fl-builder' ) );
				$text       .= sprintf( '<br />The following error was encountered: %s %s', $subscription->error, $url );
			}

			$message .= '<span style="display:block;padding:10px 20px;margin:10px 0; background: #d54e21; color: #fff;">';
			$message .= ( $check_downloads ) ? $check_downloads : sprintf( '<strong>%s<strong>', __( 'UPDATE UNAVAILABLE!', 'fl-builder' ) );
			$message .= '&nbsp;&nbsp;&nbsp;';
			$message .= ( $check_downloads ) ? '' : $text;
			$message .= '</span>';

		}
		return $message;
	}

	static private function check_downloads( $subscription, $plugin_data, $settings ) {

		$plugin_name = ( ! $plugin_data ) ? $settings['name'] : $plugin_data['Name'];
		$out         = '';

		if ( '{FL_BUILDER_NAME}' !== $plugin_name && isset( $subscription->downloads ) && ! in_array( $plugin_name, $subscription->downloads, true ) ) {

			$show_warning = false;
			$version      = '';

			// find available plugin Version
			foreach ( $subscription->downloads as $ver ) {
				if ( stristr( $ver, 'Beaver Builder Plugin' ) ) {
					preg_match( '#\((.*)\sVersion\)$#', $ver, $match );
					$version = ( isset( $match[1] ) ) ? $match[1] : false;
					break;
				}
			}

			switch ( $plugin_name ) {
				// pro - show warning if standard is pnly available version
				case 'Beaver Builder Plugin (Pro Version)':
					$show_warning = ( 'Standard' === $version ) ? true : false;
					break;
				// agency show warning if available is NOT agency
				case 'Beaver Builder Plugin (Agency Version)':
					$show_warning = ( 'Agency' !== $version ) ? true : false;
					break;
				case 'Beaver Themer':
					$show_warning = true;
					break;
			}

			if ( ! $version ) {
				$show_warning = true;
			}

			if ( $show_warning ) {
				if ( ! $version ) {
					// translators: %1$s: Plugin name
					$out .= sprintf( __( 'Updates for Beaver Builder will not work as you appear to have %1$s activated but you have no active subscription.', 'fl-builder' ), '<strong>' . $plugin_name . '</strong>', $version );
				} else {
					// translators: %2$s: Plugin name
					$out .= sprintf( __( 'Updates for Beaver Builder will not work as you appear to have %1$s activated but your license is for %2$s version.', 'fl-builder' ), '<strong>' . $plugin_name . '</strong>', $version );
				}

				if ( 'Beaver Themer' === $plugin_name ) {
					$out = __( 'Updates for Themer will not work as you do not have a valid subscription for this plugin.', 'fl-builder' );
				}
			}
		}
		return $out;
	}

	/**
	 * Static method for retrieving the plugin file path for a
	 * product relative to the plugins directory.
	 *
	 * @since 1.0
	 * @access private
	 * @param string $slug The product slug.
	 * @return string
	 */
	static private function get_plugin_file( $slug ) {
		if ( 'bb-plugin' == $slug ) {
			$file = $slug . '/fl-builder.php';
		} else {
			$file = $slug . '/' . $slug . '.php';
		}

		return $file;
	}

	/**
	 * Static method for sending a request to the store
	 * or update API.
	 *
	 * @since 1.0
	 * @access private
	 * @param string $api_url The API URL to use.
	 * @param array $args An array of args to send along with the request.
	 * @return mixed The response or false if there is an error.
	 */
	static private function api_request( $api_url = false, $args = array() ) {
		if ( $api_url ) {

			$params = array();

			foreach ( $args as $key => $val ) {
				$params[] = $key . '=' . urlencode( $val );
			}

			return self::remote_get( $api_url . '?' . implode( '&', $params ) );
		}

		return false;
	}

	/**
	 * Get a remote response.
	 *
	 * @since 1.0
	 * @access private
	 * @param string $url The URL to get.
	 * @return mixed The response or false if there is an error.
	 */
	static private function remote_get( $url ) {
		$request      = wp_remote_get( $url );
		$error        = new stdClass();
		$error->error = 'connection';

		if ( is_wp_error( $request ) ) {
			return $error;
		}
		if ( wp_remote_retrieve_response_code( $request ) != 200 ) {
			return $error;
		}

		$body = wp_remote_retrieve_body( $request );

		if ( is_wp_error( $body ) ) {
			return $error;
		}

		$body_decoded = json_decode( $body );

		if ( ! is_object( $body_decoded ) ) {
			return $error;
		}

		return $body_decoded;
	}

	/**
	 * Validate domain and strip any query params
	 * @since 2.3
	 */
	private static function validate_domain( $url ) {
		$pos = strpos( $url, '?' );
		$url = ( $pos ) ? untrailingslashit( substr( $url, 0, $pos ) ) : $url;
		return $url;
	}

	static public function verify_version( $version ) {

		if ( get_option( 'fl_beta_updates', false ) ) {
			// if version already is beta strip -beta.
			$version = rtrim( $version, '-beta' );
			if ( false === strpos( $version, 'beta' ) ) {
				$version .= '-beta';
			}
		}

		if ( get_option( 'fl_alpha_updates', false ) ) {
			// if version already is beta strip -beta.
			$version = rtrim( $version, '-beta' );
			$version = rtrim( $version, '-alpha' );
			if ( false === strpos( $version, 'alpha' ) ) {
				$version .= '-alpha';
			}
		}
		return $version;
	}
}
