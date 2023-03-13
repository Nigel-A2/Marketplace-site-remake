<?php

class WPBDP__Manual_Upgrade_Helper {

    private $installer;
    private $manual_upgrades = array();
    private $callback;
    private $config_callback = null;

    public function __construct( $installer ) {
        $this->installer = $installer;

        $this->load_manual_upgrades();
		$this->prepare_manual_upgrade_callbacks();

        add_action( 'admin_notices', array( &$this, 'upgrade_required_notice' ) );
        add_action( 'admin_menu', array( &$this, 'add_upgrade_page' ) );
        add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_scripts' ) );
        add_action( 'wp_ajax_wpbdp-manual-upgrade', array( &$this, 'handle_ajax' ) );
    }

    private function prepare_manual_upgrade_callbacks() {
        $versions = array_keys( $this->manual_upgrades );

        $this->current_version = reset( $versions );
        $this->current_upgrade = reset( $this->manual_upgrades[ $this->current_version ] );

        if ( isset( $this->current_upgrade['callback'] ) ) {
            $this->callback = $this->get_callback( $this->current_upgrade['callback'] );
        } else {
            $this->callback = $this->get_callback( $this->current_upgrade );
        }

        if ( isset( $this->current_upgrade['config_callback'] ) ) {
            $this->config_callback = $this->get_configuration_callback( $this->current_upgrade['config_callback'] );
        } else {
            $this->config_callback = null;
        }
    }

    private function get_callback( $params ) {
        $callback = $this->get_migration_callback( $params );

        if ( ! is_callable( $callback ) ) {
            throw new Exception( 'Invalid upgrade callback provided.' );
        }

        return $callback;
    }

    private function get_migration_callback( $params ) {
        if ( is_array( $params ) ) {
            $classname = $params[0];
            $method    = $params[1];

            $migration = $this->installer->load_migration_class( $classname );

            $callback = array( $migration, $method );
        } else {
            $callback = $params;
        }

        return $callback;
    }

    private function get_configuration_callback( $params ) {
        $callback = $this->get_migration_callback( $params );

        if ( ! is_callable( $callback ) ) {
            throw new Exception( 'Invalid upgrade config callback provided.' );
        }

        return $callback;
    }

    public function upgrade_required_notice() {
        global $pagenow;

		$page = wpbdp_get_var( array( 'param' => 'page' ) );
        if ( in_array( $pagenow, array( 'admin.php', 'edit.php' ) ) && 'wpbdp-upgrade-page' === $page ) {
            return;
        }

        if ( ! current_user_can( 'administrator' ) ) {
            return;
        }

        print '<div class="error"><p>';
        print '<strong>' . __( 'Business Directory - Manual Upgrade Required', 'business-directory-plugin' ) . '</strong>';
        print '<br />';
        _e( 'Business Directory features are currently disabled because the plugin needs to perform a manual upgrade before continuing.', 'business-directory-plugin' );
        print '<br /><br />';
        printf( '<a class="button button-primary" href="%s">%s</a>', admin_url( 'admin.php?page=wpbdp-upgrade-page' ), __( 'Perform Manual Upgrade', 'business-directory-plugin' ) );
        print '</p></div>';
    }

    public function add_upgrade_page() {
        global $submenu;

        // Make "Directory" menu items point to upgrade page.
        $menu_id = 'edit.php?post_type=' . WPBDP_POST_TYPE;
        if ( isset( $submenu[ $menu_id ] ) ) {
            foreach ( $submenu[ $menu_id ] as &$item ) {
                $item[2] = admin_url( 'admin.php?page=wpbdp-upgrade-page' );
            }
        }

        add_submenu_page(
            'options.php',
            __( 'Business Directory - Manual Upgrade', 'business-directory-plugin' ),
            __( 'Business Directory - Manual Upgrade', 'business-directory-plugin' ),
            'administrator',
            'wpbdp-upgrade-page',
            array( &$this, 'upgrade_page' )
        );
    }

    public function enqueue_scripts() {
        wp_enqueue_style(
            'wpbdp-admin',
            WPBDP_ASSETS_URL . 'css/admin.min.css',
            array(),
            WPBDP_VERSION
        );

        wp_enqueue_style(
            'wpbdp-manual-upgrade-css',
            WPBDP_ASSETS_URL . 'css/admin-manual-upgrade.min.css',
            array(),
            WPBDP_VERSION
        );

        wp_enqueue_script(
            'wpbdp-manual-upgrade',
            WPBDP_ASSETS_URL . 'js/admin-manual-upgrade.min.js',
            array(),
            WPBDP_VERSION,
			true
        );
    }

