<?php
namespace FLCacheClear;
class Hummingbird {

	var $name = 'Hummingbird Page Speed Optimization';
	var $url  = 'https://wordpress.org/plugins/hummingbird-performance/';

	static function run() {
		if ( class_exists( '\WP_Hummingbird_Utils' ) && class_exists( '\WP_Hummingbird' ) ) {
			if ( \WP_Hummingbird_Utils::get_module( 'page_cache' )->is_active() ) {
				\WP_Hummingbird_Utils::get_module( 'page_cache' )->clear_cache();
				\WP_Hummingbird_Module_Page_Cache::log_msg( 'Cache cleared by Beaver Builder.' );
			}
		}
	}
}
