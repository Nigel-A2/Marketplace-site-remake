<?php

class WPBDP__Migrations__13_0 extends WPBDP__Migration {

    public function migrate() {
        // Make sure no field shortnames conflict.
         $fields = wpbdp_get_form_fields();

         foreach ( $fields as $f )
             $f->save();
    }

}
