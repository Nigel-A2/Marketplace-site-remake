<?php
/**
 * Manage Listings View allows users to see, edit and delete their listings.
 *
 * @package BDP/Includes/Views
 */

/**
 * @since 4.0
 */
class WPBDP__Views__Manage_Listings extends WPBDP__View {

    public function __construct( $args = null ) {
        parent::__construct( $args );
        add_filter( 'wpbdp_form_field_html_value', array( $this, 'remove_expired_listings_title_links' ), 10, 3 );
        add_filter( 'wpbdp_user_can_view', array( $this, 'maybe_remove_listing_buttons'), 20, 3 );
        add_filter( 'wpbdp_user_can_edit', array( $this, 'maybe_remove_listing_buttons'), 20, 3 );
        add_filter( 'wpbdp_user_can_flagging', array( $this, 'maybe_remove_listing_buttons'), 20, 3 );
        add_filter( 'wpbdp-listing-buttons', array( $this, 'maybe_add_renew_button' ), 10, 2 );
    }

    public function dispatch() {
        $current_user = is_user_logged_in() ? wp_get_current_user() : null;

        if ( ! $current_user ) {
            $login_msg = _x( 'Please <a>login</a> to manage your listings.', 'view:manage-listings', 'business-directory-plugin' );
            $login_msg = str_replace(
                '<a>',
                '<a href="' . esc_attr( add_query_arg( 'redirect_to', urlencode( apply_filters( 'the_permalink', get_permalink() ) ), wpbdp_url( 'login' ) ) ) . '">',
                $login_msg
            );
            return $login_msg;
        }

        $args = array(
            'post_type' => WPBDP_POST_TYPE,
            'post_status' => array( 'publish', 'pending', 'draft' ),
            'paged' => get_query_var( 'paged' ) ? get_query_var( 'paged' ) : 1,
            'author' => $current_user->ID,
            'wpbdp_main_query' => true
        );
        $q = new WP_Query( $args );
        wpbdp_push_query( $q );

		$html = $this->_render_page(
			'manage_listings',
			array(
				'current_user' => $current_user,
				'query'        => $q,
				'_bar'         => ! empty( $this->show_search_bar ) ? $this->show_search_bar : false,
			)
		);

        wpbdp_pop_query();

        return $html;
    }

    public function remove_expired_listings_title_links( $value, $listing_id, $field ) {
        if ( 'title' !== $field->get_association() || current_user_can( 'administrator' ) ) {
            return $value;
        }

        $listing         = wpbdp_get_listing( $listing_id );
        $listing_status  = $listing->get_status();

        if ( 'complete' === $listing_status ) {
            return $value;
        }

        return sprintf( '%s (%s)', $field->plain_value( $listing_id ), $listing_status );
    }

    public function maybe_remove_listing_buttons( $res, $listing_id, $user_id ) {
        if ( current_user_can( 'administrator' ) ) {
            return $res;
        }

        $listing         = wpbdp_get_listing( $listing_id );
        $listing_status  = $listing->get_status();

        if ( 'complete' === $listing_status ) {
            return $res;
        }

        return false;

    }

    /**
     * Show the renew or pay buttons.
     * This shows either the "Renew Listing" or "Pay Now" button depending on the listing status.
     *
     * @return string
     */
    public function maybe_add_renew_button( $buttons, $listing_id ) {
        $listing = wpbdp_get_listing( $listing_id );
        $listing_status  = $listing->get_status();

        if ( 'complete' === $listing_status ) {
            if ( ! $listing->is_published() ) {
                $buttons .= '<span>' . esc_html__( 'Pending', 'business-directory-plugin' ) . '</span> ';
            }

            return $buttons;
        }

		$is_pending_payment = ( 'pending_payment' === $listing_status );
        $buttons = sprintf(
            '<a class="wpbdp-button button renew-listing" href="%s" %s >%s</a>',
            $is_pending_payment ? esc_url( $listing->get_payment_url() ) : esc_url( $listing->get_renewal_url() ),
            'target="_blank" rel="noopener"',
			$is_pending_payment ? esc_html__( 'Pay Now', 'business-directory-plugin' ) : esc_html__( 'Renew Listing', 'business-directory-plugin' )
        ) . $buttons;

        return $buttons;
    }
}
