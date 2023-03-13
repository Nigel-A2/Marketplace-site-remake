<?php
/**
 * @package WPBDP
 */

require_once WPBDP_PATH . 'includes/admin/upgrades/class-migration.php';

/**
 * Installer for Business Directory Plugin.
 */
class WPBDP_Installer {

    const DB_VERSION = '18.7';

    private $installed_version = null;


    public function __construct( $_db_version ) {
        $this->installed_version = $_db_version;

        add_action( 'split_shared_term', array( &$this, 'handle_term_split' ), 10, 4 );
    }

    public function install() {
        global $wpdb;

		if ( version_compare( self::DB_VERSION, $this->installed_version, '=' ) )
            return;

        $this->update_database_schema();

        if ( $this->installed_version ) {
			wpbdp_log( 'WPBDP is already installed.' );
			$this->_update();
		} elseif ( $this->_table_exists( "{$wpdb->prefix}wpbdp_form_fields" ) ) {
			wpbdp_log( 'New installation. Creating default form fields.' );
            global $wpbdp;

            // Create default category.
            wp_insert_term( _x( 'General', 'default category name', 'business-directory-plugin' ), WPBDP_CATEGORY_TAX );

            $wpbdp->formfields->create_default_fields();
            $wpbdp->settings->set_new_install_settings();

            add_option( 'wpbdp-show-drip-pointer', 1 );
            add_option( 'wpbdp-show-tracking-pointer', 1 );

            // Create default paid fee.
            $fee = new WPBDP__Fee_Plan(
				array(
					'label' => __( 'Default Plan', 'business-directory-plugin' ),
                                               'amount' => 1.0,
                                               'days' => 365,
                                               'images' => 1,
                                               'supported_categories' => 'all',
                                               'pricing_model' => 'flat',
                                               'enabled' => 1,
				)
			);
            $fee->save();
        } else {
            throw new Exception( "Table {$wpdb->prefix}wpbdp_form_fields was not created!" );
        }

		update_option( 'wpbdp-db-version', self::DB_VERSION );
    }

