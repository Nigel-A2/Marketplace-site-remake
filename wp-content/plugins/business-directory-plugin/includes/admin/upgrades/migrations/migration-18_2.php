<?php

class WPBDP__Migrations__18_2 extends WPBDP__Migration {

    public function migrate() {
        delete_site_transient( 'wpbdp_updates' );
        delete_transient( 'wpbdp_updates' );
        set_site_transient( 'update_plugins', null );
    }

}
