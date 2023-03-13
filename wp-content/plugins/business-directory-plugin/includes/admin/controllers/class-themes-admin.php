<?php
/**
 * @since 4.0
 */
class WPBDP_Themes_Admin {

    private $api;
    private $licensing;
    private $outdated_themes = array();

    function __construct( &$api, $licensing ) {
        $this->api             = $api;
        $this->licensing       = $licensing;
        $this->outdated_themes = $this->find_outdated_themes();

        add_filter( 'wpbdp_admin_menu_badge_number', array( &$this, 'admin_menu_badge_count' ) );
        add_action( 'wpbdp_admin_menu', array( &$this, 'admin_menu' ) );
        add_filter( 'wpbdp_admin_menu_reorder', array( &$this, 'admin_menu_move_themes_up' ) );

        add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );

        add_action( 'wpbdp_action_set-active-theme', array( &$this, 'set_active_theme' ) );
        add_action( 'wpbdp_action_delete-theme', array( &$this, 'delete_theme' ) );
        add_action( 'wpbdp_action_upload-theme', array( &$this, 'upload_theme' ) );
        add_action( 'wpbdp_action_create-theme-suggested-fields', array( &$this, 'create_suggested_fields' ) );

        add_action( 'wp_ajax_wpbdp-themes-update', array( &$this, '_update_theme' ) );

