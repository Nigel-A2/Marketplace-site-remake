<?php

if ( ! class_exists( 'WPBDP_Listings_API' ) ) {

    /**
     * @since 3.5.4
     */
    class WPBDP_Listings_API {

        public function __construct() {
            add_action( 'wpbdp_payment_completed', array( $this, 'update_listing_after_payment' ) );
        }

        public function update_listing_after_payment( $payment ) {
            $listing = $payment->get_listing();

            if ( ! $listing ) {
                return;
            }

            foreach ( $payment->payment_items as $item ) {
                switch ( $item['type'] ) {
                    case 'recurring_plan':
                    case 'plan':
                        $listing->update_plan( $item, array( 'recalculate' => ! empty( $item['is_renewal'] ) ? 0 : 1 ) );

                        if ( ! empty( $item['is_renewal'] ) ) {
                            $listing->renew();
                            wpbdp_insert_log(
                                array(
                                    'log_type'  => 'listing.renewal',
                                    'object_id' => $payment->listing_id,
                                    'message'   => __( 'Listing renewed', 'business-directory-plugin' ),
                                )
                            );
                        }
                        break;
                }
            }

            $listing->set_status( 'complete' );

            if ( 'initial' === $payment->payment_type ) {
				$new_status = wpbdp_get_option( 'new-post-status' );
				if ( $new_status !== 'publish' && $payment->amount > 0 ) {
					// If this was a paid listing, maybe mark as published.
					$new_status = apply_filters( 'wpbdp_paid_listing_status', $new_status, compact( 'payment' ) );
				}
				$listing->set_post_status( $new_status );
            }
        }

        /**
         * Performs a "quick search" for listings on the fields marked as quick-search fields in the plugin settings page.
         *
         * @return array The listing IDs.
         * @since 3.4
		 * @deprecated 6.0
         */
        public function quick_search() {
			_deprecated_function( __METHOD__, '6.0' );
			return array();
        }

        /**
         * @deprecated 5.0. Added back in 5.1.2 for compatibility with other plugins (#3178)
         */
        public function get_thumbnail_id( $listing_id ) {
			_deprecated_function( __METHOD__, '5.0', 'WPBDP_Listing::get_thumbnail_id' );

            if ( $listing = wpbdp_get_listing( $listing_id ) ) {
                return $listing->get_thumbnail_id();
            }

            return 0;
        }

    }
}
