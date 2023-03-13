<?php
/**
 * WPBDP Licensing class checks for licenses status, activates/deactivates licenses.
 *
 * @package BDP/Includes
 */

/**
 * @since 3.4.2
 */
class WPBDP_Licensing {

    const STORE_URL = 'https://businessdirectoryplugin.com/';

    private $items    = array(); // Items (modules and/or themes) registered with the Licensing API.
    private $licenses = array(); // License information: status, last check, etc.
	private $licenses_errors = 0; // Unverified license error information.

    public function __construct() {
		$this->licenses = get_option( 'wpbdp_licenses', array() );

        add_action( 'wpbdp_register_settings', array( &$this, 'register_settings' ) );
        add_filter( 'wpbdp_setting_type_license_key', array( $this, 'license_key_setting' ), 10, 2 );

        add_action( 'wp_ajax_wpbdp_activate_license', array( &$this, 'ajax_activate_license' ) );
        add_action( 'wp_ajax_wpbdp_deactivate_license', array( &$this, 'ajax_deactivate_license' ) );

        add_action( 'admin_notices', array( &$this, 'admin_notices' ) );
        add_filter( 'wpbdp_settings_tab_css', array( $this, 'licenses_tab_css' ), 10, 2 );

		add_action( 'admin_init', array( &$this, 'add_modules_hooks' ) );
        add_filter( 'pre_set_site_transient_update_plugins', array( $this, 'inject_update_info' ) );
		add_filter( 'plugins_api', array( $this, 'module_update_information' ), 10, 3 );

		$this->register_dismissable();
    }

	/**
	 * @since 5.10
	 * @return array|string
	 */
	private function get_license_errors() {
		if ( $this->licenses_errors !== 0 ) {
			return $this->licenses_errors;
		}

		$pro_id = 'module-' . $this->premium_id();
		$pro    = ! empty( $this->modules_array( true ) );

		$errors = get_option( 'wpbdp_licenses_errors', array() );
		if ( $pro ) {
			// Remove any other plugin errors since only the main one helps.
			if ( isset( $errors[ $pro_id ] ) ) {
				$errors = array(
					$pro_id => $errors[ $pro_id ],
				);
			} else {
				$errors = array();
			}
		}

		if ( is_array( $errors ) ) {
			// Remove an error if it's for a plugin that's deactivated.
			foreach ( $errors as $k => $error ) {
				if ( ! isset( $this->items[ $k ] ) ) {
					unset( $errors[ $k ] );
				}
			}
		}

		$this->licenses_errors = $errors;
		return $this->licenses_errors;
	}

	/**
	 * @since 5.10
	 */
	public function premium_id() {
		return 'business-directory-premium';
	}

	/**
	 * @since 5.10
	 */
	private function register_dismissable() {
		$notices = array_keys( $this->license_notices() );
		foreach ( $notices as $notice ) {
			add_action( 'wpbdp_admin_ajax_dismiss_notification_' . $notice, array( &$this, 'dismiss_notification' ) );
		}
	}

	/**
	 * @since 5.10
	 */
	public function add_modules_hooks() {
		global $pagenow;
		if ( 'plugins.php' !== $pagenow || ! $this->get_license_errors() ) {
			return;
		}

		$modules = $this->modules_array( true );
		if ( ! $modules ) {
			return;
		}

		$errors = $this->get_license_errors();
		if ( ! is_array( $errors ) ) {
			return;
		}

		foreach ( $modules as $module_id => $module ) {
			if ( isset( $errors[ $module_id ] ) ) {
				add_action( 'after_plugin_row_' . plugin_basename( $module['file'] ), array( &$this, 'show_validation_notice_under_plugin' ), 10, 3 );
			}
		}
	}

	/**
	 * @since 5.10
	 */
	private function modules_array( $pro_only = false ) {
		$modules = wp_list_filter( $this->items, array( 'item_type' => 'module' ) );
		if ( ! $pro_only || ! $modules ) {
			return $modules;
		}

		$pro_id = $this->premium_id();
		if ( isset( $modules[ $pro_id ] ) ) {
			// Only check Premium if it's available.
			$modules = array(
				$pro_id => $modules[ $pro_id ],
			);
		} else {
			$modules = array();
		}

		return $modules;
	}

	/**
	 * @since 5.10
	 */
	public function show_validation_notice_under_plugin( $plugin_file, $plugin_data ) {
		?>
		<div class="wpbdp-setting-row">
			<div class="update-message notice inline notice-warning notice-alt">
				<p>
					<?php
					echo sprintf(
						/* translators: %1%s: opening <a> tag, %2$s: closing </a> tag */
						esc_html__( 'The license key could not be verified. Please %1$scheck your license%2$s to get updates.', 'business-directory-plugin' ),
						'<strong><a href="' . esc_url( admin_url( 'admin.php?page=wpbdp_settings&tab=licenses' ) ) . '">',
						'</a></strong>'
					);
					?>
				</p>
			</div>
		</div>
		<?php
	}

