<?php
/**
 * @since 5.0
 */
class WPBDP__Cron {

    public function __construct() {
        $this->schedule_events();
    }

    private function schedule_events() {
        if ( ! wp_next_scheduled( 'wpbdp_hourly_events' ) )
            wp_schedule_event( current_time( 'timestamp' ), 'hourly', 'wpbdp_hourly_events' );

        if ( ! wp_next_scheduled( 'wpbdp_daily_events' ) )
            wp_schedule_event( current_time( 'timestamp' ), 'daily', 'wpbdp_daily_events' );
    }

}
