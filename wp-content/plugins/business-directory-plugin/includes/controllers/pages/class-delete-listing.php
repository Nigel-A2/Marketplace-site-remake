<?php
/**
 * Delete Listing view
 *
 * @package BDP/Includes/Views
 */

require_once WPBDP_PATH . 'includes/helpers/class-authenticated-listing-view.php';

/**
 * @since 4.0
 */
class WPBDP__Views__Delete_Listing extends WPBDP__Authenticated_Listing_View {

    public function dispatch() {
        $listing_id    = intval( wpbdp_get_var( array( 'param' => 'listing_id' ), 'request' ) );
        $this->listing = WPBDP_Listing::get( $listing_id );

        if ( ! $this->listing || 'trash' === get_post_status( $listing_id ) ) {
            $this->_redirect( wpbdp_url() );
        }

        $this->_auth_required(
            array(
                'wpbdp_view'          => 'delete_listing',
                'redirect_query_args' => array(
                    'listing_id' => $this->listing->get_id(),
                ),
            )
        );

        $nonce = wpbdp_get_var( array( 'param' => '_wpnonce' ), 'request' );

        if ( $nonce && wp_verify_nonce( $nonce, 'delete listing ' . $this->listing->get_id() ) ) {
            $this->listing->delete();
            $html  = wpbdp_render_msg( _x( 'Your listing has been deleted.', 'delete listing', 'business-directory-plugin' ) );
            $v     = wpbdp_load_view( 'main' );
            $html .= $v->dispatch();
            return $html;
        }

        return wpbdp_render(
            'delete-listing-confirm', array(
				'listing'       => $this->listing,
				'has_recurring' => $this->has_recurring_fee(),
            )
        );
    }

    private function has_recurring_fee() {
        global $wpdb;

        return (bool) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT 1 AS x FROM {$wpdb->prefix}wpbdp_listings WHERE listing_id = %d AND is_recurring = %d",
                $this->listing->get_id(),
                1
            )
        );
    }

}

