<?php

class WPBDP__Authenticated_Listing_View extends WPBDP__View {

	protected function authenticate() {
		if ( ! $this->listing )
			die();

		if ( current_user_can( 'administrator' ) ) {
			return true;
		}

		if ( is_user_logged_in() && $this->listing->owned_by_user() ) {
			return true;
		}

		if ( 'WPBDP__Views__Submit_Listing' == get_class( $this ) && empty( $this->editing ) && ! wpbdp_get_option( 'require-login' ) )
			return true;

		//if ( is_user_logged_in() && ( $this->listing->get_auth ) )

		$key_hash = wpbdp_get_var( array( 'param' => 'access_key_hash' ), 'request' );

		if ( wpbdp_get_option( 'enable-key-access' ) && $key_hash )
			return $this->listing->validate_access_key_hash( $key_hash );

		return false;
	}

}