        // add_action( 'wpbdp-admin-themes-extra', array( &$this, 'enter_license_key_row' ) );
    }

    function admin_menu( $slug ) {
        $count = count( $this->outdated_themes );

        if ( $count ) {
            $count_html = ' <span class="update-plugins"><span class="plugin-count">' . number_format_i18n( $count ) . '</span></span>';
        } else {
			$count_html = '';
        }

        add_submenu_page(
            $slug,
            _x( 'Directory Themes', 'themes', 'business-directory-plugin' ),
            __( 'Themes', 'business-directory-plugin' ) . $count_html,
            'administrator',
            'wpbdp-themes',
            array( &$this, 'dispatch' )
        );
    }

    function admin_menu_badge_count( $cnt = 0 ) {
        return ( (int) $cnt ) + count( $this->outdated_themes );
    }

    function admin_menu_move_themes_up( $menu ) {
        $themes_key = false;

        foreach ( $menu as $k => $i ) {
            if ( 'wpbdp-themes' === $i[2] ) {
                $themes_key = $k;
                break;
            }
        }

        if ( false === $themes_key ) {
            return $menu;
        }

        $themes = $menu[ $themes_key ];
        unset( $menu[ $themes_key ] );
        $menu = array_merge( array( $menu[0], $themes ), array_slice( $menu, 1 ) );

        return $menu;
    }

    function enqueue_scripts( $hook ) {
        global $pagenow;

		$page = wpbdp_get_var( array( 'param' => 'page' ) );

        if ( ! in_array( $pagenow, array( 'admin.php', 'edit.php' ) ) || 'wpbdp-themes' !== $page ) {
            return;
        }

        wp_enqueue_script(
            'wpbdp-admin-themes',
            WPBDP_ASSETS_URL . 'js/admin-themes.min.js',
            array(),
            WPBDP_VERSION,
			true
        );
    }

    function set_active_theme() {
        if ( ! current_user_can( 'administrator' ) ) {
            wp_die();
        }

        $theme_id = wpbdp_get_var( array( 'param' => 'theme_id' ), 'post' );

        if ( ! wp_verify_nonce( wpbdp_get_var( array( 'param' => '_wpnonce' ), 'post' ), 'activate theme ' . $theme_id ) ) {
            wp_die();
        }

        if ( ! $this->api->set_active_theme( $theme_id ) ) {
            wp_die( sprintf( _x( 'Could not change the active theme to "%s".', 'themes', 'business-directory-plugin' ), $theme_id ) );
        }

        wp_redirect( admin_url( 'admin.php?page=wpbdp-themes&message=1' ) );
        exit;
    }

    function create_suggested_fields() {
        if ( ! current_user_can( 'administrator' ) || ! wp_verify_nonce( wpbdp_get_var( array( 'param' => '_wpnonce' ) ), 'create_suggested_fields' ) ) {
            wp_die();
        }

        $missing = $this->api->missing_suggested_fields();

        global $wpbdp;
        $wpbdp->formfields->create_default_fields( $missing );

        wp_safe_redirect( admin_url( 'admin.php?page=wpbdp_admin_formfields&action=updatetags' ) );
        exit;
    }

    function dispatch() {
        $action = wpbdp_get_var( array( 'param' => 'action', 'default' => wpbdp_get_var( array( 'param' => 'v' ) ) ) );

        switch ( $action ) {
            case 'theme-install':
                return $this->theme_install();
            case 'delete-theme':
                return $this->theme_delete_confirm();
            case 'theme-selection':
            default:
                return $this->theme_selection();
        }
    }

    function theme_selection() {
        $msg = wpbdp_get_var( array( 'param' => 'message' ) );

        switch ( $msg ) {
            case 1:
				$name = $this->api->get_active_theme_data( 'name' );
				wpbdp_admin_message( sprintf( _x( 'Active theme changed to "%s".', 'themes', 'business-directory-plugin' ), $name ) );

                if ( $missing_fields = $this->api->missing_suggested_fields( 'label' ) ) {
					$msg  = sprintf( _x( '%s requires that you tag your existing fields to match some places we want to put your data on the theme. Below are fields we think are missing.', 'themes', 'business-directory-plugin' ), $name );
                    $msg .= '<br />';

                    foreach ( $missing_fields as $mf ) {
                        $msg .= '<span class="tag">' . $mf . '</span>';
                    }

                    $msg .= '<br /><br />';
                    $msg .= sprintf(
                        '<a href="%s" class="button button-primary">%s</a>',
                        esc_url( admin_url( 'admin.php?page=wpbdp_admin_formfields&action=updatetags' ) ),
                        _x( 'Map My Fields', 'themes', 'business-directory-plugin' )
                    );

                    wpbdp_admin_message( $msg, 'error' );
                }

                break;
            case 2:
                wpbdp_admin_message( _x( 'Suggested fields created successfully.', 'themes', 'business-directory-plugin' ) );
                break;
            case 3:
                wpbdp_admin_message( _x( 'Theme installed successfully.', 'themes', 'business-directory-plugin' ) );
                break;
            case 4:
                wpbdp_admin_message( _x( 'Theme was deleted successfully.', 'themes', 'business-directory-plugin' ) );
                break;
            case 5:
                wpbdp_admin_message( _x( 'Could not delete theme directory. Check permissions.', 'themes', 'business-directory-plugin' ), 'error' );
                break;
            default:
                break;
        }

        $themes       = $this->get_installed_themes();
        $active_theme = $this->api->get_active_theme();

        // Make sure the current theme is always first.
        $current = $themes[ $active_theme ];
        unset( $themes[ $active_theme ] );
        array_unshift( $themes, $current );

        echo wpbdp_render_page(
            WPBDP_PATH . 'templates/admin/themes.tpl.php',
            array(
                'themes'          => $themes,
                'active_theme'    => $active_theme,
                'outdated_themes' => $this->outdated_themes,
            )
        );
    }

    private function get_installed_themes() {
        $themes = $this->api->get_installed_themes();

        foreach ( $themes as &$theme ) {
            if ( $theme->is_core_theme ) {
                $license_status = 'valid';
            } else {
                $license_status = $this->licensing->get_license_status( null, $theme->id, 'theme' );
            }

            $theme->can_be_activated = 'valid' === $license_status;
			$theme->license_status   = $license_status;
        }

        return $themes;
    }

    function upload_theme() {
		$nonce = wpbdp_get_var( array( 'param' => '_wpnonce' ), 'post' );

		if ( ! current_user_can( 'administrator' ) || ! wp_verify_nonce( $nonce, 'upload theme zip' ) ) {
            wp_die();
        }

		// phpcs:ignore WordPress.Security.ValidatedSanitizedInput
        $theme_file = isset( $_FILES['themezip'] ) ? $_FILES['themezip'] : false;
		wpbdp_sanitize_value( 'sanitize_text_field', $theme_file );

		if ( ! $theme_file || ! is_uploaded_file( $theme_file['tmp_name'] ) || UPLOAD_ERR_OK != $theme_file['error'] ) {
            wpbdp_admin_message( _x( 'Please upload a valid theme file.', 'themes', 'business-directory-plugin' ), 'error' );
            return;
        }

        $dest = wp_normalize_path( untrailingslashit( get_temp_dir() ) . DIRECTORY_SEPARATOR . $theme_file['name'] );

        if ( ! move_uploaded_file( $theme_file['tmp_name'], $dest ) ) {
            wpbdp_admin_message(
                sprintf(
                    _x( 'Could not move "%s" to a temporary directory.', 'themes', 'business-directory-plugin' ),
                    $theme_file['name']
                ),
                'error'
            );
            return;
        }

        $res = $this->api->install_theme( $dest );

        if ( is_wp_error( $res ) ) {
            wpbdp_admin_message( $res->get_error_message(), 'error' );
            return;
        }

        wp_redirect( admin_url( 'admin.php?page=wpbdp-themes&message=3' ) );
        exit;
    }

    function theme_install() {
        echo wpbdp_render_page(
            WPBDP_PATH . 'templates/admin/themes-install.tpl.php',
            array()
        );
    }

    function theme_delete_confirm() {
		$theme_id = wpbdp_get_var( array( 'param' => 'theme_id' ), 'request' );
        $theme    = $this->api->get_theme( $theme_id );

        echo wpbdp_render_page(
            WPBDP_PATH . 'templates/admin/themes-delete-confirm.tpl.php',
            array( 'theme' => $theme )
        );
    }

    function delete_theme() {
        if ( ! isset( $_POST['dodelete'] ) || 1 != $_POST['dodelete'] ) {
            return;
        }

        // Cancel. Return to themes page.
        if ( empty( $_POST['delete-theme'] ) ) {
            wp_redirect( admin_url( 'admin.php?page=wpbdp-themes' ) );
            exit;
        }

		$theme_id = wpbdp_get_var( array( 'param' => 'theme_id' ), 'post' );
		$nonce    = wpbdp_get_var( array( 'param' => '_wpnonce' ), 'post' );

        if ( ! current_user_can( 'administrator' ) || ! wp_verify_nonce( $nonce, 'delete theme ' . $theme_id ) ) {
            wp_die();
        }

        $active_theme = $this->api->get_active_theme();
        $theme        = $this->api->get_theme( $theme_id );

        if ( in_array( $theme_id, array( 'default', 'no_theme', $active_theme ), true ) || ! $theme ) {
            wp_die();
        }

        $theme   = $this->api->get_theme( $theme_id );
        $path    = rtrim( $theme->path, '/\\' );
        $removed = false;

        if ( is_link( $path ) ) {
            $removed = unlink( $path );
        } elseif ( is_dir( $path ) ) {
            $removed = WPBDP_FS::rmdir( $path );
        }

        if ( $removed ) {
            wp_redirect( admin_url( 'admin.php?page=wpbdp-themes&message=4&deleted=' . $theme_id ) );
        } else {
			wp_redirect( admin_url( 'admin.php?page=wpbdp-themes&message=5&deleted=' . $theme_id ) );
        }

        exit;
    }

    function enter_license_key_row( $theme ) {
        if ( $theme->can_be_activated ) {
            return;
        }

        echo '<div class="wpbdp-theme-license-required-row">';
        echo str_replace( '<a>', '<a href="' . esc_url( admin_url( 'admin.php?page=wpbdp-themes&v=licenses' ) ) . '">', _x( 'Activate your <a>license key</a> to use this theme.', 'themes', 'business-directory-plugin' ) );
        echo '</div>';
    }

    public function find_outdated_themes() {
        $version  = $this->licensing->get_version_information();
        $themes   = $this->get_installed_themes();
        $outdated = array();

        foreach ( $themes as $theme_id => $theme_data ) {
            if ( ! $theme_data->can_be_activated ) {
                continue;
            }

            if ( ! array_key_exists( 'theme-' . $theme_id, $version ) ) {
                continue;
            }

            if ( ! version_compare( $theme_data->version, $version[ 'theme-' . $theme_id ]->new_version, '<' ) ) {
                continue;
            }

            $outdated[] = $theme_id;
        }

        return $outdated;
    }

    // Theme update process. {{

    public function _update_theme() {
		$nonce    = wpbdp_get_var( array( 'param' => '_wpnonce' ), 'request' );
		$theme_id = wpbdp_get_var( array( 'param' => 'theme' ), 'request' );

		if ( ! current_user_can( 'administrator' ) || ! wp_verify_nonce( $nonce, 'update theme ' . $theme_id ) ) {
            die();
        }

        $response = array( 'success' => false );

        $theme    = $this->api->get_theme( $theme_id );

        if ( ! $theme ) {
            $response['error'] = _x( 'Invalid theme ID', 'themes', 'business-directory-plugin' );
            return wp_send_json( $response );
        }

        $result = $this->run_update( $theme_id );
        if ( is_wp_error( $result ) ) {
            $response['error'] = sprintf( _x( 'Could not update theme: %s', 'themes', 'business-directory-plugin' ), $result->get_error_message() );
            return wp_send_json( $response );
        }

        $this->api->find_themes( true );
        unset( $this->outdated_themes[ $theme_id ] );

        $response['html']    = wpbdp_render_page(
            WPBDP_PATH . 'templates/admin/themes-item.tpl.php',
            array(
                'theme' => $this->api->get_theme( $theme_id ),
            )
        );
        $response['success'] = true;

        return wp_send_json( $response );
    }

    private function run_update( $theme_id ) {
        $version = $this->licensing->get_version_information();

        if ( ! $version ) {
            return new WP_Error( 'no_server_contact', 'Couldn\'t contact updates server.' );
        }

        if ( ! array_key_exists( 'theme-' . $theme_id, $version ) ) {
            return new WP_Error( 'no_package_information', 'No theme package information available.' );
        }

        // Download package.
        $url = $version[ 'theme-' . $theme_id ]->download_link;

        if ( ! $url ) {
            $version = $this->licensing->get_version_information( true );

            $url = $version[ 'theme-' . $theme_id ]->download_link;

            if ( ! $url ) {
                return new WP_Error( 'invalid_package_url', 'No package URL provided.' );
            }
        }

        $download_file = download_url( $url );
        if ( is_wp_error( $download_file ) ) {
            return new WP_Error( 'download_failed', 'Could not download theme package.', $download_file->get_error_message() );
        }

        // Unpack package.
        $upgrade_folder = $this->api->get_themes_dir() . 'upgrade/';
        if ( ! is_dir( $upgrade_folder ) ) {
            if ( ! WPBDP_FS::mkdir( $upgrade_folder ) ) {
                return new WP_Error( 'no_upgrade_folder', sprintf( 'Could not create temporary upgrade folder: %s.', $upgrade_folder ) );
            }
        }

        $working_dir = $upgrade_folder . basename( basename( $download_file, '.tmp' ), '.zip' );
        if ( is_dir( $working_dir ) && ! WPBDP_FS::rmdir( $working_dir ) ) {
            return new WP_Error( 'no_upgrade_folder', sprintf( 'Temporary upgrade folder already exists: %s.', $working_dir ) );
        }

        if ( ! WPBDP_FS::mkdir( $working_dir ) ) {
            return new WP_Error( 'no_upgrade_folder', sprintf( 'Could not create upgrade folder: %s.', $working_dir ) );
        }

        $result = WPBDP_FS::unzip( $download_file, $working_dir );
        if ( is_wp_error( $result ) ) {
            return new WP_Error( 'unpackaging_failed', 'Could not unpackage theme file.' );
        }

        $contents_folder   = $result[0];
        $orig_theme_folder = $this->api->get_themes_dir() . $theme_id;
        $theme_folder      = $contents_folder . $theme_id;
        if ( ! is_dir( $theme_folder ) || ! file_exists( trailingslashit( $theme_folder ) . 'theme.json' ) ) {
            return new WP_Error( 'no_valid_theme', 'Package is not a valid theme file.' );
        }

        if ( ! WPBDP_FS::rmdir( $orig_theme_folder ) ) {
            return new WP_Error( 'dest_not_writable', 'Could not cleanup destination directory.' );
        }

        if ( ! WPBDP_FS::movedir( $theme_folder, $this->api->get_themes_dir() ) ) {
            return new WP_Error( 'theme_not_moved', 'Could not move theme to destination directory.' );
        }

        WPBDP_FS::rmdir( $working_dir );
        WPBDP_FS::rmdir( $upgrade_folder );

        return true;
    }

	function pre_themes_templates_warning() {
		_deprecated_function( __METHOD__, '5.13.3' );

		// TODO: Remove these templates from the code.
		// This function showed a warning for v4.0 if any of the following templates were used:
		$pre_themes_templates = array(
			'businessdirectory-excerpt',
			'businessdirectory-listing',
			'businessdirectory-listings',
			'businessdirectory-main-page',
		);
	}
}
