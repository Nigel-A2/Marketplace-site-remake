<?php
namespace FLCacheClear;
class W3cache {

	var $name = 'W3 Total Cache';
	var $url  = 'https://wordpress.org/plugins/w3-total-cache/';

	static function run() {
		if ( function_exists( '\w3tc_pgcache_flush' ) ) {
			\w3tc_pgcache_flush();
		}
	}
}