    public function add_item( $args = array() ) {
        $defaults     = array(
            'item_type' => 'module',
            'file'      => '',
            'id'        => ! empty( $args['file'] ) ? trim( str_replace( '.php', '', basename( $args['file'] ) ) ) : '',
            'name'      => '',
            'version'   => '',
        );
        $args         = wp_parse_args( $args, $defaults );
        $args['slug'] = plugin_basename( $args['file'] );

        $this->items[ $args['id'] ] = $args;

        // Keep items sorted by name.
        uasort( $this->items, array( $this, 'sort_modules_by_name' ) );

        return $this->items[ $args['id'] ];
    }

    public function add_item_and_check_license( $args = array() ) {
        $item = $this->add_item( $args );

        if ( $item ) {
            $license_status = $this->get_license_status( '', $item['id'], $item['item_type'] );

            if ( in_array( $license_status, array( 'valid', 'expired' ), true ) ) {
                return true;
            }
        }

        return false;
    }

    public function get_items() {
        return $this->items;
    }

    public function register_settings() {
		$modules = $this->modules_array();
        $themes  = wp_list_filter( $this->items, array( 'item_type' => 'theme' ) );

        if ( ! $modules && ! $themes ) {
            return;
        }

		wpbdp_register_settings_group( 'licenses', __( 'Licenses', 'business-directory-plugin' ), '', array( 'icon' => 'key' ) );

		wpbdp_register_settings_group(
            'licenses/main',
            __( 'Licenses', 'business-directory-plugin' ),
            'licenses',
            array(
				'desc'        => '',
				'custom_form' => true,
            )
        );

        if ( $modules ) {
            wpbdp_register_settings_group( 'licenses/modules', __( 'Modules', 'business-directory-plugin' ), 'licenses/main' );

            foreach ( $modules as $module ) {
                wpbdp_register_setting(
                    array(
						'id'                  => 'license-key-module-' . $module['id'],
						'name'                => $module['name'],
						'licensing_item'      => $module['id'],
						'licensing_item_type' => 'module',
						'type'                => 'license_key',
						'on_update'           => array( $this, 'license_key_changed_callback' ),
						'group'               => 'licenses/modules',
                    )
                );
            }
        }

        if ( $themes ) {
            wpbdp_register_settings_group( 'licenses/themes', _x( 'Themes', 'settings', 'business-directory-plugin' ), 'licenses/main' );

            foreach ( $themes as $theme ) {
                wpbdp_register_setting(
                    array(
						'id'                  => 'license-key-theme-' . $theme['id'],
						'name'                => $theme['name'],
						'type'                => 'license_key',
						'licensing_item'      => $theme['id'],
						'licensing_item_type' => 'theme',
						'on_update'           => array( $this, 'license_key_changed_callback' ),
						'group'               => 'licenses/themes',
                    )
                );
            }
        }
    }

    public function license_key_setting( $setting, $value ) {
        $item_type = $setting['licensing_item_type'];
        $item_id   = $setting['licensing_item'];
		$errors    = $this->get_license_errors();

		if ( empty( $errors[ $item_id ] ) ) {
			$license_status = $this->get_license_status( $value, $item_id, $item_type );
			$tooltip_msg    = '';
		} else {
			$license_status    = 'not-verified';
			$tooltip_msg       = sprintf(
				/* translators: %s: item type. */
				__( '%s will not get updates until license is reauthorized.', 'business-directory-plugin' ),
				ucwords( $item_type )
			);
		}

		$licensing_info = array(
			'setting'   => $setting['id'],
			'item_type' => $item_type,
			'item_id'   => $item_id,
			'status'    => $license_status,
			'nonce'     => wp_create_nonce( 'license activation' ),
        );

		return $this->license_box( $licensing_info, $value, $tooltip_msg );
	}

	private function license_box( $atts, $value, $tooltip_msg ) {
		$licensing_info_attr = json_encode( $atts );

		$html  = '';
		$html .= '<div class="wpbdp-license-key-activation-ui wpbdp-license-status-' . esc_attr( $atts['status'] ) . '" data-licensing="' . esc_attr( $licensing_info_attr ) . '">';
		$html .= '<input type="text" id="' . esc_attr( $atts['setting'] ) . '" class="wpbdp-license-key-input" name="wpbdp_settings[' . esc_attr( $atts['setting'] ) . ']" value="' . esc_attr( $value ) . '" ' . ( 'valid' === $atts['status'] ? 'readonly="readonly"' : '' ) . ' placeholder="' . esc_attr__( 'Enter License Key here', 'business-directory-plugin' ) . '"/>';
		$html .= '<input type="button" value="' . esc_attr__( 'Authorize', 'business-directory-plugin' ) . '" data-working-msg="' . esc_attr( _x( 'Please wait...', 'settings', 'business-directory-plugin' ) ) . '" class="button button-primary wpbdp-license-key-activate-btn" />';
		$html .= '<input type="button" value="' . esc_attr( _x( 'Deauthorize', 'settings', 'business-directory-plugin' ) ) . '" data-working-msg="' . esc_attr( _x( 'Please wait...', 'settings', 'business-directory-plugin' ) ) . '" class="button wpbdp-license-key-deactivate-btn" />';
		if ( $tooltip_msg ) {
			$html .= '<span class="wpbdp-setting-description">' . esc_html( $tooltip_msg ) . '</span>';
		}

		$html .= '<div class="wpbdp-license-key-activation-status-msg wpbdp-hidden notice inline"></div>';
		$html .= '</div>';
		return $html;
	}

