<?php
/**
 * @since 5.1.6
 */
class WPBDP__Admin__Metaboxes__Listing_Flagging {

	public function __construct( $post_id ) {
		$this->listing = wpbdp_get_listing( $post_id );
	}

	/**
	 * @return string
	 */
	public function render( $echo = false ) {
		return wpbdp_render_page(
			WPBDP_PATH . 'templates/admin/metaboxes-listing-flagging.tpl.php',
			array( 'listing' => $this->listing ),
			$echo
		);
	}
}
