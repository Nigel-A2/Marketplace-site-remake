<?php

class WPBDP_SEO {

	public static function is_wp_seo_enabled() {
		return defined( 'WPSEO_VERSION' );
	}

	public static function listing_title( $listing_id ) {
		return get_the_title( $listing_id );
	}

	public static function listing_og_description( $listing_id ) {
		$listing = WPBDP_Listing::get( $listing_id );
		return $listing->get_field_value( 'excerpt' );
	}

}
