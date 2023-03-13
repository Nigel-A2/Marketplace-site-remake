<?php
namespace FLCacheClear;
class Breeze {

	var $name = 'Breeze';
	var $url  = 'https://wordpress.org/plugins/breeze/';

	static function run() {
		if ( class_exists( '\Breeze_PurgeCache' ) ) {
			\Breeze_PurgeCache::breeze_cache_flush();
		}
	}
}
