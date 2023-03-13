<?php

class WPBDP__Migrations__4_0 extends WPBDP__Migration {

	public function migrate() {
		$o = (bool) get_option( WPBDP__Settings::PREFIX . 'send-email-confirmation', false );

		if ( ! $o ) {
			update_option( WPBDP__Settings::PREFIX . 'user-notifications', array( 'listing-published' ) );
		}
		delete_option( WPBDP__Settings::PREFIX . 'send-email-confirmation' );
	}
}
