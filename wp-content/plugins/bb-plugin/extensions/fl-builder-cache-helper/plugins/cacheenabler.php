<?php
namespace FLCacheClear;
class Cacheenabler {

	var $name = 'Cache Enabler';
	var $url  = 'https://wordpress.org/plugins/cache-enabler/';

	static function run() {
		if ( class_exists( '\Cache_Enabler' ) ) {
			if ( ! is_multisite() ) {
				\Cache_Enabler::clear_total_cache();
			} else {
				\Cache_Enabler_Disk::delete_asset( site_url(), 'dir' );
			}
		}
	}
}
