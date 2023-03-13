<?php
namespace FLCacheClear;
class Pantheon {

	var $name = 'Pantheon Hosting';
	var $url  = 'https://pantheon.io/';

	static function run() {
		if ( function_exists( 'pantheon_clear_edge_all' ) ) {
			$ret = pantheon_clear_edge_all();
		}
	}
}
