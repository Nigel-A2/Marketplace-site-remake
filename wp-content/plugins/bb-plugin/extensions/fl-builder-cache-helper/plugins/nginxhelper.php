<?php
namespace FLCacheClear;
class Nginxhelper {

	public $name = 'Nginx Helper';
	public $url  = 'https://wordpress.org/plugins/nginx-helper/';

	public static function run() {
		if ( class_exists( '\Nginx_Helper' ) ) {
			global $nginx_purger;
			$nginx_purger->purge_all();
		}
	}
}