    public function license_key_changed_callback( $setting, $new_value = '', $old_value = '' ) {
        if ( $new_value == $old_value ) {
            return;
        }

        $this->licenses[ $setting['licensing_item_type'] . '-' . $setting['licensing_item'] ] = array(
			'license_key' => $new_value,
			'status'      => 'unknown',
		);
        update_option( 'wpbdp_licenses', $this->licenses );

        return $new_value;
    }

    function licenses_tab_css( $css, $tab_id ) {
        if ( 'licenses' == $tab_id ) {
            foreach ( $this->items as $item ) {
                if ( 'valid' != $this->get_license_status( '', $item['id'], $item['item_type'] ) ) {
                    $css .= ' tab-error';
                    break;
                }
            }
        }

        return $css;
    }

    /**
     * Returns the license status from license information.
     */
    public function get_license_status( $license_key = '', $item_id = '', $item_type = 'module' ) {
		$data_key = $item_type . '-' . $item_id;
		$no_prefix = $item_type . '-' . str_replace( 'bd-', '', $item_id );
		if ( ! empty( $this->licenses[ $no_prefix ] ) ) {
			// Allow for an extra 'bd-' on the theme folder name.
			$data_key = $no_prefix;
		}

		if ( ! $license_key ) {
			$license_key = wpbdp_get_option( 'license-key-' . $data_key );
		}

		if ( $license_key && ! empty( $this->licenses[ $data_key ] ) ) {
			$data = $this->licenses[ $data_key ];

			if ( ! empty( $data['license_key'] ) && $license_key == $data['license_key'] ) {
				return $data['status'];
			}
		}

        return 'invalid';
    }

	private function license_action( $item_type, $item_id, $action, $key = 0 ) {
        if ( ! in_array( $item_id, array_keys( $this->items ), true ) ) {
            return new WP_Error( 'invalid-module', esc_html__( 'Invalid item ID', 'business-directory-plugin' ) );
        }

        if ( 'deactivate' === $action ) {
            unset( $this->licenses[ $item_type . '-' . $item_id ] );
            update_option( 'wpbdp_licenses', $this->licenses );
			$key = wpbdp_get_var( array( 'param' => 'license_key' ), 'post' );
		}
		if ( ! $key ) {
			$key = wpbdp_get_option( 'license-key-' . $item_type . '-' . $item_id );
		}

		if ( ! $key ) {
			return new WP_Error( 'no-license-provided', esc_html__( 'No license key provided', 'business-directory-plugin' ) );
		}

        $request = array(
            'edd_action' => $action . '_license',
            'license'    => $key,
            'item_name'  => urlencode( $this->items[ $item_id ]['name'] ),
            'url'        => home_url(),
        );

        // Call the licensing server.
        $response = $this->license_request( add_query_arg( $request, self::STORE_URL ) );
		$this->get_license_errors();

        if ( is_wp_error( $response ) ) {
			if ( 'check' === $action ) {
				$this->licenses_errors[ $item_id ] = $response->get_error_message();
				$this->save_license_errors();
			}
            return $response;
        }

		if ( 'deactivate' !== $action ) {
			$license = $this->process_license_response( $response, $item_type, $item_id, $key );

			if ( is_wp_error( $license ) ) {
				$this->licenses_errors[ $item_id ] = $license->get_error_message();
				$this->save_license_errors();
				return $license;
			}

			if ( isset( $this->licenses_errors[ $item_id ] ) ) {
				unset( $this->licenses_errors[ $item_id ] );
				$this->save_license_errors();
			}

			$this->licenses[ $item_type . '-' . $item_id ] = $license;
			update_option( 'wpbdp_licenses', $this->licenses );

			return $license;
		}

        $license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( ! is_object( $license_data ) || ! isset( $license_data->license ) ) {
            return new WP_Error( 'invalid-license', esc_html__( 'License key is invalid', 'business-directory-plugin' ) );
        }

        if ( 'deactivated' !== $license_data->license ) {
            return new WP_Error( 'deactivation-failed', esc_html__( 'Deactivation failed', 'business-directory-plugin' ) );
        }

        return true;
    }

	/**
	 * @since 5.10
	 */
	private function save_license_errors() {
		update_option( 'wpbdp_licenses_errors', $this->licenses_errors, 'no' );
	}

