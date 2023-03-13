<?php

/**
 * Admin CSV import and export controller.
 */
class WPBDP__Admin__Csv extends WPBDP__Admin__Controller {

	public function __construct() {
		parent::__construct();

		require_once WPBDP_INC . 'admin/csv-import.php';
		$this->csv_import = new WPBDP_CSVImportAdmin();

		require_once WPBDP_INC . 'admin/csv-export.php';
		$this->csv_export = new WPBDP_Admin_CSVExport();
	}

	public function _dispatch() {
		$tabs = array( 'csv_import', 'csv_export' );

		$current_tab = wpbdp_get_var( array( 'param' => 'tab' ) );
		if ( empty( $current_tab ) ) {
			$current_tab = 'csv_import';
		}

		if ( ! in_array( $current_tab, $tabs ) ) {
			wp_die();
		}

		ob_start();
		call_user_func( array( $this->{$current_tab}, 'dispatch' ) );
		$output = ob_get_clean();
		$args = array(
			'tabbed_title' => true,
			'titles'       => array(
				'csv_import' => array(
					'url'  => esc_url( admin_url( 'admin.php?page=wpbdp_admin_csv&tab=csv_import' ) ),
					'name' => __( 'Import', 'business-directory-plugin' ),
				),
				'csv_export' => array(
					'url'  => esc_url( admin_url( 'admin.php?page=wpbdp_admin_csv&tab=csv_export' ) ),
					'name' => __( 'Export', 'business-directory-plugin' )
				),
			),
			'current_tab'  => $current_tab,
		);
		if ( 'csv_import' === $current_tab ) {
			$args['buttons'] = array(
				'example-csv' => array(
					'label' => __( 'See Example', 'business-directory-plugin' ),
					'url'   => admin_url( 'admin.php?page=wpbdp_admin_csv&action=example-csv' ),
				),
				'help'        => array(
					'label' => __( 'Help', 'business-directory-plugin' ),
					'url'   => admin_url( 'admin.php?page=wpbdp_admin_csv#help' ),
				),
			);
		}

		echo wpbdp_admin_header( $args );
		echo wpbdp_admin_notices();
		echo $output;
		echo wpbdp_admin_footer();
	}

}

