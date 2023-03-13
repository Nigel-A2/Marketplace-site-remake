<?php
namespace FLCacheClear;
class Varnish {

	static function run() {

		$settings = \FLCacheClear\Plugin::get_settings();
		if ( ! $settings['varnish'] ) {
			return false;
		}
		/**
		 * @see fl_varnish_url
		 * @since 2.3.2
		 */
		@wp_remote_request( apply_filters( 'fl_varnish_url', get_site_url() ), array( // phpcs:ignore
			'method' => 'BAN',
		) );
	}
}
