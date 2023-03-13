<?php
namespace FLCacheClear;
class Cloudflare {

	var $name    = 'Cloudflare';
	var $url     = 'https://wordpress.org/plugins/cloudflare/';
	var $filters = array( 'init' );

	static function run() {
		// nothing to do here.
	}

	function filters() {
		add_filter( 'cloudflare_purge_everything_actions', function( $actions ) {
			$actions[] = 'fl_builder_cache_cleared';
			return $actions;
		});

		add_filter( 'cloudflare_purge_url_actions', function( $actions ) {
			$actions[] = 'fl_builder_after_save_layout';
			$actions[] = 'fl_builder_after_save_user_template';
			return $actions;
		});
	}
}
