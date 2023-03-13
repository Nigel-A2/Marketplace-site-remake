<?php

class WPBDP_Themes_Updater {

    private $themes_api;


    public function __construct( &$themes_api ) {
        $this->themes_api = $themes_api;

		$this->check_for_updates();

        add_action( 'wp_ajax_wpbdp-themes-update', array( &$this, '_update_theme' ) );
        add_action( 'wpbdp-admin-themes-item-css', array( &$this, '_add_update_css' ) );
        add_action( 'wpbdp-admin-themes-extra', array( &$this, '_show_update_info' ) );
    }

    public function get_updates_count() {
        if ( ! $this->data )
            return 0;

        $count = 0;

        foreach ( array_keys( $this->data ) as $theme_id ) {
            if ( ! $this->themes_api->get_theme( $theme_id ) )
                continue;

            if ( $this->get_update_info( $theme_id ) )
                $count++;
        }

        return $count;
    }

    private function check_for_updates() {
        $data = get_transient( 'wpbdp-themes-updates' );

		if ( is_array( $data ) ) {
			$this->data = $data;
			return;
		}

		$data = array();

        $themes = $this->themes_api->get_installed_themes();
        $res = array();

        foreach ( $themes as $theme_id => $details ) {
            if ( $details->is_core_theme )
                continue;

            if ( 'valid' != $details->license_status )
                continue;

            $id = $details->id;
            $name = ! empty( $details->edd_name ) ? $details->edd_name : $details->name;
            $version = $details->version;

            if ( ! $name )
                continue;

            $res = $this->get_latest_version( $details );

            if ( ! $res )
                continue;

            $data[ $theme_id ] = array( 'latest' => $res[0],
                                        'changelog' => $res[1],
                                        'url' => $res[2],
                                        'checked' => time() );
        }

        set_transient( 'wpbdp-themes-updates', $data, 60 * 60 * 24 * 2 ); // Make this available for 48 hours.
        $this->data = $data;
    }

    private function get_latest_version( $theme ) {
        $request = array(
            'edd_action' => 'get_version',
            'item_name' => urlencode( ! empty( $theme->edd_name ) ? $theme->edd_name : $theme->name ),
            'url' => home_url(),
            'license' => $theme->license_key
        );
        $response = wp_remote_get( add_query_arg( $request, 'http://businessdirectoryplugin.com/' ), array( 'timeout' => 15, 'sslverify' => false ) );

        if ( is_wp_error( $response ) )
            return false;

        $data = json_decode( wp_remote_retrieve_body( $response ) );
        if ( ! isset( $data->new_version ) )
            return false;

        $new_version = $data->new_version;
        $url = $data->package;

        $sections = isset( $data->sections ) ? unserialize( $data->sections ) : false;
		if ( $sections && isset( $sections['changelog'] ) ) {
			$changelog = $sections['changelog'];
		} else {
			$changelog = '';
		}

        return array( $new_version, $changelog, $url );
    }

    public function get_update_info( $theme_id ) {
        if ( ! isset( $this->data[ $theme_id ] ) )
            return false;

        $theme = $this->themes_api->get_theme( $theme_id );

        if ( version_compare( $theme->version, $this->data[ $theme_id ]['latest'], '>=' ) )
            return false;

        return $this->data[ $theme_id ];
    }

    public function _add_update_css( $theme ) {
        if ( ! $this->get_update_info( $theme->id ) )
            return;

        echo ' update-available ';
    }

    public function _show_update_info( $theme ) {
        $update_info = $this->get_update_info( $theme->id );

        if ( ! $update_info )
            return;

		printf(
			'<div class="wpbdp-theme-update-info update-available wpbdp-inline-notice" data-l10n-updating="%s" data-l10n-updated="%s">',
			_x( 'Updating theme...', 'themes', 'business-directory-plugin' ),
			_x( 'Theme updated.', 'themes', 'business-directory-plugin' )
		);
        echo '<div class="update-message">';
        $msg = _x( 'New version available (<b>%s</b>). <a>Update now.</a>', 'themes', 'business-directory-plugin' );
        $msg = sprintf( $msg, $update_info['latest'] );
        $msg = str_replace( '<a>', '<a href="#" data-theme-id="' . $theme->id . '" data-nonce="' . wp_create_nonce( 'update theme ' . $theme->id ) . '" class="update-link">', $msg );
        echo $msg;
        echo '</div>';

        echo '</div>';
    }

