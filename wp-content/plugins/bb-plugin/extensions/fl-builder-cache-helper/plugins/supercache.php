<?php
namespace FLCacheClear;
class Supercache {

	var $name = 'WP Super Cache';
	var $url  = 'https://wordpress.org/plugins/wp-super-cache/';

	static function run() {
		if ( function_exists( '\wp_cache_clear_cache' ) ) {
			if ( is_multisite() ) {
				\wp_cache_clear_cache( get_current_blog_id() );
			} else {
				\wp_cache_clear_cache();
			}
		}
	}
}
