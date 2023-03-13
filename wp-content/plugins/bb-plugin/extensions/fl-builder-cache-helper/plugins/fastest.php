<?php
namespace FLCacheClear;
class Fastest {

	var $name = 'WP Fastest Cache';
	var $url  = 'https://wordpress.org/plugins/wp-fastest-cache/';

	static function run() {
		if ( class_exists( '\WpFastestCache' ) ) {
			global $wp_fastest_cache;
			$wp_fastest_cache->deleteCache( true );
		}
	}
}
