<?php

class WPBDP__Migrations__11_0 extends WPBDP__Migration {

    public function migrate() {
        // Users upgrading from < 4.x get the pre-4.0 theme.
        update_option( 'wpbdp-active-theme', 'no_theme' );
    }

}
