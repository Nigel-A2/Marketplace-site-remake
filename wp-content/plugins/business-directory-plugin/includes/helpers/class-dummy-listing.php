<?php

/**
 * @since 5.0
 */
class WPBDP__Dummy_Listing {

    private $plan = null;


    public function __construct() {
        $this->plan = wpbdp_get_fee_plan( 'free' );
    }

    public function get_id() {
        return 0;
    }

    public function set_fee_plan( $plan ) {
        $this->plan = $plan;
    }

    public function get_fee_plan() {
        $result = new StdClass();
        $result->listing_id = 0;
        $result->fee_id = $this->plan->id;
        $result->fee_price = $this->plan->amount;
        $result->fee_days = $this->plan->days;
        $result->fee_images = $this->plan->images;
        $result->is_recurring = $this->plan->recurring;
        $result->is_sticky = $this->plan->sticky;
        $result->expiration_date = date( 'Y-m-d H:i:s', strtotime( '+1 year' ) );
        $result->fee = $this->plan;
        $result->fee_label = $this->plan->label;
        $result->expired = false;

        return $result;
    }

}
