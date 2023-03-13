<?php
namespace FLCacheClear;
class ACF {
	var $name    = 'Advanced Custom Fields';
	var $url     = 'https://wordpress.org/plugins/advanced-custom-fields/';
	var $actions = array( 'admin_init' );

	function run() {
		add_action( 'acf/save_post', function( $post_id ) {
			if ( is_numeric( $post_id ) ) {
				\FLBuilderModel::delete_all_asset_cache( $post_id );
			} else {
				\FLBuilderModel::delete_asset_cache_for_all_posts();
			}
			// delete partials
			\FLBuilderModel::delete_asset_cache_for_all_posts( '*layout-partial*' );
		});
	}
}
