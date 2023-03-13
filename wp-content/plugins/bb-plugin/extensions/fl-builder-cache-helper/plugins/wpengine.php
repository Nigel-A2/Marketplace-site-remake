<?php
namespace FLCacheClear;
class Wpengine {

	var $name = 'WPEngine Hosting';
	var $url  = 'https://wpengine.com/';

	static function run() {
		if ( class_exists( '\WpeCommon' ) ) {
			\WpeCommon::purge_memcached();
			\WpeCommon::clear_maxcdn_cache();
			\WpeCommon::purge_varnish_cache();
		}
	}
}
