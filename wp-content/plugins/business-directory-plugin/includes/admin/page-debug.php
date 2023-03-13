<?php

class WPBDP_Admin_Debug_Page {

    public function __construct() {
		add_action( 'admin_init', array( &$this, 'handle_download' ) );
		add_filter( 'debug_information', array( &$this, 'register_debug_information' ) );
    }

	public function register_debug_information( $debug_info ) {
		$basic = $this->get_basic_debug();
		unset( $basic['_title'] );

		$debug = array(
			'label'  => __( 'Business Directory Plugin', 'business-directory-plugin' ),
			'fields' => [],
		);

		foreach ( $basic as $label => $value ) {
			if ( is_array( $value ) ) {
				$value = isset( $value['value'] ) ? $value['value'] : reset( $value );
			}
			$debug['fields'][] = [
				'label' => $label,
				'value' => $value,
			];
		}

		$debug_info['wpbdp'] = $debug;
		return $debug_info;
	}

    public function dispatch( $plain = false ) {

		$debug_info = array();

        // BD options
        $blacklisted                     = array( 'authorize-net-transaction-key', 'authorize-net-login-id', 'googlecheckout-merchant', 'paypal-business-email', 'wpbdp-2checkout-seller', 'recaptcha-public-key', 'recaptcha-private-key' );
		$partial_block = array( 'secret-key', 'publishable-key', 'private-key', 'public-key', 'license-key' );
        $debug_info['options']['_title'] = __( 'Plugin Settings', 'business-directory-plugin' );

        $settings_api = wpbdp_settings_api();
        $all_settings = $settings_api->get_registered_settings();
        foreach ( $all_settings as $s ) {
            if ( in_array( $s['id'], $blacklisted ) ) {
                continue;
            }

			foreach ( $partial_block as $blockme ) {
				if ( strpos( $s['id'], $blockme ) !== false ) {
					continue 2;
				}
			}

            $value = wpbdp_get_option( $s['id'] );

            if ( is_array( $value ) ) {
                if ( empty( $value ) ) {
                    $value = '';
                } else {
					$value = print_r( $value, true );
                }
            }

            $debug_info['options'][ $s['id'] ] = $value;
        }
        $debug_info['options'] = apply_filters( 'wpbdp_debug_info_section', $debug_info['options'], 'options' );

        // environment info
        $debug_info['environment']['_title']            = __( 'Environment', 'business-directory-plugin' );

        $debug_info['environment'] = apply_filters( 'wpbdp_debug_info_section', $debug_info['environment'], 'environment' );

		if ( count( $debug_info['environment'] ) === 1 ) {
			unset( $debug_info['environment'] );
		}

        $debug_info = apply_filters( 'wpbdp_debug_info', $debug_info );

        if ( $plain ) {
            foreach ( $debug_info as &$section ) {
                foreach ( $section as $k => $v ) {
                    if ( $k == '_title' ) {
                        printf( '== %s ==', esc_html( $v ) );
                        print PHP_EOL;
                        continue;
                    }

                    if ( is_array( $v ) ) {
                        if ( isset( $v['exclude'] ) && $v['exclude'] ) {
                            continue;
                        }

                        if ( ! empty( $v['html'] ) && empty( $v['value'] ) ) {
                            continue;
                        }
                    }

					printf( '%-33s = %s', esc_html( $k ), is_array( $v ) ? esc_html( $v['value'] ) : esc_html( $v ) );
                    print PHP_EOL;
                }

                print PHP_EOL . PHP_EOL;
            }
            return;
        }

        wpbdp_render_page( WPBDP_PATH . 'templates/admin/debug-info.tpl.php', array( 'debug_info' => $debug_info ), true );
    }

	private function get_basic_debug() {
		global $wpbdp;

		$debug_info = array(
			'_title'  => __( 'Plugin Info', 'business-directory-plugin' ),
			'Version' => WPBDP_VERSION,
		);

		$debug_info['Database revision (current)']   = WPBDP_Installer::DB_VERSION;
		$debug_info['Database revision (installed)'] = get_option( 'wpbdp-db-version' );

		$debug_info['Modules']     = $this->installed_plugins();
		$debug_info['Table check'] = $this->table_info();

		$debug_info['Main Page'] = sprintf( '%d (%s)', wpbdp_get_page_id( 'main' ), get_post_status( wpbdp_get_page_id( 'main' ) ) );
		$debug_info              = apply_filters( 'wpbdp_debug_info_section', $debug_info, 'basic' );

		return $debug_info;
	}

	private function installed_plugins() {
		global $wpbdp;

		// Premium modules.
		$mod_versions = array();
		foreach ( $wpbdp->licensing->get_items() as $m ) {
			$mod_versions[] = str_replace( ' Module', '', $m['name'] ) . ' - ' . $m['version'];
		}
		if ( class_exists( 'WPBDP_CategoriesModule' ) ) {
			$mod_versions[] = 'Enhanced Categories - ' . WPBDP_CategoriesModule::VERSION;
		}

		return array(
			'value' => implode( ', ', $mod_versions ),
			'html'  => implode( '<br />', $mod_versions ),
		);
	}

	private function table_info() {
		global $wpdb;

		$tables         = apply_filters( 'wpbdp_debug_info_tables_check', array( 'wpbdp_form_fields', 'wpbdp_plans', 'wpbdp_payments', 'wpbdp_listings' ) );
		$missing_tables = array();
		foreach ( $tables as &$t ) {
			if ( $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $wpdb->prefix . $t ) ) == '' ) {
				$missing_tables[] = $t;
			}
		}
		$status = __( 'OK', 'business-directory-plugin' );
		if ( $missing_tables ) {
			$status = sprintf( __( 'Missing tables: %s', 'business-directory-plugin' ), implode( ',', $missing_tables ) );
		}
		return $status;
	}

    public function handle_download() {
        global $pagenow;

        if ( ! current_user_can( 'administrator' ) || ! in_array( $pagenow, array( 'admin.php', 'edit.php' ) )
             || 'wpbdp-debug-info' !== wpbdp_get_var( array( 'param' => 'page' ) ) ) {
            return;
        }

		if ( 1 === (int) wpbdp_get_var( array( 'param' => 'download' ) ) ) {
                    header( 'Content-Description: File Transfer' );
                    header( 'Content-Type: text/plain; charset=' . get_option( 'blog_charset' ), true );
                    header( 'Content-Disposition: attachment; filename=wpbdp-debug-info.txt' );
                    header( 'Pragma: no-cache' );
                    $this->dispatch( true );
                    exit;
        }
    }

	/**
	 * @deprecated since 5.9.1
	 */
    public function ajax_ssl_test() {
		_deprecated_function( __METHOD__, '5.9.1' );
    }
}
