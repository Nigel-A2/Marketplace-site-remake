<?php
/**
 * @since 5.0
 */
class WPBDP__Admin__Metaboxes__Listing_Information {

    public function __construct( $post_id ) {
        $this->listing = WPBDP_Listing::get( $post_id );
    }

    /**
	 * @return string
	 */
    public function render() {
        $tabs = array();
        $tabs[] = array(
            'id' => 'plan-info',
			'label'   => __( 'Listing', 'business-directory-plugin' ),
			'content' => $this->plan_info_tab(),
		);
        $tabs[] = array(
            'id' => 'payments',
            'label' => _x( 'Recent Payments', 'listing metabox', 'business-directory-plugin' ),
            'content' => $this->payments_tab() );
        $tabs[] = array(
            'id' => 'other',
            'label' => _x( 'Access Key', 'listing metabox', 'business-directory-plugin' ),
            'content' => $this->other_tab() );
        $tabs = apply_filters( 'wpbdp_listing_metabox_tabs', $tabs, $this->listing );

        return wpbdp_render_page( WPBDP_PATH . 'templates/admin/metaboxes-listing-information.tpl.php', array( 'tabs' => $tabs ) );
    }

	/**
	 * @return string
	 */
    private function plan_info_tab() {
        $vars = array(
            'plans' => wpbdp_get_fee_plans(),
            'listing' => $this->listing,
            'current_plan' => $this->listing ? $this->listing->get_fee_plan() : 0,
        );

        return wpbdp_render_page( WPBDP_PATH . 'templates/admin/metaboxes-listing-information-plan.tpl.php', $vars );
    }

	/**
	 * @return string
	 */
    private function payments_tab() {
        $vars = array(
            'payments' => $this->listing->get_latest_payments(),
            'listing' => $this->listing
        );
        return wpbdp_render_page( WPBDP_PATH . 'templates/admin/metaboxes-listing-information-payments.tpl.php', $vars );
    }

	/**
	 * @return string
	 */
    private function other_tab() {
        $vars = array(
            'access_key' => $this->listing->get_access_key()
        );
        return wpbdp_render_page( WPBDP_PATH . 'templates/admin/metaboxes-listing-information-other.tpl.php', $vars );
    }

}