    /**
     * Builds the SQL queries (without running them) used to create all of the required database tables for BD.
     * Calls the `wpbdp_database_schema` filter that allows plugins to modify the schema.
	 *
     * @return array An associative array of (non prefixed)table => SQL items.
     * @since 3.3
     */
    public function get_database_schema() {
        global $wpdb;

        $schema = array();

        $schema['form_fields'] = "CREATE TABLE {$wpdb->prefix}wpbdp_form_fields (
            id bigint(20) PRIMARY KEY  AUTO_INCREMENT,
            label varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            description varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
            field_type varchar(100) NOT NULL,
            association varchar(100) NOT NULL,
            validators text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
            weight int(5) NOT NULL DEFAULT 0,
            display_flags text CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
            field_data blob NULL,
            shortname varchar(255) NOT NULL DEFAULT '',
            tag varchar(255) NOT NULL DEFAULT '',
            KEY field_type (field_type)
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

        $schema['plans'] = "CREATE TABLE {$wpdb->prefix}wpbdp_plans (
            id bigint(20) PRIMARY KEY  AUTO_INCREMENT,
            label varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            amount decimal(10,2) NOT NULL DEFAULT 0.00,
            days smallint unsigned NOT NULL DEFAULT 0,
            images smallint unsigned NOT NULL DEFAULT 0,
            sticky tinyint(1) NOT NULL DEFAULT 0,
            recurring tinyint(1) NOT NULL DEFAULT 0,
            pricing_model varchar(100) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'flat',
            pricing_details blob NULL,
            supported_categories text NOT NULL DEFAULT '',
            weight int(5) NOT NULL DEFAULT 0,
            enabled tinyint(1) NOT NULL DEFAULT 1,
            description TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
            extra_data longblob NULL,
            tag varchar(255) NOT NULL DEFAULT ''
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

        $schema['payments'] = "CREATE TABLE {$wpdb->prefix}wpbdp_payments (
            id bigint(20) PRIMARY KEY  AUTO_INCREMENT,
            listing_id bigint(20) NOT NULL DEFAULT 0,
            parent_id bigint(20) NOT NULL DEFAULT 0,
            payment_key varchar(255) NULL DEFAULT '',
            payment_type varchar(255) NULL DEFAULT '',
            payment_items longblob NULL,
            data longblob NULL,
            context varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
            payer_email varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
            payer_first_name varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
            payer_last_name varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
            payer_data blob NULL,
            gateway varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
            gateway_tx_id varchar(255) NULL DEFAULT '',
            currency_code varchar(3) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'USD',
            amount decimal(10,2) NOT NULL DEFAULT 0.00,
            status varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
            is_test boolean NULL,
            created_at datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
            processed_on timestamp NULL DEFAULT NULL,
            processed_by varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL,
            KEY listing_id (listing_id),
            KEY status (status)
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

        $schema['listings'] = "CREATE TABLE {$wpdb->prefix}wpbdp_listings (
            listing_id bigint(20) PRIMARY KEY,
            fee_id bigint(20) NULL,
            fee_price decimal(10,2) NULL DEFAULT 0.00,
            fee_days smallint unsigned NULL DEFAULT 0,
            fee_images smallint unsigned NULL DEFAULT 0,
            expiration_date timestamp NULL DEFAULT NULL,
            is_recurring tinyint(1) NOT NULL DEFAULT 0,
            is_sticky tinyint(1) NOT NULL DEFAULT 0,
            subscription_id varchar(255) NULL DEFAULT '',
            subscription_data longblob NULL,
            listing_status varchar(255) NOT NULL DEFAULT 'unknown',
            flags varchar(255) NOT NULL DEFAULT ''
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

        $schema['logs'] = "CREATE TABLE {$wpdb->prefix}wpbdp_logs (
            id bigint(20) PRIMARY KEY  AUTO_INCREMENT,
            object_id bigint(20) NULL DEFAULT 0,
            rel_object_id bigint(20) NULL DEFAULT 0,
            object_type varchar(20) NULL DEFAULT '',
            created_at datetime NOT NULL,
            log_type varchar(255) NULL DEFAULT '',
            actor varchar(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
            message text CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT '',
            data longblob NULL
        ) DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;";

        return apply_filters( 'wpbdp_database_schema', $schema );
    }

    public function update_database_schema() {
		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

        wpbdp_log( 'Running dbDelta.' );

        $schema = $this->get_database_schema();

        foreach ( $schema as $table_sql )
            dbDelta( $table_sql );
    }

    private function _table_exists( $table_name ) {
        global $wpdb;

        $result = $wpdb->get_var( "SHOW TABLES LIKE '" . $table_name . "'" );

        return strcasecmp( $result, $table_name ) === 0;
    }

    public function _update() {
        // remove deprecated option, but make sure its value is preserved
        delete_option( 'wpbusdirman_db_version' );
        update_option( 'wpbdp-db-version', $this->installed_version );

        if ( get_option( 'wpbdp-manual-upgrade-pending', array() ) ) {
            return;
        }

        $migrations = $this->get_pending_migrations();

        foreach ( $migrations as $version ) {
            wpbdp_log( sprintf( 'Running upgrade routine for version %s', $version ) );

            try {
                $m = $this->load_migration_class( 'WPBDP__Migrations__' . str_replace( '.', '_', $version ) );
            } catch ( Exception $e ) {
                $m = false;
            }

            // Load manual upgrades from database before running each migration
            $manual_upgrades = $this->get_manual_upgrades();

            if ( ! empty( $m ) && ! $manual_upgrades ) {
                $m->migrate();
            } elseif ( ! empty( $m ) ) {
                $m->request_manual_upgrade();
            }

            // Load manual upgrades from database again, to get any manual upgrade
            // added by the current migration
            if ( ! $this->get_manual_upgrades() ) {
                $this->update_installed_version( $version );
            }
        }
    }

