<?php
namespace FLCacheClear;
//phpcs:ignore WordPress.WP.CapitalPDangit.MisspelledClassName
class Wordpress {
	var $name = 'Object Caching';

	function run() {
		wp_cache_flush();
	}
}