	/**
	 * @since 5.10
	 */
	private function process_license_response( $response, $item_type, $item_id, $key ) {
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		if ( is_object( $license_data ) && isset( $license_data->license ) && 'valid' === $license_data->license ) {
			return array(
				'license_key'  => $key,
				'status'       => 'valid',
				'expires'      => $license_data->expires,
				'last_checked' => time(),
			);
		}

		$this->licenses[ $item_type . '-' . $item_id ]['status'] = 'invalid';
		update_option( 'wpbdp_licenses', $this->licenses );

		$is_revoked = isset( $license_data->error ) && 'revoked' === $license_data->error;

		if ( ! $is_revoked ) {
			$message = esc_html__( 'License key is invalid', 'business-directory-plugin' );
			return new WP_Error( 'invalid-license', $message );
		}

		return $this->revoked_license_error();
    }

	/**
	 * @since 5.10
	 */
	private function revoked_license_error() {
		$message  = '<strong>' . esc_html__( 'The license key was revoked.', 'business-directory-plugin' ) . '</strong>';
		$message .= '<br/><br/>';
		$message .= sprintf(
			/* translators: %1%s: opening <a> tag, %2$s: closing </a> tag */
			esc_html__( 'If you think this is a mistake, please contact %1$sBusiness Directory support%2$s and let them know your license is being reported as revoked by the licensing software. Please include the email address you used to purchase with your report.', 'business-directory-plugin' ),
			'<a href="https://businessdirectoryplugin.com/contact">',
			'</a>'
		);

		// The javascript handler already adds a dot at the end.
		$message = rtrim( $message, '.' );

		return new WP_Error( 'revoked-license', $message );
	}

    private function handle_failed_license_request( $response ) {
        if ( ! function_exists( 'curl_init' ) ) {
			return $this->curl_missing_error();
		}

        $ch = curl_init();

		curl_setopt( $ch, CURLOPT_URL, self::STORE_URL );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

        $r = curl_exec( $ch );

        $error_number  = curl_errno( $ch );
        $error_message = curl_error( $ch );

        curl_close( $ch );

		$error_id = 'request-failed';

        if ( in_array( $error_number, array( 7 ), true ) ) {
			$error_id = 'connection-refused';
			$message  = $this->curl_connection_error( $error_number, $error_message );
        } elseif ( in_array( $error_number, array( 35 ), true ) ) {
			$message = $this->ssl_curl_error( $error_number, $error_message );
		} else {
			$message  = _x( 'Could not contact licensing server', 'licensing', 'business-directory-plugin' );
		}

		// The javascript handler already adds a dot at the end.
		$message = rtrim( $message, '.' );

		return new WP_Error( $error_id, $message );
    }

	/**
	 * @since 5.10
	 */
	private function curl_missing_error() {
		$message  = '<strong>' . _x( "It was not possible to establish a connection with Business Directory's server. cURL was not found in your system", 'licensing', 'business-directory-plugin' ) . '</strong>';
		$message .= '<br/><br/>';
		$message .= _x( 'To ensure the security of our systems and adhere to industry best practices, we require that your server uses a recent version of cURL and a version of OpenSSL that supports TLSv1.2 (minimum version with support is OpenSSL 1.0.1c).', 'licensing', 'business-directory-plugin' );
		$message .= '<br/><br/>';
		$message .= _x( 'Upgrading your system will not only allow you to communicate with Business Directory servers but also help you prepare your website to interact with services using the latest security standards.', 'licensing', 'business-directory-plugin' );
		$message .= '<br/><br/>';
		$message .= _x( 'Please contact your hosting provider and ask them to upgrade your system. Include this message if necessary', 'licensing', 'business-directory-plugin' );
		return new WP_Error( 'request-failed', $message );
	}

	/**
	 * @since 5.10
	 */
	private function curl_connection_error( $error_number, $error_message ) {
		$message  = '<strong>' . __( 'It was not possible to establish a connection with the Business Directory server. The connection failed with the following error:', 'business-directory-plugin' ) . '</strong>';
		$message .= '<br/><br/>';
		$message .= '<code>curl: (' . esc_html( $error_number ) . ') ' . esc_html( $error_message ) . '</code>';
		$message .= '<br/><br/>';
		$message .= $this->unauthorized_message();

		return $message;
	}

	/**
	 * @since 5.10
	 */
	private function ssl_curl_error( $error_number, $error_message ) {
		$message = '<strong>' . __( 'It was not possible to establish a connection with the Business Directory server. A problem occurred in the SSL/TSL handshake:', 'business-directory-plugin' ) . '</strong>';

		$message .= '<br/><br/>';
		$message .= '<code>curl: (' . esc_html( $error_number ) . ') ' . esc_html( $error_message ) . '</code>';
		$message .= '<br/><br/>';
		$message .= _x( 'To ensure the security of our systems and adhere to industry best practices, we require that your server uses a recent version of cURL and a version of OpenSSL that supports TLSv1.2 (minimum version with support is OpenSSL 1.0.1c).', 'licensing', 'business-directory-plugin' );
		$message .= '<br/><br/>';
		$message .= _x( 'Upgrading your system will not only allow you to communicate with Business Directory servers but also help you prepare your website to interact with services using the latest security standards.', 'licensing', 'business-directory-plugin' );
		$message .= '<br/><br/>';
		$message .= _x( 'Please contact your hosting provider and ask them to upgrade your system. Include this message if necessary.', 'licensing', 'business-directory-plugin' );

		return $message;
	}