    public function update_installed_version( $new_version ) {
        $this->installed_version = $new_version;
        update_option( 'wpbdp-db-version', $new_version );
    }

    public function load_migration_class( $classname ) {
        $file = WPBDP_PATH . 'includes/admin/upgrades/migrations/migration-' . str_replace( 'WPBDP__Migrations__', '', $classname ) . '.php';

        if ( ! file_exists( $file ) ) {
            throw new Exception( "Can't load migration class: $file." );
        }

        require_once $file;

        return new $classname( $this );
    }

    public function get_manual_upgrades() {
        $manual_upgrades = get_option( 'wpbdp-manual-upgrade-pending', array() );

        // We should be able to handle pending upgrades from 4.x and 5.0-5.0.4
        if ( ! is_array( $manual_upgrades ) ) {
            $manual_upgrades = array( $this->installed_version => array( $manual_upgrades ) );
        } elseif ( isset( $manual_upgrades['callback'] ) && is_array( $manual_upgrades['callback'] ) ) {
            $version = $this->get_migration_version_from_class_name( $manual_upgrades['callback'][0] );
            $manual_upgrades = array( $version => array( $manual_upgrades ) );
        } elseif ( isset( $manual_upgrades[0] ) && wpbdp_starts_with( 'WPBDP__Migrations__', $manual_upgrades[0] ) ) {
            $version = $this->get_migration_version_from_class_name( $manual_upgrades[0] );
            $manual_upgrades = array( $version => array( $manual_upgrades ) );
        }

        return $manual_upgrades;
    }

    public function get_migration_version_from_class_name( $classname ) {
        if ( ! preg_match( '/\d[0-9_]*$/', $classname, $matches ) ) {
            // This should cause the upgrade routine to run, without preventing more
            // recent routines to be scheduled later.
            return $this->installed_version;
        }

        return str_replace( '_', '.', $matches[0] );
    }

    public function show_installation_error( $exception ) {
        require_once WPBDP_PATH . 'includes/admin/upgrades/class-installer-installation-error.php';
        new WPBDP__Installer__Installation_Error( $exception );
    }

    public function get_pending_migrations() {
        $current_version = strval( $this->installed_version );
        $current_version = ( false === strpos( $current_version, '.' ) ) ? $current_version . '.0' : $current_version;

        $latest_version = strval( self::DB_VERSION );
        $latest_version = ( false === strpos( $latest_version, '.' ) ) ? $latest_version . '.0' : $latest_version;

        $migrations = array();

        foreach ( WPBDP_FS::ls( WPBDP_PATH . 'includes/admin/upgrades/migrations/' ) as $_ ) {
            $_ = strtolower( $_ );
            if ( ! wpbdp_starts_with( basename( $_ ), 'migration-' ) ) {
                continue;
            }

			$version = str_replace(
				array( 'migration-', '.php', '_' ),
				array( '', '', '.' ),
				basename( $_ )
			);

            if ( version_compare( $version, $current_version, '<=' ) )
                continue;

            if ( version_compare( $version, $latest_version, '>' ) )
                continue;

            $migrations[] = $version;
        }

        sort( $migrations, SORT_NUMERIC );
        return $migrations;
    }

    public function setup_manual_upgrade() {
        $manual_upgrades = $this->get_manual_upgrades();

        if ( ! $manual_upgrades ) {
            return false;
        }

		require_once WPBDP_PATH . 'includes/admin/upgrades/class-manual-upgrade-helper.php';

        try {
            return new WPBDP__Manual_Upgrade_Helper( $this );
        } catch ( Exception $e ) {
            delete_option( 'wpbdp-manual-upgrade-pending' );
            return false;
        }
    }

    public function handle_term_split( $old_id, $new_id, $tt_id, $tax ) {
        if ( WPBDP_CATEGORY_TAX != $tax )
            return;

        require_once WPBDP_PATH . 'includes/admin/upgrades/migrations/migration-5_0.php';
        $m = new WPBDP__Migrations__5_0( $this );
        $m->process_term_split( $old_id );
    }
}
