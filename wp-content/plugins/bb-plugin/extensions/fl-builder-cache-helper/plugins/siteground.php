<?php
namespace FLCacheClear;
class Siteground {

	var $name = 'SiteGround Hosting';
	var $url  = 'https://wordpress.org/plugins/sg-cachepress/';

	static function run() {
		if ( function_exists( '\sg_cachepress_purge_cache' ) ) {
			\sg_cachepress_purge_cache();
		}
	}
}