    private function license_request( $url ) {
        // Call the licensing server.
        $response = wp_remote_get(
            $url,
            array(
				'timeout'    => 15,
				'user-agent' => $this->user_agent_header(),
				'sslverify'  => false,
            )
        );

        if ( is_wp_error( $response ) ) {
            return $this->handle_failed_license_request( $response );
        }

        $response_code    = wp_remote_retrieve_response_code( $response );
        $response_message = wp_remote_retrieve_response_message( $response );

        if ( 403 == $response_code ) {
            $message = $this->unauthorized_message();

            return new WP_Error( 'request-not-authorized', $message );
        }

        return $response;
    }

	/**
	 * @since 5.10
	 */
	private function unauthorized_message() {
		$message  = '<strong>' . _x( 'The server returned a 403 Forbidden error.', 'licensing', 'business-directory-plugin' ) . '</strong>';
		$message .= '<br/><br/>';
		$message .= __( 'It looks like your server is not authorized to make outgoing requests to Business Directory servers. Please contact your webhost and ask them to add our IP address 52.0.78.177 to your allow list.', 'business-directory-plugin' );

		// The javascript handler already adds a dot at the end.
		$message = rtrim( $message, '.' );
		return $message;
	}

    function sort_modules_by_name( $x, $y ) {
        return strncasecmp( $x['name'], $y['name'], 4 );
    }

	/**
	 * @since 5.16 Chaged to only show notice to administrators.
	 */
    public function admin_notices() {
		if ( ! current_user_can( 'administrator' ) ) {
			return;
		}

        global $pagenow;

		$page = wpbdp_get_var( array( 'param' => 'page' ) );

		$is_settings = in_array( $pagenow, array( 'admin.php', 'edit.php' ) ) && 'wpbdp_settings' === $page;
		if ( $is_settings || empty( $this->items ) ) {
            return;
        }

        $expired = false;
        $invalid = false;

		$has_premium = false;

        foreach ( $this->items as $item ) {
            $status = $this->get_license_status( '', $item['id'], $item['item_type'] );
			if ( $item['id'] === 'business-directory-premium' ) {
				$has_premium = true;
			}

			if ( 'expired' === $status ) {
				$expired = true;
			} elseif ( 'valid' !== $status ) {
				$invalid = true;
			}
        }

		if ( $expired ) {
			$this->show_notice( 'expired_licenses' );
		} elseif ( $invalid ) {
			$this->show_notice( 'missing_licenses' );
		} elseif ( ! empty( $this->get_license_errors() ) ) {
			$this->show_notice( 'license_status_error' );
		} elseif ( ! $has_premium ) {
			// There are add-ons without Premium.
			$content = '<a href="https://businessdirectoryplugin.com/account/downloads/" target="_blank" class="wpbdp-button-primary">' .
				__( 'Download Premium', 'business-directory-plugin' ) .
				'</a>';
			$this->show_notice( 'missing_premium', $content );
		}
    }

