<?php

class WPBDP__Migrations__12_0 extends WPBDP__Migration {

    public function migrate() {
        delete_transient( 'wpbdp-themes-updates' );
    }

}
