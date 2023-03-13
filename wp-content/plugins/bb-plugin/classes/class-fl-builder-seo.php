<?php
/**
 * @since 2.4
 */
final class FLBuilderSEO {
	public function __construct() {
		$this->filters();
	}
	private function filters() {
		/**
		 * WordPress 5.5 adds native support for sitemaps so we need to remove our post type and taxonomy.
		 */
		add_filter( 'wp_sitemaps_post_types', function( $post_types ) {
				unset( $post_types['fl-builder-template'] );
				return $post_types;
		} );
		add_filter( 'wp_sitemaps_taxonomies', function( $taxonomies ) {
				unset( $taxonomies['fl-builder-template-category'] );
				return $taxonomies;
		} );
	}
}
new FLBuilderSEO;