	/**
	 * @since 5.10
	 */
	private function show_notice( $notice_id, $content = '' ) {
		$transient_key = 'wpbdp-notice-dismissed-' . $notice_id . '-' . get_current_user_id();
		if ( get_transient( $transient_key ) ) {
			// It's been dismissed.
			return;
		}

		$nonce  = wp_create_nonce( 'dismiss notice ' . $notice_id );
		$class  = 'wpbdp-notice notice notice-error wpbdp-error is-dismissible';

		?>
		<div id="wpbdp-licensing-issues-warning" class="<?php echo esc_attr( $class ); ?>" data-dismissible-id="<?php echo esc_attr( $notice_id ); ?>" data-nonce="<?php echo esc_attr( $nonce ); ?>">
			<p>
				<b><?php echo esc_html( $this->license_notice( $notice_id ) ); ?></b>
			</p>
			<div>
				<?php
				if ( empty( $content ) ) {
					$this->link_to_license_page();
				} else {
					echo wp_kses_post( $content );
				}
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * @since 5.10
	 */
	private function license_notice( $type ) {
		$messages = $this->license_notices();
		return isset( $messages[ $type ] ) ? $messages[ $type ] : '';
	}

	/**
	 * @since 5.10
	 */
	private function license_notices() {
		return array(
			'missing_licenses'     => __( 'Business Directory license key is missing.', 'business-directory-plugin' ),
			'expired_licenses'     => __( 'Business Directory license key has expired', 'business-directory-plugin' ),
			'license_status_error' => __( 'Could not verify Business Directory license.', 'business-directory-plugin' ),
			'missing_premium'      => __( 'You have modules installed, but Business Directory Premium is missing. Install this plugin for extra features and easy license management.', 'business-directory-plugin' ),
		);
	}

	/**
	 * @since 5.9.1
	 */
	private function link_to_license_page() {
		?>
		<p>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=wpbdp_settings&tab=licenses' ) ); ?>" class="button button-primary">
    			<?php esc_html_e( 'Review license keys', 'business-directory-plugin' ); ?>
			</a>
		</p>
		<?php
	}

    public function dismiss_expired_licenses_notification() {
        set_transient( 'wpbdp-expired-licenses-notice-dismissed-' . get_current_user_id(), true, 2 * WEEK_IN_SECONDS );
    }

	/**
	 * @since 5.10
	 */
	public function dismiss_notification() {
		$nonce  = wpbdp_get_var( array( 'param' => 'nonce' ), 'post' );
		$id     = wpbdp_get_var( array( 'param' => 'id' ), 'post' );
		$time   = 'expired_licenses' === $id ? 2 * WEEK_IN_SECONDS : WEEK_IN_SECONDS;
		if ( wp_verify_nonce( $nonce, 'dismiss notice ' . $id ) ) {
			set_transient( "wpbdp-notice-dismissed-{$id}-" . get_current_user_id(), $time );
		}
	}

    public function license_check() {
		_deprecated_function( __METHOD__, '5.14' );

		$last_license_check = get_site_transient( 'wpbdp-license-check-time' );

		if ( ! empty( $last_license_check ) ) {
            return;
        }

        $this->licenses = $this->get_licenses_status();
        update_option( 'wpbdp_licenses', $this->licenses );

		set_site_transient( 'wpbdp-license-check-time', current_time( 'timestamp' ), WEEK_IN_SECONDS );
    }

    public function get_licenses_status() {
		$licenses = array();

		if ( ! $this->items ) {
			return $licenses;
		}

		// This verifies all licenses, clear licenses_errors property.
		$this->licenses_errors = array();
		$this->save_license_errors();

		$pro_id  = $this->premium_id();
		$pro_key = wpbdp_get_option( 'license-key-module-' . $pro_id );

		if ( isset( $this->items[ $pro_id ] ) ) {
			// Check Premium first.
			$this->check_single_license( $this->items[ $pro_id ], $pro_key, $licenses );
		}

		foreach ( $this->items as $item ) {
			if ( $item['id'] !== 'premium' ) {
				$this->check_single_license( $item, $pro_key, $licenses );
			}
        }

		$this->save_license_errors();

        return $licenses;
    }

	/**
	 * @since 5.10
	 */
	private function check_single_license( $item, $pro_key, &$licenses ) {
		$item_key = $item['item_type'] . '-' . $item['id'];
		$key      = wpbdp_get_option( 'license-key-' . $item_key );

		if ( ! $key && ! $pro_key ) {
			$licenses[ $item_key ] = array(
				'status'       => 'invalid',
				'last_checked' => time(),
			);
			return;
		}

		$pro_id      = $this->premium_id();
		$should_skip = $pro_key && ( $pro_key === $key || ! $key ) && $item['id'] !== $pro_id && isset( $licenses[ 'module-' . $pro_id ] );
		$should_skip = apply_filters( 'wpbdp_skip_license_check', $should_skip, $item );
		if ( $should_skip ) {
			// Only check premium license once.
			$licenses[ $item_key ] = $licenses[ 'module-' . $pro_id ];
			return;
		}

		$response = $this->license_action( $item['item_type'], $item['id'], 'check' );

		if ( is_wp_error( $response ) ) {
			$licenses[ $item_key ]             = $this->licenses[ $item_key ];
			$this->licenses_errors[ $item['id'] ] = $response->get_error_message();
			return;
		}

		$licenses[ $item_key ] = $response;
	}

    public function ajax_activate_license() {
		$nonce = wpbdp_get_var( array( 'param' => 'nonce' ), 'post' );
		if ( ! wp_verify_nonce( $nonce, 'license activation' ) ) {
			wp_die();
		}

		$setting_id = wpbdp_get_var( array( 'param' => 'setting' ), 'post' );
		$key        = wpbdp_get_var( array( 'param' => 'license_key' ), 'post' );
		$item_type  = wpbdp_get_var( array( 'param' => 'item_type' ), 'post' );
		$item_id    = wpbdp_get_var( array( 'param' => 'item_id' ), 'post' );
		$response   = array( 'success' => false );

        if ( ! $setting_id || ! $item_type || ! $item_id ) {
			$response['error'] = esc_html__( 'Missing data. Please reload this page and try again.', 'business-directory-plugin' );
			wp_send_json( $response );
        }

        if ( ! $key ) {
			$response['error'] = esc_html__( 'Please enter a license key.', 'business-directory-plugin' );
			wp_send_json( $response );
        }

        // Store the new license key. This clears stored information about the license.
        wpbdp_set_option( 'license-key-' . $item_type . '-' . $item_id, $key );

		$result = $this->license_action( $item_type, $item_id, 'activate', $key );

		$this->get_license_errors();

		if ( is_wp_error( $result ) ) {
			// Save the message for later.
			$this->licenses_errors[ $item_id ] = $result->get_error_message();

			$response = array(
				'success' => false,
				'error'   => sprintf( _x( 'Could not activate license: %s.', 'licensing', 'business-directory-plugin' ), $result->get_error_message() ),
			);
        } else {
			$response = array(
				'success' => true,
				'message' => _x( 'License activated', 'licensing', 'business-directory-plugin' ),
			);

			// Remove any saved error messages.
			if ( isset( $this->licenses_errors[ $item_id ] ) ) {
				unset( $this->licenses_errors[ $item_id ] );
			}
        }

		$this->save_license_errors();
		wp_send_json( $response );
    }

    public function ajax_deactivate_license() {
		$nonce = wpbdp_get_var( array( 'param' => 'nonce' ), 'post' );
		if ( ! wp_verify_nonce( $nonce, 'license activation' ) ) {
			wp_die();
		}

		$setting_id = wpbdp_get_var( array( 'param' => 'setting' ), 'post' );
		$key        = wpbdp_get_var( array( 'param' => 'license_key' ), 'post' );
		$item_type  = wpbdp_get_var( array( 'param' => 'item_type' ), 'post' );
		$item_id    = wpbdp_get_var( array( 'param' => 'item_id' ), 'post' );

        if ( ! $setting_id || ! $key || ! $item_type || ! $item_id ) {
            wp_die();
        }

		$result   = $this->license_action( $item_type, $item_id, 'deactivate' );
        $response = new WPBDP_AJAX_Response();

        if ( is_wp_error( $result ) ) {
            $response->send_error( sprintf( _x( 'Could not deactivate license: %s.', 'licensing', 'business-directory-plugin' ), $result->get_error_message() ) );
        } else {
            $response->set_message( _x( 'License deactivated', 'licensing', 'business-directory-plugin' ) );
            $response->send();
        }
    }

    public function get_version_information( $force_refresh = false ) {
        if ( ! $this->items ) {
            return array();
        }

		$store_url = untrailingslashit( self::STORE_URL );

		$updates = get_option( 'wpbdp_updates' );

		$due = current_time( 'timestamp' ) - DAY_IN_SECONDS;

		$needs_refresh = false === $updates || $force_refresh || $updates['last'] < $due;

        foreach ( $this->items as $item ) {
            if ( ! isset( $updates[ $item['item_type'] . '-' . $item['id'] ] ) ) {
                $needs_refresh = true;
                break;
            }
        }

        if ( ! $needs_refresh ) {
            return $updates;
        }

        $args = array(
            'edd_action' => 'batch_get_version',
            'licenses'   => array(),
            'items'      => array(),
            'url'        => home_url(),
        );

		$licenses = array();
        foreach ( $this->items as $item ) {
			$license            = wpbdp_get_option( 'license-key-' . $item['item_type'] . '-' . $item['id'] );
			$args['licenses'][] = $license;
			$args['items'][]    = $item['name'];
			$licenses[ $item['id'] ] = $license;
        }

        $request = wp_remote_get(
            self::STORE_URL,
            array(
				'timeout'    => 15,
				'user-agent' => $this->user_agent_header(),
				'sslverify'  => false,
				'body'       => $args,
            )
        );

        if ( is_wp_error( $request ) ) {
            return array();
        }

        $body = wp_remote_retrieve_body( $request );
        $body = json_decode( $body );

        if ( ! is_array( $body ) ) {
            return array();
        }

        foreach ( $body as $i => $item_information ) {
            if ( isset( $item_information->sections ) ) {
                $body[ $i ]->sections = maybe_unserialize( $item_information->sections );
            }
        }

        $updates = array();
        foreach ( $this->items as $item ) {
            $item_key = $item['item_type'] . '-' . $item['id'];

            foreach ( $body as $item_information ) {
				if ( trim( $item_information->name ) !== trim( $item['name'] ) || empty( $item_information->license ) ) {
					continue;
				}

				$updates[ $item_key ]       = $item_information;
				$updates[ $item_key ]->slug = $item['id'];

				// Update the license status too.
				if ( $item['id'] === $this->premium_id() ) {
					// Handle premium from it's own updater.
					continue;
				}

				$this->licenses[ $item_key ] = array(
					'license_key'  => $item_information->license,
					'status'       => $item_information->license_status,
					'expires'      => isset( $item_information->expires ) ? $item_information->expires : '',
					'last_checked' => time(),
					'bundle'       => $item['item_type'] === 'module' && $item_information->bundle,
				);
            }
        }

		$updates['last'] = current_time( 'timestamp' );
		update_option( 'wpbdp_updates', $updates, false );
		update_option( 'wpbdp_licenses', $this->licenses );

        return $updates;
    }

    /**
     * Inject BD modules update info into update array (`update_plugins` transient).
     */
    public function inject_update_info( $transient ) {
        if ( ! is_object( $transient ) ) {
            $transient = new stdClass();
        }

        global $pagenow;

        if ( 'plugins.php' == $pagenow && is_multisite() ) {
            return $transient;
        }

        $updates = $this->get_version_information();

        if ( ! $updates ) {
            return $transient;
        }

		$modules = $this->modules_array();

        foreach ( $modules as $module ) {

            $item_key = $module['item_type'] . '-' . $module['id'];

            if ( ! isset( $updates[ $item_key ] ) ) {
                continue;
            }

            $wp_name = plugin_basename( $module['file'] );

            if ( ! empty( $transient->response ) && ! empty( $transient->response[ $wp_name ] ) ) {
                continue;
            }

            if ( ! isset( $updates[ $item_key ]->new_version ) ) {
                continue;
            }

            if ( version_compare( $module['version'], $updates[ $item_key ]->new_version, '<' ) ) {
                $transient->response[ $wp_name ] = $updates[ $item_key ];
            }

            $transient->last_checked        = current_time( 'timestamp' );
            $transient->checked[ $wp_name ] = $module['version'];
        }

        return $transient;
    }

	/**
	 * Get item version.
	 * Get the update information of an item.
	 *
	 * @todo change to new rest api.
	 *
	 * @param array $item The module item.
	 *
	 * @since 5.17
	 *
	 * @return mixed
	 */
	private function get_item_version( $item ) {
		$http_args = array(
			'timeout'   => 15,
			'sslverify' => false,
			'body'      => array(
				'edd_action' => 'get_version',
				'item_name'  => $item['name'],
				'license'    => wpbdp_get_option( 'license-key-' . $item['item_type'] . '-' . $item['id'] ),
				'url'        => home_url(),
			),
		);
		$request   = wp_remote_post( self::STORE_URL, $http_args );
		return $request;
	}

	/**
	 * Fill the info in the plugin details popup.
	 */
    public function module_update_information( $data, $action = '', $args = null ) {
        if ( 'plugin_information' != $action || ! isset( $args->slug ) ) {
            return $data;
        }

        $item = isset( $this->items[ $args->slug ] ) ? $this->items[ $args->slug ] : false;
        if ( ! $item ) {
            return $data;
        }

		$request = $this->get_item_version( $item );

        if ( ! is_wp_error( $request ) ) {
            $request = json_decode( wp_remote_retrieve_body( $request ) );

            if ( $request && is_object( $request ) && isset( $request->sections ) ) {
                $request->sections = maybe_unserialize( $request->sections );
                $data              = $request;
            }
        }

        return $data;
    }

    function user_agent_header() {
        $user_agent = 'WordPress %s / Business Directory Plugin %s';
        $user_agent = sprintf( $user_agent, get_bloginfo( 'version' ), WPBDP_VERSION );
        return $user_agent;
    }

	/**
	 * @deprecated 5.9.1
	 */
	public function admin_menu() {
		_deprecated_function( __METHOD__, '5.9.1' );
	}
}

/**
 * @since 3.4.2
 * @deprecated since 5.0.
 */
function wpbdp_licensing_register_module( $name, $file_, $version ) {
	_deprecated_function( __FUNCTION__, '5.0' );

    global $wpbdp_compat_modules_registry;

    if ( ! isset( $wpbdp_compat_modules_registry ) ) {
        $wpbdp_compat_modules_registry = array();
    }

    // TODO: Use numbered placeholders with sprintf or named placeholders with str_replace.
    /* translators: "<module-name>" version <version-number> is not... */
    wpbdp_deprecation_warning( sprintf( _x( '"%1$s" version %2$s is not compatible with Business Directory Plugin 5.0. Please update this module to the latest available version.', 'deprecation', 'business-directory-plugin' ), '<strong>' . esc_html( $name ) . '</strong>', '<strong>' . $version . '</strong>' ) );
    $wpbdp_compat_modules_registry[] = array( $name, $file_, $version );

    return false;
}

/**
 * Added for compatibility with < 5.x modules.
 *
 * @since 5.0.1
 */
function wpbdp_compat_register_old_modules() {
    global $wpbdp_compat_modules_registry;

    if ( ! isset( $wpbdp_compat_modules_registry ) || empty( $wpbdp_compat_modules_registry ) ) {
        $wpbdp_compat_modules_registry = array();
    }

    // Gateways are a special case since they are registered in 'wpbdp_register_gateways'.
    if ( has_filter( 'wpbdp_register_gateways' ) ) {
        if ( function_exists( 'wp_get_active_and_valid_plugins' ) ) {
            $plugins = wp_get_active_and_valid_plugins();

            foreach ( $plugins as $plugin_file ) {
                $plugin_file_basename = basename( $plugin_file );

                if ( 'business-directory-paypal.php' == $plugin_file_basename ) {
                    $wpbdp_compat_modules_registry[] = array( 'PayPal Gateway Module', $plugin_file, '3.5.6' );
                } elseif ( 'business-directory-twocheckout.php' == $plugin_file_basename ) {
                    $wpbdp_compat_modules_registry[] = array( '2Checkout Gateway Module', $plugin_file, '3.6.2' );
                }
            }
        }
    }

    foreach ( $wpbdp_compat_modules_registry as $m ) {
        wpbdp()->licensing->add_item(
            array(
				'item_type' => 'module',
				'name'      => $m[0],
				'file'      => $m[1],
				'version'   => $m[2],
            )
        );
    }
}
add_action( 'wpbdp_licensing_before_updates_check', 'wpbdp_compat_register_old_modules' );