    // Theme update process. {{

    public function _update_theme() {
		if ( ! current_user_can( 'administrator' ) ) {
			wp_die();
		}

		$theme_id = wpbdp_get_var( array( 'param' => 'theme' ), 'request' );
		$nonce    = wpbdp_get_var( array( 'param' => '_wpnonce' ), 'request' );

		if ( ! wp_verify_nonce( $nonce, 'update theme ' . $theme_id ) ) {
			wp_die();
		}

        $response = new WPBDP_AJAX_Response();

        $theme = $this->themes_api->get_theme( $theme_id );

        if ( ! $theme )
            $response->send_error( 'Invalid theme ID.' );

        if ( ! $this->get_update_info( $theme_id ) )
            $response->send_error( 'Theme is already up to date.' );

        $result = $this->run_update( $theme_id );
        if ( is_wp_error( $result ) )
            $response->send_error( sprintf( _x( 'Could not update theme: %s', 'themes', 'business-directory-plugin' ), $result->get_error_message() ) );

        $this->themes_api->find_themes( true );

		$response->add(
			'html',
			wpbdp_render_page(
				WPBDP_PATH . 'templates/admin/themes-item.tpl.php',
				array( 'theme' => $this->themes_api->get_theme( $theme_id ) )
			)
		);
        $response->set_message( _x( 'Theme was updated successfully.', 'themes', 'business-directory-plugin' ) );
        $response->send();
    }

    private function run_update( $theme_id ) {
        $theme = $this->themes_api->get_theme( $theme_id );
        $update_info = $this->get_update_info( $theme_id );

        // Download package.
        $url = $update_info['url'];

        if ( ! $url )
            return new WP_Error( 'invalid_package_url', 'No package URL provided.' );

        $download_file = download_url( $url );
        if ( is_wp_error( $download_file ) )
            return new WP_Error( 'download_failed', 'Could not download theme package.', $download_file->get_error_message() );

        // Unpack package.
        $upgrade_folder = $this->themes_api->get_themes_dir() . 'upgrade/';
        if ( ! is_dir( $upgrade_folder ) ) {
            if ( ! WPBDP_FS::mkdir( $upgrade_folder ) )
                return new WP_Error( 'no_upgrade_folder', sprintf( 'Could not create temporary upgrade folder: %s.', $upgrade_folder ) );
        }

        $working_dir = $upgrade_folder . basename( basename( $download_file, '.tmp' ), '.zip' );
        if ( is_dir( $working_dir ) && ! WPBDP_FS::rmdir( $working_dir ) ) {
            return new WP_Error( 'no_upgrade_folder', sprintf( 'Temporary upgrade folder already exists: %s.', $working_dir ) );
		}

        if ( ! WPBDP_FS::mkdir( $working_dir ) )
            return new WP_Error( 'no_upgrade_folder', sprintf( 'Could not create upgrade folder: %s.', $working_dir ) );

        $result = WPBDP_FS::unzip( $download_file, $working_dir );
        if ( is_wp_error( $result ) )
            return new WP_Error( 'unpackaging_failed', 'Could not unpackage theme file.' );

        $contents_folder = $result[0];
        $orig_theme_folder = $this->themes_api->get_themes_dir() . $theme->id;
        $theme_folder = $contents_folder . $theme->id;
        if ( ! is_dir( $theme_folder ) || ! file_exists( trailingslashit( $theme_folder ) . 'theme.json' ) )
            return new WP_Error( 'no_valid_theme', 'Package is not a valid theme file.' );

        if ( ! WPBDP_FS::rmdir( $orig_theme_folder ) )
            return new WP_Error( 'dest_not_writable', 'Could not cleanup destination directory.' );

        if ( ! WPBDP_FS::movedir( $theme_folder, $this->themes_api->get_themes_dir() ) )
            return new WP_Error( 'theme_not_moved', 'Could not move theme to destination directory.' );

        WPBDP_FS::rmdir( $working_dir );
        WPBDP_FS::rmdir( $upgrade_folder );

        return true;
    }

    // }}
}
