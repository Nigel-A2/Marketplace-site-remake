<?php

require_once WPBDP_INC . 'admin/helpers/csv/class-csv-exporter.php';

/**
 * CSV Export admin pages.
 *
 * @since 3.2
 */
class WPBDP_Admin_CSVExport {

    public function __construct() {
        add_action( 'wpbdp_enqueue_admin_scripts', array( &$this, 'enqueue_scripts' ) );
        add_action( 'wp_ajax_wpbdp-csv-export', array( &$this, 'ajax_csv_export' ) );
    }

    public function enqueue_scripts() {
        $min = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

        wp_enqueue_script(
            'wpbdp-admin-export-js',
            WPBDP_ASSETS_URL . 'js/admin-export' . $min . '.js',
            array( 'wpbdp-admin-js' ),
            WPBDP_VERSION,
			true
        );

        wp_enqueue_style(
            'wpbdp-admin-export-css',
            WPBDP_ASSETS_URL . 'css/admin-export.min.css',
            array(),
            WPBDP_VERSION
        );
    }

    public function dispatch() {
		wpbdp_render_page( WPBDP_PATH . 'templates/admin/csv-export.tpl.php', array(), true );
    }

    public function ajax_csv_export() {
		WPBDP_App_Helper::permission_check( 'administrator' );
		check_ajax_referer( 'wpbdp_ajax', 'nonce' );

        $error = '';

        try {
            if ( ! isset( $_REQUEST['state'] ) ) {
				$export = new WPBDP_CSVExporter( array_merge( wpbdp_get_var( array( 'param' => 'settings' ), 'request' ), array() ) );
            } else {
				$state = json_decode( base64_decode( wpbdp_get_var( array( 'param' => 'state' ), 'request' ) ), true );
                if ( ! $state || ! is_array( $state ) || empty( $state['workingdir'] ) ) {
                    $error = _x( 'Could not decode export state information.', 'admin csv-export', 'business-directory-plugin' );
                }

                $export = WPBDP_CSVExporter::from_state( $state );

				if ( 1 === intval( wpbdp_get_var( array( 'param' => 'cleanup' ), 'request' ) ) ) {
                    $export->cleanup();
                } else {
                    $export->advance();
                }
            }
		} catch ( Exception $e ) {
            $error = $e->getMessage();
        }

        $state = ! $error ? $export->get_state() : null;

        $response = array();
        $response['error'] = $error;
        $response['state'] = $state ? base64_encode( json_encode( $state ) ) : null;
        $response['count'] = $state ? count( $state['listings'] ) : 0;
        $response['exported'] = $state ? $state['exported'] : 0;
        $response['filesize'] = $state ? size_format( $state['filesize'] ) : 0;
        $response['isDone'] = $state ? $state['done'] : false;
        $response['fileurl'] = $state ? ( $state['done'] ? $export->get_file_url() : '' ) : '';
        $response['filename'] = $state ? ( $state['done'] ? basename( $export->get_file_url() ) : '' ) : '';

        echo json_encode( $response );

        die();
    }

}

