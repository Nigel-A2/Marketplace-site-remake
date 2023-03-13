<?php
/**
 * @package WPBDP\Admin\Upgrades\Migrations
 */

/**
 * Migration for DB version 18.4
 */
class WPBDP__Migrations__18_4 extends WPBDP__Migration {

	/**
	 * Delete the ajax compat plugin if it's installed.
	 *
	 * @since 5.12.1
	 */
	public function migrate() {
		$mu_dir = ( defined( 'WPMU_PLUGIN_DIR' ) && defined( 'WPMU_PLUGIN_URL' ) ) ? WPMU_PLUGIN_DIR : trailingslashit( WP_CONTENT_DIR ) . 'mu-plugins';
		$source = WPBDP_INC . '/compatibility/wpbdp-ajax-compat-mu.php';
		$dest   = trailingslashit( $mu_dir ) . basename( $source );

		if ( file_exists( $dest ) ) {
			unlink( $dest );
		}
	}
}