    private function is_configured() {
        if ( ! $this->config_callback ) {
            return true;
        }

        $latest_data = (array) get_option( 'wpbdp-manual-upgrade-pending', array() );
        return ! empty( $latest_data['configured'] );
    }

    public function upgrade_page() {
		echo wpbdp_admin_header(
			array(
				'title'   => __( 'Directory Manual Upgrade', 'business-directory-plugin' ),
				'id'      => 'manual-upgrade',
				'sidebar' => false
			)
		);
        echo '<div class="wpbdp-manual-upgrade-wrapper">';

        if ( ! $this->is_configured() ) {
            ob_start();
            call_user_func( $this->config_callback );
            $output = ob_get_contents();
            ob_end_clean();

			/** @phpstan-ignore-next-line */
            if ( ! $this->is_configured() ) {
				// is_configured can change during the config_callback.
                echo '<form action="" method="post">';
                echo '<div class="wpbdp-manual-upgrade-configuration">';
                echo $output;
                echo '<div class="cf"><input type="submit" class="right button button-primary" value="' . _x( 'Continue', 'manual-upgrade', 'business-directory-plugin' ) . '"/></div>';
                echo '</div>';
                echo '</form>';
            }
        }

        if ( $this->is_configured() ) {
            echo '<div class="step-upgrade">';
            echo '<p>';
            _e( 'Business Directory features are currently disabled because the plugin needs to perform a manual upgrade before it can be used.', 'business-directory-plugin' );
            echo '<br />';
            _e( 'Click "Start Upgrade" and wait until the process finishes.', 'business-directory-plugin' );
            echo '</p>';
            echo '<p>';
            echo '<a href="#" class="start-upgrade button button-primary">' . _x( 'Start Upgrade', 'manual-upgrade', 'business-directory-plugin' ) . '</a>';
            echo ' ';
            echo '<a href="#" class="pause-upgrade button">' . _x( 'Pause Upgrade', 'manual-upgrade', 'business-directory-plugin' ) . '</a>';
            echo '</p>';
            echo '<textarea id="manual-upgrade-progress" rows="20" style="width: 90%; font-family: courier, monospaced; font-size: 12px;" readonly="readonly"></textarea>';
            echo '</div>';

            echo '<div class="step-done" style="display: none;">';
            echo '<p>' . _x( 'The upgrade was successfully performed. Business Directory Plugin is now available.', 'manual-upgrade', 'business-directory-plugin' ) . '</p>';
			printf(
                '<a href="%s" class="button button-primary">%s</a>',
                esc_url( admin_url( 'edit.php?post_type=wpbdp_listing' ) ),
                _x( 'Go to "Directory Admin"', 'manual-upgrade', 'business-directory-plugin' )
            );
            echo '</div>';
        }

        echo '</div>';
        echo wpbdp_admin_footer();
    }

    /* Ajax Handlers */

    public function handle_ajax() {
        if ( ! current_user_can( 'administrator' ) ) {
            return;
        }

        $response = call_user_func( $this->callback );

        // Migration routines can request additional manual upgrades
        $this->load_manual_upgrades();

        if ( $response['done'] ) {
            $this->remove_upgrade_for_version( $this->current_version, $this->current_upgrade );
        }

        if ( $this->is_upgrade_complete_for_version( $this->current_version ) ) {
            $this->installer->update_installed_version( $this->current_version );
        }

        if ( ! $this->is_upgrade_complete() ) {
            $response['done'] = false;
        }

        print wp_json_encode( $response );

        exit();
    }

    /* Manual Upgrades */

    private function load_manual_upgrades() {
        $this->manual_upgrades = $this->installer->get_manual_upgrades();
    }

    private function update_pending_manual_upgrades() {
        if ( empty( $this->manual_upgrades ) ) {
            delete_option( 'wpbdp-manual-upgrade-pending' );
        } else {
            update_option( 'wpbdp-manual-upgrade-pending', $this->manual_upgrades );
        }
    }

    private function remove_upgrade_for_version( $version, $upgrade ) {
        $index = array_search( $upgrade, $this->manual_upgrades[ $version ] );

        if ( false !== $index ) {
            unset( $this->manual_upgrades[ $version ][ $index ] );
        }

        if ( empty( $this->manual_upgrades[ $version ] ) ) {
            unset( $this->manual_upgrades[ $version ] );
        }

        $this->update_pending_manual_upgrades();
    }

    private function is_upgrade_complete_for_version( $version ) {
        return empty( $this->manual_upgrades[ $version ] );
    }

    private function is_upgrade_complete() {
        return empty( $this->manual_upgrades );
    }
}

