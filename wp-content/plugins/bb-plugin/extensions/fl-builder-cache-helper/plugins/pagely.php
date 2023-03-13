<?php
namespace FLCacheClear;
class Pagely {

	var $name = 'Pagely Hosting';
	var $url  = 'https://pagely.com/plans-pricing/';

	static function run( $post_id = false ) {

		$templates = array(
			'fl-builder-template',
			'fl-theme-layout',
		);
		if ( class_exists( '\PagelyCachePurge' ) ) {
			$purger = new \PagelyCachePurge();
			if ( $post_id && ! in_array( get_post_type( $post_id ), $templates ) ) {
				$purger->purgePost( $post_id );
			} else {
				$purger->purgeAll();
			}
		}
	}
}
