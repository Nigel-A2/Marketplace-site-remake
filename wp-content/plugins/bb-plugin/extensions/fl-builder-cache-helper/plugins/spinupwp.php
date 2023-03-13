<?php
namespace FLCacheClear;

class Spinupwp {

	var $name = 'SpinupWP';
	var $url  = 'https://spinupwp.com/';

	static function run() {

		if ( function_exists( 'spinupwp_purge_site' ) ) {
			spinupwp_purge_site();
		}
	}
}
