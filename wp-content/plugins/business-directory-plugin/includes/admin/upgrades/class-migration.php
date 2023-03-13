<?php

class WPBDP__Migration {

    private $installer;
    private $version;

    public function __construct( $installer ) {
        $this->installer = $installer;
        $this->version = $this->installer->get_migration_version_from_class_name( get_class( $this ) );
    }

    public function request_manual_upgrade( $callback = 'run_manual_upgrade' ) {
        return $this->request_manual_upgrade_with_configuration( $callback, null );
    }

    public function request_manual_upgrade_with_configuration( $callback, $config_callback ) {
        $manual_upgrades = $this->installer->get_manual_upgrades();

        $manual_upgrades[ $this->version ][] = array(
            'callback' => array( get_class( $this ), $callback ),
            'config_callback' => $config_callback ? array( get_class( $this ), $config_callback ) : null,
        );

        update_option( 'wpbdp-manual-upgrade-pending', $manual_upgrades );
    }

    public function run_manual_upgrade() {
        $default_status = _x( 'Migrating Business Directory database to version <version>.', 'installer', 'business-directory-plugin' );
        $default_status = str_replace( '<version>', $this->version, $default_status );

        $default_response = array(
            'ok' => true,
            'status' => $default_status,
            'done' => true,
        );

        $response = $this->migrate();

        if ( ! is_array( $response ) ) {
            $response = array();
        }

        return array_merge( $default_response, $response );
    }

    public function migrate() {
        // *crickets*
    }
}

