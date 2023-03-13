<?php
namespace FLCacheClear;
class Godaddy {

	var $name = 'Godaddy Hosting';

	static function run() {
		if ( class_exists( '\WPaaS\Cache' ) ) {
			if ( method_exists( '\WPaaS\Cache', 'ban' ) ) {
				\WPaaS\Cache::ban();
			}
		}
	}
}
