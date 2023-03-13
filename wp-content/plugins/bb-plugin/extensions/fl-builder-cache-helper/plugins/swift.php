<?php
namespace FLCacheClear;
class Swift {

	var $name = 'Swift Performance';
	var $url  = 'https://wordpress.org/plugins/swift-performance-lite/';

	static function run() {
		if ( class_exists( '\Swift_Performance_Cache' ) ) {
			\Swift_Performance_Cache::clear_all_cache();
		}
	}
}
