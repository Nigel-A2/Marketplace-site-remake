<?php
/**
 * @package WPBDP\Helpers
 */

/**
 * Service class used to send an email to users with their access keys.
 *
 * @since 5.1.3
 */
class WPBDP__Access_Keys_Sender {

    /**
     * Send access keys associated with given email address.
     *
     * @since 5.1.3
     */
    public function send_access_keys( $email_address ) {
        if ( ! $email_address || ! is_email( $email_address ) ) {
            $message = _x( '<email-address> is not a valid e-mail address.', 'access keys sender', 'business-directory-plugin' );
            $message = str_replace( '<email-address>', esc_html( $email_address ), $message );

            throw new Exception( $message );
        }

        $listings = $this->find_listings_by_email_address( $email_address );

        if ( empty( $listings ) ) {
            $message = _x( 'There are no listings associated to e-mail address <email-address>.', 'access keys sender', 'business-directory-plugin' );
            $message = str_replace( '<email-address>', esc_html( $email_address ), $message );

            throw new Exception( $message );
        }

        return $this->send_access_keys_for_listings( $listings, $email_address );
    }

    public function send_access_keys_for_listings( $listings, $email_address ) {
        $message = wpbdp_email_from_template(
            WPBDP_PATH . 'templates/email-access-keys.tpl.php',
            array(
                'listings' => $listings,
            )
        );

        $message->subject = sprintf( '[%s] %s', get_bloginfo( 'name' ), _x( 'Listing Access Keys', 'access keys sender', 'business-directory-plugin' ) );
        $message->to = $email_address;

        if ( ! $message->send() ) {
            $message = _x( 'An error occurred while sending the access keys for e-mail address <email-address>. Please try again.', 'access keys sender', 'business-directory-plugin' );
            $message = str_replace( '<email-address>', esc_html( $email_address ), $message );

            throw new Exception( $message );
        }

        return true;
    }

    /**
	 * TODO: Move to a class with all the other available functions/methods for
     * searching listings.
     */
    public function find_listings_by_email_address( $email_address ) {
        $listings = array();

        foreach ( wpbdp_get_listings_by_email( $email_address ) as $listing_id ) {
            $listings[] = WPBDP_Listing::get( $listing_id );
        }

        return $listings;
    }
}
