<?php
/**
 * @package WPBDP\Admin\Upgrades\Migrations
 */

/**
 * Migration for DB version 18.3
 */
class WPBDP__Migrations__18_3 extends WPBDP__Migration {

    /**
     * @since 5.1.10
     */
    public function migrate() {
        $enabled_notifications = $this->get_enabled_notifications();

        wpbdp_set_option( 'user-notifications', $enabled_notifications );
    }

    /**
     * @since 5.1.10
     */
    private function get_enabled_notifications() {
        $enabled_notifications = wpbdp_get_option( 'user-notifications' );

        if ( ! is_array( $enabled_notifications ) ) {
            return array(
                'new-listing',
                'listing-published',
                'listing-expires',
            );
        }

        $enabled_notifications[] = 'listing-expires';

        return array_unique( $enabled_notifications );
    }
}
