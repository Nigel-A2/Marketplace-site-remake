<?php
/**
 * @package WPBDP/Includes/Admin/Helpers/Listing timeline
 * @since 5.0
 */

/**
 * Class WPBDP__Listing_Timeline
 */
class WPBDP__Listing_Timeline {

    private $listing = null;

    public function __construct( $listing_id ) {
        $this->listing = wpbdp_get_listing( $listing_id );
    }

	/**
	 * @return array
	 */
    public function get_items() {
        $items = wpbdp_get_logs(
            array(
				'object_type' => 'listing',
				'object_id'   => $this->listing->get_id(),
				'order'       => 'DESC',
            )
        );

        if ( ! $items ) {
            if ( $this->recreate_logs() ) {
                return $this->get_items();
            }

            return array();
        }

        return $items;
    }

	/**
	 * @return string
	 */
    public function render() {
        $items    = $this->get_items();
        $timeline = array();

        foreach ( $items as $item ) {
            $obj            = clone $item;
            $obj->html      = '';
            $obj->timestamp = strtotime( $obj->created_at );
            $obj->extra     = '';
            $obj->actions   = array();
            $obj->display   = true;

            $callback = 'process_' . str_replace( '.', '_', $obj->log_type );
            if ( method_exists( $this, $callback ) ) {
                $obj = call_user_func( array( $this, $callback ), $obj );
            }

            if ( ! $obj->html ) {
                $obj->html    = $obj->message ? $obj->message : $obj->log_type;
                $obj->display = false;
            }

            $timeline[] = $obj;
        }

        return wpbdp_render_page( WPBDP_PATH . 'templates/admin/metaboxes-listing-timeline.tpl.php', array( 'timeline' => $timeline ) );
    }

	/**
	 * @return bool
	 */
    private function recreate_logs() {
        $post      = get_post( $this->listing->get_id() );
        $post_date = $post->post_date;

        $err = wpbdp_insert_log(
            array(
				'log_type'   => 'listing.created',
				'object_id'  => $post->ID,
				'created_at' => $post_date,
            )
        );

        if ( ! $err ) {
            return false;
        }

        $tos_acceptance = get_post_meta( $this->listing->get_id(), '_wpbdp_tos_acceptance_date', true );

        if ( $tos_acceptance ) {
            wpbdp_insert_log(
                array(
                    'log_type'   => 'listing.terms_and_conditions_accepted',
                    'object_id'  => $post->ID,
                    'created_at' => $tos_acceptance,
                )
            );
        }

        // Insert logs for payments.
        $payments = WPBDP_Payment::objects()->filter( array( 'listing_id' => $post->ID ) );
        foreach ( $payments as $p ) {
            wpbdp_insert_log(
                array(
					'log_type'      => 'listing.payment',
					'object_id'     => $post->ID,
					'rel_object_id' => $p->id,
                )
            );
        }

        return true;
    }

	/**
	 * @return object
	 */
    private function process_listing_created( $item ) {
        $item->html = _x( 'Listing created', 'listing timeline', 'business-directory-plugin' );
        return $item;
    }

	/**
	 * @return object
	 */
    private function process_listing_expired( $item ) {
        $item->html = _x( 'Listing expired', 'listing timeline', 'business-directory-plugin' );
        return $item;
    }

	/**
	 * @return object
	 */
    private function process_listing_renewal( $item ) {
        $item->html = __( 'Listing renewed', 'business-directory-plugin' );
        return $item;
    }

	/**
	 * @return object
	 */
    private function process_listing_terms_and_conditions_accepted( $item ) {
        $item->html = _x( 'T&C acceptance date', 'listing timeline', 'business-directory-plugin' );
        return $item;
    }

	/**
	 * @return string
	 */
    private function process_listing_payment( $item ) {
        $payment = WPBDP_Payment::objects()->get( $item->rel_object_id );

        if ( ! $payment ) {
            return $item;
        }

        $title = $payment->summary;

        if ( 'initial' === $payment->payment_type ) {
            if ( 'admin-submit' === $payment->context ) {
                $title = _x( 'Paid as admin', 'listing timeline', 'business-directory-plugin' );
            } elseif ( 'csv-import' === $payment->context ) {
                $title = _x( 'Listing imported', 'listing timeline', 'business-directory-plugin' );
            } else {
                $title = _x( 'Initial Payment', 'listing timeline', 'business-directory-plugin' );
            }
        }

        $item->html  = '';
        $item->html .= '<a href="' . esc_url( admin_url( 'admin.php?page=wpbdp_admin_payments&wpbdp-view=details&payment-id=' . $payment->id ) ) . '">';
        $item->html .= $title;
        $item->html .= '</a>';

        if ( 'completed' !== $payment->status ) {
            $item->html .= '<span class="payment-status tag ' . $payment->status . '">' . $payment->status . '</span>';
        }

        $item->extra .= '<span class="payment-id">Payment #' . $payment->id . '</span>';
        $item->extra .= '<span class="payment-amount">Amount: ' . wpbdp_currency_format( $payment->amount, 'force_numeric=1' ) . '</span>';

        $item->actions = array(
            'details' => '<a href="' . esc_url( admin_url( 'admin.php?page=wpbdp_admin_payments&wpbdp-view=details&payment-id=' . $payment->id ) ) . '">Go to payment</a>',
        );

        return $item;
    }

}
